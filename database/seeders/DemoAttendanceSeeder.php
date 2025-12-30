<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $admin = User::first();
        
        // الحالات المتاحة
        $statuses = ['present', 'present', 'present', 'present', 'absent', 'late', 'excused', 'leave'];
        
        // شهر أكتوبر 2025
        $this->generateMonthData($employees, $admin, 2025, 10, $statuses);
        
        // شهر نوفمبر 2025
        $this->generateMonthData($employees, $admin, 2025, 11, $statuses);
    }
    
    private function generateMonthData($employees, $admin, $year, $month, $statuses)
    {
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            
            // تخطي الجمعة والسبت (عطلة نهاية الأسبوع)
            if ($date->isFriday() || $date->isSaturday()) {
                continue;
            }
            
            foreach ($employees as $employee) {
                // اختيار حالة عشوائية
                $status = $statuses[array_rand($statuses)];
                
                AttendanceRecord::create([
                    'employee_id' => $employee->id,
                    'date' => $date->format('Y-m-d'),
                    'status' => $status,
                    'notes' => $status == 'absent' ? 'غياب بدون عذر' : null,
                    'recorded_by' => $admin->id,
                ]);
            }
        }
    }
}