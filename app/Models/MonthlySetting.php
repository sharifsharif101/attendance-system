<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlySetting extends Model
{
    protected $fillable = [
        'month',
        'weekend_days',
    ];

    // جلب أيام الإجازة لشهر معين
    public static function getWeekendDays($month)
    {
        $setting = self::where('month', $month)->first();
        
        if ($setting) {
            return json_decode($setting->weekend_days, true);
        }
        
        // إذا لم يوجد إعداد للشهر، استخدم الإعداد الافتراضي
        return Setting::getWeekendDays();
    }

    // حفظ أيام الإجازة لشهر معين
    public static function setWeekendDays($month, array $days)
    {
        return self::updateOrCreate(
            ['month' => $month],
            ['weekend_days' => json_encode($days)]
        );
    }

    // التحقق إذا كان الشهر له إعداد خاص
    public static function hasCustomSetting($month)
    {
        return self::where('month', $month)->exists();
    }
}