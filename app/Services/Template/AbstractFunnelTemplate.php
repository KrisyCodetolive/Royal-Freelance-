<?php

namespace App\Services\Template;

use App\Enums\BlockType;
use App\Enums\FunnelStatus;
use App\Enums\PageType;
use App\Models\Block;
use App\Models\Funnel;
use App\Models\Page;
use Illuminate\Support\Str;

abstract class AbstractFunnelTemplate
{
    /**
     * Template category identifier
     */
    abstract public function getCategory(): string;

    /**
     * Template icon (emoji)
     */
    abstract public function getIcon(): string;

    /**
     * Template name
     */
    abstract public function getName(): string;

    /**
     * Template description
     */
    abstract public function getDescription(): string;

    /**
     * Template tags for filtering
     */
    abstract public function getTags(): array;

    /**
     * Primary branding color
     */
    abstract public function getPrimaryColor(): string;

    /**
     * Secondary branding color
     */
    abstract public function getSecondaryColor(): string;

    /**
     * Build and return the complete funnel with all pages
     */
    abstract public function build(): Funnel;

    /**
     * Get background color
     */
    public function getBackgroundColor(): string
    {
        return '#0F172A';
    }

    /**
     * Get text color
     */
    public function getTextColor(): string
    {
        return '#FFFFFF';
    }

    /**
     * Create the base funnel
     */
    protected function createFunnel(array $overrides = []): Funnel
    {
        return Funnel::create(array_merge([
            'tenant_id' => null,
            'name' => $this->getIcon() . ' ' . $this->getName(),
            'slug' => 'template-' . Str::slug($this->getCategory()) . '-' . Str::random(6),
            'description' => $this->getDescription(),
            'status' => FunnelStatus::ACTIVE,
            'is_template' => true,
            'template_category' => $this->getCategory(),
            'template_description' => $this->getDescription(),
            'template_tags' => $this->getTags(),
            'branding' => [
                'primary_color' => $this->getPrimaryColor(),
                'secondary_color' => $this->getSecondaryColor(),
                'background_color' => $this->getBackgroundColor(),
                'text_color' => $this->getTextColor(),
            ],
        ], $overrides));
    }

    /**
     * Create a page
     */
    protected function createPage(Funnel $funnel, array $data): Page
    {
        return Page::create([
            'funnel_id' => $funnel->id,
            'type' => $data['type'],
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'sort_order' => $data['sort_order'],
            'is_active' => true,
            'is_required' => $data['is_required'] ?? true,
            'settings' => $data['settings'] ?? [],
            'branding' => $data['branding'] ?? [],
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ]);
    }

    /**
     * Create multiple blocks for a page
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
                'show_on_mobile' => $blockData['show_on_mobile'] ?? true,
                'show_on_desktop' => $blockData['show_on_desktop'] ?? true,
            ]);
        }
    }

    // =====================================================
    // COMMON BLOCK BUILDERS
    // =====================================================

    /**
     * Build a Hero Title block
     */
    protected function heroTitle(int $order, string $text, array $styles = []): array
    {
        return [
            'type' => BlockType::TITLE,
            'sort_order' => $order,
            'content' => [
                'text' => $text,
                'level' => 'h1',
                'alignment' => 'center',
            ],
            'styles' => array_merge([
                'color' => $this->getPrimaryColor(),
                'fontSize' => '3rem',
                'fontWeight' => 'bold',
                'marginBottom' => '20px',
                'textShadow' => '0 2px 4px rgba(0,0,0,0.3)',
            ], $styles),
        ];
    }

    /**
     * Build a subtitle block
     */
    protected function subtitle(int $order, string $text, array $styles = []): array
    {
        return [
            'type' => BlockType::TITLE,
            'sort_order' => $order,
            'content' => [
                'text' => $text,
                'level' => 'h2',
                'alignment' => 'center',
            ],
            'styles' => array_merge([
                'color' => '#E5E7EB',
                'fontSize' => '1.5rem',
                'maxWidth' => '800px',
                'marginBottom' => '30px',
            ], $styles),
        ];
    }

    /**
     * Build a video block
     */
    protected function video(int $order, string $url = 'https://www.youtube.com/embed/dQw4w9WgXcQ', array $styles = []): array
    {
        return [
            'type' => BlockType::VIDEO,
            'sort_order' => $order,
            'content' => [
                'video_url' => $url,
                'thumbnail' => 'https://picsum.photos/800/450',
                'autoplay' => false,
            ],
            'styles' => array_merge([
                'maxWidth' => '800px',
                'marginBottom' => '40px',
                'borderRadius' => '16px',
                'boxShadow' => '0 25px 50px -12px rgba(0,0,0,0.5)',
            ], $styles),
        ];
    }

