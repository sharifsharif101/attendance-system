<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\AttendanceStatus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MassDataSeeder extends Seeder
{
    public function run(): void
    {
        

        // إنشاء الصلاحيات والأدوار
        $this->command->info('🔐 إنشاء الصلاحيات والأدوار...');
        $this->createPermissionsAndRoles();

        // إنشاء حالات الحضور
        $this->command->info('📊 إنشاء حالات الحضور...');
        $this->createStatuses();

        // إنشاء الأقسام
        $this->command->info('🏢 إنشاء 10 أقسام...');
        $departments = $this->createDepartments();

        // إنشاء المستخدمين
        $this->command->info('👤 إنشاء المستخدمين...');
        $this->createUsers($departments);

        // إنشاء الموظفين
        $this->command->info('👥 إنشاء 500 موظف...');
        $employees = $this->createEmployees($departments);

        // إنشاء سجلات الحضور
        $this->command->info('📅 إنشاء 10,000 سجل حضور...');
        $this->createAttendanceRecords($employees);

        $this->command->info('✅ تم الانتهاء بنجاح!');
    }
 private function createPermissionsAndRoles()
{
    // مسح كاش الصلاحيات أولاً
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    $permissions = [
        'attendance.view',
        'attendance.create',
        'attendance.edit',
        'attendance.lock',
        'attendance.unlock',
        'reports.view',
        'reports.export',
        'audit.view',
        'users.manage',
        'roles.manage',
        'departments.manage',
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    // Admin
    $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin->syncPermissions(Permission::all());

    // Manager
    $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
    $manager->syncPermissions(['attendance.view', 'attendance.unlock', 'reports.view', 'reports.export', 'audit.view']);

    // General Supervisor
    $generalSupervisor = Role::firstOrCreate(['name' => 'general_supervisor', 'guard_name' => 'web']);
    $generalSupervisor->syncPermissions(['attendance.view', 'attendance.create', 'attendance.edit', 'attendance.lock', 'reports.view']);

    // Supervisor
    $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
    $supervisor->syncPermissions(['attendance.view', 'attendance.create', 'attendance.edit', 'attendance.lock']);

    // Data Entry
    $dataEntry = Role::firstOrCreate(['name' => 'data_entry', 'guard_name' => 'web']);
    $dataEntry->syncPermissions(['attendance.view', 'attendance.create']);

    // Auditor
    $auditor = Role::firstOrCreate(['name' => 'auditor', 'guard_name' => 'web']);
    $auditor->syncPermissions(['attendance.view', 'reports.view', 'audit.view']);
}
    private function createStatuses()
    {
        $statuses = [
            ['code' => 'present', 'name' => 'حاضر', 'color' => '#22c55e', 'sort_order' => 1],
            ['code' => 'absent', 'name' => 'غائب', 'color' => '#ef4444', 'sort_order' => 2],
            ['code' => 'late', 'name' => 'متأخر', 'color' => '#f97316', 'sort_order' => 3],
            ['code' => 'excused', 'name' => 'مستأذن', 'color' => '#3b82f6', 'sort_order' => 4],
            ['code' => 'leave', 'name' => 'إجازة', 'color' => '#8b5cf6', 'sort_order' => 5],
            ['code' => 'mission', 'name' => 'مهمة خارجية', 'color' => '#06b6d4', 'sort_order' => 6],
        ];

        foreach ($statuses as $status) {
            AttendanceStatus::create($status);
        }
    }

    private function createDepartments()
    {
        $departmentNames = [
            ['name' => 'تقنية المعلومات', 'code' => 'IT'],
            ['name' => 'الموارد البشرية', 'code' => 'HR'],
            ['name' => 'المالية', 'code' => 'FIN'],
            ['name' => 'المبيعات', 'code' => 'SALES'],
            ['name' => 'التسويق', 'code' => 'MKT'],
            ['name' => 'خدمة العملاء', 'code' => 'CS'],
            ['name' => 'الإنتاج', 'code' => 'PROD'],
            ['name' => 'الجودة', 'code' => 'QA'],
            ['name' => 'المشتريات', 'code' => 'PUR'],
            ['name' => 'الشؤون القانونية', 'code' => 'LEGAL'],
        ];

        $departments = [];
        foreach ($departmentNames as $dept) {
            $departments[] = Department::create([
                'name' => $dept['name'],
                'code' => $dept['code'],
                'is_active' => true,
            ]);
        }

        return $departments;
    }

    private function createUsers($departments)
    {
        // Admin
        $admin = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');
        $admin->departments()->attach(collect($departments)->pluck('id'));

        // Manager
        $manager = User::create([
            'name' => 'المدير العام',
            'email' => 'manager@demo.com',
            'password' => Hash::make('password'),
        ]);
        $manager->assignRole('manager');
        $manager->departments()->attach(collect($departments)->pluck('id'));

        // Supervisors (واحد لكل قسم)
        foreach ($departments as $index => $dept) {
            $supervisor = User::create([
                'name' => 'مشرف ' . $dept->name,
                'email' => 'supervisor' . ($index + 1) . '@demo.com',
                'password' => Hash::make('password'),
            ]);
            $supervisor->assignRole('supervisor');
            $supervisor->departments()->attach($dept->id);
        }

        // Data Entry
        $dataEntry = User::create([
            'name' => 'مدخل بيانات',
            'email' => 'data@demo.com',
            'password' => Hash::make('password'),
        ]);
        $dataEntry->assignRole('data_entry');
        $dataEntry->departments()->attach(collect($departments)->pluck('id')->take(3));
    }

    private function createEmployees($departments)
    {
        $firstNames = ['أحمد', 'محمد', 'عبدالله', 'خالد', 'سعد', 'فهد', 'ناصر', 'عبدالرحمن', 'يوسف', 'إبراهيم', 'علي', 'حسن', 'عمر', 'سلطان', 'ماجد', 'طارق', 'بدر', 'سامي', 'وليد', 'هاني'];
        $lastNames = ['الأحمد', 'المحمد', 'العبدالله', 'الخالد', 'السعد', 'الفهد', 'الناصر', 'العمر', 'اليوسف', 'الإبراهيم', 'العلي', 'الحسن', 'السلطان', 'الماجد', 'الطارق', 'البدر', 'السامي', 'الوليد', 'الهاني', 'القحطاني'];

        $employees = [];
        $employeeNumber = 1000;

        foreach ($departments as $dept) {
            // 50 موظف لكل قسم
            for ($i = 0; $i < 50; $i++) {
                $employees[] = Employee::create([
                    'name' => $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)],
                    'employee_number' => 'EMP' . $employeeNumber++,
                    'department_id' => $dept->id,
                    'is_active' => rand(1, 100) <= 95, // 95% مفعّلين
                ]);
            }
        }

        $this->command->info('   ✓ تم إنشاء ' . count($employees) . ' موظف');
        return $employees;
    }

    private function createAttendanceRecords($employees)
    {
        $statuses = ['present', 'present', 'present', 'present', 'absent', 'late', 'excused', 'leave', 'mission'];
        $adminId = User::where('email', 'admin@admin.com')->first()->id;

        // آخر 3 أشهر
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now();

        $records = [];
        $count = 0;
        $batchSize = 1000;

        $this->command->info('   ⏳ جاري إنشاء السجلات...');

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            // تخطي الجمعة والسبت
            if ($currentDate->isFriday() || $currentDate->isSaturday()) {
                $currentDate->addDay();
                continue;
            }

            // اختيار موظفين عشوائيين لهذا اليوم
            $dailyEmployees = collect($employees)->random(min(150, count($employees)));

            foreach ($dailyEmployees as $employee) {
                $records[] = [
                    'employee_id' => $employee->id,
                    'date' => $currentDate->format('Y-m-d'),
                    'status' => $statuses[array_rand($statuses)],
                    'recorded_by' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $count++;

                // إدراج دفعات
                if (count($records) >= $batchSize) {
                    AttendanceRecord::insert($records);
                    $records = [];
                    $this->command->info("   ✓ تم إدراج $count سجل...");
                }

                // التوقف عند 10,000
                if ($count >= 10000) {
                    break 2;
                }
            }

            $currentDate->addDay();
        }

        // إدراج الباقي
        if (!empty($records)) {
            AttendanceRecord::insert($records);
        }

        $this->command->info("   ✓ تم إنشاء $count سجل حضور");
    }
}