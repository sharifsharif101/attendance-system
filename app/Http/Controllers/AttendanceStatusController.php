<?php
// جودة
namespace App\Http\Controllers;

use App\Models\AttendanceStatus;
use App\Models\AttendanceStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        'code' => 'required|string|max:50|unique:attendance_statuses',
        'name' => 'required|string|max:100',
        'color' => 'required|string|max:7',
        'sort_order' => 'required|integer',
    ]);

    AttendanceStatus::create([
        'code' => $request->code,
        'name' => $request->name,
        'color' => $request->color,
        'sort_order' => $request->sort_order,
        'counts_as_present' => $request->boolean('counts_as_present'),
        'is_excluded' => $request->boolean('is_excluded'),
        'is_active' => $request->boolean('is_active'),
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
        'code' => 'required|string|max:50|unique:attendance_statuses,code,' . $status->id,
        'name' => 'required|string|max:100',
        'color' => 'required|string|max:7',
        'sort_order' => 'required|integer',
    ]);

    // استخدام Transaction لضمان سلامة البيانات
    DB::transaction(function () use ($request, $status) {
        // حفظ القيم القديمة قبل التحديث
        $oldIsExcluded = $status->is_excluded;
        $oldCountsAsPresent = $status->counts_as_present;
        $oldCode = $status->code; // Capture old code
        
        // معالجة القيم البوليانية بشكل صحيح حتى مع JSON
        $newIsExcluded = $request->boolean('is_excluded');
        $newCountsAsPresent = $request->boolean('counts_as_present');
        $isActive = $request->boolean('is_active');
        $newCode = $request->code;

        // في حال كان النموذج HTML تقليدي (checkboxes)، إذا لم يكن موجوداً فهو false
        // ولكن $request->boolean() يعالج الحالتين (وجود المفتاح كـ "on"/"1"/true أو عدم وجوده كـ false)
        // ملاحظة: إذا كان الطلب Form submit، الـ checkboxes غير المختارة لا تُرسل، لذا boolean تعيد false وهذا صحيح.

        // تسجيل التغيير في التاريخ إذا تغيرت إعدادات الحساب
        if ($oldIsExcluded !== $newIsExcluded || $oldCountsAsPresent !== $newCountsAsPresent) {
            AttendanceStatusHistory::create([
                'attendance_status_id' => $status->id,
                'status_code' => $status->code,
                'old_is_excluded' => $oldIsExcluded,
                'new_is_excluded' => $newIsExcluded,
                'old_counts_as_present' => $oldCountsAsPresent,
                'new_counts_as_present' => $newCountsAsPresent,
                'changed_by' => Auth::id(),
            ]);
        }

        // تطبيق التحديث المتتابع (Cascade Update) لإصلاح مشكلة "صانع الأيتام"
        if ($oldCode !== $newCode) {
            \App\Models\AttendanceRecord::where('status', $oldCode)
                ->update(['status' => $newCode]);
        }

        $status->update([
            'code' => $request->code,
            'name' => $request->name,
            'color' => $request->color,
            'sort_order' => $request->sort_order,
            'counts_as_present' => $newCountsAsPresent,
            'is_excluded' => $newIsExcluded,
            'is_active' => $isActive,
        ]);
    });

    return redirect()->route('statuses.index')->with('success', 'تم تحديث الحالة بنجاح');
}

    // حذف حالة
    public function destroy(AttendanceStatus $status)
    {
        // التحقق من وجود سجلات مرتبطة بهذه الحالة
        // ملاحظة: بما أن العلاقة غير معرفة كمفتاح أجنبي في قاعدة البيانات
        // ونعتمد على تطابق كود الحالة، يجب التحقق يدوياً
        $exists = \App\Models\AttendanceRecord::where('status', $status->code)->exists();

        if ($exists) {
            return redirect()->route('statuses.index')
                ->with('error', 'لا يمكن حذف هذه الحالة لأنها مستخدمة في سجلات الحضور. يمكنك إلغاء تفعيلها بدلاً من ذلك.');
        }

        $status->delete();
        return redirect()->route('statuses.index')->with('success', 'تم حذف الحالة بنجاح');
    }

    // عرض تاريخ جميع التغييرات
    public function history()
    {
        $history = AttendanceStatusHistory::with(['attendanceStatus', 'changedByUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('statuses.history', compact('history'));
    }

    // عرض تاريخ حالة محددة
    public function statusHistory(AttendanceStatus $status)
    {
        $history = AttendanceStatusHistory::where('attendance_status_id', $status->id)
            ->with('changedByUser')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('statuses.status-history', compact('status', 'history'));
    }
}