    /**
     * Build a features list block
     */
    protected function features(int $order, string $title, array $items, array $styles = []): array
    {
        return [
            'type' => BlockType::FEATURES,
            'sort_order' => $order,
            'content' => [
                'title' => $title,
                'features' => $items,
            ],
            'styles' => array_merge([
                'marginBottom' => '40px',
            ], $styles),
        ];
    }

    /**
     * Build a CTA button block
     */
    protected function ctaButton(int $order, string $text, array $options = [], array $styles = []): array
    {
        return [
            'type' => BlockType::BUTTON,
            'sort_order' => $order,
            'content' => array_merge([
                'text' => $text,
                'action_type' => 'page',
                'size' => 'large',
                'full_width' => false,
            ], $options),
            'styles' => array_merge([
                'backgroundColor' => '#10B981',
                'fontSize' => '1.2rem',
                'padding' => '20px 40px',
                'borderRadius' => '12px',
                'boxShadow' => '0 4px 14px rgba(16, 185, 129, 0.4)',
                'transition' => 'all 0.3s ease',
            ], $styles),
        ];
    }

    /**
     * Build a form block
     */
    protected function form(int $order, string $title, array $fields, string $buttonText, array $styles = []): array
    {
        return [
            'type' => BlockType::FORM,
            'sort_order' => $order,
            'content' => [
                'title' => $title,
                'fields' => $fields,
                'button_text' => $buttonText,
                'success_message' => 'Inscription réussie ! Vérifiez votre email.',
            ],
            'styles' => array_merge([
                'maxWidth' => '500px',
                'marginBottom' => '30px',
                'backgroundColor' => 'rgba(255,255,255,0.05)',
                'padding' => '30px',
                'borderRadius' => '16px',
                'backdropFilter' => 'blur(10px)',
                'border' => '1px solid rgba(255,255,255,0.1)',
            ], $styles),
        ];
    }

    /**
     * Build a countdown block
     */
    protected function countdown(int $order, string $title, int $hours = 24, array $styles = []): array
    {
        return [
            'type' => BlockType::COUNTDOWN,
            'sort_order' => $order,
            'content' => [
                'title' => $title,
                'duration' => $hours,
                'format' => 'boxes',
                'size' => 'large',
                'show_labels' => true,
            ],
            'styles' => array_merge([
                'marginBottom' => '40px',
            ], $styles),
        ];
    }

    /**
     * Build a rich text/HTML block
     */
    protected function richText(int $order, string $html, array $styles = []): array
    {
        return [
            'type' => BlockType::TEXT,
            'sort_order' => $order,
            'content' => [
                'text' => $html,
            ],
            'styles' => $styles,
        ];
    }

    /**
     * Build a testimonial block
     */
    protected function testimonial(int $order, string $name, string $text, string $role = '', string $avatar = '', array $styles = []): array
    {
        return [
            'type' => BlockType::TESTIMONIAL,
            'sort_order' => $order,
            'content' => [
                'name' => $name,
                'role' => $role,
                'text' => $text,
                'avatar' => $avatar ?: 'https://i.pravatar.cc/100?u=' . Str::slug($name),
                'rating' => 5,
            ],
            'styles' => array_merge([
                'marginBottom' => '30px',
            ], $styles),
        ];
    }

    /**
     * Build a spacer block
     */
    protected function spacer(int $order, string $height = '40px'): array
    {
        return [
            'type' => BlockType::SPACER,
            'sort_order' => $order,
            'content' => ['height' => $height],
            'styles' => [],
        ];
    }

    /**
     * Build a divider block
     */
    protected function divider(int $order, array $styles = []): array
    {
        return [
            'type' => BlockType::DIVIDER,
            'sort_order' => $order,
            'content' => [],
            'styles' => array_merge([
                'marginTop' => '40px',
                'marginBottom' => '40px',
            ], $styles),
        ];
    }

    /**
     * Build an image block
     */
    protected function image(int $order, string $url, string $alt = '', array $styles = []): array
    {
        return [
            'type' => BlockType::IMAGE,
            'sort_order' => $order,
            'content' => [
                'image_url' => $url,
                'alt_text' => $alt,
            ],
            'styles' => array_merge([
                'maxWidth' => '600px',
                'marginBottom' => '30px',
                'borderRadius' => '12px',
            ], $styles),
        ];
    }

