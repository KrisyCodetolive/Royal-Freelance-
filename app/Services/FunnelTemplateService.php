<?php

namespace App\Services;

use App\Enums\FunnelStatus;
use App\Models\Funnel;
use Illuminate\Support\Str;

class FunnelTemplateService
{
    public static function getMeta(): array
    {
        return [
            'lead_capture' => [
                'label'       => '📧 Capture de Leads',
                'description' => 'Page optin + page de remerciement (2 pages)',
                'category'    => 'lead_capture',
            ],
            'webinar' => [
                'label'       => '🎥 Webinaire',
                'description' => 'Inscription + confirmation + page replay (3 pages)',
                'category'    => 'webinar',
            ],
            'sales' => [
                'label'       => '💰 Page de Vente',
                'description' => 'Page de vente + paiement + merci (3 pages)',
                'category'    => 'sales',
            ],
            'product_launch' => [
                'label'       => '🚀 Lancement de Produit',
                'description' => 'Coming soon + vente + confirmation (3 pages)',
                'category'    => 'product_launch',
            ],
            'free_training' => [
                'label'       => '🎓 Formation Gratuite',
                'description' => 'Landing + inscription + accès formation (3 pages)',
                'category'    => 'free_training',
            ],
            'quiz' => [
                'label'       => '❓ Quiz / Sondage',
                'description' => 'Page quiz + résultats + offre (3 pages)',
                'category'    => 'quiz',
            ],
            'mlm_community' => [
                'label'       => '🤝 MLM & Communauté',
                'description' => 'Présentation + formulaire + confirmation (3 pages)',
                'category'    => 'mlm_community',
            ],
            'coming_soon' => [
                'label'       => '⏳ Coming Soon',
                'description' => 'Page d\'attente + capture email (2 pages)',
                'category'    => 'coming_soon',
            ],
        ];
    }

    public static function createFromTemplate(string $templateKey, string $name, string $subdomain): ?Funnel
    {
        $meta = self::getMeta();

        if (!isset($meta[$templateKey])) {
            return null;
        }

        $funnel = Funnel::create([
            'name'             => $name,
            'slug'             => Str::slug($name),
            'subdomain'        => $subdomain,
            'status'           => FunnelStatus::DRAFT,
            'is_template'      => false,
            'template_category'=> $meta[$templateKey]['category'],
        ]);

        foreach (self::getPages($templateKey) as $index => $page) {
            $funnel->pages()->create([
                'uuid'       => (string) Str::uuid(),
                'title'      => $page['title'],
                'slug'       => Str::slug($page['title']),
                'type'       => $page['type'],
                'sort_order' => $index + 1,
                'is_active'  => true,
                'is_required'=> $page['is_required'] ?? false,
                'settings'   => [],
            ]);
        }

        return $funnel;
    }

    private static function getPages(string $templateKey): array
    {
        return match ($templateKey) {
            'lead_capture' => [
                ['title' => 'Page de Capture',    'type' => 'capture',   'is_required' => true],
                ['title' => 'Merci',               'type' => 'thank_you', 'is_required' => false],
            ],
            'webinar' => [
                ['title' => 'Inscription Webinaire', 'type' => 'capture',      'is_required' => true],
                ['title' => 'Confirmation',           'type' => 'thank_you',   'is_required' => false],
                ['title' => 'Replay',                 'type' => 'content',     'is_required' => false],
            ],
            'sales' => [
                ['title' => 'Page de Vente',   'type' => 'sales',     'is_required' => true],
                ['title' => 'Paiement',        'type' => 'payment',   'is_required' => true],
                ['title' => 'Merci',           'type' => 'thank_you', 'is_required' => false],
            ],
            'product_launch' => [
                ['title' => 'Coming Soon',         'type' => 'landing',   'is_required' => false],
                ['title' => 'Page de Lancement',   'type' => 'sales',     'is_required' => true],
                ['title' => 'Confirmation',        'type' => 'thank_you', 'is_required' => false],
            ],
            'free_training' => [
                ['title' => 'Landing Page',        'type' => 'landing',   'is_required' => false],
                ['title' => 'Inscription',         'type' => 'capture',   'is_required' => true],
                ['title' => 'Accès Formation',     'type' => 'content',   'is_required' => false],
            ],
            'quiz' => [
                ['title' => 'Quiz',         'type' => 'quiz',      'is_required' => true],
                ['title' => 'Résultats',    'type' => 'content',   'is_required' => false],
                ['title' => 'Offre',        'type' => 'sales',     'is_required' => false],
            ],
            'mlm_community' => [
                ['title' => 'Présentation',  'type' => 'presentation', 'is_required' => false],
                ['title' => 'Rejoindre',     'type' => 'capture',      'is_required' => true],
                ['title' => 'Confirmation',  'type' => 'thank_you',    'is_required' => false],
            ],
            'coming_soon' => [
                ['title' => 'Coming Soon',     'type' => 'landing',   'is_required' => false],
                ['title' => 'Pré-inscription', 'type' => 'capture',   'is_required' => true],
            ],
            default => [],
        };
    }
}
