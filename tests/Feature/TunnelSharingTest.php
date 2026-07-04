<?php

namespace Tests\Feature;

use App\Filament\Resources\Funnels\Pages\ViewFunnel;
use App\Models\CommercialGroup;
use App\Models\Funnel;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

class TunnelSharingTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_admin_can_share_a_tunnel_with_an_individual_member(): void
    {
        $tenant = $this->createTenantOnPlan('starter'); // limite: 5 tunnels partagés
        $admin = $this->createAdminForTenant($tenant);
        $member = $this->createCommercialForTenant($tenant);
        $funnel = $this->makeFunnel($tenant);

        $this->actingAs($admin);

        Livewire::test(ViewFunnel::class, ['record' => $funnel->getKey()])
            ->callAction('assign_to_commercials', data: [
                'assignment_type' => 'individual',
                'commercial_ids' => [$member->id],
                'can_customize' => true,
                'can_edit' => true,
            ]);

        $pivot = $funnel->users()->where('user_id', $member->id)->first()?->pivot;

        $this->assertNotNull($pivot);
        $this->assertTrue((bool) $pivot->can_edit);
    }

    public function test_admin_can_share_a_tunnel_with_a_commercial_group(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);
        $funnel = $this->makeFunnel($tenant);
        $group = CommercialGroup::create(['tenant_id' => $tenant->id, 'name' => 'Groupe A']);

        $this->actingAs($admin);

        Livewire::test(ViewFunnel::class, ['record' => $funnel->getKey()])
            ->callAction('assign_to_commercials', data: [
                'assignment_type' => 'group',
                'commercial_group_ids' => [$group->id],
                'can_customize' => false,
                'can_edit' => false,
            ]);

        $pivot = $funnel->commercialGroups()->where('commercial_group_id', $group->id)->first()?->pivot;

        $this->assertNotNull($pivot);
        $this->assertFalse((bool) $pivot->can_edit);
    }

    public function test_sharing_a_new_tunnel_is_blocked_once_shared_tunnels_quota_is_reached(): void
    {
        $tenant = $this->createTenantOnPlan('free'); // limite: 0 tunnel partagé
        $admin = $this->createAdminForTenant($tenant);
        $member = $this->createCommercialForTenant($tenant);
        $funnel = $this->makeFunnel($tenant);

        $this->actingAs($admin);

        Livewire::test(ViewFunnel::class, ['record' => $funnel->getKey()])
            ->callAction('assign_to_commercials', data: [
                'assignment_type' => 'individual',
                'commercial_ids' => [$member->id],
                'can_customize' => true,
                'can_edit' => false,
            ]);

        $this->assertNull($funnel->users()->where('user_id', $member->id)->first());
    }

    public function test_sharing_an_already_shared_tunnel_with_another_member_is_not_blocked_by_quota(): void
    {
        $tenant = $this->createTenantOnPlan('starter'); // limite: 5, on la sature avec 5 AUTRES tunnels
        $admin = $this->createAdminForTenant($tenant);
        $member = $this->createCommercialForTenant($tenant);
        $sharedFunnel = $this->makeFunnel($tenant);
        $member->activateFunnel($sharedFunnel);

        for ($i = 0; $i < 4; $i++) {
            $member->activateFunnel($this->makeFunnel($tenant));
        }

        $secondMember = $this->createCommercialForTenant($tenant);

        $this->actingAs($admin);

        Livewire::test(ViewFunnel::class, ['record' => $sharedFunnel->getKey()])
            ->callAction('assign_to_commercials', data: [
                'assignment_type' => 'individual',
                'commercial_ids' => [$secondMember->id],
                'can_customize' => true,
                'can_edit' => false,
            ]);

        $this->assertNotNull($sharedFunnel->users()->where('user_id', $secondMember->id)->first());
    }

    private function makeFunnel(Tenant $tenant): Funnel
    {
        return Funnel::create([
            'tenant_id' => $tenant->id,
            'name' => 'Funnel ' . uniqid(),
            'slug' => 'funnel-' . uniqid(),
            'status' => 'draft',
            'is_template' => false,
        ]);
    }
}
