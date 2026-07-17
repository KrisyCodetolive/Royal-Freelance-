<?php

namespace Tests\Feature;

use App\Enums\BlockType;
use App\Enums\FunnelStatus;
use App\Enums\PageType;
use App\Models\Block;
use App\Models\Funnel;
use App\Models\Lead;
use App\Models\Page;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

/**
 * Module 3 — gap comblé : le quota "leads" du plan bloque désormais la
 * création de nouveaux leads (visite anonyme ET soumission de formulaire),
 * pas seulement affiché. Cf. ROADMAP_SAAS_PHASE3.md — décision utilisateur
 * du 2026-07-17 d'aller jusqu'au blocage du tracking de visite malgré le
 * risque noté sur ce chemin (le pipeline public de capture).
 */
class LeadsQuotaEnforcementTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_new_anonymous_visitor_is_not_tracked_once_the_leads_quota_is_reached(): void
    {
        $funnel = $this->makeFunnelWithFormPage();
        $this->exhaustLeadsQuota($funnel->tenant);

        $response = $this->get($this->funnelUrl($funnel));

        $response->assertOk();
        $this->assertSame(0, Lead::where('tenant_id', $funnel->tenant_id)->count());
    }

    public function test_returning_visitor_with_an_existing_cookie_is_still_tracked_even_if_the_quota_is_reached(): void
    {
        $funnel = $this->makeFunnelWithFormPage();

        $lead = Lead::create([
            'uuid' => (string) Str::uuid(),
            'tenant_id' => $funnel->tenant_id,
            'funnel_id' => $funnel->id,
            'status' => 'cold',
        ]);

        $this->exhaustLeadsQuota($funnel->tenant, existing: 1);

        $response = $this->withCookie('rlm_visitor', $lead->uuid)->get($this->funnelUrl($funnel));

        $response->assertOk();
        $this->assertSame(1, Lead::where('tenant_id', $funnel->tenant_id)->count());
    }

    public function test_form_submission_is_rejected_with_a_clear_message_once_the_leads_quota_is_reached(): void
    {
        $funnel = $this->makeFunnelWithFormPage();
        $this->exhaustLeadsQuota($funnel->tenant);

        $response = $this->post($this->funnelUrl($funnel) . 'racine/submit', [
            'field_1' => 'visiteur@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(0, Lead::where('tenant_id', $funnel->tenant_id)->count());
    }

    public function test_form_submission_still_updates_an_existing_lead_even_if_the_quota_is_reached(): void
    {
        $funnel = $this->makeFunnelWithFormPage();

        $lead = Lead::create([
            'uuid' => (string) Str::uuid(),
            'tenant_id' => $funnel->tenant_id,
            'funnel_id' => $funnel->id,
            'status' => 'cold',
        ]);

        $this->exhaustLeadsQuota($funnel->tenant, existing: 1);

        $response = $this->withCookie('rlm_visitor', $lead->uuid)
            ->post($this->funnelUrl($funnel) . 'racine/submit', [
                'field_1' => 'visiteur@example.com',
            ]);

        $response->assertRedirect();
        $response->assertSessionMissing('error');
        $this->assertSame(1, Lead::where('tenant_id', $funnel->tenant_id)->count());
        $this->assertSame('visiteur@example.com', $lead->fresh()->email);
    }

    public function test_leads_are_still_created_normally_when_under_the_quota(): void
    {
        $funnel = $this->makeFunnelWithFormPage();

        $response = $this->get($this->funnelUrl($funnel));

        $response->assertOk();
        $this->assertSame(1, Lead::where('tenant_id', $funnel->tenant_id)->count());
    }

    private function funnelUrl(Funnel $funnel): string
    {
        return 'http://' . $funnel->subdomain . '.' . config('app.subdomain_base') . '/';
    }

    private function makeFunnelWithFormPage(): Funnel
    {
        $tenant = $this->createTenantOnPlan();

        $funnel = Funnel::create([
            'tenant_id' => $tenant->id,
            'name' => 'Funnel ' . uniqid(),
            'slug' => 'funnel-' . uniqid(),
            'subdomain' => 'sub-' . uniqid(),
            'status' => FunnelStatus::ACTIVE,
            'published_at' => now(),
            'is_template' => false,
        ]);

        $page = Page::create([
            'funnel_id' => $funnel->id,
            'type' => PageType::CAPTURE,
            'title' => 'Page racine',
            'slug' => 'racine',
            'sort_order' => 1,
            'is_active' => true,
            'is_required' => true,
        ]);

        Block::create([
            'page_id' => $page->id,
            'type' => BlockType::FORM,
            'content' => [
                'fields' => [
                    ['label' => 'Email', 'type' => 'email', 'required' => true],
                ],
                'success_message' => 'Merci !',
            ],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        return $funnel->fresh();
    }

    private function exhaustLeadsQuota($tenant, int $existing = 0): void
    {
        // max_leads = nombre déjà existant : la limite est donc déjà atteinte
        // (remaining = max(0, limite - utilisé) = 0) sans avoir à créer des
        // centaines de leads pour épuiser le quota réel du plan.
        Plan::where('id', $tenant->currentPlan()->id)->update(['max_leads' => $existing]);
    }
}
