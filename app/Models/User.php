<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // الأقسام التي يشرف عليها
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class);
    }

    // سجلات الحضور التي أدخلها
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'recorded_by');
    }

    // الأيام التي قفلها
    public function dayLocks(): HasMany
    {
        return $this->hasMany(DayLock::class, 'locked_by');
    }
}