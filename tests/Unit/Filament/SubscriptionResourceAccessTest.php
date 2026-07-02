<?php

namespace Tests\Unit\Filament;

use App\Filament\Resources\Subscriptions\SubscriptionResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

class SubscriptionResourceAccessTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_tenant_admin_cannot_access_subscription_resource(): void
    {
        $tenant = $this->createTenantOnPlan();
        $admin = $this->createAdminForTenant($tenant);

        $this->actingAs($admin);

        $this->assertFalse(SubscriptionResource::canAccess());
    }

    public function test_super_admin_can_access_subscription_resource(): void
    {
        $this->actingAs($this->createSuperAdmin());

        $this->assertTrue(SubscriptionResource::canAccess());
    }

    public function test_guest_cannot_access_subscription_resource(): void
    {
        $this->assertFalse(SubscriptionResource::canAccess());
    }
}
