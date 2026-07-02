<?php

namespace Tests\Concerns;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Helpers communs aux tests Phase 3 SaaS (Module 1/2/3) : rôles Spatie,
 * plans, tenants avec abonnement actif.
 */
trait SetsUpSaasTestData
{
    protected function seedRolesAndPlans(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['super_admin', 'admin', 'manager', 'commercial'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        (new PlanSeeder())->run();
    }

    protected function createTenantOnPlan(string $planSlug = 'free'): Tenant
    {
        $tenant = Tenant::create(['name' => 'Tenant ' . Str::random(8)]);

        $plan = Plan::where('slug', $planSlug)->firstOrFail();

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'cycle' => 'monthly',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => null,
        ]);

        return $tenant;
    }

    protected function createAdminForTenant(Tenant $tenant): User
    {
        $admin = User::factory()->create(['tenant_id' => $tenant->id, 'is_active' => true]);
        $admin->assignRole('admin');

        return $admin;
    }

    protected function createSuperAdmin(): User
    {
        $superAdmin = User::factory()->create(['is_active' => true]);
        $superAdmin->assignRole('super_admin');

        return $superAdmin;
    }

    protected function createCommercialForTenant(Tenant $tenant): User
    {
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'is_active' => true]);
        $user->assignRole('commercial');

        return $user;
    }
}
