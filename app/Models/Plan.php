<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'price_monthly',
        'price_yearly',
        'max_tunnels',
        'max_mailing_lists',
        'max_leads',
        'max_shared_tunnels',
        'available_roles',
        'dashboard_level',
        'support_level',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'available_roles' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Une limite à null signifie "illimité".
     */
    public function isUnlimited(string $quotaField): bool
    {
        return is_null($this->{$quotaField});
    }
}
