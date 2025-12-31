<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // عرض الأدوار والصلاحيات
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        
        // تجميع الصلاحيات حسب النوع
        $groupedPermissions = [
            'الحضور' => $permissions->filter(fn($p) => str_starts_with($p->name, 'attendance.')),
            'التقارير' => $permissions->filter(fn($p) => str_starts_with($p->name, 'reports.')),
            'التدقيق' => $permissions->filter(fn($p) => str_starts_with($p->name, 'audit.')),
            'الإدارة' => $permissions->filter(fn($p) => str_ends_with($p->name, '.manage')),
        ];
        
        // ترجمة أسماء الصلاحيات
        $permissionLabels = [
            'attendance.view' => 'عرض الحضور',
            'attendance.create' => 'تسجيل الحضور',
            'attendance.edit' => 'تعديل الحضور',
            'attendance.lock' => 'قفل اليوم',
            'attendance.unlock' => 'فتح اليوم',
            'reports.view' => 'عرض التقارير',
            'reports.export' => 'تصدير التقارير',
            'audit.view' => 'عرض سجل التدقيق',
            'users.manage' => 'إدارة المستخدمين',
            'roles.manage' => 'إدارة الأدوار',
            'departments.manage' => 'إدارة الأقسام',
        ];
        
        // ترجمة أسماء الأدوار
        $roleLabels = [
            'admin' => 'مدير النظام',
            'manager' => 'مدير',
            'general_supervisor' => 'مشرف عام',
            'supervisor' => 'مشرف قسم',
            'data_entry' => 'مدخل بيانات',
            'auditor' => 'مدقق',
        ];

        return view('roles.index', compact('roles', 'permissions', 'groupedPermissions', 'permissionLabels', 'roleLabels'));
    }

    // صفحة إضافة دور
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        
        $permissionLabels = [
            'attendance.view' => 'عرض الحضور',
            'attendance.create' => 'تسجيل الحضور',
            'attendance.edit' => 'تعديل الحضور',
            'attendance.lock' => 'قفل اليوم',
            'attendance.unlock' => 'فتح اليوم',
            'reports.view' => 'عرض التقارير',
            'reports.export' => 'تصدير التقارير',
            'audit.view' => 'عرض سجل التدقيق',
            'users.manage' => 'إدارة المستخدمين',
            'roles.manage' => 'إدارة الأدوار',
            'departments.manage' => 'إدارة الأقسام',
        ];

        return view('roles.create', compact('permissions', 'permissionLabels'));
    }

    // حفظ دور جديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'تم إضافة الدور بنجاح');
    }

    // صفحة تعديل دور
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        $permissionLabels = [
            'attendance.view' => 'عرض الحضور',
            'attendance.create' => 'تسجيل الحضور',
            'attendance.edit' => 'تعديل الحضور',
            'attendance.lock' => 'قفل اليوم',
            'attendance.unlock' => 'فتح اليوم',
            'reports.view' => 'عرض التقارير',
            'reports.export' => 'تصدير التقارير',
            'audit.view' => 'عرض سجل التدقيق',
            'users.manage' => 'إدارة المستخدمين',
            'roles.manage' => 'إدارة الأدوار',
            'departments.manage' => 'إدارة الأقسام',
        ];

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'permissionLabels'));
    }

    // تحديث دور
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'تم تحديث الدور بنجاح');
    }

    // حذف دور
    public function destroy(Role $role)
    {
        // منع حذف دور admin
        if ($role->name === 'admin') {
            return back()->with('error', 'لا يمكن حذف دور مدير النظام');
        }

        // التحقق من عدم وجود مستخدمين بهذا الدور
        if ($role->users()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الدور لوجود مستخدمين مرتبطين به');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'تم حذف الدور بنجاح');
    }
}