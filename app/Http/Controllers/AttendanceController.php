<?php
// ختم الجودة
namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\DayLock;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AttendanceStatus;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // عرض صفحة التحضير
// عرض صفحة التحضير
    public function index(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $departmentId = $request->get('department_id');
$statuses = AttendanceStatus::getActive();

        // جلب الأقسام حسب صلاحية المستخدم
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            // الأدمن يرى كل الأقسام
            $departments = Department::where('is_active', true)->get();
        } else {
            // المستخدم العادي يرى فقط أقسامه
            $departments = $user->departments()->where('is_active', true)->get();
        }
        
        $employees = collect();
        $isLocked = false;

        if ($departmentId) {
            // التحقق من أن المستخدم لديه صلاحية على هذا القسم
            if (!$user->hasRole('admin') && !$user->departments->contains($departmentId)) {
                abort(403, 'ليس لديك صلاحية على هذا القسم');
            }

            $employees = Employee::where('department_id', $departmentId)
                ->where('is_active', true)
                ->with(['attendanceRecords' => function ($query) use ($date) {
                    $query->where('date', $date);
                }])
                ->get();

            $isLocked = $this->isDayLocked($date, $departmentId);
        }

        // التحقق من العطلات
        $month = Carbon::parse($date)->format('Y-m');
        $weekendDays = \App\Models\MonthlySetting::getWeekendDays($month);
        $dayName = strtolower(Carbon::parse($date)->format('l'));
        $isWeekend = in_array($dayName, $weekendDays);
        
        $isOfficialHoliday = \App\Models\OfficialHoliday::where('date', $date)->exists();
        
        $nonWorkingDayReason = '';
        if ($isOfficialHoliday) {
            $nonWorkingDayReason = 'عطلة رسمية';
        } elseif ($isWeekend) {
            $nonWorkingDayReason = 'إجازة أسبوعية';
        }

        return view('attendance.index', compact('departments', 'employees', 'date', 'departmentId', 'isLocked', 'statuses', 'isWeekend', 'isOfficialHoliday', 'nonWorkingDayReason'));
    }

    // حفظ الحضور
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'department_id' => 'required|exists:departments,id',
            'date' => 'required|date',
            'status' => ['required', \Illuminate\Validation\Rule::exists('attendance_statuses', 'code')->where('is_active', true)],
            'check_in_time' => 'nullable|date_format:H:i',
        ]);

        $this->checkDepartmentAccess($request->department_id);

        // التحقق من أن الموظف يتبع للقسم المحدد (حماية أمنية)
        $employee = Employee::where('id', $request->employee_id)
            ->where('department_id', $request->department_id)
            ->first();

        if (!$employee) {
            return back()->with('error', 'الموظف غير موجود في هذا القسم أو ليس لديك صلاحية عليه');
        }

        // التحقق من القفل
        if ($this->isDayLocked($request->date, $request->department_id)) {
            return back()->with('error', 'هذا اليوم مقفل ولا يمكن التعديل');
        }



        // جلب إعدادات الحالة لحفظها مع السجل
        $statusModel = AttendanceStatus::where('code', $request->status)->first();
        
        AttendanceRecord::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date' => $request->date,
            ],
            [
                'status' => $request->status,
                'check_in_time' => $request->check_in_time,
                'notes' => $request->notes,
                'recorded_by' => Auth::id(),
                'is_excluded_snapshot' => $statusModel?->is_excluded ?? false,
                'counts_as_present_snapshot' => $statusModel?->counts_as_present ?? false,
            ]
        );

        return back()->with('success', 'تم حفظ الحضور بنجاح');
    }
    // التحضير الجماعي
    public function bulkStore(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'date' => 'required|date',
            'status' => ['required', \Illuminate\Validation\Rule::exists('attendance_statuses', 'code')->where('is_active', true)],
        ]);

        $this->checkDepartmentAccess($request->department_id);

        // التحقق من القفل
        // التحقق من القفل
        if ($this->isDayLocked($request->date, $request->department_id)) {
            return back()->with('error', 'هذا اليوم مقفل ولا يمكن التعديل');
        }

        return DB::transaction(function () use ($request) {
            // جلب موظفين القسم
            $employees = Employee::where('department_id', $request->department_id)
                ->where('is_active', true)
                ->get();

            // جلب إعدادات الحالة لحفظها مع السجل
            $statusModel = AttendanceStatus::where('code', $request->status)->first();
            
            foreach ($employees as $employee) {
                AttendanceRecord::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => $request->date,
                    ],
                    [
                        'status' => $request->status,
                        'recorded_by' => Auth::id(),
                        'is_excluded_snapshot' => $statusModel?->is_excluded ?? false,
                        'counts_as_present_snapshot' => $statusModel?->counts_as_present ?? false,
                    ]
                );
            }

            return back()->with('success', 'تم تحضير ' . $employees->count() . ' موظف بنجاح');
        });
    }

    // حفظ الكل دفعة واحدة
    public function storeAll(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'attendance' => 'required|array',
            'attendance.*.employee_id' => 'required|exists:employees,id',
            'attendance.*.status' => ['required', \Illuminate\Validation\Rule::exists('attendance_statuses', 'code')->where('is_active', true)],
        ]);

        $this->checkDepartmentAccess($request->department_id);

         // التحقق من القفل
        if ($this->isDayLocked($request->date, $request->department_id)) {
            return back()->with('error', 'هذا اليوم مقفل ولا يمكن التعديل');
        }

        return DB::transaction(function () use ($request) {
            $count = 0;
            // جلب جميع الحالات لاستخدامها
            $allStatuses = AttendanceStatus::all()->keyBy('code');

            // حماية أمنية: جلب أرقام الموظفين الصالحين في هذا القسم فقط
            $requestEmployeeIds = collect($request->attendance)->pluck('employee_id')->toArray();
            $validEmployeeIds = Employee::where('department_id', $request->department_id)
                ->whereIn('id', $requestEmployeeIds)
                ->pluck('id')
                ->toArray();
            
            foreach ($request->attendance as $record) {
                // تخطي الموظفين الذين لا ينتمون للقسم
                if (!in_array($record['employee_id'], $validEmployeeIds)) {
                    continue;
                }

                $statusModel = $allStatuses->get($record['status']);
                
                AttendanceRecord::updateOrCreate(
                    [
                        'employee_id' => $record['employee_id'],
                        'date' => $request->date,
                    ],
                    [
                        'status' => $record['status'],
                        'notes' => $record['notes'] ?? null,
                        'recorded_by' => Auth::id(),
                        'is_excluded_snapshot' => $statusModel?->is_excluded ?? false,
                        'counts_as_present_snapshot' => $statusModel?->counts_as_present ?? false,
                    ]
                );
                $count++;
            }

            return back()->with('success', 'تم حفظ ' . $count . ' سجل بنجاح');
        });
    }
    // قفل اليوم
    public function lockDay(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
        ]);

        $this->checkDepartmentAccess($request->department_id);

        // التحقق إذا كان مقفل مسبقاً
        // التحقق إذا كان مقفل مسبقاً
        if ($this->isDayLocked($request->date, $request->department_id)) {
            return back()->with('error', 'هذا اليوم مقفل مسبقاً');
        }

        // التحقق من تسجيل جميع الموظفين
        $department = Department::findOrFail($request->department_id);
        $employeesCount = $department->employees()->where('is_active', true)->count();
        $recordsCount = AttendanceRecord::where('date', $request->date)
            ->whereHas('employee', function ($query) use ($request) {
                $query->where('department_id', $request->department_id)
                      ->where('is_active', true);
            })
            ->count();

        if ($recordsCount < $employeesCount) {
            return back()->with('error', 'لا يمكن القفل - يوجد ' . ($employeesCount - $recordsCount) . ' موظف بدون تحضير');
        }

        DayLock::create([
            'date' => $request->date,
            'department_id' => $request->department_id,
            'locked_by' => Auth::id(),
            'locked_at' => now(),
        ]);

        return back()->with('success', 'تم قفل اليوم بنجاح');
    }

    // فتح اليوم
    public function unlockDay(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
        ]);

        $this->checkDepartmentAccess($request->department_id);

        $deleted = DayLock::where('date', $request->date)
            ->where('department_id', $request->department_id)
            ->delete();

        if ($deleted) {
            return back()->with('success', 'تم فتح اليوم بنجاح');
        }

        return back()->with('error', 'هذا اليوم غير مقفل');
    }
    // التحقق من صلاحية المستخدم على القسم
