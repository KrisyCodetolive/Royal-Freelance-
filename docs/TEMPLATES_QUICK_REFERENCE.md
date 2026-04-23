# 🚀 Quick Reference - Templates Funnel

> Aide-mémoire rapide pour les développeurs

---

## Créer un Template (minimum)

```php
<?php

namespace App\Services\Template;

use App\Enums\PageType;
use App\Models\Funnel;

class MonTemplate extends AbstractFunnelTemplate
{
    public function getCategory(): string { return 'mon_template'; }
    public function getIcon(): string { return '🎯'; }
    public function getName(): string { return 'Mon Template'; }
    public function getDescription(): string { return 'Description...'; }
    public function getTags(): array { return ['tag1']; }
    public function getPrimaryColor(): string { return '#3B82F6'; }
    public function getSecondaryColor(): string { return '#1E40AF'; }

    public function build(): Funnel
    {
        $funnel = $this->createFunnel();
        
        $page = $this->createPage($funnel, [
            'type' => PageType::LANDING,
            'title' => 'Page',
            'sort_order' => 1,
        ]);

        $this->createBlocks($page, [
            $this->heroTitle(1, 'TITRE'),
            $this->ctaButton(2, 'ACTION →'),
        ]);

        return $funnel;
    }
}
```

---

## Blocs Disponibles - Cheat Sheet

### Contenu
```php
$this->heroTitle($order, 'TITRE', ['color' => '#FFF'])
$this->subtitle($order, 'Sous-titre')
$this->richText($order, '<p>HTML content</p>')
$this->image($order, 'https://url.com/image.jpg', ['alt' => 'Description'])
$this->video($order, 'https://youtube.com/watch?v=XXX')
$this->audio($order, 'https://url.com/audio.mp3', ['title' => 'Podcast'])
```

### Actions
```php
$this->ctaButton($order, 'CLIQUER ICI', ['url' => '/page'], ['backgroundColor' => '#10B981'])
$this->whatsappButton($order, 'WhatsApp', '+33123456789', 'Message par défaut')
$this->form($order, 'Titre formulaire', [
    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
    ['name' => 'name', 'label' => 'Nom', 'type' => 'text'],
], 'ENVOYER')
$this->countdown($order, '⏰ OFFRE EXPIRE DANS :', 24)  // heures
```

### Mise en page
```php
$this->spacer($order, '40px')
$this->divider($order)
```

### Marketing
```php
$this->features($order, 'TITRE:', [
    ['title' => 'Feature 1', 'text' => 'Description', 'icon' => 'heroicon-o-check'],
])

$this->testimonial($order, 'Jean D.', 'Témoignage...', 'Client depuis 2 ans')

$this->pricing($order, [
    [
        'name' => 'Basic',
        'price' => '47€',
        'period' => '/mois',
        'features' => ['Feature 1', 'Feature 2'],
        'button_text' => 'Choisir',
        'is_popular' => false,
    ],
])

$this->faq($order, '❓ FAQ', [
    ['question' => 'Question ?', 'answer' => 'Réponse.'],
])

$this->iconBox($order, '🎯', 'Titre', 'Description')

$this->trustBadges($order, [
    ['icon' => '✅', 'text' => 'Garanti'],
    ['icon' => '🔒', 'text' => 'Sécurisé'],
])

$this->socialIcons($order, [
    ['platform' => 'facebook', 'url' => 'https://facebook.com/...'],
    ['platform' => 'instagram', 'url' => 'https://instagram.com/...'],
])
```

### Avancé
```php
$this->progress($order, 75, 'Étape 3 sur 4')

$this->stickyBar($order, '🔥 Offre limitée !', 'VOIR L\'OFFRE')

$this->popup($order, '🎁 Attendez !', [
    'body' => '<p>Ne partez pas...</p>',
    'trigger' => 'exit_intent',  // ou 'delay', 'scroll'
    'delay' => 5000,             // pour trigger delay
    'scroll_percent' => 50,      // pour trigger scroll
    'button_text' => 'CONTINUER',
    'show_once' => true,
])

$this->orderBump($order, 
    'Ajouter l\'option VIP', 
    'Description de l\'upsell...', 
    '+27€'
)

$this->embed($order, '<iframe src="..."></iframe>', ['aspectRatio' => '16:9'])
```

