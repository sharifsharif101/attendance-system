<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\QrToken;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrDisplayController extends Controller
{
    /**
     * عرض شاشة QR Code للحضور
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        
        // جلب الأقسام المتاحة للمستخدم
        if ($user->hasRole('admin')) {
            $departments = Department::where('is_active', true)->get();
        } else {
            $departments = $user->departments()->where('is_active', true)->get();
        }

        $selectedDepartmentId = $request->get('department_id', $departments->first()?->id);
        $type = $request->get('type', 'check_in');
        
        // جلب إعدادات QR
        $refreshSeconds = (int) Setting::get('qr_refresh_seconds', 60);
        $companyLogo = Setting::get('company_logo', null);
        $companyName = Setting::get('company_name', 'نظام الحضور');

        return view('qr.display', compact(
            'departments',
            'selectedDepartmentId',
            'type',
            'refreshSeconds',
            'companyLogo',
            'companyName'
        ));
    }

    /**
     * توليد رمز QR جديد (AJAX)
     */
    public function generate(Request $request)
    {
        $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'type' => 'required|in:check_in,check_out',
        ]);

        $user = Auth::user();
        $departmentId = $request->department_id;

        // التحقق من صلاحية المستخدم على القسم
        if ($departmentId && !$user->hasRole('admin')) {
            $userDepartmentIds = $user->departments()->pluck('departments.id')->toArray();
            if (!in_array($departmentId, $userDepartmentIds)) {
                return response()->json(['error' => 'ليس لديك صلاحية على هذا القسم'], 403);
            }
        }

        // جلب مدة الصلاحية من الإعدادات
        $refreshSeconds = (int) Setting::get('qr_refresh_seconds', 60);

        // توليد رمز جديد
        $qrToken = QrToken::generateToken($departmentId, $request->type, $refreshSeconds);

        // إنشاء رابط التسجيل
        $attendUrl = route('attend.form', ['token' => $qrToken->token]);

        // توليد QR Code كـ SVG (تحويله لنص)
        $qrCode = QrCode::size(300)
            ->margin(2)
            ->generate($attendUrl);

        return response()->json([
            'success' => true,
            'qr_code' => (string) $qrCode,
            'token' => $qrToken->token,
            'expires_at' => $qrToken->expires_at->toIso8601String(),
            'refresh_seconds' => $refreshSeconds,
        ]);
    }
}
