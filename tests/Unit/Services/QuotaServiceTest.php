<?php

namespace Tests\Unit\Services;

use App\Enums\EmailSequenceTrigger;
use App\Models\EmailSequence;
use App\Models\Funnel;
use App\Models\Lead;
use App\Services\QuotaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

class QuotaServiceTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    private QuotaService $quota;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
        $this->quota = app(QuotaService::class);
    }

    public function test_tenant_without_subscription_has_zero_limit_on_every_quota(): void
    {
        $tenant = \App\Models\Tenant::create(['name' => 'No Sub Tenant']);

        $this->assertSame(0, $this->quota->limit($tenant, 'tunnels'));
        $this->assertTrue($this->quota->hasReachedLimit($tenant, 'tunnels'));
    }

    public function test_free_plan_limits_match_seeded_values(): void
    {
        $tenant = $this->createTenantOnPlan('free');

        $this->assertSame(2, $this->quota->limit($tenant, 'tunnels'));
        $this->assertSame(1, $this->quota->limit($tenant, 'mailing_lists'));
        $this->assertSame(1000, $this->quota->limit($tenant, 'leads'));
        $this->assertSame(0, $this->quota->limit($tenant, 'shared_tunnels'));
    }

    public function test_usage_counts_real_funnels_and_excludes_templates(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);
        $this->actingAs($admin);

        $this->makeFunnel($tenant, is_template: false);
        $this->makeFunnel($tenant, is_template: false);
        $this->makeFunnel($tenant, is_template: true); // ne doit pas compter

        $this->assertSame(2, $this->quota->usage($tenant)['tunnels']);
    }

    public function test_usage_counts_mailing_lists_and_leads(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);
        $this->actingAs($admin);

        $funnel = $this->makeFunnel($tenant);

        EmailSequence::create([
            'tenant_id' => $tenant->id,
            'name' => 'Séquence 1',
            'trigger' => EmailSequenceTrigger::MANUAL,
            'status' => 'draft',
        ]);

        Lead::create(['tenant_id' => $tenant->id, 'funnel_id' => $funnel->id]);
        Lead::create(['tenant_id' => $tenant->id, 'funnel_id' => $funnel->id]);

        $usage = $this->quota->usage($tenant);
        $this->assertSame(1, $usage['mailing_lists']);
        $this->assertSame(2, $usage['leads']);
    }

    public function test_shared_tunnels_usage_counts_funnels_with_at_least_one_assigned_user(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);
        $this->actingAs($admin);

        $shared = $this->makeFunnel($tenant);
        $this->makeFunnel($tenant); // pas partagé

        $commercial = $this->createCommercialForTenant($tenant);
        $commercial->activateFunnel($shared);

        $this->assertSame(1, $this->quota->usage($tenant)['shared_tunnels']);
    }

    public function test_shared_tunnels_usage_counts_funnels_shared_via_a_commercial_group(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);
        $this->actingAs($admin);

        $shared = $this->makeFunnel($tenant);
        $group = \App\Models\CommercialGroup::create(['tenant_id' => $tenant->id, 'name' => 'Groupe A']);
        $group->addFunnel($shared);

        $this->assertSame(1, $this->quota->usage($tenant)['shared_tunnels']);
    }

    public function test_can_share_funnel_is_false_once_shared_tunnels_limit_is_reached(): void
    {
        $tenant = $this->createTenantOnPlan('starter'); // limite: 5 tunnels partagés
        $admin = $this->createAdminForTenant($tenant);
        $this->actingAs($admin);
        $commercial = $this->createCommercialForTenant($tenant);

        for ($i = 0; $i < 5; $i++) {
            $commercial->activateFunnel($this->makeFunnel($tenant));
        }

        $newFunnel = $this->makeFunnel($tenant);
        $this->assertFalse($this->quota->canShareFunnel($tenant, $newFunnel));
    }

    public function test_can_share_funnel_is_true_for_an_already_shared_funnel_even_at_the_limit(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);
        $this->actingAs($admin);
        $commercial = $this->createCommercialForTenant($tenant);

        $shared = $this->makeFunnel($tenant);
        $commercial->activateFunnel($shared);

        for ($i = 0; $i < 4; $i++) {
            $commercial->activateFunnel($this->makeFunnel($tenant));
        }

        $this->assertTrue($this->quota->hasReachedLimit($tenant, 'shared_tunnels'));
        $this->assertTrue($this->quota->canShareFunnel($tenant, $shared));
    }

    public function test_reaching_free_plan_tunnel_limit_blocks_further_creation(): void
    {
        $tenant = $this->createTenantOnPlan('free');
        $admin = $this->createAdminForTenant($tenant);
        $this->actingAs($admin);

        $this->makeFunnel($tenant);
        $this->makeFunnel($tenant);

        $this->assertSame(0, $this->quota->remaining($tenant, 'tunnels'));
        $this->assertTrue($this->quota->hasReachedLimit($tenant, 'tunnels'));
    }

    public function test_unlimited_plan_quota_is_never_reached(): void
    {
        $tenant = $this->createTenantOnPlan('prestige');
        $admin = $this->createAdminForTenant($tenant);
        $this->actingAs($admin);

        for ($i = 0; $i < 5; $i++) {
            $this->makeFunnel($tenant);
        }

        $this->assertNull($this->quota->remaining($tenant, 'tunnels'));
        $this->assertFalse($this->quota->hasReachedLimit($tenant, 'tunnels'));
    }

    private function makeFunnel(\App\Models\Tenant $tenant, bool $is_template = false): Funnel
    {
        return Funnel::create([
            'tenant_id' => $tenant->id,
            'name' => 'Funnel ' . uniqid(),
            'slug' => 'funnel-' . uniqid(),
            'status' => 'draft',
            'is_template' => $is_template,
        ]);
    }
}
