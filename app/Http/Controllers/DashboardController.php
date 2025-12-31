<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceStatus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\MonthlySetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        $currentMonth = Carbon::now()->format('Y-m');
        
        // إحصائيات عامة
        $totalEmployees = Employee::where('is_active', true)->count();
        $totalDepartments = Department::where('is_active', true)->count();
        $totalUsers = User::count();
        
   
        // إحصائيات اليوم
        $todayRecords = AttendanceRecord::where('date', $today)->count();

        // إحصائيات اليوم حسب الحالة
        $todayStats = AttendanceRecord::where('date', $today)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

$todayPresent = ($todayStats['present'] ?? 0) + ($todayStats['late'] ?? 0);
$todayAbsent = $todayStats['absent'] ?? 0;
        
        // نسبة الحضور اليوم
        $todayAttendanceRate = $totalEmployees > 0 
            ? round(($todayPresent / $totalEmployees) * 100) 
            : 0;
        
        // إحصائيات الشهر الحالي
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        
        $monthlyStats = AttendanceRecord::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // حالات الحضور مع الألوان
        $statuses = AttendanceStatus::getActive();
        
        // إحصائيات حسب القسم لهذا الشهر
        $departmentStats = Department::where('is_active', true)
            ->withCount(['employees as total_employees' => function($query) {
                $query->where('is_active', true);
            }])
            ->with(['employees' => function($query) use ($startOfMonth, $endOfMonth) {
                $query->where('is_active', true)
                    ->with(['attendanceRecords' => function($q) use ($startOfMonth, $endOfMonth) {
                        $q->whereBetween('date', [$startOfMonth, $endOfMonth]);
                    }]);
            }])
            ->get()
            ->map(function($dept) {
                $presentCount = 0;
                $totalRecords = 0;
                
                foreach($dept->employees as $emp) {
                    foreach($emp->attendanceRecords as $record) {
                        $totalRecords++;
                        if(in_array($record->status, ['present', 'late'])) {
                            $presentCount++;
                        }
                    }
                }
                
                $dept->attendance_rate = $totalRecords > 0 
                    ? round(($presentCount / $totalRecords) * 100) 
                    : 0;
                
                return $dept;
            });
        
        // آخر 5 سجلات حضور
        $recentRecords = AttendanceRecord::with(['employee', 'recorder'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        
return view('dashboard', compact(
    'totalEmployees',
    'totalDepartments', 
    'totalUsers',
    'todayRecords',
    'todayPresent',
    'todayAbsent',
    'todayAttendanceRate',
    'todayStats',
    'monthlyStats',
    'statuses',
    'departmentStats',
    'recentRecords',
    'today',
    'currentMonth'
));
    }
}