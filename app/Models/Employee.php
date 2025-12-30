<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'employee_number',
        'department_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // القسم التابع له
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // سجلات الحضور
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}