<?php

namespace App\Services;

use App\Enums\BlockType;
use App\Models\Block;
use App\Models\Funnel;
use App\Models\Page;
use Illuminate\Support\Collection;

class PageBuilderService
{
    /**
     * Get all available block types with their configurations
     */
    public function getAvailableBlockTypes(): array
    {
        return [
            [
                'type' => BlockType::TITLE->value,
                'label' => 'Titre',
                'icon' => 'heroicon-o-h1',
                'category' => 'content',
                'description' => 'Titre principal ou secondaire',
                'defaults' => [
                    'content' => ['text' => 'Nouveau titre', 'level' => 'h1', 'alignment' => 'center'],
                    'styles' => ['fontSize' => '2.5rem', 'fontWeight' => 'bold'],
                ],
            ],
            [
                'type' => BlockType::TEXT->value,
                'label' => 'Texte',
                'icon' => 'heroicon-o-document-text',
                'category' => 'content',
                'description' => 'Paragraphe ou texte formaté',
                'defaults' => [
                    'content' => ['html' => '<p>Votre texte ici...</p>'],
                    'styles' => ['fontSize' => '1rem'],
                ],
            ],
            [
                'type' => BlockType::IMAGE->value,
                'label' => 'Image',
                'icon' => 'heroicon-o-photo',
                'category' => 'media',
                'description' => 'Image avec légende optionnelle',
                'defaults' => [
                    'content' => ['url' => '', 'alt' => '', 'caption' => ''],
                    'styles' => ['maxWidth' => '100%', 'borderRadius' => '8px'],
                ],
            ],
            [
                'type' => BlockType::VIDEO->value,
                'label' => 'Vidéo',
                'icon' => 'heroicon-o-video-camera',
                'category' => 'media',
                'description' => 'YouTube, Vimeo ou fichier vidéo',
                'defaults' => [
                    'content' => ['url' => '', 'autoplay' => false, 'controls' => true],
                    'styles' => ['aspectRatio' => '16/9', 'maxWidth' => '800px'],
                ],
            ],
            [
                'type' => BlockType::BUTTON->value,
                'label' => 'Bouton CTA',
                'icon' => 'heroicon-o-cursor-arrow-rays',
                'category' => 'action',
                'description' => 'Bouton d\'appel à l\'action',
                'defaults' => [
                    'content' => ['text' => 'Cliquez ici', 'url' => '#', 'target' => '_self'],
                    'styles' => ['backgroundColor' => '#3B82F6', 'color' => '#FFFFFF', 'padding' => '12px 32px', 'borderRadius' => '8px'],
                ],
            ],
            [
                'type' => BlockType::FORM->value,
                'label' => 'Formulaire',
                'icon' => 'heroicon-o-clipboard-document-list',
                'category' => 'action',
                'description' => 'Formulaire de capture',
                'defaults' => [
                    'content' => [
                        'title' => 'Inscrivez-vous',
                        'fields' => [
                            ['name' => 'email', 'type' => 'email', 'label' => 'Email', 'required' => true, 'placeholder' => 'Votre email'],
                        ],
                        'button_text' => 'ENVOYER',
                        'privacy_text' => 'Vos données sont sécurisées',
                        'image_url' => '',
                        'layout' => 'center', // center, left (form left, image right), right (form right, image left)
                    ],
                    'styles' => ['backgroundColor' => '#1F2937', 'padding' => '24px', 'borderRadius' => '12px'],
                ],
                'field_types' => [
                    ['value' => 'text', 'label' => 'Texte'],
                    ['value' => 'email', 'label' => 'Email'],
                    ['value' => 'tel', 'label' => 'Téléphone'],
                    ['value' => 'textarea', 'label' => 'Zone de texte'],
                    ['value' => 'number', 'label' => 'Nombre'],
                    ['value' => 'date', 'label' => 'Date'],
                    ['value' => 'select', 'label' => 'Liste déroulante'],
                    ['value' => 'radio', 'label' => 'Choix unique'],
                    ['value' => 'checkbox', 'label' => 'Cases à cocher'],
                    ['value' => 'country', 'label' => 'Pays'],
                    ['value' => 'city', 'label' => 'Ville'],
                    ['value' => 'gender', 'label' => 'Sexe'],
                    ['value' => 'profession', 'label' => 'Profession/Fonction'],
                    ['value' => 'company', 'label' => 'Entreprise'],
                    ['value' => 'url', 'label' => 'Site web'],
                    ['value' => 'whatsapp', 'label' => 'WhatsApp'],
                ],
            ],
            [
                'type' => BlockType::HERO->value,
                'label' => 'Section Hero (Bannière)',
                'icon' => 'heroicon-o-star',
                'category' => 'layout',
                'description' => 'Bannière principale avec titre, texte, CTA et image',
                'defaults' => [
                    'content' => [
                        'title' => 'Votre Titre Accrocheur',
                        'subtitle' => 'Une sous-accroche persuasive pour captiver vos visiteurs.',
                        'button_text' => 'Commencer maintenant',
                        'button_url' => '#',
                        'image_url' => '',
                        'layout' => 'left', // left (text left, image right), right, center (text center, image bg or below)
                    ],
                    'styles' => ['backgroundColor' => 'transparent', 'padding' => '60px 20px', 'minHeight' => '400px'],
                ],
            ],
            [
                'type' => BlockType::TEXT_IMAGE->value,
                'label' => 'Texte + Image (2 Col)',
                'icon' => 'heroicon-o-columns-2',
                'category' => 'layout',
                'description' => 'Illustration à côté du texte',
                'defaults' => [
                    'content' => [
                        'title' => 'Titre de section',
                        'text' => '<p>Lorem ipsum dolor sit amet...</p>',
                        'image_url' => '',
                        'image_position' => 'right', // left or right
                    ],
                    'styles' => ['padding' => '40px 20px'],
                ],
            ],
            [
                'type' => BlockType::FEATURES->value,
                'label' => 'Liste Avantages',
                'icon' => 'heroicon-o-list-bullet',
                'category' => 'layout',
                'description' => 'Grille de 3 points clés ou avantages',
                'defaults' => [
                    'content' => [
                        'features' => [
                            ['title' => 'Avantage 1', 'text' => 'Description courte.', 'icon' => 'heroicon-o-check-circle'],
                            ['title' => 'Avantage 2', 'text' => 'Description courte.', 'icon' => 'heroicon-o-check-circle'],
                            ['title' => 'Avantage 3', 'text' => 'Description courte.', 'icon' => 'heroicon-o-check-circle'],
                        ],
                    ],
                    'styles' => ['padding' => '40px 20px', 'gap' => '20px'],
                ],
            ],
            [
                'type' => BlockType::SPACER->value,
                'label' => 'Espacement',
                'icon' => 'heroicon-o-arrows-up-down',
                'category' => 'layout',
                'description' => 'Espace vertical',
                'defaults' => [
                    'content' => ['height' => '40px'],
                    'styles' => [],
                ],
            ],
            [
                'type' => BlockType::DIVIDER->value,
                'label' => 'Séparateur',
                'icon' => 'heroicon-o-minus',
                'category' => 'layout',
                'description' => 'Ligne de séparation',
                'defaults' => [
                    'content' => [],
                    'styles' => ['borderColor' => '#374151', 'borderWidth' => '1px', 'margin' => '20px 0'],
                ],
            ],
            [
                'type' => BlockType::TESTIMONIAL->value,
                'label' => 'Témoignage',
                'icon' => 'heroicon-o-chat-bubble-bottom-center-text',
                'category' => 'advanced',
                'description' => 'Avis client avec photo',
                'defaults' => [
                    'content' => ['text' => 'Super produit !', 'author' => 'Jean D.', 'role' => 'Client', 'avatar' => ''],
                    'styles' => ['backgroundColor' => '#1F2937', 'padding' => '20px', 'borderRadius' => '12px'],
                ],
            ],
            [
                'type' => BlockType::COUNTDOWN->value,
                'label' => 'Compte à rebours',
                'icon' => 'heroicon-o-clock',
                'category' => 'advanced',
                'description' => 'Timer d\'urgence',
                'defaults' => [
                    'content' => ['title' => 'Offre expire dans :', 'hours' => 24],
                    'styles' => ['backgroundColor' => '#EF4444', 'color' => '#FFFFFF', 'padding' => '16px', 'borderRadius' => '8px'],
                ],
            ],
            // =========================================================================
            // NOUVEAUX TYPES DE BLOCS
            // =========================================================================
            [
                'type' => BlockType::SECTION->value,
                'label' => 'Section',
                'icon' => 'heroicon-o-square-2-stack',
                'category' => 'layout',
                'description' => 'Conteneur pour grouper des blocs',
                'defaults' => [
                    'content' => ['background_type' => 'color'],
                    'styles' => ['padding' => '40px 20px', 'backgroundColor' => 'transparent'],
                ],
            ],
            [
                'type' => BlockType::COLUMNS->value,
                'label' => 'Colonnes',
                'icon' => 'heroicon-o-view-columns',
                'category' => 'layout',
                'description' => 'Mise en page multi-colonnes',
                'defaults' => [
                    'content' => ['layout' => '1/2,1/2', 'gap' => '20px'],
                    'styles' => ['padding' => '20px 0'],
                ],
            ],
            [
                'type' => BlockType::PRICING->value,
                'label' => 'Tableau de prix',
                'icon' => 'heroicon-o-currency-euro',
                'category' => 'advanced',
                'description' => 'Plans tarifaires avec fonctionnalités',
                'defaults' => [
                    'content' => [
                        'title' => 'Nos Offres',
                        'plans' => [
                            [
                                'name' => 'Basic',
                                'price' => '47€',
                                'period' => '/mois',
                                'features' => ['Feature 1', 'Feature 2', 'Feature 3'],
                                'button_text' => 'Choisir',
                                'button_url' => '#',
                                'is_popular' => false,
                            ],
                            [
                                'name' => 'Pro',
                                'price' => '97€',
                                'period' => '/mois',
                                'features' => ['Tout Basic +', 'Feature 4', 'Feature 5', 'Feature 6'],
                                'button_text' => 'Choisir',
                                'button_url' => '#',
                                'is_popular' => true,
                            ],
                        ],
                    ],
                    'styles' => ['padding' => '40px 20px'],
                ],
            ],
            [
                'type' => BlockType::FAQ->value,
                'label' => 'FAQ Accordéon',
                'icon' => 'heroicon-o-question-mark-circle',
                'category' => 'advanced',
                'description' => 'Questions fréquentes en accordéon',
                'defaults' => [
                    'content' => [
                        'title' => '❓ Questions Fréquentes',
                        'questions' => [
                            ['question' => 'Comment ça marche ?', 'answer' => 'Réponse détaillée ici...'],
                            ['question' => 'Y a-t-il une garantie ?', 'answer' => 'Oui, 14 jours satisfait ou remboursé.'],
                        ],
                    ],
                    'styles' => ['padding' => '20px'],
                ],
            ],
            [
                'type' => BlockType::PROGRESS->value,
                'label' => 'Barre de progression',
                'icon' => 'heroicon-o-chart-bar',
                'category' => 'advanced',
                'description' => 'Indicateur de progression',
                'defaults' => [
                    'content' => ['percent' => 50, 'label' => 'Étape 1 sur 2', 'color' => '#10B981'],
                    'styles' => ['padding' => '10px 0'],
                ],
            ],
            [
                'type' => BlockType::ICON_BOX->value,
                'label' => 'Icône + Texte',
                'icon' => 'heroicon-o-light-bulb',
                'category' => 'content',
                'description' => 'Icône avec titre et description',
                'defaults' => [
                    'content' => [
                        'icon' => '🎯',
                        'title' => 'Titre',
                        'text' => 'Description du point clé',
                        'alignment' => 'center',
                    ],
                    'styles' => ['padding' => '20px', 'backgroundColor' => '#1F2937', 'borderRadius' => '12px'],
                ],
            ],
            [
                'type' => BlockType::TRUST_BADGES->value,
                'label' => 'Badges de confiance',
                'icon' => 'heroicon-o-shield-check',
                'category' => 'advanced',
                'description' => 'Garanties et certifications',
                'defaults' => [
                    'content' => [
                        'badges' => [
                            ['icon' => '✅', 'text' => '14 jours satisfait ou remboursé'],
                            ['icon' => '🔒', 'text' => 'Paiement 100% sécurisé'],
                            ['icon' => '📧', 'text' => 'Support réactif'],
                        ],
                    ],
                    'styles' => ['padding' => '20px'],
                ],
            ],
            [
                'type' => BlockType::SOCIAL_ICONS->value,
                'label' => 'Réseaux sociaux',
                'icon' => 'heroicon-o-share',
                'category' => 'content',
                'description' => 'Liens vers vos réseaux sociaux',
                'defaults' => [
                    'content' => [
                        'platforms' => [
                            ['platform' => 'facebook', 'url' => 'https://facebook.com'],
                            ['platform' => 'instagram', 'url' => 'https://instagram.com'],
                            ['platform' => 'whatsapp', 'url' => 'https://wa.me/'],
                        ],
                        'size' => 'md',
                        'alignment' => 'center',
                    ],
                    'styles' => ['padding' => '20px'],
                ],
            ],
            [
                'type' => BlockType::ORDER_BUMP->value,
                'label' => 'Order Bump (Upsell)',
                'icon' => 'heroicon-o-gift',
                'category' => 'action',
                'description' => 'Case à cocher pour upsell',
                'defaults' => [
                    'content' => [
                        'title' => '🎁 Offre Exclusive !',
                        'description' => 'Ajoutez cette option à votre commande',
                        'price' => '+27€',
                        'original_price' => '47€',
                        'checkbox_label' => 'Oui, j\'ajoute cette option !',
                    ],
                    'styles' => ['backgroundColor' => '#FEF3C7', 'padding' => '20px', 'borderRadius' => '12px', 'borderColor' => '#F59E0B'],
                ],
            ],
            [
                'type' => BlockType::STICKY_BAR->value,
                'label' => 'Barre fixe',
                'icon' => 'heroicon-o-bars-3',
                'category' => 'advanced',
                'description' => 'Barre fixe en haut ou bas de page',
                'defaults' => [
                    'content' => [
                        'text' => '🔥 Offre limitée ! Plus que quelques places...',
                        'button_text' => 'EN PROFITER',
                        'button_url' => '#',
                        'position' => 'top',
                        'show_countdown' => false,
                    ],
                    'styles' => ['backgroundColor' => '#EF4444', 'color' => '#FFFFFF', 'padding' => '12px'],
                ],
            ],
            [
                'type' => BlockType::POPUP->value,
                'label' => 'Popup Modal',
                'icon' => 'heroicon-o-window',
                'category' => 'advanced',
                'description' => 'Popup avec différents déclencheurs',
                'defaults' => [
                    'content' => [
                        'title' => '🎁 Attendez !',
                        'body' => '<p>Ne partez pas sans cette offre exclusive...</p>',
                        'button_text' => 'CONTINUER',
                        'button_url' => '#',
                        'trigger' => 'exit_intent', // exit_intent, delay, scroll
                        'delay' => 5000,
                        'scroll_percent' => 50,
                        'show_once' => true,
                    ],
                    'styles' => ['backgroundColor' => '#1F2937', 'color' => '#FFFFFF', 'borderRadius' => '16px'],
                ],
            ],
            [
                'type' => BlockType::AUDIO->value,
                'label' => 'Lecteur Audio',
                'icon' => 'heroicon-o-speaker-wave',
                'category' => 'media',
                'description' => 'Fichier audio ou podcast',
                'defaults' => [
                    'content' => [
                        'url' => '',
                        'title' => 'Écouter l\'épisode',
                        'autoplay' => false,
                        'show_download' => false,
                    ],
                    'styles' => ['padding' => '20px'],
                ],
            ],
            [
                'type' => BlockType::EMBED->value,
                'label' => 'Code Embed',
                'icon' => 'heroicon-o-code-bracket',
                'category' => 'advanced',
                'description' => 'Intégrer du code HTML externe',
                'defaults' => [
                    'content' => [
                        'code' => '',
                        'aspect_ratio' => '16:9',
                    ],
                    'styles' => ['padding' => '20px'],
                ],
            ],
        ];
    }

