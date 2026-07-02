<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TenantInvitation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'token',
        'tenant_id',
        'invited_by',
        'email',
        'role',
        'expires_at',
        'used_at',
        'used_by',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (TenantInvitation $invitation) {
            $invitation->token = $invitation->token ?? Str::random(48);
        });
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    public function isValid(): bool
    {
        return !$this->isUsed() && !$this->isExpired();
    }

    public function markUsedBy(User $user): void
    {
        $this->update([
            'used_at' => now(),
            'used_by' => $user->id,
        ]);
    }

    public function getInviteUrlAttribute(): string
    {
        return route('register', ['invitation' => $this->token]);
    }
}
