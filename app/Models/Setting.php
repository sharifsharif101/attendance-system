<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    // جلب قيمة إعداد
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    // حفظ قيمة إعداد
    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    // جلب أيام الإجازة كمصفوفة
    public static function getWeekendDays()
    {
        $value = self::get('weekend_days', '["friday","saturday"]');
        return json_decode($value, true) ?? ['friday', 'saturday'];
    }

    // حفظ أيام الإجازة
    public static function setWeekendDays(array $days)
    {
        return self::set('weekend_days', json_encode($days));
    }
}