<?php

namespace Tests\Feature;

use App\Filament\Widgets\QuotaUsageWidget;
use App\Models\Funnel;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

class QuotaUsageWidgetTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_owner_sees_usage_and_upgrade_cta_once_a_quota_is_near_its_limit(): void
    {
        $tenant = $this->createTenantOnPlan('free'); // limite: 2 tunnels
        $owner = $this->createOwnerForTenant($tenant);
        $this->makeFunnel($tenant);
        $this->makeFunnel($tenant); // 2/2 -> at limit

        $this->actingAs($owner);

        $widget = Livewire::test(QuotaUsageWidget::class);

        $this->assertTrue($widget->instance()->hasAnyQuotaNearLimit());
        $this->assertTrue($widget->instance()->canManageBilling());

        $usage = $widget->instance()->getUsage();
        $this->assertSame(2, $usage['tunnels']['used']);
        $this->assertSame(2, $usage['tunnels']['limit']);
        $this->assertTrue($usage['tunnels']['is_near_limit']);
    }

    public function test_editor_sees_the_same_tenant_usage_but_cannot_manage_billing(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $editor = $this->createEditorForTenant($tenant);
        $this->makeFunnel($tenant);

        $this->actingAs($editor);

        $widget = Livewire::test(QuotaUsageWidget::class);

        $this->assertFalse($widget->instance()->canManageBilling());
        $this->assertSame(1, $widget->instance()->getUsage()['tunnels']['used']);
    }

    public function test_quota_not_near_limit_when_usage_is_low(): void
    {
        $tenant = $this->createTenantOnPlan('prestige'); // illimité
        $owner = $this->createOwnerForTenant($tenant);
        $this->makeFunnel($tenant);

        $this->actingAs($owner);

        $widget = Livewire::test(QuotaUsageWidget::class);

        $this->assertFalse($widget->instance()->hasAnyQuotaNearLimit());
        $this->assertNull($widget->instance()->getUsage()['tunnels']['limit']);
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
