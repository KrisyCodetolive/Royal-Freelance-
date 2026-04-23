# Royal LeadMagnet - Architecture Data Layer Back-Office Admin

## Vue d'Ensemble

Architecture **SaaS-ready**, **évolutive** et **scalable** avec Filament 4 + Laravel 12.

---

## 1. Architecture Multi-Tenant

```
┌─────────────────────────────────────────────────────────────┐
│                    ARCHITECTURE SAAS                        │
├─────────────────────────────────────────────────────────────┤
│  Tenant (Organization)                                      │
│  ├── Users (Admin, Manager, Commercial)                     │
│  ├── Offers (Formations, Livres, Programmes)               │
│  ├── Funnels → Pages → Blocks                              │
│  ├── Leads → Events, Tags, Alerts                          │
│  └── Settings (Branding, Scoring, Notifications)           │
└─────────────────────────────────────────────────────────────┘
```

### Stratégie Multi-Tenancy
- **Column-based**: Chaque table avec `tenant_id`
- **Global Scope** automatique via Trait `BelongsToTenant`
- **Middleware** pour injection automatique du tenant

---

## 2. Modèles Eloquent

### 2.1 Diagramme Relationnel

```
Tenant (1) ─────┬───── (N) User
                ├───── (N) Offer ──── (1) ─── (N) Funnel
                ├───── (N) Tag                    │
                ├───── (N) ScoringRule            │
                └───── (N) Template               │
                                                  │
Funnel (1) ─────┬───── (N) Page ──── (1) ─── (N) Block
                └───── (N) Lead
                             │
Lead (1) ───────┬───── (N) Event
                ├───── (N) LeadTag (pivot)
                ├───── (N) Note
                └───── (N) Alert
```

### 2.2 Liste des Modèles

| Modèle | Description | Traits |
|--------|-------------|--------|
| `Tenant` | Organisation/Client SaaS | HasUuid, HasSlug |
| `User` | Utilisateurs (Admin/Manager/Commercial) | BelongsToTenant, HasRoles |
| `Offer` | Formations, Livres, Programmes | BelongsToTenant, HasMedia |
| `Funnel` | Tunnel de vente | BelongsToTenant, HasSlug, HasStatus |
| `Page` | Pages du tunnel | Sortable, HasBlocks |
| `Block` | Blocs de contenu | Sortable, HasSettings |
| `Lead` | Prospects | BelongsToTenant, HasScore, Trackable |
| `Event` | Événements tracking | - |
| `Tag` | Tags (auto/manuels) | BelongsToTenant |
| `Alert` | Notifications/Alertes | BelongsToTenant |
| `ScoringRule` | Règles de scoring | BelongsToTenant |
| `Template` | Templates messages | BelongsToTenant |
| `Note` | Notes sur prospects | - |

---

## 3. Enums et Value Objects

### 3.1 Enums (app/Enums/)

```php
// UserRole.php
enum UserRole: string {
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case COMMERCIAL = 'commercial';
}

// OfferType.php
enum OfferType: string {
    case FORMATION = 'formation';
    case LIVRE = 'livre';
    case PROGRAMME = 'programme';
    case MLM = 'mlm';
}

// FunnelStatus.php
enum FunnelStatus: string {
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case ARCHIVED = 'archived';
}

// PageType.php
enum PageType: string {
    case CAPTURE = 'capture';
    case THANK_YOU = 'thank_you';
    case PRESENTATION = 'presentation';
    case MODALITIES = 'modalities';
    case FAQ = 'faq';
    case TESTIMONIALS = 'testimonials';
    case PAYMENT = 'payment';
}

// BlockType.php
enum BlockType: string {
    case TITLE = 'title';
    case TEXT = 'text';
    case IMAGE = 'image';
    case VIDEO = 'video';
    case BUTTON = 'button';
    case FORM = 'form';
    case SPACER = 'spacer';
}

// LeadStatus.php
enum LeadStatus: string {
    case COLD = 'cold';
    case WARM = 'warm';
    case HOT = 'hot';
    case ULTRA_HOT = 'ultra_hot';
    case CLIENT = 'client';
    case MEMBER = 'member';
}

// EventType.php
enum EventType: string {
    case PAGE_VIEW = 'page_view';
    case FORM_SUBMIT = 'form_submit';
    case VIDEO_PLAY = 'video_play';
    case VIDEO_25 = 'video_25';
    case VIDEO_50 = 'video_50';
    case VIDEO_75 = 'video_75';
    case VIDEO_100 = 'video_100';
    case CTA_CLICK = 'cta_click';
    case WHATSAPP_CLICK = 'whatsapp_click';
    case PAYMENT_CLICK = 'payment_click';
    case CONVERSION = 'conversion';
}

// AlertType.php
enum AlertType: string {
    case HOT_LEAD = 'hot_lead';
    case ENGAGED = 'engaged';
    case WHATSAPP_CLICK = 'whatsapp_click';
    case INACTIVE_7_DAYS = 'inactive_7_days';
    case INACTIVE_14_DAYS = 'inactive_14_days';
    case NEW_REGISTRATION = 'new_registration';
}
```

