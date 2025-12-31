<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // عرض قائمة الموظفين
    public function index(Request $request)
    {
        $departmentId = $request->get('department_id');
        $departments = Department::where('is_active', true)->get();
        
        $employees = Employee::with('department')
            ->when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('employees.index', compact('employees', 'departments', 'departmentId'));
    }

    // صفحة إضافة موظف
    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('employees.create', compact('departments'));
    }

    // حفظ موظف جديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'employee_number' => 'required|string|max:50|unique:employees,employee_number',
            'department_id' => 'required|exists:departments,id',
        ]);

        Employee::create([
            'name' => $request->name,
            'employee_number' => $request->employee_number,
            'department_id' => $request->department_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('employees.index')->with('success', 'تم إضافة الموظف بنجاح');
    }

    // صفحة تعديل موظف
    public function edit(Employee $employee)
    {
        $departments = Department::where('is_active', true)->get();
        return view('employees.edit', compact('employee', 'departments'));
    }

    // تحديث موظف
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'employee_number' => 'required|string|max:50|unique:employees,employee_number,' . $employee->id,
            'department_id' => 'required|exists:departments,id',
        ]);

        $employee->update([
            'name' => $request->name,
            'employee_number' => $request->employee_number,
            'department_id' => $request->department_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('employees.index')->with('success', 'تم تحديث الموظف بنجاح');
    }

    // حذف موظف
    public function destroy(Employee $employee)
    {
        if ($employee->attendanceRecords()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الموظف لوجود سجلات حضور مرتبطة به');
        }

        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'تم حذف الموظف بنجاح');
    }
}