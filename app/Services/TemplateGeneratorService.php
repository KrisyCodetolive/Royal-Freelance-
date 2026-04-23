<?php

namespace App\Services;

use App\Enums\BlockType;
use App\Enums\PageType;
use App\Models\Block;
use App\Models\Funnel;
use App\Models\Page;
use App\Services\Template\AbstractFunnelTemplate;
use App\Services\Template\CoachingProgramTemplate;
use App\Services\Template\DigitalBookTemplate;
use App\Services\Template\FreeTrainingTemplate;
use App\Services\Template\LeadCaptureTemplate;
use App\Services\Template\MLMCommunityTemplate;
use App\Services\Template\OnlineTrainingTemplate;
use App\Services\Template\ProductLaunchTemplate;
use App\Services\Template\QuizTemplate;
use App\Services\Template\WebinarTemplate;
use Illuminate\Support\Str;

/**
 * Service de génération de templates de tunnels
 * 
 * Ce service orchestre la création de templates prédéfinis en utilisant
 * des classes spécialisées pour chaque type de tunnel.
 */
class TemplateGeneratorService
{
    /**
     * Liste des templates disponibles
     */
    protected array $templates = [
        OnlineTrainingTemplate::class,
        DigitalBookTemplate::class,
        CoachingProgramTemplate::class,
        MLMCommunityTemplate::class,
        LeadCaptureTemplate::class,
        FreeTrainingTemplate::class,
        WebinarTemplate::class,
        ProductLaunchTemplate::class,
        QuizTemplate::class,
    ];

    /**
     * Génère tous les templates par défaut
     */
    public function generateAllDefaults(): array
    {
        $funnels = [];

        foreach ($this->templates as $templateClass) {
            $template = new $templateClass();
            $funnels[] = $template->build();
        }

        return $funnels;
    }

    /**
     * Génère un template spécifique par sa catégorie
     */
    public function generateByCategory(string $category): ?Funnel
    {
        foreach ($this->templates as $templateClass) {
            $template = new $templateClass();
            if ($template->getCategory() === $category) {
                return $template->build();
            }
        }

        return null;
    }

    /**
     * Retourne la liste des templates disponibles
     */
    public function getAvailableTemplates(): array
    {
        $available = [];

        foreach ($this->templates as $templateClass) {
            $template = new $templateClass();
            $available[] = [
                'category' => $template->getCategory(),
                'icon' => $template->getIcon(),
                'name' => $template->getName(),
                'description' => $template->getDescription(),
                'tags' => $template->getTags(),
                'primary_color' => $template->getPrimaryColor(),
            ];
        }

        return $available;
    }

    /**
     * Recherche des templates par tag
     */
    public function searchByTag(string $tag): array
    {
        $matching = [];

        foreach ($this->templates as $templateClass) {
            $template = new $templateClass();
            if (in_array($tag, $template->getTags(), true)) {
                $matching[] = [
                    'category' => $template->getCategory(),
                    'icon' => $template->getIcon(),
                    'name' => $template->getName(),
                    'description' => $template->getDescription(),
                ];
            }
        }

        return $matching;
    }

    /**
     * Récupère les options de templates pour un formulaire
     */
    public static function getTemplateOptions(): array
    {
        $service = new self();
        $options = [];

        foreach ($service->getAvailableTemplates() as $template) {
            $options[$template['category']] = $template['icon'] . ' ' . $template['name'];
        }

        return $options;
    }

    // =========================================================================
    // PAGE TEMPLATES (pour création de pages individuelles)
    // =========================================================================

    /**
     * Templates de pages disponibles
     */
    public static function getPageTemplates(): array
    {
        return [
            'blank' => 'Vierge',
            'capture_split' => 'Capture avec Image/Split',
            'capture_video' => 'Capture avec Vidéo',
            'webinar_registration' => 'Inscription Webinaire',
            'thank_you' => 'Page de Remerciement',
            'sales_page' => 'Page de Vente',
            'checkout' => 'Page de Paiement',
            'quiz_question' => 'Question de Quiz',
        ];
    }

