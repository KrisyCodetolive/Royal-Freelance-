<?php

namespace Tests\Feature;

use App\Filament\Pages\MySubscription;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

class MySubscriptionPageTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_owner_can_access_the_page_and_change_the_tenant_plan(): void
    {
        $tenant = $this->createTenantOnPlan('free');
        $owner = $this->createOwnerForTenant($tenant);
        $starter = Plan::where('slug', 'starter')->firstOrFail();

        $this->actingAs($owner);

        Livewire::test(MySubscription::class)
            ->callAction('changePlan', data: [
                'plan_id' => $starter->id,
                'cycle' => 'yearly',
            ]);

        $subscription = $tenant->fresh()->activeSubscription();

        $this->assertSame('starter', $subscription->plan->slug);
        $this->assertSame('yearly', $subscription->cycle);
        $this->assertNotNull($subscription->ends_at);
        $this->assertTrue($subscription->ends_at->isFuture());
    }

    public function test_plain_admin_cannot_access_the_page(): void
    {
        $tenant = $this->createTenantOnPlan();
        $admin = $this->createAdminForTenant($tenant);

        $this->actingAs($admin);

        $this->assertFalse(MySubscription::canAccess());
    }

    public function test_commercial_cannot_access_the_page(): void
    {
        $tenant = $this->createTenantOnPlan();
        $commercial = $this->createCommercialForTenant($tenant);

        $this->actingAs($commercial);

        $this->assertFalse(MySubscription::canAccess());
    }
}
