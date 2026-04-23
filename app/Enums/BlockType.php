<?php

namespace App\Enums;

enum BlockType: string
{
    // ============================================
    // CONTENT BLOCKS
    // ============================================
    case TITLE = 'title';
    case TEXT = 'text';
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';

    // ============================================
    // ACTION BLOCKS
    // ============================================
    case BUTTON = 'button';
    case FORM = 'form';
    case POPUP = 'popup';
    case ORDER_BUMP = 'order_bump';

    // ============================================
    // LAYOUT BLOCKS
    // ============================================
    case SECTION = 'section';
    case COLUMNS = 'columns';
    case SPACER = 'spacer';
    case DIVIDER = 'divider';
    case STICKY_BAR = 'sticky_bar';

    // ============================================
    // ADVANCED BLOCKS
    // ============================================
    case HERO = 'hero';
    case TEXT_IMAGE = 'text_image';
    case FEATURES = 'features';
    case TESTIMONIAL = 'testimonial';
    case COUNTDOWN = 'countdown';
    case PRICING = 'pricing';
    case FAQ = 'faq';
    case PROGRESS = 'progress';
    case ICON_BOX = 'icon_box';

    // ============================================
    // TRUST & SOCIAL BLOCKS
    // ============================================
    case TRUST_BADGES = 'trust_badges';
    case SOCIAL_ICONS = 'social_icons';

    // ============================================
    // EMBED BLOCKS
    // ============================================
    case EMBED = 'embed';

    /**
     * Label affiché dans l'interface
     */
    public function label(): string
    {
        return match ($this) {
                // Content
            self::TITLE => 'Titre',
            self::TEXT => 'Texte / HTML',
            self::IMAGE => 'Image',
            self::VIDEO => 'Vidéo',
            self::AUDIO => 'Audio',

                // Action
            self::BUTTON => 'Bouton CTA',
            self::FORM => 'Formulaire',
            self::POPUP => 'Popup / Modal',
            self::ORDER_BUMP => 'Order Bump',

                // Layout
            self::SECTION => 'Section',
            self::COLUMNS => 'Colonnes',
            self::SPACER => 'Espacement',
            self::DIVIDER => 'Séparateur',
            self::STICKY_BAR => 'Barre Fixe',

                // Advanced
            self::HERO => 'Section Hero',
            self::TEXT_IMAGE => 'Texte + Image',
            self::FEATURES => 'Liste Avantages',
            self::TESTIMONIAL => 'Témoignage',
            self::COUNTDOWN => 'Compte à rebours',
            self::PRICING => 'Tableau de Prix',
            self::FAQ => 'FAQ Accordéon',
            self::PROGRESS => 'Barre de Progression',
            self::ICON_BOX => 'Icône + Texte',

                // Trust & Social
            self::TRUST_BADGES => 'Badges de Confiance',
            self::SOCIAL_ICONS => 'Réseaux Sociaux',

                // Embed
            self::EMBED => 'Code HTML / Embed',
        };
    }

    /**
     * Icône Heroicon
     */
    public function icon(): string
    {
        return match ($this) {
                // Content
            self::TITLE => 'heroicon-o-h1',
            self::TEXT => 'heroicon-o-document-text',
            self::IMAGE => 'heroicon-o-photo',
            self::VIDEO => 'heroicon-o-video-camera',
            self::AUDIO => 'heroicon-o-speaker-wave',

                // Action
            self::BUTTON => 'heroicon-o-cursor-arrow-rays',
            self::FORM => 'heroicon-o-clipboard-document-list',
            self::POPUP => 'heroicon-o-window',
            self::ORDER_BUMP => 'heroicon-o-plus-circle',

                // Layout
            self::SECTION => 'heroicon-o-rectangle-group',
            self::COLUMNS => 'heroicon-o-view-columns',
            self::SPACER => 'heroicon-o-arrows-up-down',
            self::DIVIDER => 'heroicon-o-minus',
            self::STICKY_BAR => 'heroicon-o-bars-2',

                // Advanced
            self::HERO => 'heroicon-o-star',
            self::TEXT_IMAGE => 'heroicon-o-squares-2x2',
            self::FEATURES => 'heroicon-o-list-bullet',
            self::TESTIMONIAL => 'heroicon-o-chat-bubble-bottom-center-text',
            self::COUNTDOWN => 'heroicon-o-clock',
            self::PRICING => 'heroicon-o-currency-dollar',
            self::FAQ => 'heroicon-o-question-mark-circle',
            self::PROGRESS => 'heroicon-o-chart-bar',
            self::ICON_BOX => 'heroicon-o-cube',

                // Trust & Social
            self::TRUST_BADGES => 'heroicon-o-shield-check',
            self::SOCIAL_ICONS => 'heroicon-o-share',

                // Embed
            self::EMBED => 'heroicon-o-code-bracket',
        };
    }