    /**
     * Applique un template de page
     */
    public function applyPageTemplate(Page $page, string $templateKey): void
    {
        match ($templateKey) {
            'capture_split' => $this->applyCaptureWithSplitTemplate($page),
            'capture_video' => $this->applyCaptureWithVideoTemplate($page),
            'webinar_registration' => $this->applyWebinarRegistrationTemplate($page),
            'thank_you' => $this->applyThankYouTemplate($page),
            'sales_page' => $this->applySalesPageTemplate($page),
            'checkout' => $this->applyCheckoutTemplate($page),
            'quiz_question' => $this->applyQuizQuestionTemplate($page),
            default => null,
        };
    }

    private function applyCaptureWithSplitTemplate(Page $page): void
    {
        $this->createBlocks($page, [
            [
                'type' => BlockType::TITLE,
                'sort_order' => 1,
                'content' => [
                    'text' => '🎁 RECEVEZ VOTRE [RESSOURCE] GRATUITEMENT',
                    'level' => 'h1',
                    'alignment' => 'center',
                ],
                'styles' => ['color' => '#FBBF24', 'fontSize' => '2.5rem'],
            ],
            [
                'type' => BlockType::TITLE,
                'sort_order' => 2,
                'content' => [
                    'text' => 'Découvrez comment [BÉNÉFICE] grâce à notre méthode exclusive',
                    'level' => 'h2',
                    'alignment' => 'center',
                ],
                'styles' => ['color' => '#E5E7EB', 'fontSize' => '1.25rem'],
            ],
            [
                'type' => BlockType::FORM,
                'sort_order' => 3,
                'content' => [
                    'title' => 'Accédez gratuitement',
                    'fields' => [
                        ['name' => 'email', 'label' => 'Votre email', 'type' => 'email', 'required' => true],
                    ],
                    'button_text' => 'RECEVOIR MAINTENANT 🚀',
                    'layout' => 'right',
                    'image_url' => 'https://picsum.photos/600/800',
                ],
                'styles' => ['maxWidth' => '800px'],
            ],
        ]);
    }

    private function applyCaptureWithVideoTemplate(Page $page): void
    {
        $this->createBlocks($page, [
            [
                'type' => BlockType::TITLE,
                'sort_order' => 1,
                'content' => ['text' => '🎬 REGARDEZ CETTE VIDÉO', 'level' => 'h1'],
                'styles' => ['color' => '#A78BFA', 'fontSize' => '2.5rem'],
            ],
            [
                'type' => BlockType::VIDEO,
                'sort_order' => 2,
                'content' => [
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'autoplay' => false,
                ],
                'styles' => ['maxWidth' => '800px', 'borderRadius' => '16px'],
            ],
            [
                'type' => BlockType::FORM,
                'sort_order' => 3,
                'content' => [
                    'title' => 'Accédez à la suite',
                    'fields' => [
                        ['name' => 'email', 'label' => 'Votre email', 'type' => 'email', 'required' => true],
                    ],
                    'button_text' => 'CONTINUER ➜',
                ],
                'styles' => ['maxWidth' => '450px'],
            ],
        ]);
    }

    private function applyWebinarRegistrationTemplate(Page $page): void
    {
        $this->createBlocks($page, [
            [
                'type' => BlockType::TITLE,
                'sort_order' => 1,
                'content' => ['text' => '🎙️ WEBINAIRE GRATUIT', 'level' => 'h1'],
                'styles' => ['color' => '#F97316'],
            ],
            [
                'type' => BlockType::COUNTDOWN,
                'sort_order' => 2,
                'content' => [
                    'title' => 'Le webinaire commence dans :',
                    'duration' => 72,
                    'format' => 'boxes',
                ],
            ],
            [
                'type' => BlockType::FORM,
                'sort_order' => 3,
                'content' => [
                    'title' => 'Réservez votre place',
                    'fields' => [
                        ['name' => 'first_name', 'label' => 'Prénom', 'type' => 'text', 'required' => true],
                        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                    ],
                    'button_text' => 'JE RÉSERVE MA PLACE 🎯',
                ],
            ],
        ]);
    }