    /**
     * Build a hero section block
     */
    protected function hero(int $order, string $title, string $subtitle, string $buttonText, array $options = [], array $styles = []): array
    {
        return [
            'type' => BlockType::HERO,
            'sort_order' => $order,
            'content' => array_merge([
                'title' => $title,
                'subtitle' => $subtitle,
                'button_text' => $buttonText,
                'button_url' => '#form-section',
                'image_url' => 'https://picsum.photos/1920/800',
                'layout' => 'center',
            ], $options),
            'styles' => array_merge([
                'minHeight' => '70vh',
                'marginBottom' => '40px',
            ], $styles),
        ];
    }

    /**
     * Build a WhatsApp button
     */
    protected function whatsappButton(int $order, string $text, string $number = '+33123456789', string $message = '', array $styles = []): array
    {
        return [
            'type' => BlockType::BUTTON,
            'sort_order' => $order,
            'content' => [
                'text' => $text,
                'action_type' => 'whatsapp',
                'whatsapp_number' => $number,
                'whatsapp_message' => $message,
                'size' => 'medium',
            ],
            'styles' => array_merge([
                'backgroundColor' => '#25D366',
                'padding' => '16px 32px',
                'borderRadius' => '50px',
            ], $styles),
        ];
    }

    // =====================================================
    // NEW BLOCK BUILDERS (Systeme.io Style)
    // =====================================================

    /**
     * Build a section container block
     */
    protected function section(int $order, array $children = [], array $styles = []): array
    {
        return [
            'type' => BlockType::SECTION,
            'sort_order' => $order,
            'content' => [
                'children' => $children,
            ],
            'styles' => array_merge([
                'paddingTop' => '60px',
                'paddingBottom' => '60px',
                'backgroundColor' => 'transparent',
            ], $styles),
        ];
    }

    /**
     * Build a columns layout block
     * @param string $layout Layout format: "1/2,1/2" or "1/3,2/3" or "1/4,1/4,1/4,1/4"
     */
    protected function columns(int $order, string $layout = '1/2,1/2', array $styles = []): array
    {
        return [
            'type' => BlockType::COLUMNS,
            'sort_order' => $order,
            'content' => [],
            'columns_layout' => $layout,
            'styles' => array_merge([
                'gap' => '24px',
                'marginBottom' => '40px',
            ], $styles),
        ];
    }

    /**
     * Build a pricing table block
     */
    protected function pricing(int $order, array $plans, array $styles = []): array
    {
        return [
            'type' => BlockType::PRICING,
            'sort_order' => $order,
            'content' => [
                'plans' => $plans,
                // Example plan structure:
                // [
                //     'name' => 'Basic',
                //     'price' => '29€',
                //     'period' => '/mois',
                //     'description' => 'Pour démarrer',
                //     'features' => ['Feature 1', 'Feature 2'],
                //     'button_text' => 'Choisir',
                //     'is_popular' => false,
                //     'old_price' => '49€',
                // ]
            ],
            'styles' => array_merge([
                'marginBottom' => '40px',
            ], $styles),
        ];
    }

    /**
     * Build a FAQ accordion block
     */
    protected function faq(int $order, string $title, array $items, array $styles = []): array
    {
        return [
            'type' => BlockType::FAQ,
            'sort_order' => $order,
            'content' => [
                'title' => $title,
                'items' => $items,
                // Example item structure:
                // ['question' => '...', 'answer' => '...']
            ],
            'styles' => array_merge([
                'maxWidth' => '800px',
                'marginBottom' => '40px',
            ], $styles),
        ];
    }

    /**
     * Build a progress bar block
     */
    protected function progress(int $order, int $percentage, string $label = '', array $styles = []): array
    {
        return [
            'type' => BlockType::PROGRESS,
            'sort_order' => $order,
            'content' => [
                'percentage' => $percentage,
                'label' => $label,
                'show_percentage' => true,
                'color' => $this->getPrimaryColor(),
            ],
            'styles' => array_merge([
                'marginBottom' => '30px',
            ], $styles),
        ];
    }

    /**
     * Build an icon box block
     */
    protected function iconBox(int $order, string $icon, string $title, string $text, array $styles = []): array
    {
        return [
            'type' => BlockType::ICON_BOX,
            'sort_order' => $order,
            'content' => [
                'icon' => $icon, // heroicon name or emoji
                'title' => $title,
                'text' => $text,
                'alignment' => 'center',
            ],
            'styles' => array_merge([
                'padding' => '24px',
                'backgroundColor' => 'rgba(255,255,255,0.05)',
                'borderRadius' => '16px',
                'marginBottom' => '20px',
            ], $styles),
        ];
    }

