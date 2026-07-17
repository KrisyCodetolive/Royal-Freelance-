<?php

namespace Tests\Feature;

use App\Filament\Resources\Subscriptions\Pages\CreateSubscription;
use App\Filament\Resources\TenantInvitations\Pages\CreateTenantInvitation;
use App\Filament\Resources\TenantInvitations\Pages\ListTenantInvitations;
use App\Models\Plan;
use App\Models\TenantInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Symfony\Component\Mailer\Exception\TransportException;
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

    /**
     * Retour QA : "difficulté à copier le lien pour les invitations" — le
     * bouton copier natif de Filament (->copyable()) repose sur
     * navigator.clipboard, qui échoue silencieusement hors contexte sécurisé
     * (HTTP sur un sous-domaine autre que localhost). Remplacé par un bouton
     * avec fallback document.execCommand('copy'). Ce test vérifie juste que
     * la liste rend sans exception et affiche le bouton de copie.
     */
    public function test_invitations_list_renders_with_a_copy_link_button(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);

        TenantInvitation::create([
            'tenant_id' => $tenant->id,
            'email' => 'commercial@example.com',
            'role' => 'commercial',
            'invited_by' => $admin->id,
            'token' => 'test-token',
            'expires_at' => now()->addDays(7),
        ]);

        $this->actingAs($admin);

        Livewire::test(ListTenantInvitations::class)
            ->loadTable()
            ->assertOk()
            ->assertSeeHtml('x-on:click="copy()"');
    }

    /**
     * Retour QA (crash réel) : création d'une invitation → 500
     * TransportException "Connection refused" (Mailpit non démarré en local).
     * L'invitation était déjà en base au moment du crash — Mail::send() ne
     * doit plus faire échouer toute la requête.
     */
    public function test_invitation_is_still_created_even_if_the_confirmation_email_fails_to_send(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);

        Mail::shouldReceive('to')->andReturnSelf();
        Mail::shouldReceive('send')->andThrow(
            new TransportException('Connection could not be established with host "127.0.0.1:1025"')
        );

        $this->actingAs($admin);

        Livewire::test(CreateTenantInvitation::class)
            ->fillForm(['email' => 'sans-smtp@example.com'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertNotNull(TenantInvitation::where('email', 'sans-smtp@example.com')->first());
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
