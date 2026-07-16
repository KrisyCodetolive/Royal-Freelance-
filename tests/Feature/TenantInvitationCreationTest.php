<?php

namespace Tests\Feature;

use App\Filament\Resources\Subscriptions\Pages\CreateSubscription;
use App\Filament\Resources\TenantInvitations\Pages\CreateTenantInvitation;
use App\Models\Plan;
use App\Models\TenantInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

/**
 * Couvre la création via les formulaires Filament (pas juste le modèle) :
 * un mauvais namespace d'import (`Filament\Forms\Components\Section`/`Grid`
 * au lieu de `Filament\Schemas\Components\...` sous Filament 4) faisait
 * planter ces pages sans que les tests basés sur TenantInvitation::create()
 * ne le détectent.
 */
class TenantInvitationCreationTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_admin_can_create_a_tenant_invitation_via_the_filament_form(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);

        $this->actingAs($admin);

        Livewire::test(CreateTenantInvitation::class)
            ->fillForm(['email' => 'nouveau-commercial@example.com'])
            ->call('create')
            ->assertHasNoFormErrors();

        $invitation = TenantInvitation::where('email', 'nouveau-commercial@example.com')->first();

        $this->assertNotNull($invitation);
        $this->assertSame($tenant->id, $invitation->tenant_id);
        $this->assertNotEmpty($invitation->token);
    }

    public function test_owner_can_choose_the_admin_role_when_inviting_a_member(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $owner = $this->createOwnerForTenant($tenant);

        $this->actingAs($owner);

        Livewire::test(CreateTenantInvitation::class)
            ->fillForm([
                'email' => 'futur-admin@example.com',
                'role' => 'admin',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $invitation = TenantInvitation::where('email', 'futur-admin@example.com')->first();

        $this->assertSame('admin', $invitation->role);
    }

    public function test_owner_cannot_invite_anyone_on_the_free_plan(): void
    {
        $tenant = $this->createTenantOnPlan('free');
        $owner = $this->createOwnerForTenant($tenant);

        $this->actingAs($owner);

        $this->assertFalse(\App\Filament\Resources\TenantInvitations\TenantInvitationResource::canCreate());
    }

    public function test_owner_cannot_invite_a_viewer_on_the_starter_plan(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $owner = $this->createOwnerForTenant($tenant);

        $this->actingAs($owner);

        Livewire::test(CreateTenantInvitation::class)
            ->fillForm([
                'email' => 'futur-viewer@example.com',
                'role' => 'viewer',
            ])
            ->call('create')
            ->assertNotified();

        $this->assertNull(TenantInvitation::where('email', 'futur-viewer@example.com')->first());
    }

    public function test_super_admin_can_create_a_subscription_via_the_filament_form(): void
    {
        $tenant = $this->createTenantOnPlan();
        $superAdmin = $this->createSuperAdmin();
        $plan = Plan::where('slug', 'starter')->firstOrFail();

        $this->actingAs($superAdmin);

        Livewire::test(CreateSubscription::class)
            ->fillForm([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'cycle' => 'monthly',
                'status' => 'active',
                'starts_at' => now()->toDateTimeString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertTrue($tenant->subscriptions()->where('plan_id', $plan->id)->exists());
    }
}
