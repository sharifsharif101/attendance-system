<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // الموظفين التابعين للقسم
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    // المشرفين على القسم
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    // أقفال الأيام للقسم
    public function dayLocks(): HasMany
    {
        return $this->hasMany(DayLock::class);
    }
}