---

## 4. Traits Réutilisables (app/Traits/)

### 4.1 BelongsToTenant
```php
trait BelongsToTenant {
    protected static function bootBelongsToTenant(): void {
        static::creating(fn($model) => 
            $model->tenant_id ??= auth()->user()?->tenant_id
        );
        
        static::addGlobalScope('tenant', fn($query) => 
            $query->where('tenant_id', auth()->user()?->tenant_id)
        );
    }
    
    public function tenant(): BelongsTo {
        return $this->belongsTo(Tenant::class);
    }
}
```

### 4.2 HasScore
```php
trait HasScore {
    public function recalculateScore(): void {
        $rules = ScoringRule::where('tenant_id', $this->tenant_id)->get();
        $score = 0;
        
        foreach ($rules as $rule) {
            $count = $this->events()->where('type', $rule->event_type)->count();
            $score += $count * $rule->points;
        }
        
        $this->update(['score' => $score]);
        $this->updateStatusFromScore();
    }
    
    protected function updateStatusFromScore(): void {
        $thresholds = $this->tenant->settings['scoring_thresholds'] ?? [
            'cold' => 0, 'warm' => 11, 'hot' => 31, 'ultra_hot' => 61
        ];
        // Logic pour déterminer le statut basé sur le score
    }
}
```

### 4.3 Trackable
```php
trait Trackable {
    public function track(EventType $type, array $data = []): Event {
        return $this->events()->create([
            'type' => $type,
            'data' => $data,
            'page_id' => $data['page_id'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
```

---

## 5. Structure des Fichiers

```
app/
├── Enums/
│   ├── UserRole.php
│   ├── OfferType.php
│   ├── FunnelStatus.php
│   ├── PageType.php
│   ├── BlockType.php
│   ├── LeadStatus.php
│   ├── EventType.php
│   └── AlertType.php
├── Models/
│   ├── Tenant.php
│   ├── User.php
│   ├── Offer.php
│   ├── Funnel.php
│   ├── Page.php
│   ├── Block.php
│   ├── Lead.php
│   ├── Event.php
│   ├── Tag.php
│   ├── LeadTag.php
│   ├── Alert.php
│   ├── ScoringRule.php
│   ├── Template.php
│   └── Note.php
├── Traits/
│   ├── BelongsToTenant.php
│   ├── HasScore.php
│   ├── Trackable.php
│   └── HasSettings.php
├── Services/
│   ├── ScoringService.php
│   ├── AlertService.php
│   ├── TrackingService.php
│   └── FunnelDuplicationService.php
├── Observers/
│   ├── LeadObserver.php
│   ├── EventObserver.php
│   └── FunnelObserver.php
└── Filament/
    ├── Resources/
    │   ├── TenantResource.php
    │   ├── UserResource.php
    │   ├── OfferResource.php
    │   ├── FunnelResource.php
    │   ├── PageResource.php
    │   ├── LeadResource.php
    │   ├── TagResource.php
    │   └── AlertResource.php
    ├── Pages/
    │   ├── Dashboard.php
    │   └── Settings.php
    └── Widgets/
        ├── StatsOverview.php
        ├── FunnelPerformance.php
        └── RecentAlerts.php
```

