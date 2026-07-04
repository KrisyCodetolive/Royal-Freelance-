<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasSlug;

    protected $fillable = [
        'name',
        'shop_name',
        'subdomain',
        'bio',
        'whatsapp_number',
        'social_links',
        'branding',
        'email',
        'password',
        'tenant_id',
        'phone',
        'avatar',
        'is_active',
        'last_login_at',
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
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'social_links' => 'array',
            'branding' => 'array',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('shop_name')
            ->saveSlugsTo('subdomain')
            ->slugsShouldBeNoLongerThan(30)
            ->doNotGenerateSlugsOnUpdate();
    }

    protected static function boot(): void
    {
        parent::boot();

        // Generate subdomain from shop_name if not provided
        static::saving(function (User $user) {
            if ($user->shop_name && !$user->subdomain) {
                $baseSubdomain = Str::slug($user->shop_name);
                $subdomain = $baseSubdomain;
                $counter = 1;

                while (static::where('subdomain', $subdomain)->where('id', '!=', $user->id ?? 0)->exists()) {
                    $subdomain = $baseSubdomain . '-' . $counter++;
                }

                $user->subdomain = $subdomain;
            }
        });
    }

    // Filament
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && ($this->isAdmin() || $this->isEditor() || $this->isViewer());
    }

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assignedFunnels(): HasMany
    {
        return $this->hasMany(Funnel::class, 'assigned_to');
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function broughtLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'brought_by');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Commercial groups this user belongs to
     */
    public function commercialGroups(): BelongsToMany
    {
        return $this->belongsToMany(CommercialGroup::class, 'commercial_group_user')
            ->withTimestamps();
    }

    /**
     * Funnels this commercial can use (via pivot)
     */
    public function usableFunnels(): BelongsToMany
    {
        return $this->belongsToMany(Funnel::class, 'funnel_user')
            ->withPivot(['custom_slug', 'custom_branding', 'is_active', 'can_edit', 'leads_count', 'conversions_count'])
            ->withTimestamps();
    }

    /**
     * Get all funnels available to this user (via commercial groups)
     */
    public function availableFunnels()
    {
        if ($this->isAdmin()) {
            // Admins can access all funnels in their tenant
            return Funnel::where('tenant_id', $this->tenant_id);
        }

        // Funnels via groupes commerciaux
        $groupFunnelIds = $this->commercialGroups()
            ->with('funnels')
            ->get()
            ->pluck('funnels')
            ->flatten()
            ->pluck('id')
            ->unique();

        // Funnels assignés directement (colonne assigned_to)
        $assignedIds = $this->assignedFunnels()->pluck('id');

        // Funnels ajoutés manuellement via la table pivot funnel_user
        $usableIds = $this->usableFunnels()->pluck('funnels.id');

        $allIds = $groupFunnelIds->merge($assignedIds)->merge($usableIds)->unique();

        return Funnel::whereIn('id', $allIds);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeCommercials($query)
    {
        return $query->role('commercial');
    }

    public function scopeWithSubdomain($query)
    {
        return $query->whereNotNull('subdomain');
    }

    // Helpers
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['super_admin', 'admin', 'owner']);
    }

    public function isManager(): bool
    {
        return $this->hasRole(['super_admin', 'admin', 'owner', 'manager']);
    }

    /**
     * Owner = propriétaire du workspace (tenant). Seul rôle habilité à
     * gérer l'abonnement de son tenant (cf. royal-leadpro-phase3.html,
     * section "4 Rôles" — Owner a toutes les permissions d'Admin + billing).
     */
    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function isCommercial(): bool
    {
        return $this->hasRole('commercial');
    }

    /**
     * Editor = rôle workspace (Module 5 CDC) : accès panel restreint aux
     * tunnels/leads qui lui sont assignés ou partagés en édition.
     */
    public function isEditor(): bool
    {
        return $this->hasRole('editor');
    }

    /**
     * Viewer = rôle workspace (Module 5 CDC) : accès panel en lecture seule
     * aux tunnels/leads qui lui sont partagés.
     */
    public function isViewer(): bool
    {
        return $this->hasRole('viewer');
    }

    public function hasShop(): bool
    {
        return !empty($this->shop_name) && !empty($this->subdomain);
    }

    public function getUnreadAlertsCount(): int
    {
        return $this->alerts()->unread()->count();
    }

    public function getAvatarUrl(): ?string
    {
        if ($this->avatar) {
            return $this->avatar;
        }

        // Gravatar fallback
        $hash = md5(strtolower(trim($this->email)));

        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=200";
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get the full subdomain URL for this commercial
     */
    public function getSubdomainUrl(): ?string
    {
        if (!$this->subdomain) {
            return null;
        }

        $parsed = parse_url(config('app.url'));
        $scheme = $parsed['scheme'] ?? 'https';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $baseDomain = config('app.subdomain_base', 'royalleadmagnet.com');

        return "{$scheme}://{$this->subdomain}.{$baseDomain}{$port}";
    }

    /**
     * Get funnel URL for this commercial's subdomain
     */
    public function getFunnelUrl(Funnel $funnel): string
    {
        // Check if user has custom slug for this funnel
        $pivot = $this->usableFunnels()->where('funnel_id', $funnel->id)->first()?->pivot;
        $slug = $pivot?->custom_slug ?? $funnel->slug;

        if ($this->subdomain) {
            $parsed = parse_url(config('app.url'));
            $scheme = $parsed['scheme'] ?? 'https';
            $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
            $baseDomain = config('app.subdomain_base', 'royalleadmagnet.com');
            return "{$scheme}://{$this->subdomain}.{$baseDomain}{$port}/f/{$slug}";
        }

        return url("/f/{$slug}");
    }

    /**
     * Get WhatsApp link with pre-filled message
     */
    public function getWhatsAppLink(?string $message = null): ?string
    {
        if (!$this->whatsapp_number) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $this->whatsapp_number);
        $text = $message ? urlencode($message) : '';

        return "https://wa.me/{$phone}" . ($text ? "?text={$text}" : '');
    }

    /**
     * Get branding value with fallback to tenant branding
     */
    public function getBranding(string $key, mixed $default = null): mixed
    {
        // First check user branding
        if (isset($this->branding[$key])) {
            return $this->branding[$key];
        }

        // Fallback to tenant branding
        return $this->tenant?->branding[$key] ?? $default;
    }

    /**
     * Get social link
     */
    public function getSocialLink(string $platform): ?string
    {
        return $this->social_links[$platform] ?? null;
    }

    /**
     * Activate a funnel for this commercial (create pivot)
     */
    public function activateFunnel(Funnel $funnel, ?string $customSlug = null, ?array $customBranding = null): void
    {
        $this->usableFunnels()->syncWithoutDetaching([
            $funnel->id => [
                'custom_slug' => $customSlug,
                'custom_branding' => $customBranding ? json_encode($customBranding) : null,
                'is_active' => true,
            ],
        ]);
    }

    /**
     * Get stats for commercial dashboard
     */
    public function getCommercialStats(): array
    {
        return [
            'total_leads' => $this->broughtLeads()->withoutGlobalScope('tenant')->count(),
            'hot_leads' => $this->broughtLeads()->withoutGlobalScope('tenant')->hot()->count(),
            'conversions' => $this->broughtLeads()->withoutGlobalScope('tenant')->converted()->count(),
            'active_funnels' => $this->availableFunnels()->count(),
        ];
    }
}
