<?php

namespace Tests\Feature;

use App\Enums\EmailSequenceTrigger;
use App\Enums\EmailSequenceStatus;
use App\Enums\FunnelStatus;
use App\Filament\Resources\EmailSequences\Pages\CreateEmailSequence;
use App\Filament\Resources\Funnels\Pages\CreateFunnel;
use App\Models\EmailSequence;
use App\Models\Funnel;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

class QuotaEnforcementTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_funnel_creation_is_blocked_once_free_plan_tunnel_quota_is_reached(): void
    {
        $tenant = $this->createTenantOnPlan('free'); // limite: 2 tunnels
        $admin = $this->createAdminForTenant($tenant);
        $this->fillFunnelQuota($tenant, 2);

        $this->actingAs($admin);

        Livewire::test(CreateFunnel::class)
            ->fillForm([
                'name' => 'Tunnel de trop',
                'slug' => 'tunnel-de-trop',
                'status' => FunnelStatus::DRAFT->value,
            ])
            ->call('create');

        $this->assertNull(Funnel::where('slug', 'tunnel-de-trop')->first());
    }

    public function test_funnel_creation_succeeds_when_under_quota(): void
    {
        $tenant = $this->createTenantOnPlan('free'); // limite: 2 tunnels
        $admin = $this->createAdminForTenant($tenant);
        $this->fillFunnelQuota($tenant, 1); // encore 1 de dispo

        $this->actingAs($admin);

        Livewire::test(CreateFunnel::class)
            ->fillForm([
                'name' => 'Tunnel OK',
                'slug' => 'tunnel-ok',
                'status' => FunnelStatus::DRAFT->value,
            ])
            ->call('create');

        $this->assertNotNull(Funnel::where('slug', 'tunnel-ok')->first());
    }

    public function test_email_sequence_creation_is_blocked_once_free_plan_mailing_list_quota_is_reached(): void
    {
        $tenant = $this->createTenantOnPlan('free'); // limite: 1 liste mailing
        $admin = $this->createAdminForTenant($tenant);

        EmailSequence::create([
            'tenant_id' => $tenant->id,
            'name' => 'Séquence existante',
            'trigger' => EmailSequenceTrigger::MANUAL,
            'status' => EmailSequenceStatus::DRAFT,
        ]);

        $this->actingAs($admin);

        Livewire::test(CreateEmailSequence::class)
            ->fillForm([
                'name' => 'Séquence de trop',
                'status' => EmailSequenceStatus::DRAFT->value,
                'trigger' => EmailSequenceTrigger::MANUAL->value,
            ])
            ->call('create');

        $this->assertNull(EmailSequence::where('name', 'Séquence de trop')->first());
    }

    private function fillFunnelQuota(Tenant $tenant, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            Funnel::create([
                'tenant_id' => $tenant->id,
                'name' => 'Funnel ' . uniqid(),
                'slug' => 'funnel-' . uniqid(),
                'status' => 'draft',
                'is_template' => false,
            ]);
        }
    }
}
