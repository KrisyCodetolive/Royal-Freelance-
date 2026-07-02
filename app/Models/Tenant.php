<?php

namespace App\Models;

use App\Traits\HasSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Tenant extends Model
{
    use HasFactory, HasSlug, HasSettings, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'domain',
        'email',
        'phone',
        'logo',
        'favicon',
        'branding',
        'settings',
        'timezone',
        'currency',
        'locale',
        'is_active',
        'trial_ends_at',
        'suspended_at',
    ];

    protected $casts = [
        'branding' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Tenant $tenant) {
            $tenant->uuid = $tenant->uuid ?? (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function funnels(): HasMany
    {
        return $this->hasMany(Funnel::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function scoringRules(): HasMany
    {
        return $this->hasMany(ScoringRule::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

    public function commercialGroups(): HasMany
    {
        return $this->hasMany(CommercialGroup::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()->active()->latest('starts_at')->first();
    }

    public function currentPlan(): ?Plan
    {
        return $this->activeSubscription()?->plan;
    }

    /**
     * Get all commercials (users with commercial role) in this tenant
     */
    public function commercials()
    {
        return $this->users()->role('commercial');
    }

    // Helpers
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function getBrandingColor(string $key, string $default = '#6366f1'): string
    {
        return $this->branding[$key] ?? $default;
    }

    public function getScoringThresholds(): array
    {
        return array_merge([
            'cold' => 0,
            'warm' => 11,
            'hot' => 31,
            'ultra_hot' => 61,
        ], $this->settings['scoring_thresholds'] ?? []);
    }
}
