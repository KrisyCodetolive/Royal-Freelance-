<?php

namespace Tests\Feature;

use App\Filament\Resources\Funnels\FunnelResource;
use App\Filament\Resources\Leads\LeadResource;
use App\Models\Funnel;
use App\Models\Lead;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

class WorkspaceRolesTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_editor_and_viewer_can_access_the_panel(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $editor = $this->createEditorForTenant($tenant);
        $viewer = $this->createViewerForTenant($tenant);

        $this->assertTrue($editor->canAccessPanel(app(\Filament\Panel::class)));
        $this->assertTrue($viewer->canAccessPanel(app(\Filament\Panel::class)));
    }

    public function test_editor_only_sees_funnels_assigned_or_shared_with_them(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $editor = $this->createEditorForTenant($tenant);

        $assigned = $this->makeFunnel($tenant, ['assigned_to' => $editor->id]);
        $shared = $this->makeFunnel($tenant);
        $editor->activateFunnel($shared);
        $private = $this->makeFunnel($tenant);

        $this->actingAs($editor);
        $visibleIds = FunnelResource::getEloquentQuery()->pluck('id')->all();

        $this->assertContains($assigned->id, $visibleIds);
        $this->assertContains($shared->id, $visibleIds);
        $this->assertNotContains($private->id, $visibleIds);
    }

    public function test_admin_sees_all_tenant_funnels_regardless_of_assignment(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);
        $unassigned = $this->makeFunnel($tenant);

        $this->actingAs($admin);
        $visibleIds = FunnelResource::getEloquentQuery()->pluck('id')->all();

        $this->assertContains($unassigned->id, $visibleIds);
    }

    public function test_editor_can_edit_a_funnel_shared_with_edit_permission_but_not_a_read_only_share(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $editor = $this->createEditorForTenant($tenant);

        $editable = $this->makeFunnel($tenant);
        $editable->users()->attach($editor->id, ['can_edit' => true]);

        $readOnly = $this->makeFunnel($tenant);
        $readOnly->users()->attach($editor->id, ['can_edit' => false]);

        $this->actingAs($editor);

        $this->assertTrue(FunnelResource::canEdit($editable));
        $this->assertFalse(FunnelResource::canEdit($readOnly));
    }

    public function test_viewer_can_never_edit_or_create_or_delete_funnels(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $viewer = $this->createViewerForTenant($tenant);
        $funnel = $this->makeFunnel($tenant);
        $funnel->users()->attach($viewer->id, ['can_edit' => true]); // même en édition, un Viewer ne doit jamais éditer

        $this->actingAs($viewer);

        $this->assertFalse(FunnelResource::canCreate());
        $this->assertFalse(FunnelResource::canEdit($funnel));
        $this->assertFalse(FunnelResource::canDelete($funnel));
    }

    public function test_only_admin_can_create_or_delete_funnels(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);
        $editor = $this->createEditorForTenant($tenant);
        $funnel = $this->makeFunnel($tenant, ['assigned_to' => $editor->id]);

        $this->actingAs($admin);
        $this->assertTrue(FunnelResource::canCreate());
        $this->assertTrue(FunnelResource::canDelete($funnel));

        $this->actingAs($editor);
        $this->assertFalse(FunnelResource::canCreate());
        $this->assertFalse(FunnelResource::canDelete($funnel));
    }

    public function test_editor_only_sees_leads_from_their_accessible_funnels(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $editor = $this->createEditorForTenant($tenant);

        $assignedFunnel = $this->makeFunnel($tenant, ['assigned_to' => $editor->id]);
        $otherFunnel = $this->makeFunnel($tenant);

        $ownLead = Lead::create(['tenant_id' => $tenant->id, 'funnel_id' => $assignedFunnel->id]);
        $otherLead = Lead::create(['tenant_id' => $tenant->id, 'funnel_id' => $otherFunnel->id]);

        $this->actingAs($editor);
        $visibleIds = LeadResource::getEloquentQuery()->pluck('id')->all();

        $this->assertContains($ownLead->id, $visibleIds);
        $this->assertNotContains($otherLead->id, $visibleIds);
    }

    public function test_viewer_cannot_edit_leads(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $viewer = $this->createViewerForTenant($tenant);
        $funnel = $this->makeFunnel($tenant);
        $funnel->users()->attach($viewer->id, ['can_edit' => false]);
        $lead = Lead::create(['tenant_id' => $tenant->id, 'funnel_id' => $funnel->id]);

        $this->actingAs($viewer);

        $this->assertFalse(LeadResource::canEdit($lead));
    }

    private function makeFunnel(Tenant $tenant, array $overrides = []): Funnel
    {
        return Funnel::create(array_merge([
            'tenant_id' => $tenant->id,
            'name' => 'Funnel ' . uniqid(),
            'slug' => 'funnel-' . uniqid(),
            'status' => 'draft',
            'is_template' => false,
        ], $overrides));
    }
}