private function checkDepartmentAccess($departmentId)
{
    $user = Auth::user();
    
    // الأدمن يمكنه الوصول لكل الأقسام
    if ($user->hasRole('admin')) {
        return true;
    }
    
    // التحقق من أن القسم ضمن أقسام المستخدم
    $userDepartmentIds = $user->departments()->pluck('departments.id')->toArray();
    
    if (!in_array($departmentId, $userDepartmentIds)) {
        abort(403, 'ليس لديك صلاحية على هذا القسم');
    }
    
    return true;
}

private function isDayLocked($date, $departmentId)
{
    return DayLock::where('date', $date)
        ->where('department_id', $departmentId)
        ->exists();
}

// حفظ جماعي عبر AJAX
public function ajaxBulkStore(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'department_id' => 'required|exists:departments,id',
        'employee_ids' => 'required|array',
        'employee_ids.*' => 'exists:employees,id',
        'status' => ['required', \Illuminate\Validation\Rule::exists('attendance_statuses', 'code')->where('is_active', true)],
    ]);

    $this->checkDepartmentAccess($request->department_id);

    // التحقق من القفل
    // التحقق من القفل
    if ($this->isDayLocked($request->date, $request->department_id)) {
        return response()->json([
            'success' => false,
            'message' => 'هذا اليوم مقفل'
        ], 403);
    }

    return DB::transaction(function () use ($request) {
        $count = 0;
        // جلب إعدادات الحالة لحفظها مع السجل
        $statusModel = AttendanceStatus::where('code', $request->status)->first();
        
        // حماية أمنية: جلب الموظفين الذين ينتمون فعلاً للقسم المحدد
        $validEmployees = Employee::where('department_id', $request->department_id)
            ->whereIn('id', $request->employee_ids)
            ->where('is_active', true)
            ->get();

        foreach ($validEmployees as $employee) {
            AttendanceRecord::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date' => $request->date,
                ],
                [
                    'status' => $request->status,
                    'recorded_by' => Auth::id(),
                    'is_excluded_snapshot' => $statusModel?->is_excluded ?? false,
                    'counts_as_present_snapshot' => $statusModel?->counts_as_present ?? false,
                ]
            );
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "تم حفظ $count سجل بنجاح",
            'count' => $count
        ]);
    });
}
}