    /**
     * Get block types grouped by category
     */
    public function getBlockTypesByCategory(): array
    {
        $blocks = collect($this->getAvailableBlockTypes());

        return [
            'content' => $blocks->where('category', 'content')->values()->toArray(),
            'media' => $blocks->where('category', 'media')->values()->toArray(),
            'action' => $blocks->where('category', 'action')->values()->toArray(),
            'layout' => $blocks->where('category', 'layout')->values()->toArray(),
            'advanced' => $blocks->where('category', 'advanced')->values()->toArray(),
        ];
    }

    /**
     * Create a new block for a page
     */
    public function createBlock(Page $page, string $type, ?int $sortOrder = null): Block
    {
        $blockTypes = collect($this->getAvailableBlockTypes());
        $blockConfig = $blockTypes->firstWhere('type', $type);

        if (!$blockConfig) {
            throw new \InvalidArgumentException("Unknown block type: {$type}");
        }

        $maxOrder = $page->blocks()->max('sort_order') ?? 0;

        return Block::create([
            'page_id' => $page->id,
            'type' => $type,
            'content' => $blockConfig['defaults']['content'] ?? [],
            'styles' => $blockConfig['defaults']['styles'] ?? [],
            'settings' => [],
            'sort_order' => $sortOrder ?? ($maxOrder + 1),
            'is_active' => true,
            'show_on_mobile' => true,
            'show_on_desktop' => true,
        ]);
    }

