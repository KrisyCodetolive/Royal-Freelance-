<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Plans Phase 3 SaaS (voir royal-leadpro-phase3.html).
     * Quotas "+X" du cahier des charges lus comme cumulatifs par palier
     * (décision validée le 2026-07-02, voir ROADMAP_SAAS_PHASE3.md).
     */
    public function run(): void
    {
        $plans = [
            [
                'slug' => 'free',
                'name' => 'Gratuit',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'max_tunnels' => 2,
                'max_mailing_lists' => 1,
                'max_leads' => 1000,
                'max_shared_tunnels' => 0,
                'available_roles' => [],
                'dashboard_level' => 'standard',
                'support_level' => 'communautaire',
            ],
            [
                'slug' => 'starter',
                'name' => 'Starter',
                'price_monthly' => 5000,
                'price_yearly' => 50000,
                'max_tunnels' => 10,
                'max_mailing_lists' => 10,
                'max_leads' => 2000,
                'max_shared_tunnels' => 5,
                'available_roles' => ['owner', 'admin', 'editor'],
                'dashboard_level' => 'avance',
                'support_level' => 'prioritaire',
            ],
            [
                'slug' => 'prestige',
                'name' => 'Prestige',
                'price_monthly' => 20000,
                'price_yearly' => 200000,
                'max_tunnels' => null,
                'max_mailing_lists' => 20,
                'max_leads' => 3000,
                'max_shared_tunnels' => null,
                'available_roles' => ['owner', 'admin', 'editor', 'viewer'],
                'dashboard_level' => 'super',
                'support_level' => 'dedie_24_7',
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
