<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء مستخدم أدمن
        $admin = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // إنشاء الأقسام
        $it = Department::create(['name' => 'تقنية المعلومات', 'code' => 'IT']);
        $hr = Department::create(['name' => 'الموارد البشرية', 'code' => 'HR']);
        $fin = Department::create(['name' => 'المالية', 'code' => 'FIN']);

        // ربط الأدمن بجميع الأقسام
        $admin->departments()->attach([$it->id, $hr->id, $fin->id]);

        // إنشاء موظفين لقسم IT
        Employee::create(['name' => 'أحمد محمد', 'employee_number' => 'EMP001', 'department_id' => $it->id]);
        Employee::create(['name' => 'سارة علي', 'employee_number' => 'EMP002', 'department_id' => $it->id]);
        Employee::create(['name' => 'خالد عبدالله', 'employee_number' => 'EMP003', 'department_id' => $it->id]);

        // إنشاء موظفين لقسم HR
        Employee::create(['name' => 'فاطمة أحمد', 'employee_number' => 'EMP004', 'department_id' => $hr->id]);
        Employee::create(['name' => 'محمد سعد', 'employee_number' => 'EMP005', 'department_id' => $hr->id]);

        // إنشاء موظفين لقسم Finance
        Employee::create(['name' => 'نورة خالد', 'employee_number' => 'EMP006', 'department_id' => $fin->id]);
        Employee::create(['name' => 'عبدالرحمن يوسف', 'employee_number' => 'EMP007', 'department_id' => $fin->id]);
    }
}