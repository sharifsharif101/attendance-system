<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceStatus;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use Carbon\Carbon;
use App\Models\MonthlySetting;
class ReportController extends Controller
{
    // التقرير اليومي
    public function daily(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $departmentId = $request->get('department_id');

        // جلب الأقسام حسب صلاحية المستخدم
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            $departments = Department::where('is_active', true)->get();
        } else {
            $departments = $user->departments()->where('is_active', true)->get();
        }

        $statuses = AttendanceStatus::getActive();
        $records = collect();
        $summary = [];

        if ($departmentId) {
            $records = AttendanceRecord::with(['employee'])
                ->where('date', $date)
                ->whereHas('employee', function ($query) use ($departmentId) {
                    $query->where('department_id', $departmentId);
                })
                ->get();

            // إحصائيات الحالات
            foreach ($statuses as $status) {
                $summary[$status->code] = [
                    'name' => $status->name,
                    'color' => $status->color,
                    'count' => $records->where('status', $status->code)->count(),
                ];
            }
        }

        return view('reports.daily', compact('departments', 'records', 'date', 'departmentId', 'statuses', 'summary'));
    }

    // التقرير الشهري
 // التقرير الشهري
public function monthly(Request $request)
{
    $month = $request->get('month', date('Y-m'));
    $departmentId = $request->get('department_id');

    // جلب الأقسام حسب صلاحية المستخدم
    $user = Auth::user();
    if ($user->hasRole('admin')) {
        $departments = Department::where('is_active', true)->get();
    } else {
        $departments = $user->departments()->where('is_active', true)->get();
    }

    $statuses = AttendanceStatus::getActive();
    $employees = collect();
    $summary = [];
    $workingDays = 0;

    if ($departmentId) {
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

  
// جلب أيام الإجازة من إعدادات الشهر
$weekendDays = MonthlySetting::getWeekendDays($month);

// حساب أيام العمل
$start = Carbon::parse($startDate);
$end = Carbon::parse($endDate);

while ($start <= $end) {
    $dayName = strtolower($start->format('l'));
    if (!in_array($dayName, $weekendDays)) {
        $workingDays++;
    }
    $start->addDay();
}
        $employees = Employee::where('department_id', $departmentId)
            ->where('is_active', true)
            ->with(['attendanceRecords' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }])
            ->get();

        // إحصائيات لكل موظف
        foreach ($employees as $employee) {
            $employeeStats = [];
            $presentDays = 0;
            
            foreach ($statuses as $status) {
                $count = $employee->attendanceRecords
                    ->where('status', $status->code)
                    ->count();
                $employeeStats[$status->code] = $count;
                
                // حساب أيام الحضور (حاضر + متأخر)
                if (in_array($status->code, ['present', 'late'])) {
                    $presentDays += $count;
                }
            }
            
            // نسبة الانضباط
            $employeeStats['working_days'] = $workingDays;
            $employeeStats['attendance_rate'] = $workingDays > 0 
                ? round(($presentDays / $workingDays) * 100) 
                : 0;
            
            $summary[$employee->id] = $employeeStats;
        }
    }

    return view('reports.monthly', compact('departments', 'employees', 'month', 'departmentId', 'statuses', 'summary', 'workingDays'));
}
}