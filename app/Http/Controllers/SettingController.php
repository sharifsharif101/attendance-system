<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\MonthlySetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // عرض صفحة الإعدادات
    public function index(Request $request)
    {
        $month = $request->get('month', date('Y-m'));
        
        // التحقق إذا كان للشهر إعداد خاص
        $hasCustomSetting = MonthlySetting::hasCustomSetting($month);
        $weekendDays = MonthlySetting::getWeekendDays($month);
        $defaultWeekendDays = Setting::getWeekendDays();
        
        $days = [
            'sunday' => 'الأحد',
            'monday' => 'الإثنين',
            'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
            'thursday' => 'الخميس',
            'friday' => 'الجمعة',
            'saturday' => 'السبت',
        ];

        return view('settings.index', compact('weekendDays', 'defaultWeekendDays', 'days', 'month', 'hasCustomSetting'));
    }

    // حفظ الإعدادات الافتراضية
    public function update(Request $request)
    {
        $weekendDays = $request->input('weekend_days', []);
        Setting::setWeekendDays($weekendDays);

        return redirect()->route('settings.index')->with('success', 'تم حفظ الإعدادات الافتراضية بنجاح');
    }

    // حفظ إعدادات شهر معين
    public function updateMonth(Request $request)
    {
        $month = $request->input('month');
        $weekendDays = $request->input('weekend_days', []);
        
        MonthlySetting::setWeekendDays($month, $weekendDays);

        return redirect()->route('settings.index', ['month' => $month])
            ->with('success', 'تم حفظ إعدادات شهر ' . $month . ' بنجاح');
    }

    // حذف إعداد شهر (الرجوع للافتراضي)
    public function resetMonth(Request $request)
    {
        $month = $request->input('month');
        MonthlySetting::where('month', $month)->delete();

        return redirect()->route('settings.index', ['month' => $month])
            ->with('success', 'تم إعادة الشهر للإعدادات الافتراضية');
    }
}