# 📚 Documentation Complète - Système de Templates Funnel

> **Version**: 2.0  
> **Date de mise à jour**: Janvier 2026  
> **Auteur**: Équipe Royal-LeadMagnet

---

## Table des Matières

1. [Vue d'ensemble](#1-vue-densemble)
2. [Architecture du Système](#2-architecture-du-système)
3. [Types de Blocs Disponibles](#3-types-de-blocs-disponibles)
4. [Créer un Nouveau Template](#4-créer-un-nouveau-template)
5. [Modifier un Template Existant](#5-modifier-un-template-existant)
6. [Utiliser le Page Builder](#6-utiliser-le-page-builder)
7. [Master Blocks](#7-master-blocks)
8. [Conditions d'Affichage](#8-conditions-daffichage)
9. [Personnalisation des Styles](#9-personnalisation-des-styles)
10. [Bonnes Pratiques](#10-bonnes-pratiques)
11. [Dépannage](#11-dépannage)

---

## 1. Vue d'ensemble

### Qu'est-ce qu'un Template ?

Un **template** est un modèle de funnel pré-configuré avec des pages et des blocs prêts à l'emploi. Les templates accélèrent la création de funnels marketing en fournissant une structure éprouvée.

### Composants Clés

```
Template
   └── Funnel
         ├── Page 1 (Landing)
         │     ├── Bloc Titre
         │     ├── Bloc Vidéo
         │     └── Bloc Formulaire
         ├── Page 2 (Capture)
         │     └── ...
         └── Page 3 (Thank You)
               └── ...
```

### Templates Disponibles

| Template | Description | Pages | Cas d'usage |
|----------|-------------|-------|-------------|
| `OnlineTrainingTemplate` | Formation en ligne | 5 | Cours, formations |
| `DigitalBookTemplate` | Ebook/Guide | 5 | Lead magnets, ebooks |
| `CoachingProgramTemplate` | Programme coaching | 5 | Coaching, consulting |
| `MLMCommunityTemplate` | MLM & Communauté | 5 | Recrutement, équipes |
| `LeadCaptureTemplate` | Capture simple | 2 | Génération de leads |
| `FreeTrainingTemplate` | Formation gratuite | 3 | Webinaires gratuits |
| `WebinarTemplate` | Webinaire | 3 | Événements live |
| `ProductLaunchTemplate` | Lancement produit | 2 | Early bird, teasing |
| `QuizTemplate` | Quiz interactif | 5 | Qualification leads |

---

## 2. Architecture du Système

### Structure des Fichiers

```
app/
├── Services/
│   └── Template/
│       ├── AbstractFunnelTemplate.php   # Classe de base
│       ├── OnlineTrainingTemplate.php   # Template formation
│       ├── DigitalBookTemplate.php      # Template ebook
│       └── ...
├── Models/
│   ├── Funnel.php                       # Modèle funnel
│   ├── Page.php                         # Modèle page
│   └── Block.php                        # Modèle bloc
└── Enums/
    ├── BlockType.php                    # Types de blocs
    ├── PageType.php                     # Types de pages
    └── FunnelStatus.php                 # Statuts funnel

resources/views/
├── funnel/
│   ├── page.blade.php                   # Vue publique
│   └── blocks/                          # Vues des blocs
│       ├── title.blade.php
│       ├── pricing.blade.php
│       └── ...
└── livewire/page-builder/
    ├── page-builder.blade.php           # Page builder
    ├── block-editor.blade.php           # Éditeur de bloc
    ├── blocks/                          # Aperçus blocs
    └── editors/                         # Formulaires d'édition
```

### Classe de Base : AbstractFunnelTemplate

Tous les templates étendent `AbstractFunnelTemplate` qui fournit :

```php
// Méthodes abstraites à implémenter
abstract public function build(): Funnel;
abstract public function getCategory(): string;
abstract public function getName(): string;
abstract public function getDescription(): string;

// Méthodes utilitaires disponibles
protected function createFunnel(array $options = []): Funnel
protected function createPage(Funnel $funnel, array $options): Page
protected function createBlocks(Page $page, array $blocks): void

// Helpers pour créer des blocs
protected function heroTitle(int $order, string $title, array $styles = []): array
protected function subtitle(int $order, string $text): array
protected function ctaButton(int $order, string $text, array $options = []): array
protected function form(int $order, string $title, array $fields, string $buttonText): array
protected function video(int $order, string $url = null): array
protected function countdown(int $order, string $title, int $hours = 24): array
protected function pricing(int $order, array $plans): array
protected function faq(int $order, string $title, array $questions): array
protected function iconBox(int $order, string $icon, string $title, string $text): array
protected function trustBadges(int $order, array $badges): array
protected function socialIcons(int $order, array $platforms): array
protected function stickyBar(int $order, string $text, string $buttonText): array
protected function popup(int $order, string $title, array $options): array
protected function orderBump(int $order, string $title, string $description, string $price): array
protected function progress(int $order, int $percent, string $label = null): array
// ... et plus
```

---

## 3. Types de Blocs Disponibles

### Blocs de Contenu

| Type | Description | Propriétés principales |
|------|-------------|----------------------|
| `TITLE` | Titre accrocheur | `text`, `level` (h1-h6) |
| `TEXT` | Texte/paragraphe | `richText` (HTML) |
| `IMAGE` | Image | `url`, `alt`, `caption` |
| `VIDEO` | Vidéo YouTube/Vimeo | `url`, `autoplay` |
| `AUDIO` | Lecteur audio | `url`, `title` |

### Blocs d'Action

| Type | Description | Propriétés principales |
|------|-------------|----------------------|
| `BUTTON` | Bouton CTA | `text`, `url`, `style` |
| `FORM` | Formulaire capture | `fields[]`, `buttonText` |
| `COUNTDOWN` | Compte à rebours | `endDate`, `title` |

### Blocs de Mise en Page

| Type | Description | Propriétés principales |
|------|-------------|----------------------|
| `SECTION` | Conteneur section | `background`, `padding` |
| `COLUMNS` | Colonnes | `layout` (1/2,1/2 ou 1/3,2/3...) |
| `SPACER` | Espace vertical | `height` |
| `DIVIDER` | Ligne séparatrice | `style`, `width` |

### Blocs Avancés

| Type | Description | Propriétés principales |
|------|-------------|----------------------|
| `PRICING` | Tableau tarifs | `plans[]` avec features |
| `FAQ` | Accordéon Q&A | `questions[]` |
| `TESTIMONIAL` | Témoignage | `name`, `text`, `avatar` |
| `FEATURES` | Liste fonctionnalités | `items[]` |
| `ICON_BOX` | Icône + texte | `icon`, `title`, `text` |
| `PROGRESS` | Barre progression | `percent`, `label`, `color` |
| `TRUST_BADGES` | Badges confiance | `badges[]` |
| `SOCIAL_ICONS` | Réseaux sociaux | `platforms[]` |
| `ORDER_BUMP` | Upsell checkbox | `title`, `description`, `price` |
| `STICKY_BAR` | Barre fixe | `text`, `buttonText`, `position` |
| `POPUP` | Modal popup | `title`, `trigger`, `content` |
| `EMBED` | Code HTML embed | `code`, `aspectRatio` |

---

## 4. Créer un Nouveau Template

### Étape 1 : Créer la Classe Template

```php
<?php

namespace App\Services\Template;

use App\Enums\PageType;
use App\Models\Funnel;

class MonNouveauTemplate extends AbstractFunnelTemplate
{
    // =========================================================================
    // MÉTADONNÉES DU TEMPLATE
    // =========================================================================
    
    public function getCategory(): string
    {
        return 'mon_template';  // Identifiant unique
    }

    public function getIcon(): string
    {
        return '🎯';  // Emoji affiché dans la liste
    }

    public function getName(): string
    {
        return 'Mon Nouveau Template';
    }

    public function getDescription(): string
    {
        return 'Description courte expliquant le cas d\'usage de ce template.';
    }

    public function getTags(): array
    {
        return ['tag1', 'tag2', 'conversion'];
    }

    public function getPrimaryColor(): string
    {
        return '#3B82F6';  // Couleur principale
    }

    public function getSecondaryColor(): string
    {
        return '#1E40AF';  // Couleur secondaire
    }

    // =========================================================================
    // CONSTRUCTION DU FUNNEL
    // =========================================================================
    
    public function build(): Funnel
    {
        // Créer le funnel avec les options de base
        $funnel = $this->createFunnel([
            'meta_title' => 'Titre SEO de mon funnel',
            'meta_description' => 'Description SEO pour les moteurs de recherche.',
        ]);

        // Construire les pages
        $this->buildLandingPage($funnel);
        $this->buildCapturePage($funnel);
        $this->buildThankYouPage($funnel);

        return $funnel;
    }

    // =========================================================================
    // PAGE 1 : LANDING
    // =========================================================================
    
    private function buildLandingPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::LANDING,
            'title' => 'Page d\'Accueil',
            'sort_order' => 1,
        ]);

        $this->createBlocks($page, [
            // Les blocs utilisent le numéro d'ordre comme premier argument
            
            $this->heroTitle(1, 'TITRE PRINCIPAL ACCROCHEUR', [
                'color' => '#FBBF24',
            ]),

            $this->subtitle(2, 'Sous-titre expliquant le bénéfice principal'),

            $this->video(3, 'https://www.youtube.com/watch?v=XXXXX'),

            $this->features(4, '🎯 CE QUE VOUS ALLEZ APPRENDRE :', [
                ['title' => 'Bénéfice 1', 'text' => 'Description', 'icon' => 'heroicon-o-check'],
                ['title' => 'Bénéfice 2', 'text' => 'Description', 'icon' => 'heroicon-o-check'],
            ]),

            $this->ctaButton(5, 'ACCÉDER MAINTENANT 🚀', [], [
                'backgroundColor' => '#10B981',
            ]),
        ]);
    }

    // ... autres pages
}
```

### Étape 2 : Enregistrer le Template

Ajouter le template dans `TemplateGeneratorService.php` :

```php
use App\Services\Template\MonNouveauTemplate;

public function getAvailableTemplates(): array
{
    return [
        // ... autres templates
        'mon_template' => MonNouveauTemplate::class,
    ];
}
```

### Étape 3 : Créer les Vues Blade (si nouveaux types de blocs)

Si vous créez de nouveaux types de blocs, ajoutez les vues :

```
resources/views/funnel/blocks/mon_bloc.blade.php          # Vue publique
resources/views/livewire/page-builder/blocks/mon_bloc.blade.php   # Aperçu builder
resources/views/livewire/page-builder/editors/mon_bloc.blade.php  # Formulaire édition
```

---

## 5. Modifier un Template Existant

### Ajouter un Nouveau Bloc à une Page

```php
private function buildLandingPage(Funnel $funnel): void
{
    // ... code existant ...

    $this->createBlocks($page, [
        // Blocs existants...
        
        // Ajouter un nouveau bloc (attention à l'ordre)
        $this->trustBadges(10, [
            ['icon' => '✅', 'text' => '14 jours satisfait ou remboursé'],
            ['icon' => '🔒', 'text' => 'Paiement sécurisé'],
            ['icon' => '📧', 'text' => 'Support 24/7'],
        ]),
    ]);
}
```

### Modifier le Contenu d'un Bloc

```php
// Avant
$this->heroTitle(1, 'ANCIEN TITRE'),

// Après - avec options de style
$this->heroTitle(1, '🔥 NOUVEAU TITRE PLUS IMPACTANT', [
    'color' => '#EF4444',        // Couleur du texte
    'fontSize' => '3rem',         // Taille de police
    'textAlign' => 'center',      // Alignement
]),
```

### Ajouter une Nouvelle Page

```php
public function build(): Funnel
{
    $funnel = $this->createFunnel();

    $this->buildLandingPage($funnel);
    $this->buildCapturePage($funnel);
    $this->buildNewPage($funnel);      // Nouvelle page
    $this->buildThankYouPage($funnel);

    return $funnel;
}

private function buildNewPage(Funnel $funnel): void
{
    $page = $this->createPage($funnel, [
        'type' => PageType::CONTENT,   // ou UPSELL, DOWNSELL, etc.
        'title' => 'Ma Nouvelle Page',
        'sort_order' => 3,             // Position dans le funnel
    ]);

    $this->createBlocks($page, [
        // Blocs de la page...
    ]);
}
```

---

## 6. Utiliser le Page Builder

### Accéder au Page Builder

1. Aller dans **Filament Admin > Funnels**
2. Sélectionner un funnel
3. Cliquer sur une page
4. Cliquer sur **"Ouvrir le Builder"**

### Interface du Page Builder

```
┌─────────────────────────────────────────────────────────────────────┐
│  [Logo]  Ma Page  │  Desktop │ Tablet │ Mobile │  Aperçu │ Publier │
├──────┬──────────────────────────────────────────────┬───────────────┤
│      │                                              │               │
│  +   │              CANVAS PREVIEW                  │   ÉDITEUR     │
│      │                                              │   DE BLOC     │
│ Blocs│            [Blocs de la page]                │               │
│      │                                              │  - Contenu    │
│      │                                              │  - Design     │
│      │                                              │  - Master     │
│      │                                              │  - Conditions │
│      │                                              │               │
└──────┴──────────────────────────────────────────────┴───────────────┘
```

### Ajouter un Bloc

1. Cliquer sur le bouton **"+"** dans la barre latérale
2. Choisir l'onglet **"Blocs"** ou **"Master"**
3. Cliquer sur le type de bloc souhaité
4. Le bloc est ajouté et sélectionné

### Éditer un Bloc

1. Survoler un bloc dans le canvas
2. Cliquer sur **"Éditer"**
3. Modifier le contenu dans le panneau de droite
4. Cliquer sur **"Sauvegarder"**

### Réorganiser les Blocs

- **Glisser-déposer** : Utiliser la poignée de déplacement
- **Flèches** : Utiliser les boutons haut/bas

### Dupliquer/Supprimer

- **Dupliquer** : Icône de copie dans la toolbar du bloc
- **Supprimer** : Icône poubelle (avec confirmation)

---

## 7. Master Blocks

### Concept

Les **Master Blocks** sont des blocs réutilisables. Quand vous modifiez un master block, toutes ses instances sont automatiquement mises à jour.

**Cas d'usage :**
- Headers/Footers communs
- CTAs récurrents
- Sections de garantie
- Témoignages vedettes

### Créer un Master Block

1. Sélectionner un bloc existant
2. Ouvrir la section **"Master Block"**
3. Cliquer sur **"Convertir en Master Block"**
4. Donner un nom descriptif (ex: "CTA Principal", "Footer Standard")
5. Valider

### Utiliser un Master Block

1. Ouvrir le panneau d'ajout de blocs
2. Cliquer sur l'onglet **"Master"**
3. Sélectionner le master block
4. Une instance est créée sur la page

### Synchroniser les Instances

**Depuis le Master :**
```
Master Block (modifié)
   └── "Synchroniser X instances" → Met à jour toutes les instances
```

**Depuis une Instance :**
```
Instance
   └── "Synchroniser depuis le master" → Récupère les dernières modifications
```

### Détacher une Instance

Si vous voulez modifier une instance indépendamment :

1. Sélectionner l'instance
2. Ouvrir **"Master Block"**
3. Cliquer sur **"Détacher du master"**
4. L'instance devient un bloc normal indépendant

---

## 8. Conditions d'Affichage

### Concept

Les conditions d'affichage permettent de montrer/cacher des blocs selon des critères dynamiques (date, heure, appareil, URL, etc.).

### Types de Conditions

#### Temporelles
```php
// Plage de dates
['type' => 'date_range', 'start' => '2025-01-01', 'end' => '2025-12-31']

// Plage horaire
['type' => 'time_range', 'start' => '09:00', 'end' => '18:00']

// Jours de la semaine (0=Dim, 1=Lun, ..., 6=Sam)
['type' => 'day_of_week', 'days' => [1, 2, 3, 4, 5]]  // Lun-Ven
```

#### URL & Trafic
```php
// URL contient
['type' => 'url_contains', 'value' => 'utm_source=facebook']

// URL égale
['type' => 'url_equals', 'value' => '/offre-speciale']

// Référent contient
['type' => 'referrer_contains', 'value' => 'google.com']
```

#### Appareil
```php
// Type d'appareil
['type' => 'device', 'value' => 'mobile']   // mobile, tablet, desktop
```

#### Cookies & Visiteurs
```php
// Cookie existe
['type' => 'cookie_exists', 'name' => 'returning_visitor']

// Cookie égal à
['type' => 'cookie_equals', 'name' => 'segment', 'value' => 'premium']

// Nombre de visites
['type' => 'visitor_count', 'operator' => '>=', 'value' => 2]
```

#### Utilisateur
```php
// Utilisateur connecté
['type' => 'is_logged_in', 'value' => true]

// Rôle utilisateur (Spatie)
['type' => 'user_role', 'value' => 'premium']
```

#### Variables Contextuelles
```php
// Variable personnalisée (depuis session, quiz, etc.)
['type' => 'context_variable', 'key' => 'quiz_score', 'operator' => '>', 'value' => 50]
```

### Opérateurs de Combinaison

```php
// Toutes les règles doivent être vraies
['operator' => 'AND', 'rules' => [...]]

// Au moins une règle doit être vraie
['operator' => 'OR', 'rules' => [...]]
```

### Exemple Complet

```php
// Afficher un bloc de promotion uniquement :
// - En semaine (Lun-Ven)
// - Entre 9h et 18h
// - Pour les visiteurs venant de Facebook

$block->display_conditions = [
    'operator' => 'AND',
    'rules' => [
        ['type' => 'day_of_week', 'days' => [1, 2, 3, 4, 5]],
        ['type' => 'time_range', 'start' => '09:00', 'end' => '18:00'],
        ['type' => 'url_contains', 'value' => 'utm_source=facebook'],
    ]
];
```

### Ajouter via le Page Builder

1. Sélectionner un bloc
2. Ouvrir **"Conditions d'affichage"**
3. Cliquer sur **"Ajouter une condition"**
4. Choisir le type et configurer
5. Répéter pour ajouter d'autres règles

---

## 9. Personnalisation des Styles

### Styles Inline dans les Templates

```php
$this->heroTitle(1, 'MON TITRE', [
    // Typographie
    'color' => '#FFFFFF',
    'fontSize' => '3rem',
    'fontWeight' => 'bold',
    'textAlign' => 'center',
    'lineHeight' => '1.2',
    
    // Espacement
    'marginTop' => '20px',
    'marginBottom' => '20px',
    'paddingTop' => '10px',
    'paddingBottom' => '10px',
    
    // Fond & Bordures
    'backgroundColor' => '#1F2937',
    'borderRadius' => '8px',
    'boxShadow' => '0 4px 6px rgba(0,0,0,0.1)',
]);
```

### Styles Responsifs (Mobile)

```php
$block->mobile_styles = [
    'fontSize' => '1.5rem',      // Plus petit sur mobile
    'padding' => '10px',
    'textAlign' => 'left',
];
```

### Animation

```php
$block->animation = 'fade-in';    // fade-in, slide-up, zoom-in, bounce
$block->animation_delay = 200;    // Délai en ms
```

### Classes CSS Personnalisées

Dans les vues Blade, vous pouvez ajouter des classes Tailwind :

```blade
<div class="
    bg-gradient-to-r from-purple-500 to-pink-500
    hover:scale-105 transition-transform
    shadow-lg rounded-2xl
">
    {{ $content }}
</div>
```

---

## 10. Bonnes Pratiques

### Structure des Templates

✅ **DO:**
- Organiser les blocs par ordre logique (attention → intérêt → désir → action)
- Utiliser des noms de méthodes descriptifs (`buildCapturePage`, `buildUpsellPage`)
- Commenter les sections importantes
- Utiliser les helpers de `AbstractFunnelTemplate`

❌ **DON'T:**
- Coder en dur des valeurs spécifiques au client
- Créer des pages avec trop de blocs (> 15)
- Ignorer la version mobile
- Utiliser des images non optimisées

### Contenu Marketing

✅ **DO:**
- Titres avec bénéfices concrets et chiffres
- Urgence RÉELLE (pas de fausse rareté)
- Témoignages authentiques avec photos
- CTAs avec verbes d'action à la 1ère personne

❌ **DON'T:**
- Promesses impossibles
- Murs de texte sans structure
- Pop-ups agressifs multiples
- CTAs génériques ("Cliquez ici")

### Performance

```php
// Optimiser les images
$this->image(1, '/storage/images/hero.webp', [
    'alt' => 'Description accessible',
    'loading' => 'lazy',           // Chargement différé
    'width' => 800,
    'height' => 600,
]);

// Limiter les vidéos auto-play
$this->video(2, 'https://...', [
    'autoplay' => false,           // Laisser le choix à l'utilisateur
    'thumbnail' => '/path/to/thumbnail.jpg',
]);
```

### Master Blocks

✅ **DO:**
- Créer des masters pour les éléments récurrents
- Nommer clairement ("CTA Inscription Newsletter", "Footer Légal")
- Synchroniser régulièrement

❌ **DON'T:**
- Créer trop de masters (garder ceux vraiment réutilisables)
- Modifier un master sans vérifier l'impact
- Détacher sans raison valable

---

## 11. Dépannage

### Le template ne s'affiche pas

```bash
# Vérifier la syntaxe PHP
php -l app/Services/Template/MonTemplate.php

# Vider les caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### Bloc non trouvé

1. Vérifier que le type existe dans `BlockType.php`
2. Vérifier que la vue Blade existe dans `resources/views/funnel/blocks/`
3. Vérifier le nom exact (snake_case)

### Erreur de migration

```bash
# Voir les migrations en attente
php artisan migrate:status

# Exécuter les migrations
php artisan migrate

# Si erreur, rollback et retry
php artisan migrate:rollback
php artisan migrate
```

### Styles non appliqués

1. Vérifier la structure JSON des styles
2. Inspecter avec les DevTools du navigateur
3. Vérifier les conflits CSS (spécificité)

### Master Block non synchronisé

```php
// Forcer la synchronisation
$masterBlock = Block::find($id);
$count = $masterBlock->syncToInstances();
echo "{$count} instances mises à jour";
```

### Conditions non évaluées

1. Vérifier le format JSON des conditions
2. Vérifier que `shouldDisplay()` est appelé dans la vue
3. Tester les conditions individuellement

```php
// Debug
$block = Block::find($id);
$context = ['quiz_score' => 75];
dd($block->shouldDisplay($context)); // true/false
```

---

## 📞 Support

Pour toute question technique :
- **Documentation API** : `/docs/api`
- **Issues GitHub** : Créer une issue avec le label "template"
- **Email** : support@royal-leadmagnet.com

---

*Documentation mise à jour le 8 janvier 2026*
