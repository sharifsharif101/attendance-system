<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'employee_number' => $request->employee_number,
            'department_id' => $request->department_id,
            'national_id' => $request->national_id,
            'nationality' => $request->nationality,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'phone' => $request->phone,
            'email' => $request->email,
            'passport_number' => $request->passport_number,
            'passport_expiry' => $request->passport_expiry,
            'residency_number' => $request->residency_number,
            'residency_expiry' => $request->residency_expiry,
            'hire_date' => $request->hire_date,
            'job_title' => $request->job_title,
            'contract_type' => $request->contract_type,
            'contract_expiry' => $request->contract_expiry,
            'is_active' => $request->has('is_active'),
        ];

        // رفع الصورة
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employees', 'public');
        }

        Employee::create($data);

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
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'employee_number' => $request->employee_number,
            'department_id' => $request->department_id,
            'national_id' => $request->national_id,
            'nationality' => $request->nationality,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'phone' => $request->phone,
            'email' => $request->email,
            'passport_number' => $request->passport_number,
            'passport_expiry' => $request->passport_expiry,
            'residency_number' => $request->residency_number,
            'residency_expiry' => $request->residency_expiry,
            'hire_date' => $request->hire_date,
            'job_title' => $request->job_title,
            'contract_type' => $request->contract_type,
            'contract_expiry' => $request->contract_expiry,
            'is_active' => $request->has('is_active'),
        ];

        // رفع الصورة الجديدة
        if ($request->hasFile('photo')) {
            // حذف الصورة القديمة
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            $data['photo'] = $request->file('photo')->store('employees', 'public');
        }

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'تم تحديث الموظف بنجاح');
    }

    // حذف موظف
    public function destroy(Employee $employee)
    {
        if ($employee->attendanceRecords()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الموظف لوجود سجلات حضور مرتبطة به');
        }

        // حذف الصورة
        if ($employee->photo) {
            Storage::disk('public')->delete($employee->photo);
        }

        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'تم حذف الموظف بنجاح');
    }
    // تقرير الوثائق المنتهية
public function expiringDocuments(Request $request)
{
    $type = $request->get('type', 'all');
    $today = now()->format('Y-m-d');
    $thirtyDays = now()->addDays(30)->format('Y-m-d');
    $sixtyDays = now()->addDays(60)->format('Y-m-d');

    $query = Employee::with('department')->where('is_active', true);

    if ($type === 'passport') {
        $query->whereNotNull('passport_expiry')
              ->where('passport_expiry', '<=', $thirtyDays)
              ->orderBy('passport_expiry');
    } elseif ($type === 'residency') {
        $query->whereNotNull('residency_expiry')
              ->where('residency_expiry', '<=', $sixtyDays)
              ->orderBy('residency_expiry');
    } elseif ($type === 'contract') {
        $query->whereNotNull('contract_expiry')
              ->where('contract_expiry', '<=', $thirtyDays)
              ->orderBy('contract_expiry');
    } elseif ($type === 'expired') {
        $query->where(function($q) use ($today) {
            $q->where('passport_expiry', '<', $today)
              ->orWhere('residency_expiry', '<', $today)
              ->orWhere('contract_expiry', '<', $today);
        });
    } else {
        // الكل
        $query->where(function($q) use ($sixtyDays) {
            $q->whereNotNull('passport_expiry')->where('passport_expiry', '<=', $sixtyDays)
              ->orWhereNotNull('residency_expiry')->where('residency_expiry', '<=', $sixtyDays)
              ->orWhereNotNull('contract_expiry')->where('contract_expiry', '<=', $sixtyDays);
        });
    }

    $employees = $query->get();

    return view('employees.expiring-documents', compact('employees', 'type', 'today'));
}
}