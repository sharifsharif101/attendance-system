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
        $employees = collect();
        $summary = [];

        if ($departmentId) {
            // جلب جميع الموظفين في القسم مع سجل الحضور لليوم المحدد (إن وجد)
            $employees = Employee::where('department_id', $departmentId)
                ->where('is_active', true)
                ->with(['attendanceRecords' => function ($query) use ($date) {
                    $query->where('date', $date);
                }])
                ->get();

            // إحصائيات الحالات
            foreach ($statuses as $status) {
                // عدد الموظفين الذين لديهم هذه الحالة
                $count = $employees->filter(function($emp) use ($status) {
                    return $emp->attendanceRecords->first()?->status === $status->code;
                })->count();

                $summary[$status->code] = [
                    'name' => $status->name,
                    'color' => $status->color,
                    'count' => $count,
                ];
            }
            
            // إضافة إحصائية "غير مسجل" (الذين ليس لديهم سجل)
            $notRecordedCount = $employees->filter(function($emp) {
                return $emp->attendanceRecords->isEmpty();
            })->count();
            
             $summary['not_recorded'] = [
                'name' => 'غير مسجل',
                'color' => '#9ca3af', // gray-400
                'count' => $notRecordedCount,
            ];
        }

        return view('reports.daily', compact('departments', 'employees', 'date', 'departmentId', 'statuses', 'summary'));
    }

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

            // إذا كان الشهر الحالي، نأخذ حتى اليوم فقط (تم التعطيل للسماح بالمستقبل)
            // if (Carbon::now()->format('Y-m') == $month) {
            //     $endDate = date('Y-m-d');
            // }

            // جلب أيام الإجازة من إعدادات الشهر (عطلة أسبوعية)
            $weekendDays = MonthlySetting::getWeekendDays($month);
            // جلب العطلات الرسمية
            $officialHolidays = \App\Models\OfficialHoliday::whereBetween('date', [$startDate, $endDate])
                ->pluck('date')
                ->map(fn($d) => $d->format('Y-m-d'))
                ->toArray();

            // حساب أيام العمل
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $workingDates = []; // لتخزين تواريخ أيام العمل

            while ($start <= $end) {
                $currentDateStr = $start->format('Y-m-d');
                $dayName = strtolower($start->format('l'));
                
                // يوم عمل إذا لم يكن عطلة أسبوعية ولا عطلة رسمية
                if (!in_array($dayName, $weekendDays) && !in_array($currentDateStr, $officialHolidays)) {
                    $workingDays++;
                    $workingDates[] = $currentDateStr;
                }
                $start->addDay();
            }

            $employees = Employee::where('department_id', $departmentId)
                ->where('is_active', true)
                ->with(['attendanceRecords' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }])
                ->get();

            // جلب الحالات التي تحسب حضور
            $presentStatusCodes = $statuses->where('counts_as_present', true)->pluck('code')->toArray();

            // إحصائيات لكل موظف
            foreach ($employees as $employee) {
                $employeeStats = [];
                $presentDays = 0;
                $excludedDays = 0;

                foreach ($statuses as $status) {
                    $statusRecords = $employee->attendanceRecords->where('status', $status->code);
                    $count = $statusRecords->count();
                    $employeeStats[$status->code] = $count;
                }

                // حساب أيام الحضور (فقط في أيام العمل لضمان عدم تجاوز 100%)
                // استخدام counts_as_present_snapshot إذا كان محفوظاً، وإلا الرجوع للجدول
                foreach ($employee->attendanceRecords as $record) {
                    if (in_array($record->date->format('Y-m-d'), $workingDates)) {
                        $countsAsPresent = $record->counts_as_present_snapshot ?? in_array($record->status, $presentStatusCodes);
                        if ($countsAsPresent) {
                            $presentDays++;
                        }
                    }
                }

                // حساب الأيام المستثناة (بشرط أن تكون في يوم عمل)
                // استخدام is_excluded_snapshot إذا كان محفوظاً، وإلا الرجوع للجدول
                foreach ($employee->attendanceRecords as $record) {
                    if (in_array($record->date->format('Y-m-d'), $workingDates)) {
                        $isExcluded = $record->is_excluded_snapshot ?? $statuses->where('code', $record->status)->first()?->is_excluded;
                        if ($isExcluded) {
                            $excludedDays++;
                        }
                    }
                }

                // حساب أيام العمل الفعلية المتوقعة بعد خصم الأيام المستثناة
                $effectiveWorkingDays = $workingDays - $excludedDays;
                if ($effectiveWorkingDays < 0) $effectiveWorkingDays = 0;

                // نسبة الانضباط
                $isFullyExcluded = ($effectiveWorkingDays === 0);
                
                $employeeStats['working_days'] = $workingDays;
                $employeeStats['excluded_days'] = $excludedDays;
                $employeeStats['effective_working_days'] = $effectiveWorkingDays;
                $employeeStats['is_fully_excluded'] = $isFullyExcluded;
                $employeeStats['attendance_rate'] = $effectiveWorkingDays > 0
                    ? round(($presentDays / $effectiveWorkingDays) * 100)
                    : 0;

                $summary[$employee->id] = $employeeStats;
            }
        }

        return view('reports.monthly', compact('departments', 'employees', 'month', 'departmentId', 'statuses', 'summary', 'workingDays'));
    }
}
