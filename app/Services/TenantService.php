<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use App\Models\ScoringRule;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * Create a new tenant with all required defaults
     */
    public function create(array $data): Tenant
    {
        $tenant = Tenant::create([
            'uuid' => Str::uuid(),
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'timezone' => $data['timezone'] ?? 'Africa/Douala',
            'currency' => $data['currency'] ?? 'XOF',
            'locale' => $data['locale'] ?? 'fr',
            'branding' => $data['branding'] ?? $this->getDefaultBranding(),
            'settings' => $data['settings'] ?? $this->getDefaultSettings(),
        ]);

        // Create default scoring rules
        $this->createDefaultScoringRules($tenant);

        return $tenant;
    }

    /**
     * Setup a tenant with an admin user (Onboarding Step 1)
     *
     * `adminData['password']` peut être un mot de passe en clair ou déjà
     * haché (ex: inscription self-service en plusieurs étapes qui hache le
     * mot de passe avant de le garder en session) : le cast `hashed` du
     * modèle `User` détecte lequel via `Hash::isHashed()` et ne le hache
     * pas une seconde fois.
     */
    public function setupWithAdmin(array $tenantData, array $adminData): array
    {
        $tenant = $this->create($tenantData);

        $admin = User::create([
            'name' => $adminData['name'],
            'email' => $adminData['email'],
            'password' => $adminData['password'],
            'tenant_id' => $tenant->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('admin');

        return [
            'tenant' => $tenant,
            'admin' => $admin,
        ];
    }

    /**
     * Get default branding settings
     */
    public function getDefaultBranding(): array
    {
        return [
            'primary_color' => '#6366f1',
            'secondary_color' => '#8b5cf6',
            'accent_color' => '#f59e0b',
            'text_color' => '#1f2937',
            'background_color' => '#ffffff',
            'font_family' => 'Inter',
        ];
    }

    /**
     * Get default tenant settings
     */
    public function getDefaultSettings(): array
    {
        return [
            'scoring_thresholds' => [
                'cold' => 0,
                'warm' => 11,
                'hot' => 31,
                'ultra_hot' => 61,
            ],
            'notifications' => [
                'email_on_hot_lead' => true,
                'email_on_new_registration' => true,
                'push_enabled' => true,
            ],
            'tracking' => [
                'track_video_progress' => true,
                'track_page_time' => true,
            ],
        ];
    }

    /**
     * Create default scoring rules for a tenant
     */
    public function createDefaultScoringRules(Tenant $tenant): void
    {
        ScoringRule::createDefaultsForTenant($tenant);
    }

    /**
     * Update tenant branding
     */
    public function updateBranding(Tenant $tenant, array $branding): Tenant
    {
        $tenant->update([
            'branding' => array_merge($tenant->branding ?? [], $branding),
        ]);

        return $tenant->fresh();
    }

    /**
     * Update scoring thresholds
     */
    public function updateScoringThresholds(Tenant $tenant, array $thresholds): Tenant
    {
        $settings = $tenant->settings ?? [];
        $settings['scoring_thresholds'] = array_merge(
            $settings['scoring_thresholds'] ?? [],
            $thresholds
        );

        $tenant->update(['settings' => $settings]);

        return $tenant->fresh();
    }

    /**
     * Get tenant statistics
     */
    public function getStats(Tenant $tenant): array
    {
        return [
            'total_funnels' => $tenant->funnels()->count(),
            'active_funnels' => $tenant->funnels()->active()->count(),
            'total_leads' => $tenant->leads()->count(),
            'hot_leads' => $tenant->leads()->hot()->count(),
            'conversions' => $tenant->leads()->converted()->count(),
            'total_commercials' => $tenant->commercials()->count(),
            'commercial_groups' => $tenant->commercialGroups()->count(),
        ];
    }
}
