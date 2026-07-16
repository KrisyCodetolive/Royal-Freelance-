<?php

namespace Tests\Feature;

use App\Filament\Resources\CommercialGroups\CommercialGroupResource;
use App\Filament\Resources\Funnels\Pages\ListFunnels;
use App\Filament\Resources\Funnels\RelationManagers\PagesRelationManager;
use App\Filament\Resources\TenantInvitations\TenantInvitationResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Funnel;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

/**
 * Retours QA manuels sur Modules 4/5/6 : Editor/Viewer avaient accès à la
 * gestion d'équipe (Users/CommercialGroups/TenantInvitations, aucune de ces
 * ressources n'était gatée) et pouvaient contourner les restrictions de
 * rôle sur les tunnels/pages via des actions non protégées (bulk delete,
 * RelationManager Pages sans policy).
 */
class TeamManagementPermissionsTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_editor_cannot_view_team_management_resources(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $editor = $this->createEditorForTenant($tenant);

        $this->actingAs($editor);

        $this->assertFalse(UserResource::canViewAny());
        $this->assertFalse(CommercialGroupResource::canViewAny());
        $this->assertFalse(TenantInvitationResource::canViewAny());
    }

    public function test_viewer_cannot_view_team_management_resources(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $viewer = $this->createViewerForTenant($tenant);

        $this->actingAs($viewer);

        $this->assertFalse(UserResource::canViewAny());
        $this->assertFalse(CommercialGroupResource::canViewAny());
        $this->assertFalse(TenantInvitationResource::canViewAny());
    }

    public function test_admin_can_view_team_management_resources(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);

        $this->actingAs($admin);

        $this->assertTrue(UserResource::canViewAny());
        $this->assertTrue(CommercialGroupResource::canViewAny());
        $this->assertTrue(TenantInvitationResource::canViewAny());
    }

    /**
     * Retour QA : "je vois tout le monde sans les équipiers à moi". Cause :
     * User importe BelongsToTenant mais ne l'applique jamais dans son `use`
     * de traits — aucun scope automatique, UserResource n'avait pas non plus
     * de getEloquentQuery() propre. Un Owner voyait tous les utilisateurs de
     * tous les tenants de la plateforme.
     */
    public function test_owner_only_sees_users_from_their_own_tenant(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $owner = $this->createOwnerForTenant($tenant);
        $this->createEditorForTenant($tenant);

        $otherTenant = $this->createTenantOnPlan('starter');
        $this->createAdminForTenant($otherTenant);
        $this->createViewerForTenant($otherTenant);

        $this->actingAs($owner);

        $visibleIds = UserResource::getEloquentQuery()->pluck('tenant_id')->unique();

        $this->assertEquals(2, UserResource::getEloquentQuery()->count());
        $this->assertEquals([$tenant->id], $visibleIds->all());
    }

    /**
     * Retour QA : "dans le tableau de bord, section équipe, je vois des
     * équipiers qui ne devraient pas être là". Cause distincte de
     * UserResource : TeamPerformanceWidget (widget "Performance Équipe" du
     * Dashboard) interrogeait User::role('commercial') sans aucun filtre de
     * tenant. Décision utilisateur : le widget doit montrer tous les rôles
     * du tenant (pas seulement les commerciaux), donc le filtre role() a
     * été retiré en plus du scope tenant ajouté.
     */
    public function test_team_performance_widget_shows_every_role_from_the_owners_tenant_only(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $owner = $this->createOwnerForTenant($tenant);
        $ownCommercial = $this->createCommercialForTenant($tenant);

        $otherTenant = $this->createTenantOnPlan('starter');
        $this->createCommercialForTenant($otherTenant);

        $this->actingAs($owner);

        $widget = new \App\Filament\Widgets\TeamPerformanceWidget();
        $table = $widget->table(new \Filament\Tables\Table($widget));

        $visibleIds = (clone $table->getQuery())->pluck('id')->sort()->values();

        $this->assertEquals(
            collect([$owner->id, $ownCommercial->id])->sort()->values()->all(),
            $visibleIds->all()
        );
    }

    public function test_super_admin_sees_users_across_all_tenants(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $this->createOwnerForTenant($tenant);

        $otherTenant = $this->createTenantOnPlan('starter');
        $this->createAdminForTenant($otherTenant);

        $superAdmin = $this->createSuperAdmin();

        $this->actingAs($superAdmin);

        $visibleTenantIds = UserResource::getEloquentQuery()->pluck('tenant_id')->unique();

        $this->assertTrue($visibleTenantIds->contains($tenant->id));
        $this->assertTrue($visibleTenantIds->contains($otherTenant->id));
    }

    public function test_editor_cannot_bulk_delete_funnels(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $editor = $this->createEditorForTenant($tenant);

        $funnel = $this->makeFunnel($tenant, ['assigned_to' => $editor->id]);

        $this->actingAs($editor);

        Livewire::test(ListFunnels::class)
            ->assertTableBulkActionHidden('delete', $funnel);

        $this->assertNotSoftDeleted($funnel);
    }

    public function test_admin_can_bulk_delete_funnels(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $admin = $this->createAdminForTenant($tenant);

        $funnel = $this->makeFunnel($tenant);

        $this->actingAs($admin);

        Livewire::test(ListFunnels::class)
            ->assertTableBulkActionVisible('delete', $funnel);
    }

    public function test_viewer_cannot_create_a_page_via_relation_manager_even_on_a_tunnel_shared_in_edit(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $viewer = $this->createViewerForTenant($tenant);

        $funnel = $this->makeFunnel($tenant);
        $funnel->users()->attach($viewer->id, ['is_active' => true, 'can_edit' => true]);

        $this->actingAs($viewer);

        Livewire::test(PagesRelationManager::class, [
            'ownerRecord' => $funnel,
            'pageClass' => \App\Filament\Resources\Funnels\Pages\ViewFunnel::class,
        ])->assertTableActionHidden('create');
    }

    public function test_editor_cannot_delete_a_page_via_relation_manager(): void
    {
        $tenant = $this->createTenantOnPlan('starter');
        $editor = $this->createEditorForTenant($tenant);

        $funnel = $this->makeFunnel($tenant, ['assigned_to' => $editor->id]);
        $page = $this->makePage($funnel);

        $this->actingAs($editor);

        Livewire::test(PagesRelationManager::class, [
            'ownerRecord' => $funnel,
            'pageClass' => \App\Filament\Resources\Funnels\Pages\ViewFunnel::class,
        ])->assertTableActionHidden('delete', $page);

        $this->assertNotSoftDeleted($page);
    }

    private function makeFunnel(\App\Models\Tenant $tenant, array $overrides = []): Funnel
    {
        return Funnel::create(array_merge([
            'tenant_id' => $tenant->id,
            'name' => 'Funnel ' . uniqid(),
            'slug' => 'funnel-' . uniqid(),
            'status' => 'draft',
            'is_template' => false,
        ], $overrides));
    }

    private function makePage(Funnel $funnel, array $overrides = []): Page
    {
        return Page::create(array_merge([
            'funnel_id' => $funnel->id,
            'type' => \App\Enums\PageType::CAPTURE,
            'title' => 'Page ' . uniqid(),
            'slug' => 'page-' . uniqid(),
            'sort_order' => 1,
            'is_active' => true,
            'is_required' => true,
        ], $overrides));
    }
}
