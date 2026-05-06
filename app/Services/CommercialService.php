<?php

namespace App\Services;

use App\Models\CommercialGroup;
use App\Models\Funnel;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;

class CommercialService
{
    /**
     * Create a new commercial user (Onboarding: Add Commercial)
     */
    public function createCommercial(Tenant $tenant, array $data): User
    {
        $commercial = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'] ?? Str::random(12)),
            'tenant_id' => $tenant->id,
            'shop_name' => $data['shop_name'] ?? null,
            'bio' => $data['bio'] ?? null,
            'phone' => $data['phone'] ?? null,
            'whatsapp_number' => $data['whatsapp_number'] ?? $data['phone'] ?? null,
            'social_links' => $data['social_links'] ?? null,
            'branding' => $data['branding'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $commercial->assignRole('commercial');

        // Assign to groups if specified
        if (!empty($data['group_ids'])) {
            $this->assignToGroups($commercial, $data['group_ids']);
        }

        return $commercial;
    }

    /**
     * Create a commercial group (Onboarding: Create Team)
     */
    public function createGroup(Tenant $tenant, array $data): CommercialGroup
    {
        return CommercialGroup::create([
            'tenant_id' => $tenant->id,
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Assign commercial to groups
     */
    public function assignToGroups(User $commercial, array $groupIds): void
    {
        $commercial->commercialGroups()->sync($groupIds);
    }

    /**
     * Add commercial to a single group
     */
    public function addToGroup(User $commercial, CommercialGroup $group): void
    {
        $commercial->commercialGroups()->syncWithoutDetaching($group->id);
    }

    /**
     * Remove commercial from a group
     */
    public function removeFromGroup(User $commercial, CommercialGroup $group): void
    {
        $commercial->commercialGroups()->detach($group->id);
    }

    /**
     * Assign funnels to a group
     */
    public function assignFunnelsToGroup(CommercialGroup $group, array $funnelIds, bool $canCustomize = false): void
    {
        $syncData = [];
        foreach ($funnelIds as $funnelId) {
            $syncData[$funnelId] = ['can_customize' => $canCustomize];
        }

        $group->funnels()->sync($syncData);
    }

    /**
     * Activate a funnel for a commercial
     */
    public function activateFunnel(User $commercial, Funnel $funnel, ?string $customSlug = null, ?array $customBranding = null): void
    {
        $commercial->activateFunnel($funnel, $customSlug, $customBranding);

        // Update funnel stats for this commercial
        $funnel->updateStatsForCommercial($commercial);
    }

    /**
     * Deactivate a funnel for a commercial
     */
    public function deactivateFunnel(User $commercial, Funnel $funnel): void
    {
        $commercial->usableFunnels()->updateExistingPivot($funnel->id, [
            'is_active' => false,
        ]);
    }

    /**
     * Update commercial profile
     */
    public function updateProfile(User $commercial, array $data): User
    {
        $updateData = [];
        $fields = ['name', 'shop_name', 'bio', 'phone', 'whatsapp_number', 'social_links', 'branding', 'avatar'];

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        $commercial->update($updateData);

        return $commercial->fresh();
    }

    /**
     * Update commercial subdomain
     */
    public function updateSubdomain(User $commercial, string $subdomain): User
    {
        // Check if subdomain is available
        if (User::where('subdomain', $subdomain)->where('id', '!=', $commercial->id)->exists()) {
            throw new \InvalidArgumentException('Ce sous-domaine est déjà utilisé.');
        }

        $commercial->update(['subdomain' => Str::slug($subdomain)]);

        return $commercial->fresh();
    }

    /**
     * Get commercial statistics
     */
    public function getStats(User $commercial): array
    {
        return $commercial->getCommercialStats();
    }

    /**
     * Get commercial leaderboard for a tenant
     */
    public function getLeaderboard(Tenant $tenant, int $limit = 10): array
    {
        return $tenant->commercials()
            ->withCount([
                'broughtLeads',
                'broughtLeads as conversions_count' => function ($query) {
                    $query->whereNotNull('converted_at');
                }
            ])
            ->orderByDesc('brought_leads_count')
            ->limit($limit)
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'shop_name' => $user->shop_name,
                'avatar' => $user->getAvatarUrl(),
                'leads' => $user->brought_leads_count,
                'conversions' => $user->conversions_count,
                'conversion_rate' => $user->brought_leads_count > 0
                    ? round($user->conversions_count / $user->brought_leads_count * 100, 2)
                    : 0,
            ])
            ->toArray();
    }

    /**
     * Get available funnels for a commercial
     */
    public function getAvailableFunnels(User $commercial): \Illuminate\Database\Eloquent\Collection
    {
        return $commercial->availableFunnels()->active()->get();
    }

    /**
     * Check if commercial can access a funnel
     */
    public function canAccessFunnel(User $commercial, Funnel $funnel): bool
    {
        // Check direct column assignment (assigned_to)
        if ($funnel->assigned_to === $commercial->id) {
            return true;
        }

        // Check via pivot table (usableFunnels)
        if ($commercial->usableFunnels()->where('funnel_id', $funnel->id)->exists()) {
            return true;
        }

        // Check via commercial groups
        return $commercial->commercialGroups()
            ->whereHas('funnels', fn($q) => $q->where('funnels.id', $funnel->id))
            ->exists();
    }
}
