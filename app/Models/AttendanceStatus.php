<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceStatus extends Model
{
    protected $fillable = [
        'name',
        'code',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // جلب الحالات النشطة مرتبة
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}