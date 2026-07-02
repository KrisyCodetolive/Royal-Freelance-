<?php

namespace Tests\Unit\Models;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_null_quota_field_is_unlimited(): void
    {
        $plan = Plan::create(['slug' => 'unlimited-plan', 'name' => 'Illimité', 'max_tunnels' => null]);

        $this->assertTrue($plan->isUnlimited('max_tunnels'));
    }

    public function test_numeric_quota_field_is_not_unlimited(): void
    {
        $plan = Plan::create(['slug' => 'capped-plan', 'name' => 'Plafonné', 'max_tunnels' => 10]);

        $this->assertFalse($plan->isUnlimited('max_tunnels'));
    }

    public function test_available_roles_cast_to_array(): void
    {
        $plan = Plan::create([
            'slug' => 'starter-like',
            'name' => 'Starter',
            'available_roles' => ['owner', 'admin', 'editor'],
        ]);

        $this->assertSame(['owner', 'admin', 'editor'], $plan->fresh()->available_roles);
    }
}
