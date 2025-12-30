<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    // عرض قائمة الأقسام
    public function index()
    {
        $departments = Department::withCount('employees')->get();
        return view('departments.index', compact('departments'));
    }

    // صفحة إضافة قسم
    public function create()
    {
        return view('departments.create');
    }

    // حفظ قسم جديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
        ]);

        Department::create([
            'name' => $request->name,
            'code' => $request->code,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('departments.index')->with('success', 'تم إضافة القسم بنجاح');
    }

    // صفحة تعديل قسم
    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    // تحديث قسم
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
        ]);

        $department->update([
            'name' => $request->name,
            'code' => $request->code,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('departments.index')->with('success', 'تم تحديث القسم بنجاح');
    }

    // حذف قسم
    public function destroy(Department $department)
    {
        if ($department->employees()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف القسم لوجود موظفين مرتبطين به');
        }

        $department->delete();
        return redirect()->route('departments.index')->with('success', 'تم حذف القسم بنجاح');
    }
}