    /**
     * Catégorie du bloc
     */
    public function category(): string
    {
        return match ($this) {
            self::TITLE, self::TEXT, self::IMAGE, self::VIDEO, self::AUDIO => 'content',
            self::BUTTON, self::FORM, self::POPUP, self::ORDER_BUMP => 'action',
            self::SECTION, self::COLUMNS, self::SPACER, self::DIVIDER, self::STICKY_BAR => 'layout',
            self::HERO, self::TEXT_IMAGE, self::FEATURES, self::TESTIMONIAL, self::COUNTDOWN,
            self::PRICING, self::FAQ, self::PROGRESS, self::ICON_BOX => 'advanced',
            self::TRUST_BADGES, self::SOCIAL_ICONS => 'social',
            self::EMBED => 'embed',
        };
    }

    /**
     * Description détaillée du bloc
     */
    public function description(): string
    {
        return match ($this) {
            self::TITLE => 'Ajoutez un titre H1, H2, H3...',
            self::TEXT => 'Ajoutez du texte formaté ou du HTML personnalisé',
            self::IMAGE => 'Insérez une image avec options de style',
            self::VIDEO => 'Intégrez une vidéo YouTube, Vimeo ou MP4',
            self::AUDIO => 'Ajoutez un lecteur audio ou podcast',

            self::BUTTON => 'Bouton d\'action avec lien ou action spéciale',
            self::FORM => 'Formulaire de capture d\'emails ou de paiement',
            self::POPUP => 'Fenêtre modale déclenchée par action',
            self::ORDER_BUMP => 'Case à cocher pour ajouter un produit au panier',

            self::SECTION => 'Conteneur principal avec background personnalisé',
            self::COLUMNS => 'Mise en page en 2, 3 ou 4 colonnes',
            self::SPACER => 'Ajoute de l\'espace vertical',
            self::DIVIDER => 'Ligne de séparation horizontale',
            self::STICKY_BAR => 'Barre fixe en haut ou en bas de page',

            self::HERO => 'Grande bannière avec titre, sous-titre et CTA',
            self::TEXT_IMAGE => 'Texte à gauche/droite avec image',
            self::FEATURES => 'Liste d\'avantages avec icônes',
            self::TESTIMONIAL => 'Avis client avec photo et note',
            self::COUNTDOWN => 'Minuteur pour créer l\'urgence',
            self::PRICING => 'Tableau comparatif de tarifs',
            self::FAQ => 'Questions/Réponses en accordéon',
            self::PROGRESS => 'Barre de progression visuelle',
            self::ICON_BOX => 'Icône avec titre et description',

            self::TRUST_BADGES => 'Logos de confiance (SSL, paiement sécurisé...)',
            self::SOCIAL_ICONS => 'Liens vers les réseaux sociaux',

            self::EMBED => 'Code HTML, iframe ou script personnalisé',
        };
    }

    /**
     * Si le bloc peut contenir des enfants
     */
    public function canHaveChildren(): bool
    {
        return match ($this) {
            self::SECTION, self::COLUMNS, self::POPUP => true,
            default => false,
        };
    }

    /**
     * Si le bloc est un conteneur de mise en page
     */
    public function isLayoutBlock(): bool
    {
        return in_array($this->category(), ['layout']);
    }

    /**
     * Si le bloc nécessite une configuration avancée
     */
    public function requiresAdvancedConfig(): bool
    {
        return match ($this) {
            self::FORM, self::PRICING, self::COUNTDOWN, self::POPUP, self::ORDER_BUMP => true,
            default => false,
        };
    }

    /**
     * Retourne tous les blocs par catégorie
     */
    public static function byCategory(): array
    {
        $grouped = [];
        foreach (self::cases() as $case) {
            $category = $case->category();
            $grouped[$category][] = $case;
        }
        return $grouped;
    }

    /**
     * Labels des catégories
     */
    public static function categoryLabels(): array
    {
        return [
            'content' => '📝 Contenu',
            'action' => '🎯 Action',
            'layout' => '📐 Mise en page',
            'advanced' => '⚡ Avancé',
            'social' => '🌐 Social & Confiance',
            'embed' => '💻 Code',
        ];
    }
}