    /**
     * Update block content
     */
    public function updateBlock(Block $block, array $content, array $styles = [], array $settings = []): Block
    {
        $block->update([
            'content' => array_merge($block->content ?? [], $content),
            'styles' => array_merge($block->styles ?? [], $styles),
            'settings' => array_merge($block->settings ?? [], $settings),
        ]);

        return $block->fresh();
    }

    /**
     * Reorder blocks
     */
    public function reorderBlocks(Page $page, array $blockIds): void
    {
        foreach ($blockIds as $index => $blockId) {
            Block::where('id', $blockId)
                ->where('page_id', $page->id)
                ->update(['sort_order' => $index + 1]);
        }
    }

    /**
     * Duplicate a block
     */
    public function duplicateBlock(Block $block): Block
    {
        // Increment the sort_order of all subsequent blocks with the same parent in the same column
        $block->page->blocks()
            ->where('parent_id', $block->parent_id)
            ->where('column_index', $block->column_index)
            ->where('sort_order', '>', $block->sort_order)
            ->increment('sort_order');

        $newBlock = $block->replicate(['uuid']);
        $newBlock->sort_order = $block->sort_order + 1;
        $newBlock->save();

        return $newBlock;
    }

    /**
     * Get page branding with fallbacks
     */
    public function getPageBranding(Page $page): array
    {
        $funnel = $page->funnel;

        return array_merge(
            [
                'primary_color' => '#3B82F6',
                'secondary_color' => '#1E40AF',
                'background_color' => '#0F172A',
                'text_color' => '#FFFFFF',
                'font_family' => 'Inter, sans-serif',
            ],
            $funnel?->tenant?->branding ?? [],
            $funnel?->branding ?? [],
            $page->branding ?? []
        );
    }

