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
        
        // حالات الحضور مع الألوان
        $statuses = AttendanceStatus::getActive();
        
        // الحالات التي تُحسب كحضور
        $presentStatusCodes = AttendanceStatus::where('counts_as_present', true)
            ->pluck('code')
            ->toArray();
        
        // الحالات المستثناة من الحساب
        $excludedStatusCodes = AttendanceStatus::where('is_excluded', true)
            ->pluck('code')
            ->toArray();
        
        // إحصائيات اليوم
        $todayRecords = AttendanceRecord::where('date', $today)->count();

        // إحصائيات اليوم حسب الحالة
        $todayStats = AttendanceRecord::where('date', $today)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // حساب الحضور (الحالات التي تُحسب كحضور)
        $todayPresent = 0;
        foreach ($presentStatusCodes as $code) {
            $todayPresent += $todayStats[$code] ?? 0;
        }
        
        // حساب المستثنين
        $todayExcluded = 0;
        foreach ($excludedStatusCodes as $code) {
            $todayExcluded += $todayStats[$code] ?? 0;
        }
        
        $todayAbsent = $todayStats['absent'] ?? 0;
        
        // نسبة الحضور اليوم (مع استثناء الإجازات)
        $effectiveEmployees = $totalEmployees - $todayExcluded;
        $todayAttendanceRate = $effectiveEmployees > 0 
            ? round(($todayPresent / $effectiveEmployees) * 100) 
            : 0;
        
        // إحصائيات الشهر الحالي
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        
        $monthlyStats = AttendanceRecord::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // إحصائيات حسب القسم لهذا اليوم
        $departmentStats = Department::where('is_active', true)
            ->withCount(['employees as total_employees' => function($query) {
                $query->where('is_active', true);
            }])
            ->with(['employees' => function($query) use ($today) {
                $query->where('is_active', true)
                    ->with(['attendanceRecords' => function($q) use ($today) {
                        $q->where('date', $today);
                    }]);
            }])
            ->get()
            ->map(function($dept) use ($presentStatusCodes, $excludedStatusCodes) {
                $presentCount = 0;
                $excludedCount = 0;
                
                foreach($dept->employees as $emp) {
                    foreach($emp->attendanceRecords as $record) {
                        if(in_array($record->status, $presentStatusCodes)) {
                            $presentCount++;
                        }
                        if(in_array($record->status, $excludedStatusCodes)) {
                            $excludedCount++;
                        }
                    }
                }
                
                $effectiveTotal = $dept->total_employees - $excludedCount;
                $dept->attendance_rate = $effectiveTotal > 0 
                    ? round(($presentCount / $effectiveTotal) * 100) 
                    : 0;
                $dept->excluded_count = $excludedCount;
                
                return $dept;
            });
        
        // آخر 5 سجلات حضور
        $recentRecords = AttendanceRecord::with(['employee', 'recorder'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        // تنبيهات الوثائق المنتهية قريباً
$today = Carbon::today();
$thirtyDaysLater = Carbon::today()->addDays(30);
$sixtyDaysLater = Carbon::today()->addDays(60);

// جوازات تنتهي خلال 30 يوم
$expiringPassports = Employee::where('is_active', true)
    ->whereNotNull('passport_expiry')
    ->whereBetween('passport_expiry', [$today, $thirtyDaysLater])
    ->count();

// إقامات تنتهي خلال 60 يوم
$expiringResidencies = Employee::where('is_active', true)
    ->whereNotNull('residency_expiry')
    ->whereBetween('residency_expiry', [$today, $sixtyDaysLater])
    ->count();

// عقود تنتهي خلال 30 يوم
$expiringContracts = Employee::where('is_active', true)
    ->whereNotNull('contract_expiry')
    ->whereBetween('contract_expiry', [$today, $thirtyDaysLater])
    ->count();

// وثائق منتهية
$expiredDocuments = Employee::where('is_active', true)
    ->where(function($q) use ($today) {
        $q->where('passport_expiry', '<', $today)
          ->orWhere('residency_expiry', '<', $today)
          ->orWhere('contract_expiry', '<', $today);
    })
    ->count();
return view('dashboard', compact(
    'totalEmployees',
    'totalDepartments', 
    'totalUsers',
    'todayRecords',
    'todayPresent',
    'todayAbsent',
    'todayExcluded',
    'todayAttendanceRate',
    'todayStats',
    'monthlyStats',
    'statuses',
    'departmentStats',
    'recentRecords',
    'today',
    'currentMonth',
    'expiringPassports',
    'expiringResidencies',
    'expiringContracts',
    'expiredDocuments'
));
    }
}