<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // مسح الكاش
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء الصلاحيات
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
            Permission::create(['name' => $permission]);
        }

        // إنشاء الأدوار مع صلاحياتها
        
        // مدخل بيانات
        Role::create(['name' => 'data_entry'])
            ->givePermissionTo(['attendance.view', 'attendance.create']);

        // مشرف قسم
        Role::create(['name' => 'supervisor'])
            ->givePermissionTo(['attendance.view', 'attendance.create', 'attendance.edit', 'attendance.lock']);

        // مشرف عام
        Role::create(['name' => 'general_supervisor'])
            ->givePermissionTo(['attendance.view', 'attendance.create', 'attendance.edit', 'attendance.lock', 'reports.view']);

        // مدير
        Role::create(['name' => 'manager'])
            ->givePermissionTo(['attendance.view', 'attendance.unlock', 'reports.view', 'reports.export', 'audit.view']);

        // مدير النظام
        Role::create(['name' => 'admin'])
            ->givePermissionTo(Permission::all());

        // مراجع
        Role::create(['name' => 'auditor'])
            ->givePermissionTo(['attendance.view', 'reports.view', 'audit.view']);
    }
}