    /**
     * Build trust badges block
     */
    protected function trustBadges(int $order, array $badges = [], array $styles = []): array
    {
        $defaultBadges = [
            ['icon' => '🔒', 'text' => 'Paiement sécurisé SSL'],
            ['icon' => '✅', 'text' => 'Garantie satisfait 30j'],
            ['icon' => '💳', 'text' => 'CB, PayPal acceptés'],
            ['icon' => '📧', 'text' => 'Support 7j/7'],
        ];

        return [
            'type' => BlockType::TRUST_BADGES,
            'sort_order' => $order,
            'content' => [
                'badges' => !empty($badges) ? $badges : $defaultBadges,
                'layout' => 'horizontal', // horizontal, vertical, grid
            ],
            'styles' => array_merge([
                'marginTop' => '30px',
                'marginBottom' => '30px',
            ], $styles),
        ];
    }

    /**
     * Build social icons block
     */
    protected function socialIcons(int $order, array $links = [], array $styles = []): array
    {
        $defaultLinks = [
            ['platform' => 'facebook', 'url' => '#'],
            ['platform' => 'instagram', 'url' => '#'],
            ['platform' => 'youtube', 'url' => '#'],
            ['platform' => 'twitter', 'url' => '#'],
            ['platform' => 'linkedin', 'url' => '#'],
        ];

        return [
            'type' => BlockType::SOCIAL_ICONS,
            'sort_order' => $order,
            'content' => [
                'links' => !empty($links) ? $links : $defaultLinks,
                'size' => 'medium', // small, medium, large
                'style' => 'filled', // filled, outline, minimal
            ],
            'styles' => array_merge([
                'marginBottom' => '30px',
            ], $styles),
        ];
    }

    /**
     * Build an order bump block
     */
    protected function orderBump(int $order, string $title, string $description, string $price, array $styles = []): array
    {
        return [
            'type' => BlockType::ORDER_BUMP,
            'sort_order' => $order,
            'content' => [
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'original_price' => null,
                'image_url' => null,
                'checkbox_text' => 'Oui, j\'ajoute cette offre !',
                'product_id' => null,
            ],
            'styles' => array_merge([
                'backgroundColor' => 'rgba(251, 191, 36, 0.1)',
                'borderColor' => '#F59E0B',
                'borderWidth' => '2px',
                'borderStyle' => 'dashed',
                'padding' => '20px',
                'borderRadius' => '12px',
                'marginBottom' => '20px',
            ], $styles),
        ];
    }

    /**
     * Build a sticky bar block
     */
    protected function stickyBar(int $order, string $text, string $buttonText, array $styles = []): array
    {
        return [
            'type' => BlockType::STICKY_BAR,
            'sort_order' => $order,
            'content' => [
                'text' => $text,
                'button_text' => $buttonText,
                'button_url' => '#cta',
                'position' => 'bottom', // top, bottom
                'show_close' => true,
            ],
            'styles' => array_merge([
                'backgroundColor' => $this->getPrimaryColor(),
                'padding' => '12px 24px',
            ], $styles),
        ];
    }

    /**
     * Build an audio player block
     */
    protected function audio(int $order, string $url, string $title = '', array $styles = []): array
    {
        return [
            'type' => BlockType::AUDIO,
            'sort_order' => $order,
            'content' => [
                'audio_url' => $url,
                'title' => $title,
                'autoplay' => false,
                'show_download' => false,
            ],
            'styles' => array_merge([
                'maxWidth' => '600px',
                'marginBottom' => '30px',
            ], $styles),
        ];
    }

    /**
     * Build an embed/HTML block
     */
    protected function embed(int $order, string $html, array $styles = []): array
    {
        return [
            'type' => BlockType::EMBED,
            'sort_order' => $order,
            'content' => [
                'html' => $html,
                'sandbox' => true,
            ],
            'styles' => array_merge([
                'marginBottom' => '30px',
            ], $styles),
        ];
    }

    /**
     * Build a popup/modal block
     */
    protected function popup(int $order, string $title, array $content = [], array $styles = []): array
    {
        return [
            'type' => BlockType::POPUP,
            'sort_order' => $order,
            'content' => [
                'title' => $title,
                'body' => $content['body'] ?? '',
                'trigger' => $content['trigger'] ?? 'exit_intent', // exit_intent, delay, scroll, click
                'delay_seconds' => $content['delay_seconds'] ?? 5,
                'scroll_percentage' => $content['scroll_percentage'] ?? 50,
                'show_once' => $content['show_once'] ?? true,
                'button_text' => $content['button_text'] ?? null,
                'button_url' => $content['button_url'] ?? null,
            ],
            'styles' => array_merge([
                'maxWidth' => '500px',
                'backgroundColor' => '#1F2937',
                'borderRadius' => '16px',
                'padding' => '40px',
            ], $styles),
        ];
    }

