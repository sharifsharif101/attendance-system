<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AttendanceRecord extends Model
{
    use LogsActivity;

    protected $fillable = [
        'employee_id',
        'date',
        'status',
        'check_in_time',
        'check_out_time',
        'notes',
        'recorded_by',
        'is_excluded_snapshot',
        'counts_as_present_snapshot',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'is_excluded_snapshot' => 'boolean',
        'counts_as_present_snapshot' => 'boolean',
    ];

    // إعدادات سجل التدقيق
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    // الموظف
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // المستخدم الذي سجل الحضور
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}