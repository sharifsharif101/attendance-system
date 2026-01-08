<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrToken extends Model
{
    protected $fillable = [
        'token',
        'department_id',
        'type',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * التحقق من صلاحية الرمز
     */
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    /**
     * القسم المرتبط
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * توليد رمز جديد
     */
    public static function generateToken(?int $departmentId, string $type = 'check_in', int $expiresInSeconds = 15): self
    {
        // حذف الرموز المنتهية للقسم (أو العامة)
        self::where('department_id', $departmentId)
            ->where('expires_at', '<', now())
            ->delete();

        return self::create([
            'token' => bin2hex(random_bytes(32)),
            'department_id' => $departmentId,
            'type' => $type,
            'expires_at' => now()->addSeconds($expiresInSeconds),
        ]);
    }

    /**
     * البحث عن رمز صالح
     */
    public static function findValidToken(string $token): ?self
    {
        return self::where('token', $token)
            ->where('expires_at', '>', now())
            ->where('is_used', false)
            ->first();
    }
}
