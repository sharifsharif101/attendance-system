<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\DayLock;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceStatus;
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

            $isLocked = DayLock::where('date', $date)
                ->where('department_id', $departmentId)
                ->exists();
        }

    return view('attendance.index', compact('departments', 'employees', 'date', 'departmentId', 'isLocked', 'statuses'));
    }

    // حفظ الحضور
    public function store(Request $request)
    {
            $this->checkDepartmentAccess($request->department_id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused,leave',
        ]);

        // التحقق من القفل
        $employee = Employee::findOrFail($request->employee_id);
        $isLocked = DayLock::where('date', $request->date)
            ->where('department_id', $employee->department_id)
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'هذا اليوم مقفل ولا يمكن التعديل');
        }

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
            ]
        );

        return back()->with('success', 'تم حفظ الحضور بنجاح');
    }
    // التحضير الجماعي
    public function bulkStore(Request $request)
    {
            $this->checkDepartmentAccess($request->department_id);

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused,leave',
        ]);

        // التحقق من القفل
        $isLocked = DayLock::where('date', $request->date)
            ->where('department_id', $request->department_id)
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'هذا اليوم مقفل ولا يمكن التعديل');
        }

        // جلب موظفين القسم
        $employees = Employee::where('department_id', $request->department_id)
            ->where('is_active', true)
            ->get();

        foreach ($employees as $employee) {
            AttendanceRecord::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date' => $request->date,
                ],
                [
                    'status' => $request->status,
                    'recorded_by' => Auth::id(),
                ]
            );
        }

        return back()->with('success', 'تم تحضير ' . $employees->count() . ' موظف بنجاح');
    }

    // حفظ الكل دفعة واحدة
    public function storeAll(Request $request)
    {
            $this->checkDepartmentAccess($request->department_id);

        $request->validate([
            'date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'attendance' => 'required|array',
            'attendance.*.employee_id' => 'required|exists:employees,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused,leave',
        ]);

        // التحقق من القفل
        $isLocked = DayLock::where('date', $request->date)
            ->where('department_id', $request->department_id)
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'هذا اليوم مقفل ولا يمكن التعديل');
        }

        $count = 0;
        foreach ($request->attendance as $record) {
            AttendanceRecord::updateOrCreate(
                [
                    'employee_id' => $record['employee_id'],
                    'date' => $request->date,
                ],
                [
                    'status' => $record['status'],
                    'notes' => $record['notes'] ?? null,
                    'recorded_by' => Auth::id(),
                ]
            );
            $count++;
        }

        return back()->with('success', 'تم حفظ ' . $count . ' سجل بنجاح');
    }
    // قفل اليوم
    public function lockDay(Request $request)
    {
            $this->checkDepartmentAccess($request->department_id);

        $request->validate([
            'date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
        ]);

        // التحقق إذا كان مقفل مسبقاً
        $exists = DayLock::where('date', $request->date)
            ->where('department_id', $request->department_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'هذا اليوم مقفل مسبقاً');
        }

        // التحقق من تسجيل جميع الموظفين
        $department = Department::findOrFail($request->department_id);
        $employeesCount = $department->employees()->where('is_active', true)->count();
        $recordsCount = AttendanceRecord::where('date', $request->date)
            ->whereHas('employee', function ($query) use ($request) {
                $query->where('department_id', $request->department_id);
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
            $this->checkDepartmentAccess($request->department_id);

        $request->validate([
            'date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
        ]);

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
}