<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

/**
 * Module 7 — gap comblé : l'espace commercial (`/commercial/*`) est
 * désormais couvert par la suspension d'un tenant, au même titre que le
 * panel Filament et les tunnels publics. Cf. ROADMAP_SAAS_PHASE3.md.
 */
class CommercialSuspensionTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_commercial_of_a_suspended_tenant_cannot_access_the_dashboard(): void
    {
        $tenant = $this->createTenantOnPlan();
        $commercial = $this->createCommercialForTenant($tenant);

        $this->actingAs($commercial);
        $this->get('/commercial')->assertOk();

        $tenant->suspend();

        // Ré-authentifier avec un modèle frais : le guard de test garde en
        // cache l'instance User (et sa relation tenant déjà chargée) de la
        // requête précédente (même remarque que pour le panel Filament).
        $this->actingAs($commercial->fresh());

        $response = $this->get('/commercial');
        $response->assertForbidden();
        $response->assertSee('suspendu');
        $response->assertSee('support@royalleadpro.com');
    }

    public function test_commercial_of_a_reactivated_tenant_can_access_the_dashboard_again(): void
    {
        $tenant = $this->createTenantOnPlan();
        $commercial = $this->createCommercialForTenant($tenant);

        $tenant->suspend();
        $tenant->reactivate();

        $this->actingAs($commercial->fresh());
        $this->get('/commercial')->assertOk();
    }

    public function test_login_with_valid_credentials_for_a_suspended_tenant_shows_an_explicit_message(): void
    {
        $tenant = $this->createTenantOnPlan();
        $commercial = $this->createCommercialForTenant($tenant);
        $tenant->suspend();

        $response = $this->post('/login', [
            'email' => $commercial->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'suspendu',
            session('errors')->get('email')[0],
        );
        $this->assertGuest();
    }

    public function test_login_with_wrong_password_still_shows_the_generic_message(): void
    {
        $tenant = $this->createTenantOnPlan();
        $commercial = $this->createCommercialForTenant($tenant);
        $tenant->suspend();

        $response = $this->post('/login', [
            'email' => $commercial->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'incorrects',
            session('errors')->get('email')[0],
        );
        $this->assertGuest();
    }
}
