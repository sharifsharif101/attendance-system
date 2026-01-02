<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceStatus;
use App\Models\Employee;
use App\Models\MonthlySetting;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeReportController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $departmentId = $request->get('department_id', '');
        
        $employees = Employee::query()
            ->with('department')
            ->when($query, function($q) use ($query) {
                $q->where(function($q2) use ($query) {
                    $q2->where('name', 'like', "%{$query}%")
                       ->orWhere('employee_number', 'like', "%{$query}%");
                });
            })
            ->when($departmentId, function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(50)
            ->get();
        
        $departments = \App\Models\Department::where('is_active', true)->get();
        
        return view('reports.search', compact('employees', 'departments', 'query', 'departmentId'));
    }

    public function show(Request $request, Employee $employee)
    {
        // الفترة الزمنية
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();
        
        // إذا كان الشهر الحالي، نأخذ حتى اليوم فقط
        if ($endDate->isFuture()) {
            $endDate = Carbon::today();
        }

        // جلب سجلات الحضور للموظف
        $records = AttendanceRecord::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date', 'desc')
            ->get();

        // جلب حالات الحضور
        $statuses = AttendanceStatus::getActive();
        
        // الحالات التي تُحسب كحضور
        $presentStatusCodes = AttendanceStatus::where('counts_as_present', true)
            ->pluck('code')
            ->toArray();
        
        // الحالات المستثناة من الحساب
        $excludedStatusCodes = AttendanceStatus::where('is_excluded', true)
            ->pluck('code')
            ->toArray();

        // حساب الإحصائيات
        $summary = [];
        foreach ($statuses as $status) {
            $summary[$status->code] = [
                'name' => $status->name,
                'color' => $status->color,
                'count' => $records->where('status', $status->code)->count(),
                'counts_as_present' => $status->counts_as_present,
                'is_excluded' => $status->is_excluded,
            ];
        }

        // حساب أيام العمل (استبعاد الإجازات الأسبوعية والعطلات الرسمية)
        $weekendDays = MonthlySetting::getWeekendDays($month);
        // جلب العطلات الرسمية خلال الشهر
        $officialHolidays = \App\Models\OfficialHoliday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('date')
            ->map(fn($d) => $d->format('Y-m-d')) // تأكد من تنسيق التاريخ
            ->toArray();

        $workingDays = 0;
        $workingDates = []; // لتخزين تواريخ أيام العمل
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $currentDateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->dayOfWeek;
            $dayMap = [0 => 'sunday', 1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday'];
            
            // يوم عمل إذا لم يكن عطلة أسبوعية ولا عطلة رسمية
            if (!in_array($dayMap[$dayOfWeek], $weekendDays) && !in_array($currentDateStr, $officialHolidays)) {
                $workingDays++;
                $workingDates[] = $currentDateStr;
            }
            $currentDate->addDay();
        }

        // حساب أيام الحضور والأيام المستثناة ضمن حلقة واحدة لضمان عدم التعارض
        $presentDays = 0;
        $excludedDays = 0;

        foreach ($records as $record) {
            $recordDate = \Carbon\Carbon::parse($record->date)->format('Y-m-d');
            
            // نتأكد أن اليوم هو يوم عمل أصلاً
            if (in_array($recordDate, $workingDates)) {
                // هل يحسب كحضور؟
                $countsAsPresent = $record->counts_as_present_snapshot ?? in_array($record->status, $presentStatusCodes);
                // هل هو يوم مستثنى؟
                $isExcluded = $record->is_excluded_snapshot ?? in_array($record->status, $excludedStatusCodes);

                if ($countsAsPresent) {
                    $presentDays++;
                    // هام: إذا حضر الموظف، لا نحسب اليوم كيوم مستثنى حتى لو كان كذلك
                    // بذلك يصبح يوماً عادياً 100% ولا نتجاوز النسبة
                } elseif ($isExcluded) {
                    // نحسبه مستثنى فقط إذا لم يحضر
                    $excludedDays++;
                }
            }
        }

        
        // حساب نسبة الانضباط (مع استثناء الإجازات)
        $effectiveWorkingDays = $workingDays - $excludedDays;
        $attendanceRate = $effectiveWorkingDays > 0 ? round(($presentDays / $effectiveWorkingDays) * 100) : 0;

        // الإحصائيات السنوية
        $yearlyStats = $this->getYearlyStats($employee, Carbon::parse($month)->year);

        return view('reports.employee', compact(
            'employee',
            'records',
            'statuses',
            'summary',
            'workingDays',
            'excludedDays',
            'effectiveWorkingDays',
            'presentDays',
            'attendanceRate',
            'month',
            'startDate',
            'endDate',
            'yearlyStats'
        ));
    }

    private function getYearlyStats(Employee $employee, $year)
    {
        $stats = [];
        
        // الحالات التي تُحسب كحضور
        $presentStatusCodes = AttendanceStatus::where('counts_as_present', true)
            ->pluck('code')
            ->toArray();
        
        // الحالات المستثناة من الحساب
        $excludedStatusCodes = AttendanceStatus::where('is_excluded', true)
            ->pluck('code')
            ->toArray();
        
        for ($m = 1; $m <= 12; $m++) {
            $month = sprintf('%d-%02d', $year, $m);
            $startDate = Carbon::parse($month)->startOfMonth();
            $endDate = Carbon::parse($month)->endOfMonth();
            
            // تخطي الأشهر المستقبلية
            if ($startDate->isFuture()) {
                continue;
            }

            // إذا كان الشهر الحالي
            if ($endDate->isFuture()) {
                $endDate = Carbon::today();
            }

            $records = AttendanceRecord::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();

            // حساب أيام العمل وبناء قائمة تواريخ أيام العمل
            $weekendDays = MonthlySetting::getWeekendDays($month);
            // جلب العطلات الرسمية
            $officialHolidays = \App\Models\OfficialHoliday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->pluck('date')
                ->map(fn($d) => $d->format('Y-m-d'))
                ->toArray();

            $workingDays = 0;
            $workingDates = [];
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                $currentDateStr = $currentDate->format('Y-m-d');
                $dayOfWeek = $currentDate->dayOfWeek;
                $dayMap = [0 => 'sunday', 1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday'];
                
                if (!in_array($dayMap[$dayOfWeek], $weekendDays) && !in_array($currentDateStr, $officialHolidays)) {
                    $workingDays++;
                    $workingDates[] = $currentDateStr;
                }
                $currentDate->addDay();
            }

            // حساب أيام الحضور والأيام المستثناة
            $presentDays = 0;
            $excludedDays = 0;

            foreach ($records as $record) {
                $recordDate = \Carbon\Carbon::parse($record->date)->format('Y-m-d');
                if (in_array($recordDate, $workingDates)) {
                    $countsAsPresent = $record->counts_as_present_snapshot ?? in_array($record->status, $presentStatusCodes);
                    $isExcluded = $record->is_excluded_snapshot ?? in_array($record->status, $excludedStatusCodes);

                    if ($countsAsPresent) {
                        $presentDays++;
                    } elseif ($isExcluded) {
                        $excludedDays++;
                    }
                }
            }
            
            // حساب نسبة الانضباط
            $effectiveWorkingDays = $workingDays - $excludedDays;
            $attendanceRate = $effectiveWorkingDays > 0 ? round(($presentDays / $effectiveWorkingDays) * 100) : 0;

            $stats[] = [
                'month' => $month,
                'month_name' => $this->getArabicMonth($m),
                'working_days' => $workingDays,
                'excluded_days' => $excludedDays,
                'effective_days' => $effectiveWorkingDays,
                'present_days' => $presentDays,
                'absent_days' => $records->where('status', 'absent')->count(),
                'attendance_rate' => $attendanceRate,
            ];
        }

        return $stats;
    }

    private function getArabicMonth($month)
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];
        return $months[$month] ?? '';
    }
}