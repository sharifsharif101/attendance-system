<?php

namespace Database\Seeders;

use App\Models\AttendanceStatus;
use Illuminate\Database\Seeder;

class AttendanceStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'حاضر', 'code' => 'present', 'color' => '#22c55e', 'sort_order' => 1],
            ['name' => 'غائب', 'code' => 'absent', 'color' => '#ef4444', 'sort_order' => 2],
            ['name' => 'متأخر', 'code' => 'late', 'color' => '#f97316', 'sort_order' => 3],
            ['name' => 'مستأذن', 'code' => 'excused', 'color' => '#3b82f6', 'sort_order' => 4],
            ['name' => 'إجازة', 'code' => 'leave', 'color' => '#8b5cf6', 'sort_order' => 5],
        ];

        foreach ($statuses as $status) {
            AttendanceStatus::create($status);
        }
    }
}