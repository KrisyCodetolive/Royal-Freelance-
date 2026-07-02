<?php

namespace Tests\Unit\Models;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubscription(array $attributes = []): Subscription
    {
        $tenant = Tenant::create(['name' => 'Tenant Test']);
        $plan = Plan::create(['slug' => 'plan-' . uniqid(), 'name' => 'Plan']);

        return Subscription::create(array_merge([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'cycle' => 'monthly',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => null,
        ], $attributes));
    }

    public function test_active_status_without_end_date_is_active(): void
    {
        $this->assertTrue($this->makeSubscription(['ends_at' => null])->isActive());
    }

    public function test_active_status_with_future_end_date_is_active(): void
    {
        $this->assertTrue($this->makeSubscription(['ends_at' => now()->addDays(5)])->isActive());
    }

    public function test_active_status_with_past_end_date_is_not_active(): void
    {
        $this->assertFalse($this->makeSubscription(['ends_at' => now()->subDay()])->isActive());
    }

    public function test_suspended_status_is_never_active(): void
    {
        $this->assertFalse($this->makeSubscription(['status' => 'suspended', 'ends_at' => null])->isActive());
    }

    public function test_tenant_current_plan_resolves_via_active_subscription(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant With Plan']);
        $plan = Plan::create(['slug' => 'starter-test', 'name' => 'Starter']);

        $tenant->subscriptions()->create([
            'plan_id' => $plan->id,
            'cycle' => 'monthly',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => null,
        ]);

        $this->assertSame($plan->id, $tenant->currentPlan()?->id);
    }

    public function test_tenant_without_subscription_has_no_current_plan(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant No Plan']);

        $this->assertNull($tenant->currentPlan());
    }
}