---

## 6. Migrations Clés

### 6.1 tenants
```php
Schema::create('tenants', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('domain')->nullable()->unique();
    $table->json('settings')->nullable(); // branding, scoring_thresholds, etc.
    $table->json('branding')->nullable(); // logo, colors, fonts
    $table->timestamp('trial_ends_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

### 6.2 funnels
```php
Schema::create('funnels', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
    $table->foreignId('offer_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('status')->default('draft'); // FunnelStatus enum
    $table->string('template')->nullable(); // Template utilisé
    $table->string('whatsapp_url')->nullable();
    $table->string('payment_url')->nullable();
    $table->json('branding')->nullable(); // Override tenant branding
    $table->json('settings')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['tenant_id', 'status']);
    $table->index(['tenant_id', 'assigned_to']);
});
```

### 6.3 leads
```php
Schema::create('leads', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
    $table->foreignId('funnel_id')->constrained()->cascadeOnDelete();
    $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
    $table->string('email')->nullable();
    $table->string('phone')->nullable();
    $table->string('first_name')->nullable();
    $table->string('last_name')->nullable();
    $table->string('source')->nullable(); // utm_source
    $table->string('medium')->nullable(); // utm_medium
    $table->string('campaign')->nullable(); // utm_campaign
    $table->integer('score')->default(0);
    $table->string('status')->default('cold'); // LeadStatus enum
    $table->json('custom_fields')->nullable();
    $table->timestamp('last_activity_at')->nullable();
    $table->timestamp('converted_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['tenant_id', 'status']);
    $table->index(['tenant_id', 'funnel_id']);
    $table->index(['tenant_id', 'score']);
    $table->index(['email']);
    $table->index(['phone']);
});
```

### 6.4 events
```php
Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
    $table->foreignId('page_id')->nullable()->constrained()->nullOnDelete();
    $table->string('type'); // EventType enum
    $table->json('data')->nullable();
    $table->string('ip_address')->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamps();
    
    $table->index(['lead_id', 'type']);
    $table->index(['lead_id', 'created_at']);
});
```

---

## 7. Services Layer

### 7.1 ScoringService
```php
class ScoringService {
    public function calculateScore(Lead $lead): int;
    public function updateLeadStatus(Lead $lead): void;
    public function getThresholds(Tenant $tenant): array;
}
```

### 7.2 AlertService
```php
class AlertService {
    public function checkAndCreateAlerts(Lead $lead, Event $event): void;
    public function notifyUsers(Alert $alert): void;
    public function markAsRead(Alert $alert): void;
}
```

### 7.3 FunnelDuplicationService
```php
class FunnelDuplicationService {
    public function duplicate(Funnel $funnel, array $overrides = []): Funnel;
    public function duplicatePages(Funnel $source, Funnel $target): void;
    public function duplicateBlocks(Page $source, Page $target): void;
}
```

---

## 8. Packages Requis

```json
{
    "require": {
        "filament/filament": "^4.0",
        "spatie/laravel-permission": "^6.0",
        "spatie/laravel-medialibrary": "^11.0",
        "spatie/laravel-activitylog": "^4.0",
        "spatie/laravel-sluggable": "^3.0",
        "stancl/tenancy": "^3.0" // optionnel si multi-tenant avancé
    }
}
```

---

## 9. Plan de Vérification

### Tests Automatisés
- Tests unitaires pour les Services
- Tests de Feature pour les Filament Resources
- Tests d'intégration pour le scoring et les alertes

### Vérification Manuelle
- CRUD complet via Filament
- Duplication de tunnels
- Calcul automatique du scoring
- Génération d'alertes

---

## Prochaines Étapes

1. **Valider cette architecture** avec le client
2. **Installer les dépendances** (Filament 4, Spatie packages)
3. **Créer les migrations** dans l'ordre des dépendances
4. **Implémenter les modèles** avec leurs traits
5. **Créer les Filament Resources**