    private function applyThankYouTemplate(Page $page): void
    {
        $this->createBlocks($page, [
            [
                'type' => BlockType::TITLE,
                'sort_order' => 1,
                'content' => ['text' => '🎉 MERCI !', 'level' => 'h1'],
                'styles' => ['color' => '#10B981'],
            ],
            [
                'type' => BlockType::TEXT,
                'sort_order' => 2,
                'content' => [
                    'text' => '<div class="bg-green-900/40 p-8 rounded-2xl border border-green-500/30 text-center"><div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">✅</div><h3 class="text-xl font-bold text-green-400 mb-4">Inscription confirmée !</h3><p>Vérifiez votre boîte email pour les prochaines étapes.</p></div>',
                ],
            ],
            [
                'type' => BlockType::BUTTON,
                'sort_order' => 3,
                'content' => [
                    'text' => 'Rejoindre WhatsApp 💬',
                    'action_type' => 'whatsapp',
                    'whatsapp_number' => '+33123456789',
                ],
                'styles' => ['backgroundColor' => '#25D366'],
            ],
        ]);
    }

    private function applySalesPageTemplate(Page $page): void
    {
        $this->createBlocks($page, [
            [
                'type' => BlockType::TITLE,
                'sort_order' => 1,
                'content' => ['text' => '🏆 [TITRE DE L\'OFFRE]', 'level' => 'h1'],
                'styles' => ['color' => '#F59E0B'],
            ],
            [
                'type' => BlockType::VIDEO,
                'sort_order' => 2,
                'content' => ['video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ'],
            ],
            [
                'type' => BlockType::FEATURES,
                'sort_order' => 3,
                'content' => [
                    'title' => '📦 CE QUI EST INCLUS :',
                    'features' => [
                        ['title' => 'Feature 1', 'text' => 'Description de la feature'],
                        ['title' => 'Feature 2', 'text' => 'Description de la feature'],
                        ['title' => 'Feature 3', 'text' => 'Description de la feature'],
                    ],
                ],
            ],
            [
                'type' => BlockType::BUTTON,
                'sort_order' => 4,
                'content' => ['text' => 'ACHETER MAINTENANT 🛒', 'action_type' => 'page'],
                'styles' => ['backgroundColor' => '#10B981', 'fontSize' => '1.3rem'],
            ],
        ]);
    }

    private function applyCheckoutTemplate(Page $page): void
    {
        $this->createBlocks($page, [
            [
                'type' => BlockType::TITLE,
                'sort_order' => 1,
                'content' => ['text' => '🔐 FINALISEZ VOTRE COMMANDE', 'level' => 'h1'],
                'styles' => ['color' => '#10B981'],
            ],
            [
                'type' => BlockType::FORM,
                'sort_order' => 2,
                'content' => [
                    'title' => 'Vos informations',
                    'fields' => [
                        ['name' => 'full_name', 'label' => 'Nom complet', 'type' => 'text', 'required' => true],
                        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                        ['name' => 'phone', 'label' => 'Téléphone', 'type' => 'tel', 'required' => true],
                    ],
                    'button_text' => 'PAYER MAINTENANT 🔒',
                ],
                'styles' => ['maxWidth' => '500px'],
            ],
        ]);
    }

    private function applyQuizQuestionTemplate(Page $page): void
    {
        $this->createBlocks($page, [
            [
                'type' => BlockType::TEXT,
                'sort_order' => 1,
                'content' => [
                    'text' => '<div class="w-full bg-gray-700 rounded-full h-2 mb-8"><div class="bg-teal-500 h-2 rounded-full" style="width: 50%"></div></div>',
                ],
            ],
            [
                'type' => BlockType::TITLE,
                'sort_order' => 2,
                'content' => ['text' => 'Votre question ici ?', 'level' => 'h2'],
            ],
            [
                'type' => BlockType::FORM,
                'sort_order' => 3,
                'content' => [
                    'fields' => [
                        [
                            'name' => 'answer',
                            'type' => 'radio',
                            'required' => true,
                            'options' => [
                                'Option A',
                                'Option B',
                                'Option C',
                                'Option D',
                            ]
                        ],
                    ],
                    'button_text' => 'SUIVANT ➜',
                ],
            ],
        ]);
    }

    /**
     * Crée des blocs pour une page
     */
    protected function createBlocks(Page $page, array $blocksData): void
    {
        foreach ($blocksData as $blockData) {
            Block::create([
                'page_id' => $page->id,
                'type' => $blockData['type'],
                'content' => $blockData['content'] ?? [],
                'settings' => $blockData['settings'] ?? [],
                'styles' => $blockData['styles'] ?? [],
                'sort_order' => $blockData['sort_order'],
                'is_active' => true,
                'show_on_mobile' => true,
                'show_on_desktop' => true,
            ]);
        }
    }
}