---

## Types de Pages

```php
use App\Enums\PageType;

PageType::LANDING      // Page d'accueil
PageType::CAPTURE      // Capture d'email
PageType::CONTENT      // Contenu (vidéo, formation)
PageType::PAYMENT      // Paiement
PageType::UPSELL       // Vente additionnelle
PageType::DOWNSELL     // Offre de repli
PageType::THANK_YOU    // Merci
PageType::QUIZ         // Quiz interactif
```

---

## Styles Communs

```php
// Dans un helper de bloc
$this->heroTitle(1, 'TITRE', [
    'color' => '#FBBF24',           // Couleur texte
    'backgroundColor' => '#1F2937', // Fond
    'fontSize' => '3rem',
    'fontWeight' => 'bold',
    'textAlign' => 'center',        // left, center, right
    'marginTop' => '20px',
    'marginBottom' => '20px',
    'padding' => '20px',
    'borderRadius' => '8px',
    'boxShadow' => '0 4px 6px rgba(0,0,0,0.1)',
])
```

---

## Conditions d'Affichage

```php
$block->display_conditions = [
    'operator' => 'AND',  // ou 'OR'
    'rules' => [
        ['type' => 'date_range', 'start' => '2025-01-01', 'end' => '2025-12-31'],
        ['type' => 'time_range', 'start' => '09:00', 'end' => '18:00'],
        ['type' => 'day_of_week', 'days' => [1,2,3,4,5]],
        ['type' => 'device', 'value' => 'mobile'],
        ['type' => 'url_contains', 'value' => 'utm_source=fb'],
        ['type' => 'cookie_exists', 'name' => 'returning'],
        ['type' => 'visitor_count', 'operator' => '>=', 'value' => 2],
        ['type' => 'is_logged_in', 'value' => true],
        ['type' => 'context_variable', 'key' => 'score', 'operator' => '>', 'value' => 50],
    ]
];
```

---

## Master Blocks - Code

```php
use App\Services\MasterBlockService;

$service = app(MasterBlockService::class);

// Lister
$masters = $service->getAllMasterBlocks($tenantId);

// Convertir
$service->convertToMasterBlock($block, 'Nom du Master');

// Créer instance
$instance = $service->createInstance($masterBlock, $page);

// Synchroniser
$service->syncMasterToInstances($masterBlock);
$service->syncInstanceFromMaster($instance);

// Détacher
$service->detachInstance($instance);
```

---

## Helpers Utiles

```php
// Dans AbstractFunnelTemplate

// Générer une garantie
$this->guaranteeBadge('14')  // 14 jours

// Générer des étapes
$this->stepsHtml([
    'Étape 1',
    'Étape 2',
    'Étape 3',
], 'VOS PROCHAINES ÉTAPES :')
```

---

## Commandes Artisan

```bash
# Vérifier syntaxe
php -l app/Services/Template/MonTemplate.php

# Vider caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Migrations
php artisan migrate
php artisan migrate:rollback
php artisan migrate:status
```

---

## Arborescence Fichiers

```
app/Services/Template/
├── AbstractFunnelTemplate.php    # Base class
├── OnlineTrainingTemplate.php
├── DigitalBookTemplate.php
├── CoachingProgramTemplate.php
├── MLMCommunityTemplate.php
├── LeadCaptureTemplate.php
├── FreeTrainingTemplate.php
├── WebinarTemplate.php
├── ProductLaunchTemplate.php
└── QuizTemplate.php

resources/views/
├── funnel/blocks/                # Vues publiques
└── livewire/page-builder/
    ├── blocks/                   # Aperçus builder
    └── editors/                  # Formulaires édition
```

---

*Quick Reference v2.0 - Janvier 2026*
