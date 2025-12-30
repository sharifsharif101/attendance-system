<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DayLock extends Model
{
    protected $fillable = [
        'date',
        'department_id',
        'locked_by',
        'locked_at',
    ];

    protected $casts = [
        'date' => 'date',
        'locked_at' => 'datetime',
    ];

    // القسم
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // المستخدم الذي قفل اليوم
    public function lockedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
}