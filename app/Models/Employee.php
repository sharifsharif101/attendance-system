<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
protected $fillable = [
    'name',
    'employee_number',
    'department_id',
    'national_id',
    'nationality',
    'birth_date',
    'gender',
    'marital_status',
    'phone',
    'email',
    'passport_number',
    'passport_expiry',
    'residency_number',
    'residency_expiry',
    'hire_date',
    'job_title',
    'contract_type',
    'contract_expiry',
    'photo',
    'is_active',
];

protected $casts = [
    'birth_date' => 'date',
    'passport_expiry' => 'date',
    'residency_expiry' => 'date',
    'hire_date' => 'date',
    'contract_expiry' => 'date',
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