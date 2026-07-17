<?php

namespace Tests\Unit\Filament;

use App\Filament\Resources\Tenants\Pages\ListTenants;
use App\Filament\Resources\Tenants\Pages\ViewTenant;
use App\Filament\Resources\Tenants\TenantResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

/**
 * Retour QA : le super_admin (porteur de projet) doit avoir toutes les infos
 * (email, propriétaire, plan...) de tous les tenants de la plateforme, sauf
 * le mot de passe. TenantResource est réservé au super_admin, en lecture
 * seule (les tenants naissent de l'inscription self-service).
 */
class TenantResourceAccessTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_tenant_owner_cannot_access_tenant_resource(): void
    {
        $tenant = $this->createTenantOnPlan();
        $owner = $this->createOwnerForTenant($tenant);

        $this->actingAs($owner);

        $this->assertFalse(TenantResource::canAccess());
    }

    public function test_super_admin_can_access_tenant_resource(): void
    {
        $this->actingAs($this->createSuperAdmin());

        $this->assertTrue(TenantResource::canAccess());
    }

    public function test_super_admin_sees_tenant_name_email_and_owner_in_the_list(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $tenant->update(['name' => 'Boutique Awa', 'email' => 'contact@boutique-awa.example']);
        $owner = $this->createOwnerForTenant($tenant);

        $this->actingAs($this->createSuperAdmin());

        Livewire::test(ListTenants::class)
            ->loadTable()
            ->assertOk()
            ->assertSee('Boutique Awa')
            ->assertSee('contact@boutique-awa.example')
            ->assertSee($owner->name);
    }

    public function test_tenant_resource_is_read_only(): void
    {
        $tenant = $this->createTenantOnPlan();

        $this->assertFalse(TenantResource::canCreate());
        $this->assertFalse(TenantResource::canEdit($tenant));
        $this->assertFalse(TenantResource::canDelete($tenant));
    }

    public function test_super_admin_can_view_a_tenant_with_its_owner_and_plan(): void
    {
        $tenant = $this->createTenantOnPlan('prestige');
        $owner = $this->createOwnerForTenant($tenant);

        $this->actingAs($this->createSuperAdmin());

        Livewire::test(ViewTenant::class, ['record' => $tenant->getRouteKey()])
            ->assertOk()
            ->assertSee($tenant->name)
            ->assertSee($owner->name)
            ->assertSee($owner->email)
            ->assertSee('Prestige');
    }
}