    // =====================================================
    // COMMON HTML BUILDERS
    // =====================================================

    /**
     * Build a styled box/card HTML
     */
    protected function styledBox(string $content, string $borderColor = 'green', string $bgOpacity = '30'): string
    {
        $colorMap = [
            'green' => ['bg' => 'green-900', 'border' => 'green-600', 'title' => 'green-400'],
            'blue' => ['bg' => 'blue-900', 'border' => 'blue-600', 'title' => 'blue-400'],
            'yellow' => ['bg' => 'yellow-900', 'border' => 'yellow-600', 'title' => 'yellow-400'],
            'red' => ['bg' => 'red-900', 'border' => 'red-600', 'title' => 'red-400'],
            'purple' => ['bg' => 'purple-900', 'border' => 'purple-600', 'title' => 'purple-400'],
            'orange' => ['bg' => 'orange-900', 'border' => 'orange-600', 'title' => 'orange-400'],
        ];

        $colors = $colorMap[$borderColor] ?? $colorMap['green'];

        return sprintf(
            '<div class="bg-%s/%s p-6 rounded-lg border border-%s mb-8">%s</div>',
            $colors['bg'],
            $bgOpacity,
            $colors['border'],
            $content
        );
    }

    /**
     * Build a pricing display HTML
     */
    protected function pricingHtml(string $oldPrice, string $newPrice, string $savings, string $urgency = ''): string
    {
        $urgencyHtml = $urgency ? "<p class=\"text-sm text-gray-400 mt-2\">{$urgency}</p>" : '';

        return $this->styledBox(
            "<h3 class=\"text-green-400 font-bold text-2xl mb-3\">💰 PRIX SPÉCIAL</h3>
            <div class=\"text-4xl font-bold mb-2\">
                <span class=\"line-through text-gray-500\">{$oldPrice}</span>
                <span class=\"text-green-400 ml-4\">{$newPrice}</span>
            </div>
            <p class=\"text-lg mb-2\">Économisez {$savings}</p>
            {$urgencyHtml}",
            'green'
        );
    }

    /**
     * Build a guarantee badge HTML
     */
    protected function guaranteeBadge(string $days = '30'): string
    {
        return "<p class=\"text-center text-sm text-gray-400\">
            ✅ Garantie satisfait ou remboursé {$days} jours<br>
            🔒 Paiement 100% sécurisé SSL
        </p>";
    }

    /**
     * Build a social proof counter HTML
     */
    protected function socialProof(string $number, string $label): string
    {
        return "<div class=\"text-center mb-8\">
            <p class=\"text-4xl font-bold text-{$this->getPrimaryColor()}\">{$number}+</p>
            <p class=\"text-gray-400\">{$label}</p>
        </div>";
    }

    /**
     * Build steps list HTML
     */
    protected function stepsHtml(array $steps, string $title = 'Prochaines étapes :'): string
    {
        $stepsHtml = '';
        foreach ($steps as $index => $step) {
            $num = $index + 1;
            $stepsHtml .= "<li><strong>{$num}.</strong> {$step}</li>";
        }

        return $this->styledBox(
            "<h3 class=\"text-green-400 font-bold text-xl mb-4\">{$title}</h3>
            <ul class=\"space-y-3 text-left max-w-lg mx-auto\">{$stepsHtml}</ul>",
            'green'
        );
    }

    /**
     * Build comparison columns HTML
     */
    protected function comparisonHtml(array $without, array $with): string
    {
        $withoutItems = implode('', array_map(fn($item) => "<li>• {$item}</li>", $without));
        $withItems = implode('', array_map(fn($item) => "<li>• {$item}</li>", $with));

        return "<div class=\"grid md:grid-cols-2 gap-6 mb-8\">
            <div class=\"bg-red-900/30 p-6 rounded-lg border border-red-600\">
                <h4 class=\"text-red-400 font-bold mb-3\">❌ SANS NOTRE SOLUTION</h4>
                <ul class=\"text-sm space-y-2\">{$withoutItems}</ul>
            </div>
            <div class=\"bg-green-900/30 p-6 rounded-lg border border-green-600\">
                <h4 class=\"text-green-400 font-bold mb-3\">✅ AVEC NOTRE SOLUTION</h4>
                <ul class=\"text-sm space-y-2\">{$withItems}</ul>
            </div>
        </div>";
    }
}
