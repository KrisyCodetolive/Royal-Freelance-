<?php

namespace App\Models;

use App\Enums\EventType;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoringRule extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'event_type',
        'name',
        'description',
        'points',
        'is_active',
    ];

    protected $casts = [
        'event_type' => EventType::class,
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helpers
    public static function getDefaultRules(): array
    {
        return collect(EventType::cases())
            ->map(fn(EventType $type) => [
                'event_type' => $type->value,
                'name' => $type->label(),
                'points' => $type->defaultPoints(),
                'is_active' => true,
            ])
            ->all();
    }

    public static function createDefaultsForTenant(Tenant $tenant): void
    {
        foreach (self::getDefaultRules() as $rule) {
            self::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'event_type' => $rule['event_type'],
                ],
                $rule
            );
        }
    }
}
