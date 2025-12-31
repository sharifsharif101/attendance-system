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

        // حساب الإحصائيات
        $summary = [];
        foreach ($statuses as $status) {
            $summary[$status->code] = [
                'name' => $status->name,
                'color' => $status->color,
                'count' => $records->where('status', $status->code)->count(),
            ];
        }

        // حساب أيام العمل (استبعاد الإجازات الأسبوعية)
        $weekendDays = MonthlySetting::getWeekendDays($month);
        $workingDays = 0;
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->dayOfWeek;
            $dayMap = [0 => 'sunday', 1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday'];
            
            if (!in_array($dayMap[$dayOfWeek], $weekendDays)) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        // حساب نسبة الانضباط
        $presentDays = ($summary['present']['count'] ?? 0) + ($summary['late']['count'] ?? 0);
        $attendanceRate = $workingDays > 0 ? round(($presentDays / $workingDays) * 100) : 0;

        // الإحصائيات السنوية
        $yearlyStats = $this->getYearlyStats($employee, Carbon::parse($month)->year);

        return view('reports.employee', compact(
            'employee',
            'records',
            'statuses',
            'summary',
            'workingDays',
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

            // حساب أيام العمل
            $weekendDays = MonthlySetting::getWeekendDays($month);
            $workingDays = 0;
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                $dayOfWeek = $currentDate->dayOfWeek;
                $dayMap = [0 => 'sunday', 1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday'];
                
                if (!in_array($dayMap[$dayOfWeek], $weekendDays)) {
                    $workingDays++;
                }
                $currentDate->addDay();
            }

            $presentDays = $records->whereIn('status', ['present', 'late'])->count();
            $attendanceRate = $workingDays > 0 ? round(($presentDays / $workingDays) * 100) : 0;

            $stats[] = [
                'month' => $month,
                'month_name' => $this->getArabicMonth($m),
                'working_days' => $workingDays,
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