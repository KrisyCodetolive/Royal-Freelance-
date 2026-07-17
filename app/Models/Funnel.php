<?php

namespace App\Models;

use App\Enums\FunnelStatus;
use App\Traits\BelongsToTenant;
use App\Traits\HasSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Funnel extends Model implements HasMedia
{
    use HasFactory, BelongsToTenant, HasSlug, HasSettings, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'offer_id',
        'assigned_to',
        'name',
        'slug',
        'subdomain',
        'custom_domain',
        'ssl_active',
        'domain_verified_at',
        'description',
        'status',
        'is_template',
        'template_category',
        'template_description',
        'template_thumbnail',
        'template_tags',
        'template_uses_count',
        'template',
        'whatsapp_url',
        'whatsapp_message',
        'payment_url',
        'branding',
        'settings',
        'meta_title',
        'meta_description',
        'meta_image',
        'views_count',
        'leads_count',
        'conversions_count',
        'conversion_rate',
        'published_at',
    ];

    protected $casts = [
        'status' => FunnelStatus::class,
        'is_template' => 'boolean',
        'template_tags' => 'array',
        'branding' => 'array',
        'settings' => 'array',
        'conversion_rate' => 'decimal:2',
        'published_at' => 'datetime',
        'domain_verified_at' => 'datetime',
        'ssl_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Funnel $funnel) {
            $funnel->uuid = $funnel->uuid ?? (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Check if funnel has a custom URL (subdomain or custom domain)
     */
    public function hasCustomUrl(): bool
    {
        return !empty($this->subdomain) || (!empty($this->custom_domain) && $this->domain_verified_at);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('meta_image')->singleFile();
    }

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class)->orderBy('sort_order');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function events(): HasManyThrough
    {
        return $this->hasManyThrough(Event::class, Lead::class);
    }

    /**
     * Get all analytics events for this funnel (via pages)
     */
    public function analytics(): HasManyThrough
    {
        return $this->hasManyThrough(Event::class, Page::class);
    }

    /**
     * Email sequences for this funnel
     */
    public function emailSequences(): HasMany
    {
        return $this->hasMany(EmailSequence::class);
    }

    /**
     * Commercial groups that have access to this funnel
     */
    public function commercialGroups(): BelongsToMany
    {
        return $this->belongsToMany(CommercialGroup::class, 'commercial_group_funnel')
            ->withPivot(['can_customize', 'can_edit'])
            ->withTimestamps();
    }

    /**
     * Commercials who are using this funnel
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'funnel_user')
            ->withPivot(['custom_slug', 'custom_branding', 'is_active', 'can_edit', 'leads_count', 'conversions_count'])
            ->withTimestamps();
    }

    /**
     * Get all commercials who have access to this funnel (via groups)
     */
    public function getAccessibleCommercials()
    {
        return User::whereHas('commercialGroups', function ($query) {
            $query->whereHas('funnels', function ($q) {
                $q->where('funnels.id', $this->id);
            });
        })->orWhereHas('usableFunnels', function ($query) {
            $query->where('funnels.id', $this->id);
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', FunnelStatus::ACTIVE);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', FunnelStatus::DRAFT);
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopeNotTemplates($query)
    {
        return $query->where('is_template', false);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('template_category', $category);
    }

    public function scopeAvailableToUser($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            // Assigned directly
            $q->where('assigned_to', $user->id)
                // Or in user's commercial groups
                ->orWhereHas('commercialGroups.users', function ($groupQuery) use ($user) {
                    $groupQuery->where('users.id', $user->id);
                })
                // Or activated by user
                ->orWhereHas('users', function ($userQuery) use ($user) {
                    $userQuery->where('users.id', $user->id);
                });
        });
    }

    /**
     * Module 5 : un Owner/Admin peut toujours éditer. Un Viewer n'édite
     * jamais, quel que soit son pivot de partage (rôle strictement lecture
     * seule). Un Editor peut éditer si le tunnel lui est assigné directement,
     * ou partagé avec `can_edit` (individuellement ou via un de ses groupes).
     */
    public function canBeEditedBy(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isViewer()) {
            return false;
        }

        if ($this->assigned_to === $user->id) {
            return true;
        }

        if ($this->users()->where('user_id', $user->id)->wherePivot('can_edit', true)->exists()) {
            return true;
        }

        return $this->commercialGroups()
            ->wherePivot('can_edit', true)
            ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
            ->exists();
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === FunnelStatus::ACTIVE;
    }

    /**
     * Distinct de isActive() (statut du tunnel) : un tenant suspendu par le
     * super_admin (Module 7) coupe l'accès public à tous ses tunnels, même
     * ceux marqués actifs — sans changer leur statut ni le comportement
     * existant de isActive() ailleurs dans le code (panel, listes...).
     */
    public function isPubliclyAccessible(): bool
    {
        return $this->isActive() && !($this->tenant?->isSuspended() ?? false);
    }

    public function isDraft(): bool
    {
        return $this->status === FunnelStatus::DRAFT;
    }

    /**
     * Get the public URL for this funnel (subdomain or custom domain preferred)
     */
    public function getPublicUrl(?string $pageSlug = null): string
    {
        return app(\App\Services\SubdomainService::class)->getFunnelUrl($this, $pageSlug);
    }

    /**
     * Get the public URL for a specific commercial's subdomain
     */
    public function getUrlForCommercial(User $commercial): string
    {
        if (!$commercial->subdomain) {
            return $this->getPublicUrl();
        }

        // Check for custom slug in pivot
        $pivot = $this->users()->where('user_id', $commercial->id)->first()?->pivot;
        $slug = $pivot?->custom_slug ?? $this->slug;

        $parsed = parse_url(config('app.url'));
        $scheme = $parsed['scheme'] ?? 'https';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $baseDomain = config('app.subdomain_base', 'royalleadmagnet.com');

        return "{$scheme}://{$commercial->subdomain}.{$baseDomain}{$port}/f/{$slug}";
    }

    /**
     * Get branding for a specific commercial (with fallbacks)
     */
    public function getBrandingForCommercial(User $commercial): array
    {
        // Priority: Commercial custom branding > Funnel branding > Tenant branding
        $pivot = $this->users()->where('user_id', $commercial->id)->first()?->pivot;
        $customBranding = $pivot?->custom_branding ? json_decode($pivot->custom_branding, true) : [];

        return array_merge(
            $this->tenant?->branding ?? [],
            $this->branding ?? [],
            $commercial->branding ?? [],
            $customBranding
        );
    }

    public function getWhatsAppLink(?string $message = null): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->whatsapp_url);
        $text = urlencode($message ?? $this->whatsapp_message ?? '');

        return "https://wa.me/{$phone}" . ($text ? "?text={$text}" : '');
    }

    /**
     * Get WhatsApp link for specific commercial (uses their number)
     */
    public function getWhatsAppLinkForCommercial(User $commercial, ?string $message = null): string
    {
        // Use commercial's WhatsApp if available, fallback to funnel's
        $whatsapp = $commercial->whatsapp_number ?? $this->whatsapp_url;
        $phone = preg_replace('/[^0-9]/', '', $whatsapp ?? '');
        $text = urlencode($message ?? $this->whatsapp_message ?? '');

        return "https://wa.me/{$phone}" . ($text ? "?text={$text}" : '');
    }

    public function updateStats(): void
    {
        $this->leads_count = $this->leads()->count();
        $this->conversions_count = $this->leads()->whereNotNull('converted_at')->count();
        $this->conversion_rate = $this->leads_count > 0
            ? round(($this->conversions_count / $this->leads_count) * 100, 2)
            : 0;
        $this->save();
    }

    /**
     * Update stats for a specific commercial using this funnel
     */
    public function updateStatsForCommercial(User $commercial): void
    {
        $leads = $this->leads()->where('brought_by', $commercial->id)->count();
        $conversions = $this->leads()->where('brought_by', $commercial->id)->whereNotNull('converted_at')->count();

        $this->users()->updateExistingPivot($commercial->id, [
            'leads_count' => $leads,
            'conversions_count' => $conversions,
        ]);
    }

    /**
     * Assign this funnel to a commercial group
     */
    public function assignToGroup(CommercialGroup $group, bool $canCustomize = false): void
    {
        $this->commercialGroups()->syncWithoutDetaching([
            $group->id => ['can_customize' => $canCustomize],
        ]);
    }

    public function duplicate(?string $newName = null): self
    {
        $clone = $this->replicate([
            'uuid',
            'slug',
            'subdomain',
            'custom_domain',
            'domain_verified_at',
            'ssl_active',
            'leads_count',
            'conversions_count',
            'conversion_rate',
            'published_at',
            'template_uses_count',
            'pages_count',
            'views_count',
        ]);
        $clone->name = $newName ?? $this->name . ' (copie)';
        $clone->status = FunnelStatus::DRAFT;
        $clone->is_template = false; // Copies are never templates

        // Générer un nouveau slug basé sur le nom
        $clone->slug = \Illuminate\Support\Str::slug($clone->name);

        // S'assurer que le slug est unique
        $originalSlug = $clone->slug;
        $counter = 1;
        while (self::where('slug', $clone->slug)->where('id', '!=', $clone->id ?? 0)->exists()) {
            $clone->slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Générer un nouveau sous-domaine unique
        $subdomainService = app(\App\Services\SubdomainService::class);
        $clone->subdomain = $subdomainService->generateFromName($clone->name);

        $clone->save();

        // Duplicate pages and blocks
        foreach ($this->pages as $page) {
            $newPage = $page->replicate(['uuid']);
            $newPage->funnel_id = $clone->id;
            $newPage->save();

            foreach ($page->blocks as $block) {
                $newBlock = $block->replicate(['uuid']);
                $newBlock->page_id = $newPage->id;
                $newBlock->save();
            }
        }

        return $clone;
    }

    /**
     * Create a new funnel from this template
     */
    public function createFromTemplate(string $name, ?int $tenantId = null, ?int $offerId = null): self
    {
        if (!$this->is_template) {
            throw new \InvalidArgumentException('This funnel is not a template');
        }

        $funnel = $this->duplicate($name);
        $funnel->is_template = false;
        $funnel->template_category = null;
        $funnel->template_description = null;
        $funnel->template_thumbnail = null;
        $funnel->template_tags = null;

        // Activer le tunnel créé depuis template
        $funnel->status = \App\Enums\FunnelStatus::ACTIVE;

        if ($tenantId) {
            $funnel->tenant_id = $tenantId;
        }

        if ($offerId) {
            $funnel->offer_id = $offerId;
        }

        $funnel->save();

        // Increment template usage count
        static::withoutEvents(fn () => $this->increment('template_uses_count'));

        return $funnel;
    }

    /**
     * Check if this funnel is a template
     */
    public function isTemplate(): bool
    {
        return $this->is_template === true;
    }

    /**
     * Convert this funnel to a template
     */
    public function convertToTemplate(string $category, ?string $description = null): self
    {
        $this->is_template = true;
        $this->template_category = $category;
        $this->template_description = $description ?? $this->description;
        $this->save();

        return $this;
    }
}

