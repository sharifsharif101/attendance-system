<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceStatus;
use App\Models\Employee;
use App\Models\QrToken;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SelfAttendanceController extends Controller
{
    /**
     * عرض نموذج تسجيل الحضور الذاتي
     */
    public function showForm(string $token)
    {
        $qrToken = QrToken::findValidToken($token);

        if (!$qrToken) {
            return view('qr.self-attendance', [
                'error' => 'رمز QR غير صالح أو منتهي الصلاحية',
                'expired' => true,
            ]);
        }

        $department = $qrToken->department;
        $type = $qrToken->type;
        $expiresIn = now()->diffInSeconds($qrToken->expires_at, false);

        if ($expiresIn < 0) {
            $expiresIn = 0;
        }

        return view('qr.self-attendance', compact('qrToken', 'department', 'type', 'expiresIn'));
    }

    /**
     * معالجة طلب تسجيل الحضور
     */
    public function submit(Request $request, string $token)
    {
        $request->validate([
            'employee_number' => 'required|string',
        ]);

        // التحقق من صلاحية الرمز
        $qrToken = QrToken::findValidToken($token);

        if (!$qrToken) {
            return back()->with('error', 'رمز QR غير صالح أو منتهي الصلاحية. يرجى مسح رمز جديد.');
        }

        // البحث عن الموظف (بدون تقييد بالقسم)
        $employee = Employee::where('employee_number', $request->employee_number)
            ->where('is_active', true)
            ->first();

        if (!$employee) {
            return back()
                ->withInput()
                ->with('error', 'الرقم الوظيفي غير صحيح أو الموظف غير نشط.');
        }

        $today = Carbon::today();
        $now = Carbon::now();
        $type = $qrToken->type;

        return DB::transaction(function () use ($employee, $today, $now, $type, $qrToken) {
            // البحث عن سجل اليوم
            $record = AttendanceRecord::where('employee_id', $employee->id)
                ->where('date', $today)
                ->first();

            if ($type === 'check_in') {
                return $this->handleCheckIn($employee, $record, $today, $now, $qrToken);
            } else {
                return $this->handleCheckOut($employee, $record, $today, $now, $qrToken);
            }
        });
    }

    /**
     * معالجة تسجيل الحضور
     */
    private function handleCheckIn($employee, $record, $today, $now, $qrToken)
    {
        // التحقق من عدم التسجيل المسبق
        if ($record && $record->check_in_time) {
            $checkInTime = Carbon::parse($record->check_in_time)->format('h:i A');
            return back()->with('warning', "تم تسجيل حضورك مسبقاً الساعة $checkInTime");
        }

        // تحديد الحالة (حاضر أو متأخر)
        $status = $this->determineStatus($now);
        $statusModel = AttendanceStatus::where('code', $status)->first();

        // إنشاء أو تحديث السجل
        AttendanceRecord::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $today,
            ],
            [
                'status' => $status,
                'check_in_time' => $now->format('H:i'),
                'recorded_by' => null, // تسجيل ذاتي
                'is_excluded_snapshot' => $statusModel?->is_excluded ?? false,
                'counts_as_present_snapshot' => $statusModel?->counts_as_present ?? true,
            ]
        );

        return redirect()->route('attend.success', [
            'name' => $employee->name,
            'time' => $now->format('h:i A'),
            'type' => 'check_in',
            'status' => $status,
        ]);
    }

    /**
     * معالجة تسجيل الانصراف
     */
    private function handleCheckOut($employee, $record, $today, $now, $qrToken)
    {
        // التحقق من وجود سجل حضور
        if (!$record) {
            return back()->with('error', 'لم يتم تسجيل حضورك اليوم. يرجى تسجيل الحضور أولاً.');
        }

        // التحقق من عدم تسجيل الانصراف مسبقاً
        if ($record->check_out_time) {
            $checkOutTime = Carbon::parse($record->check_out_time)->format('h:i A');
            return back()->with('warning', "تم تسجيل انصرافك مسبقاً الساعة $checkOutTime");
        }

        // تحديث وقت الانصراف
        $record->update([
            'check_out_time' => $now->format('H:i'),
        ]);

        return redirect()->route('attend.success', [
            'name' => $employee->name,
            'time' => $now->format('h:i A'),
            'type' => 'check_out',
        ]);
    }

    /**
     * تحديد الحالة بناءً على الوقت (حاضر أو متأخر)
     */
    private function determineStatus($checkInTime): string
    {
        // جلب وقت بداية الدوام من الإعدادات
        $workStartTime = Setting::get('work_start_time', '08:00');
        $lateThreshold = Setting::get('late_threshold_minutes', 0);

        $workStart = Carbon::createFromFormat('H:i', $workStartTime);
        $lateTime = $workStart->copy()->addMinutes((int) $lateThreshold);

        // إذا حضر بعد وقت بداية الدوام + فترة السماح
        if ($checkInTime->gt($lateTime)) {
            // التحقق من وجود حالة "متأخر"
            $lateStatus = AttendanceStatus::where('code', 'late')->where('is_active', true)->first();
            if ($lateStatus) {
                return 'late';
            }
        }

        return 'present';
    }

    /**
     * صفحة النجاح
     */
    public function success(Request $request)
    {
        return view('qr.success', [
            'name' => $request->get('name', 'الموظف'),
            'time' => $request->get('time', now()->format('h:i A')),
            'type' => $request->get('type', 'check_in'),
            'status' => $request->get('status', 'present'),
        ]);
    }
}