    /**
     * Available fonts for the builder
     */
    public function getAvailableFonts(): array
    {
        return [
            'Inter, sans-serif' => 'Inter',
            'Outfit, sans-serif' => 'Outfit',
            'Poppins, sans-serif' => 'Poppins',
            'Roboto, sans-serif' => 'Roboto',
            'Montserrat, sans-serif' => 'Montserrat',
            'Playfair Display, serif' => 'Playfair Display',
            'Georgia, serif' => 'Georgia',
        ];
    }

    /**
     * Generate preview HTML for a page
     */
    public function generatePreviewHtml(Page $page): string
    {
        $branding = $this->getPageBranding($page);
        $blocks = $page->blocks()->active()->ordered()->get();

        $html = '';
        foreach ($blocks as $block) {
            $html .= $this->renderBlockHtml($block, $branding);
        }

        return $html;
    }

    /**
     * Render a single block as HTML
     */
    public function renderBlockHtml(Block $block, array $branding = []): string
    {
        $type = $block->type->value ?? $block->type;
        $content = $block->content ?? [];
        $styles = $block->styles ?? [];

        return view("components.builder.blocks.{$type}", [
            'block' => $block,
            'content' => $content,
            'styles' => $styles,
            'branding' => $branding,
        ])->render();
    }
}
