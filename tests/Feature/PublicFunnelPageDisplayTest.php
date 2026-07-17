<?php

namespace Tests\Feature;

use App\Enums\FunnelStatus;
use App\Enums\PageType;
use App\Models\Funnel;
use App\Models\Page;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Retour QA : "impossible d'afficher les pages d'un tunnel". Cause trouvée :
 * FunnelController::showPage()/submit() déclaraient leurs paramètres
 * ($pageSlug, $subdomain) alors que Laravel lie les paramètres de route
 * positionnellement dans l'ordre domaine puis URI — les deux valeurs étaient
 * donc inversées en interne, et toute page autre que la racine du tunnel
 * (qui n'a qu'un seul paramètre, sans ambiguïté) renvoyait 404.
 */
class PublicFunnelPageDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_page_of_a_published_funnel_is_reachable_by_subdomain(): void
    {
        $funnel = $this->makePublishedFunnelWithPages();

        $response = $this->get('http://' . $funnel->subdomain . '.' . config('app.subdomain_base') . '/');

        $response->assertOk();
    }

    public function test_a_specific_page_of_a_published_funnel_is_reachable_by_subdomain(): void
    {
        $funnel = $this->makePublishedFunnelWithPages();
        $secondPage = $funnel->pages()->where('sort_order', 2)->firstOrFail();

        $response = $this->get('http://' . $funnel->subdomain . '.' . config('app.subdomain_base') . '/' . $secondPage->slug);

        $response->assertOk();
        $response->assertSee($secondPage->title);
    }

    public function test_a_draft_funnel_page_is_not_publicly_reachable(): void
    {
        $funnel = $this->makePublishedFunnelWithPages(['status' => FunnelStatus::DRAFT, 'published_at' => null]);
        $secondPage = $funnel->pages()->where('sort_order', 2)->firstOrFail();

        $response = $this->get('http://' . $funnel->subdomain . '.' . config('app.subdomain_base') . '/' . $secondPage->slug);

        $response->assertNotFound();
    }

    /**
     * Retour QA (2ème occurrence, jour suivant) : "j'arrive pas à afficher les
     * pages d'un tunnel". Cause : showPageWithSlug()/submitWithSlug() n'avaient
     * pas été mis à jour après le fix ci-dessus sur showPage()/submit() — ils
     * leur transmettaient toujours les paramètres dans l'ancien ordre, donc le
     * slug du tunnel et celui de la page restaient inversés pour toutes les
     * routes /f/{funnelSlug}/{pageSlug} (avec ou sans sous-domaine).
     */
    public function test_root_page_of_a_published_funnel_is_reachable_via_f_prefix_without_subdomain(): void
    {
        $funnel = $this->makePublishedFunnelWithPages();

        $response = $this->get('http://' . config('app.subdomain_base') . '/f/' . $funnel->slug);

        $response->assertOk();
    }

    public function test_a_specific_page_of_a_published_funnel_is_reachable_via_f_prefix_without_subdomain(): void
    {
        $funnel = $this->makePublishedFunnelWithPages();
        $secondPage = $funnel->pages()->where('sort_order', 2)->firstOrFail();

        $response = $this->get('http://' . config('app.subdomain_base') . '/f/' . $funnel->slug . '/' . $secondPage->slug);

        $response->assertOk();
        $response->assertSee($secondPage->title);
    }

    public function test_a_specific_page_of_a_published_funnel_is_reachable_via_f_prefix_with_subdomain(): void
    {
        $funnel = $this->makePublishedFunnelWithPages();
        $secondPage = $funnel->pages()->where('sort_order', 2)->firstOrFail();

        $response = $this->get('http://commercial-sub.' . config('app.subdomain_base') . '/f/' . $funnel->slug . '/' . $secondPage->slug);

        $response->assertOk();
        $response->assertSee($secondPage->title);
    }

    private function makePublishedFunnelWithPages(array $overrides = []): Funnel
    {
        $tenant = Tenant::create(['name' => 'Tenant ' . uniqid()]);

        $funnel = Funnel::create(array_merge([
            'tenant_id' => $tenant->id,
            'name' => 'Funnel ' . uniqid(),
            'slug' => 'funnel-' . uniqid(),
            'subdomain' => 'sub-' . uniqid(),
            'status' => FunnelStatus::ACTIVE,
            'published_at' => now(),
            'is_template' => false,
        ], $overrides));

        Page::create([
            'funnel_id' => $funnel->id,
            'type' => PageType::CAPTURE,
            'title' => 'Page racine',
            'slug' => 'racine-' . uniqid(),
            'sort_order' => 1,
            'is_active' => true,
            'is_required' => true,
        ]);

        Page::create([
            'funnel_id' => $funnel->id,
            'type' => PageType::CAPTURE,
            'title' => 'Deuxième page',
            'slug' => 'deuxieme-' . uniqid(),
            'sort_order' => 2,
            'is_active' => true,
            'is_required' => true,
        ]);

        return $funnel;
    }
}
