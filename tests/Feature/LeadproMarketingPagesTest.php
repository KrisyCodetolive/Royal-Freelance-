<?php

namespace Tests\Feature;

use App\Models\Plan;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Module 8 — gap comblé : les sections "Fonctionnalités" et "Tarifs" de la
 * landing (jusque-là uniquement des ancres sur `/`) ont désormais leurs
 * propres pages dédiées, en plus de la home inchangée. Cf.
 * ROADMAP_SAAS_PHASE3.md.
 */
class LeadproMarketingPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new PlanSeeder())->run();
    }

    public function test_the_home_page_still_renders_with_its_anchor_sections(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Fonctionnalités');
        $response->assertSee('Tarifs');
        $response->assertSee('id="fonctionnalites"', false);
        $response->assertSee('id="tarifs"', false);
    }

    public function test_the_dedicated_features_page_renders(): void
    {
        $response = $this->get('/fonctionnalites');

        $response->assertOk();
        $response->assertSee('<title>Fonctionnalités - Royal LeadPro', false);
        $response->assertSee('Tunnels de vente');
    }

    public function test_the_dedicated_pricing_page_renders_the_real_active_plans(): void
    {
        $response = $this->get('/tarifs');

        $response->assertOk();
        $response->assertSee('<title>Tarifs - Royal LeadPro', false);

        foreach (Plan::where('is_active', true)->get() as $plan) {
            $response->assertSee($plan->name);
        }
    }

    public function test_nav_links_on_the_dedicated_pages_point_to_the_other_dedicated_page_not_a_broken_anchor(): void
    {
        $features = $this->get('/fonctionnalites');
        $features->assertSee(route('pricing'), false);
        $features->assertDontSee('href="#tarifs"', false);

        $pricing = $this->get('/tarifs');
        $pricing->assertSee(route('features'), false);
        $pricing->assertDontSee('href="#fonctionnalites"', false);
    }
}
