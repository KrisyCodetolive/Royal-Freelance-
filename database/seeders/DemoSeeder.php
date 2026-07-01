<?php

namespace Database\Seeders;

use App\Enums\FunnelStatus;
use App\Enums\LeadStatus;
use App\Models\Funnel;
use App\Models\Lead;
use App\Models\Page;
use App\Models\Block;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        // ── 3 commerciaux ────────────────────────────────────────────────
        $commercials = $this->createCommercials($tenant);

        // ── 3 tunnels avec pages et blocs ────────────────────────────────
        $funnels = $this->createFunnels($tenant);

        // ── Assigner les tunnels aux commerciaux ──────────────────────────
        $this->assignFunnels($funnels, $commercials);

        // ── Générer du trafic réaliste (leads sur 90 jours) ──────────────
        $this->generateTraffic($funnels, $commercials, $tenant);
    }

    private function createCommercials(Tenant $tenant): array
    {
        $data = [
            ['name' => 'Sophie Martin',  'email' => 'sophie@genius.ci',  'subdomain' => 'sophie',  'whatsapp' => '+2250701000001'],
            ['name' => 'Kouamé Jean',    'email' => 'kouame@genius.ci',  'subdomain' => 'kouame',  'whatsapp' => '+2250701000002'],
            ['name' => 'Awa Traoré',     'email' => 'awa@genius.ci',     'subdomain' => 'awa',     'whatsapp' => '+2250701000003'],
        ];

        $commercials = [];
        foreach ($data as $d) {
            $user = User::firstOrCreate(
                ['email' => $d['email']],
                [
                    'name'              => $d['name'],
                    'password'          => bcrypt('password'),
                    'tenant_id'         => $tenant->id,
                    'subdomain'         => $d['subdomain'],
                    'whatsapp_number'   => $d['whatsapp'],
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('commercial');
            $commercials[] = $user;
        }

        return $commercials;
    }

    private function createFunnels(Tenant $tenant): array
    {
        $funnelDefs = [
            [
                'name'        => 'Formation Crypto Pro',
                'slug'        => 'formation-crypto-pro',
                'subdomain'   => 'crypto-pro',
                'description' => 'Tunnel de vente pour la formation crypto',
                'pages'       => [
                    ['slug' => 'capture',   'title' => 'Rejoignez la formation',   'type' => 'landing'],
                    ['slug' => 'merci',     'title' => 'Merci pour votre inscription', 'type' => 'thank_you'],
                ],
            ],
            [
                'name'        => 'Coaching Business 90j',
                'slug'        => 'coaching-business-90j',
                'subdomain'   => 'coaching-90j',
                'description' => 'Programme d\'accompagnement business',
                'pages'       => [
                    ['slug' => 'inscription',  'title' => 'Démarrez votre transformation', 'type' => 'landing'],
                    ['slug' => 'confirmation', 'title' => 'Votre place est réservée',       'type' => 'thank_you'],
                ],
            ],
            [
                'name'        => 'Masterclass Immobilier',
                'slug'        => 'masterclass-immo',
                'subdomain'   => 'immo-live',
                'description' => 'Masterclass gratuite investissement immobilier',
                'pages'       => [
                    ['slug' => 'accueil',     'title' => 'Masterclass Immobilier GRATUITE', 'type' => 'landing'],
                    ['slug' => 'inscription', 'title' => 'Confirmez votre présence',        'type' => 'thank_you'],
                ],
            ],
        ];

        $funnels = [];
        foreach ($funnelDefs as $def) {
            $funnel = Funnel::create([
                'uuid'        => Str::uuid(),
                'tenant_id'   => $tenant->id,
                'name'        => $def['name'],
                'slug'        => $def['slug'],
                'subdomain'   => $def['subdomain'],
                'description' => $def['description'],
                'status'      => FunnelStatus::ACTIVE,
                'is_template' => false,
                'branding'    => ['primary_color' => '#f59e0b', 'secondary_color' => '#6366f1'],
            ]);

            foreach ($def['pages'] as $i => $pageDef) {
                $page = Page::create([
                    'uuid'       => Str::uuid(),
                    'funnel_id'  => $funnel->id,
                    'type'       => $pageDef['type'],
                    'title'      => $pageDef['title'],
                    'slug'       => $pageDef['slug'],
                    'sort_order' => $i + 1,
                    'is_active'  => true,
                ]);

                // Bloc titre
                Block::create([
                    'uuid'       => Str::uuid(),
                    'page_id'    => $page->id,
                    'type'       => 'title',
                    'sort_order' => 1,
                    'is_active'  => true,
                    'content'    => ['text' => $pageDef['title'], 'level' => 'h1'],
                    'settings'   => [],
                    'styles'     => [],
                ]);

                // Bloc formulaire sur la page landing
                if ($pageDef['type'] === 'landing') {
                    Block::create([
                        'uuid'       => Str::uuid(),
                        'page_id'    => $page->id,
                        'type'       => 'form',
                        'sort_order' => 2,
                        'is_active'  => true,
                        'content'    => [
                            'fields' => [
                                ['type' => 'text',  'label' => 'Prénom',  'required' => true],
                                ['type' => 'email', 'label' => 'Email',   'required' => true],
                                ['type' => 'tel',   'label' => 'Téléphone', 'required' => false],
                            ],
                            'submit_label' => 'Je m\'inscris gratuitement',
                        ],
                        'settings' => [],
                        'styles'   => [],
                    ]);
                }
            }

            $funnels[] = $funnel;
        }

        return $funnels;
    }

    private function assignFunnels(array $funnels, array $commercials): void
    {
        // Sophie → Crypto + Coaching
        // Kouamé → Coaching + Immo
        // Awa    → Crypto + Immo
        $assignments = [
            0 => [0, 1], // Sophie → Crypto, Coaching
            1 => [1, 2], // Kouamé → Coaching, Immo
            2 => [0, 2], // Awa    → Crypto, Immo
        ];

        foreach ($assignments as $cIdx => $fIndexes) {
            $commercial = $commercials[$cIdx];
            foreach ($fIndexes as $fIdx) {
                $funnel = $funnels[$fIdx];
                \DB::table('funnel_user')->insertOrIgnore([
                    'funnel_id'   => $funnel->id,
                    'user_id'     => $commercial->id,
                    'custom_slug' => $commercial->subdomain . '-' . $funnel->slug,
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }

    private function generateTraffic(array $funnels, array $commercials, Tenant $tenant): void
    {
        $statuses  = ['cold', 'cold', 'cold', 'warm', 'warm', 'hot', 'ultra_hot', 'client'];
        $devices   = ['mobile', 'mobile', 'desktop', 'tablet'];
        $countries = ['CI', 'SN', 'CM', 'BF', 'ML', 'FR', 'BE'];
        $firstNames = ['Koffi', 'Amara', 'Fatou', 'Jean', 'Marie', 'Ibrahim', 'Aicha', 'Paul', 'Grace', 'Seydou'];
        $lastNames  = ['Koné', 'Traoré', 'Diallo', 'Martin', 'Dupont', 'Coulibaly', 'Bamba', 'Ouédraogo'];

        // Répartition : chaque commercial amène des leads sur ses tunnels
        $assignments = [
            [$commercials[0], $funnels[0], 55],  // Sophie → Crypto (fort)
            [$commercials[0], $funnels[1], 30],  // Sophie → Coaching
            [$commercials[1], $funnels[1], 45],  // Kouamé → Coaching (fort)
            [$commercials[1], $funnels[2], 20],  // Kouamé → Immo
            [$commercials[2], $funnels[0], 25],  // Awa → Crypto
            [$commercials[2], $funnels[2], 40],  // Awa → Immo (fort)
        ];

        foreach ($assignments as [$commercial, $funnel, $total]) {
            $page = $funnel->pages()->first();

            for ($i = 0; $i < $total; $i++) {
                // Date aléatoire sur les 90 derniers jours, plus dense sur les 30 derniers
                $daysAgo = $this->weightedDaysAgo();
                $createdAt = now()->subDays($daysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                $status = $statuses[array_rand($statuses)];
                $convertedAt = in_array($status, ['client', 'member'])
                    ? $createdAt->copy()->addHours(rand(1, 72))
                    : null;

                Lead::create([
                    'uuid'           => Str::uuid(),
                    'tenant_id'      => $tenant->id,
                    'funnel_id'      => $funnel->id,
                    'brought_by'     => $commercial->id,
                    'first_name'     => $firstNames[array_rand($firstNames)],
                    'last_name'      => $lastNames[array_rand($lastNames)],
                    'email'          => strtolower(Str::random(6)) . '@test.com',
                    'phone'          => '+225070' . rand(1000000, 9999999),
                    'status'         => $status,
                    'score'          => $this->scoreFromStatus($status),
                    'device_type'    => $devices[array_rand($devices)],
                    'country'        => $countries[array_rand($countries)],
                    'source'         => 'whatsapp',
                    'medium'         => 'social',
                    'last_activity_at' => $createdAt->copy()->addHours(rand(0, 48)),
                    'converted_at'   => $convertedAt,
                    'created_at'     => $createdAt,
                    'updated_at'     => $createdAt,
                ]);

                // Incrémenter les compteurs du funnel
                Funnel::withoutEvents(fn () => $funnel->increment('views_count'));
                if ($convertedAt) {
                    Funnel::withoutEvents(fn () => $funnel->increment('conversions_count'));
                }
            }
        }

        // Recalculer les taux de conversion
        foreach ($funnels as $funnel) {
            $funnel->conversion_rate = $funnel->views_count > 0
                ? round($funnel->conversions_count / $funnel->views_count * 100, 1)
                : 0;
            $funnel->leads_count = Lead::withoutGlobalScope('tenant')->where('funnel_id', $funnel->id)->count();
            $funnel->save();
        }
    }

    private function weightedDaysAgo(): int
    {
        // 60% des leads sur les 30 derniers jours, 40% entre 31 et 90 jours
        if (rand(1, 100) <= 60) {
            return rand(0, 29);
        }
        return rand(30, 89);
    }

    private function scoreFromStatus(string $status): int
    {
        return match ($status) {
            'cold'      => rand(0, 10),
            'warm'      => rand(11, 30),
            'hot'       => rand(31, 60),
            'ultra_hot' => rand(61, 90),
            'client'    => rand(70, 100),
            'member'    => rand(60, 100),
            default     => 0,
        };
    }
}
