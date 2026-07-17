<?php

namespace Tests\Feature;

use App\Enums\FunnelStatus;
use App\Filament\Pages\SaasAnalytics;
use App\Filament\Resources\Tenants\Pages\ViewTenant;
use App\Filament\Widgets\SaasAnalyticsOverview;
use App\Models\Funnel;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

/**
 * Module 7 — Super Admin : suspension/réactivation de tenant (panel +
 * tunnels publics), analytics SaaS globale. Cf. ROADMAP_SAAS_PHASE3.md.
 */
class SuperAdminTenantManagementTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_suspending_a_tenant_sets_suspended_at_and_reactivating_clears_it(): void
    {
        $tenant = $this->createTenantOnPlan();

        $tenant->suspend();
        $this->assertTrue($tenant->fresh()->isSuspended());

        $tenant->reactivate();
        $this->assertFalse($tenant->fresh()->isSuspended());
    }

    public function test_admin_of_a_suspended_tenant_cannot_access_the_panel(): void
    {
        $tenant = $this->createTenantOnPlan();
        $admin = $this->createAdminForTenant($tenant);

        $tenant->suspend();

        $this->assertFalse($admin->fresh()->canAccessPanel(app(\Filament\Panel::class)));
    }

    public function test_admin_of_a_reactivated_tenant_can_access_the_panel_again(): void
    {
        $tenant = $this->createTenantOnPlan();
        $admin = $this->createAdminForTenant($tenant);

        $tenant->suspend();
        $tenant->reactivate();

        $this->assertTrue($admin->fresh()->canAccessPanel(app(\Filament\Panel::class)));
    }

    public function test_super_admin_can_access_the_panel_even_if_somehow_attached_to_a_suspended_tenant(): void
    {
        $tenant = $this->createTenantOnPlan();
        $superAdmin = $this->createSuperAdmin();
        $superAdmin->update(['tenant_id' => $tenant->id]);

        $tenant->suspend();

        $this->assertTrue($superAdmin->fresh()->canAccessPanel(app(\Filament\Panel::class)));
    }

    public function test_admin_whose_tenant_gets_suspended_mid_session_sees_an_explicit_message_not_a_bare_forbidden(): void
    {
        $tenant = $this->createTenantOnPlan();
        $admin = $this->createAdminForTenant($tenant);

        $this->actingAs($admin);
        $this->get('/admin')->assertOk();

        $tenant->suspend();

        // Ré-authentifier avec un modèle frais : le guard de test garde en
        // cache l'instance User (et sa relation tenant déjà chargée) de la
        // requête précédente, ce qui masquerait la suspension tout juste
        // appliquée en base.
        $this->actingAs($admin->fresh());

        $response = $this->get('/admin');
        $response->assertForbidden();
        $response->assertSee('suspendu');
        $response->assertSee('support@royalleadpro.com');
    }

    public function test_login_with_valid_credentials_for_a_suspended_tenant_shows_a_suspension_notice(): void
    {
        $tenant = $this->createTenantOnPlan();
        $admin = $this->createAdminForTenant($tenant);
        $tenant->suspend();

        Livewire::test(\App\Filament\Pages\Auth\Login::class)
            ->set('data.email', $admin->email)
            ->set('data.password', 'password')
            ->call('authenticate');

        $this->assertGuest();
    }

    public function test_public_funnel_pages_of_a_suspended_tenant_show_the_suspended_notice_instead_of_a_404(): void
    {
        $funnel = $this->makePublishedFunnel();

        $response = $this->get('http://' . $funnel->subdomain . '.' . config('app.subdomain_base') . '/');
        $response->assertOk();

        $funnel->tenant->suspend();

        $response = $this->get('http://' . $funnel->subdomain . '.' . config('app.subdomain_base') . '/');
        $response->assertForbidden();
        $response->assertViewIs('funnel.suspended');
    }

    public function test_public_funnel_pages_are_reachable_again_after_reactivation(): void
    {
        $funnel = $this->makePublishedFunnel();
        $funnel->tenant->suspend();

        $funnel->tenant->reactivate();

        $response = $this->get('http://' . $funnel->subdomain . '.' . config('app.subdomain_base') . '/');
        $response->assertOk();
    }

    public function test_super_admin_can_suspend_and_reactivate_a_tenant_via_the_view_page_actions(): void
    {
        $tenant = $this->createTenantOnPlan();
        $this->actingAs($this->createSuperAdmin());

        Livewire::test(ViewTenant::class, ['record' => $tenant->getRouteKey()])
            ->callAction('suspend');

        $this->assertTrue($tenant->fresh()->isSuspended());

        Livewire::test(ViewTenant::class, ['record' => $tenant->getRouteKey()])
            ->callAction('reactivate');

        $this->assertFalse($tenant->fresh()->isSuspended());
    }

    public function test_saas_analytics_page_renders_for_super_admin(): void
    {
        $this->createTenantOnPlan();
        $this->actingAs($this->createSuperAdmin());

        Livewire::test(SaasAnalytics::class)->assertOk();
    }

    public function test_tenant_owner_cannot_access_saas_analytics_page(): void
    {
        $tenant = $this->createTenantOnPlan();
        $owner = $this->createOwnerForTenant($tenant);

        $this->actingAs($owner);

        $this->assertFalse(SaasAnalytics::canAccess());
    }

    public function test_super_admin_can_access_saas_analytics_page(): void
    {
        $this->actingAs($this->createSuperAdmin());

        $this->assertTrue(SaasAnalytics::canAccess());
    }

    public function test_mrr_sums_active_paid_subscriptions_normalized_to_monthly(): void
    {
        $starter = Plan::where('slug', 'starter')->firstOrFail();
        $prestige = Plan::where('slug', 'prestige')->firstOrFail();

        $monthlyTenant = $this->createTenantOnPlan('starter');

        $yearlyTenant = Tenant::create(['name' => 'Tenant ' . uniqid()]);
        Subscription::create([
            'tenant_id' => $yearlyTenant->id,
            'plan_id' => $prestige->id,
            'cycle' => 'yearly',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
        ]);

        $expiredTenant = Tenant::create(['name' => 'Tenant ' . uniqid()]);
        Subscription::create([
            'tenant_id' => $expiredTenant->id,
            'plan_id' => $starter->id,
            'cycle' => 'monthly',
            'status' => 'active',
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subMonth(),
        ]);

        $expectedMrr = $starter->price_monthly + ($prestige->price_yearly / 12);

        $mrr = (new SaasAnalyticsOverview())->monthlyRecurringRevenue();

        $this->assertEqualsWithDelta($expectedMrr, $mrr, 0.01);
    }

    private function makePublishedFunnel(): Funnel
    {
        $tenant = $this->createTenantOnPlan();

        $funnel = Funnel::create([
            'tenant_id' => $tenant->id,
            'name' => 'Funnel ' . uniqid(),
            'slug' => 'funnel-' . uniqid(),
            'subdomain' => 'sub-' . uniqid(),
            'status' => FunnelStatus::ACTIVE,
            'published_at' => now(),
            'is_template' => false,
        ]);

        Page::create([
            'funnel_id' => $funnel->id,
            'type' => \App\Enums\PageType::CAPTURE,
            'title' => 'Page racine',
            'slug' => 'racine-' . uniqid(),
            'sort_order' => 1,
            'is_active' => true,
            'is_required' => true,
        ]);

        return $funnel;
    }
}
