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
    $query = $request->get('q', '');
    $departmentId = $request->get('department_id', '');

    $employees = Employee::with('department')
        ->when($query, function($q) use ($query) {
            $q->where(function($q2) use ($query) {
                $q2->where('name', 'like', "%{$query}%")
                   ->orWhere('employee_number', 'like', "%{$query}%");
            });
        })
        ->when($departmentId, function($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })
        ->orderBy('employee_number')
        ->paginate(20)
        ->withQueryString();

    $departments = Department::where('is_active', true)->get();

    return view('employees.index', compact('employees', 'departments', 'query', 'departmentId'));
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