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
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime:H:i',
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