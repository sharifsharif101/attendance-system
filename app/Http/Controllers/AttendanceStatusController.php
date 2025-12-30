<?php

namespace App\Http\Controllers;

use App\Models\AttendanceStatus;
use Illuminate\Http\Request;

class AttendanceStatusController extends Controller
{
    // عرض قائمة الحالات
    public function index()
    {
        $statuses = AttendanceStatus::orderBy('sort_order')->get();
        return view('statuses.index', compact('statuses'));
    }

    // صفحة إضافة حالة
    public function create()
    {
        return view('statuses.create');
    }

    // حفظ حالة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:attendance_statuses,code',
            'color' => 'required|string|max:7',
            'sort_order' => 'nullable|integer',
        ]);

        AttendanceStatus::create([
            'name' => $request->name,
            'code' => $request->code,
            'color' => $request->color,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('statuses.index')->with('success', 'تم إضافة الحالة بنجاح');
    }

    // صفحة تعديل حالة
    public function edit(AttendanceStatus $status)
    {
        return view('statuses.edit', compact('status'));
    }

    // تحديث حالة
    public function update(Request $request, AttendanceStatus $status)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:attendance_statuses,code,' . $status->id,
            'color' => 'required|string|max:7',
            'sort_order' => 'nullable|integer',
        ]);

        $status->update([
            'name' => $request->name,
            'code' => $request->code,
            'color' => $request->color,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('statuses.index')->with('success', 'تم تحديث الحالة بنجاح');
    }

    // حذف حالة
    public function destroy(AttendanceStatus $status)
    {
        $status->delete();
        return redirect()->route('statuses.index')->with('success', 'تم حذف الحالة بنجاح');
    }
}