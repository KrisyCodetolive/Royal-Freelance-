<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'cycle',
        'status',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && (is_null($this->ends_at) || $this->ends_at->isFuture());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Date de fin pour une nouvelle activation (paiement mocké, pas de vraie
     * passerelle — voir ROADMAP_SAAS_PHASE3.md). Le plan Gratuit n'expire
     * jamais ; les plans payants suivent le cycle choisi pour que
     * `subscriptions:expire` ait un vrai comportement à terme.
     */
    public static function computeEndsAt(Plan $plan, string $cycle): ?\Illuminate\Support\Carbon
    {
        if ($plan->slug === 'free') {
            return null;
        }

        return $cycle === 'yearly' ? now()->addYear() : now()->addMonth();
    }
}
