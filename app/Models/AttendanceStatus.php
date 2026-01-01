<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceStatus extends Model
{
protected $fillable = [
    'code',
    'name',
    'color',
    'counts_as_present',
    'is_excluded',
    'sort_order',
    'is_active',
];

 

  protected $casts = [
    'counts_as_present' => 'boolean',
    'is_excluded' => 'boolean',
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