<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Check if a specific date is a holiday.
     *
     * @param string|Carbon $date
     * @return bool
     */
    public static function isHoliday($date)
    {
        $date = $date instanceof Carbon ? $date->format('Y-m-d') : $date;
        return self::where('date', $date)->exists();
    }

    /**
     * Get holidays for a specific month.
     *
     * @param string $month Y-m format
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getHolidaysInMonth($month)
    {
        return self::where('date', 'like', "{$month}-%")->get();
    }
}
