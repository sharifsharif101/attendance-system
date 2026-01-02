<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceStatusHistory extends Model
{
    protected $table = 'attendance_status_history';

    protected $fillable = [
        'attendance_status_id',
        'status_code',
        'old_is_excluded',
        'new_is_excluded',
        'old_counts_as_present',
        'new_counts_as_present',
        'changed_by',
        'reason',
    ];

    protected $casts = [
        'old_is_excluded' => 'boolean',
        'new_is_excluded' => 'boolean',
        'old_counts_as_present' => 'boolean',
        'new_counts_as_present' => 'boolean',
    ];

    // الحالة المرتبطة
    public function attendanceStatus(): BelongsTo
    {
        return $this->belongsTo(AttendanceStatus::class);
    }

    // المستخدم الذي قام بالتغيير
    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
