<p align="center">
  <img src="public/images/logo.png" width="200" alt="Royal LeadMagnet Logo">
</p>

<h1 align="center">🎯 Royal LeadMagnet</h1>

<p align="center">
  <strong>Plateforme CRM de Génération & Gestion de Leads</strong>
</p>

<p align="center">
  <a href="#"><img src="https://img.shields.io/badge/version-1.0.0-blue.svg" alt="Version"></a>
  <a href="#"><img src="https://img.shields.io/badge/Laravel-12.x-red.svg" alt="Laravel"></a>
  <a href="#"><img src="https://img.shields.io/badge/Filament-4.x-orange.svg" alt="Filament"></a>
  <a href="#"><img src="https://img.shields.io/badge/PHP-8.2+-purple.svg" alt="PHP"></a>
  <a href="#"><img src="https://img.shields.io/badge/License-Proprietary-green.svg" alt="License"></a>
</p>

---

## 📋 Description

**Royal LeadMagnet** est une plateforme SaaS complète de génération et gestion de leads, conçue pour les entreprises africaines. Elle permet de créer des tunnels de vente (funnels), capturer des prospects, suivre leur engagement et automatiser les relances.

### 🎯 Fonctionnalités Principales

- 🚀 **Tunnels de Vente** - Créez des pages de capture avec vidéos, formulaires et CTAs
- 📊 **Tracking Avancé** - 23 champs par lead (device, géolocalisation, comportement)
- 🏷️ **Système de Tags** - Organisation et segmentation des leads
- 🤖 **Auto-Tagging** - Attribution automatique basée sur le comportement
- 📧 **Séquences Email** - Automatisation des relances
- 📋 **Pipeline Kanban** - Vue visuelle du parcours des leads
- 🔔 **Alertes Temps Réel** - Notifications pour les leads chauds
- 👥 **Multi-Commerciaux** - Attribution et suivi par équipe

---

## 🛠️ Stack Technique

| Technologie | Version | Usage |
|-------------|---------|-------|
| **Laravel** | 12.x | Framework PHP |
| **Filament** | 4.x | Admin Panel |
| **Livewire** | 3.x | Composants réactifs |
| **MySQL** | 8.x | Base de données |
| **Tailwind CSS** | 3.x | Styling |
| **Chart.js** | 4.x | Graphiques |
| **Sortable.js** | 1.x | Drag & Drop |

---

## 📁 Structure du Projet

```
app/
├── Console/Commands/        # Commandes Artisan
│   ├── CheckInactiveLeads.php
│   └── AttributeExistingLeads.php
├── Filament/
│   ├── Resources/           # CRUD Filament
│   │   ├── Leads/
│   │   ├── Tags/
│   │   ├── EmailSequences/
│   │   ├── Funnels/
│   │   └── Pages/
│   └── Widgets/             # Widgets Dashboard
├── Http/Controllers/
│   ├── CommercialDashboardController.php
│   ├── CommercialAlertsController.php
│   ├── FunnelController.php
│   └── TrackingApiController.php
├── Models/
│   ├── Lead.php
│   ├── Funnel.php
│   ├── Tag.php
│   ├── Alert.php
│   └── EmailSequence.php
├── Observers/               # Auto-tagging
│   ├── EventObserver.php
│   └── LeadObserver.php
└── Services/
    ├── GeolocationService.php
    ├── TrackingService.php
    ├── AlertService.php
    ├── ScoringService.php
    └── SubdomainService.php

resources/views/
├── commercial/              # Dashboard Commercial
│   ├── dashboard.blade.php
│   ├── leads/
│   │   ├── index.blade.php
│   │   └── kanban.blade.php
│   ├── alerts/
│   ├── funnels/
│   └── widgets/
└── funnel/                  # Pages Publiques
    └── page.blade.php
```

---

## 🚀 Installation

### Prérequis

- PHP 8.2+
- Composer 2.x
- Node.js 18+
- MySQL 8.x

### Installation Locale

```bash
# Cloner le repository
git clone https://github.com/your-org/royal-leadmagnet.git
cd royal-leadmagnet

# Installer les dépendances PHP
composer install

# Installer les dépendances JS
npm install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Configurer la base de données dans .env
# DB_DATABASE=royal_leadmagnet
# DB_USERNAME=root
# DB_PASSWORD=

# Exécuter les migrations
php artisan migrate --seed

# Compiler les assets
npm run build

# Lancer le serveur
php artisan serve
```

### Configuration Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://royalleadpro.com

# Wildcard Subdomains
APP_SUBDOMAIN_BASE=royalleadpro.com
SESSION_DOMAIN=.royalleadpro.com

# Géolocalisation (optionnel - 4 APIs fallback incluses)
# IPAPI_KEY=your_key
```

---

## 📊 Fonctionnalités Détaillées

### 1. Tracking Enrichi (23 champs)

```
Device & Navigateur:
├── device_type (mobile/tablet/desktop)
├── browser + browser_version
├── os + os_version
├── screen_resolution
└── language

Géolocalisation:
├── country, city, region
├── timezone
├── latitude, longitude
└── ip_address

Comportement:
├── time_on_page
├── scroll_depth
├── events_count
└── last_activity_at
```

### 2. Auto-Tagging

| Trigger | Tag Assigné |
|---------|-------------|
| Vidéo vue 100% | `video_complete` |
| Clic WhatsApp | `whatsapp_click` |
| Score ≥ 31 | `hot_lead` |
| Score ≥ 61 | `ultra_hot` |
| Inactif 7 jours | `relance_7j` |

### 3. Scoring Automatique

| Action | Points |
|--------|--------|
| Page vue | +1 |
| Vidéo 50% | +5 |
| Vidéo 100% | +15 |
| Formulaire soumis | +10 |
| Clic WhatsApp | +25 |
| Achat | +100 |

---

## 🔧 Commandes Artisan

```bash
# Vérifier les leads inactifs (cron quotidien)
php artisan leads:check-inactive

# Attribuer les leads existants aux commerciaux
php artisan leads:attribute-existing

# Vider le cache de géolocalisation
php artisan cache:clear
```

### Scheduler (Crontab)

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🌐 URLs & Routes

### Admin Filament
```
/admin                    → Dashboard Admin
/admin/leads              → Gestion Leads
/admin/tags               → Gestion Tags
/admin/email-sequences    → Séquences Email
/admin/funnels            → Tunnels de Vente
```

### Dashboard Commercial
```
/commercial/dashboard     → Dashboard Commercial
/commercial/leads         → Liste Leads (+ Kanban)
/commercial/alerts        → Alertes
/commercial/funnels       → Configuration Liens
/commercial/profile       → Profil
```

### Pages Publiques (Funnels)
```
/f/{slug}                           → Funnel par slug
{subdomain}.royalleadpro.com        → Funnel par sous-domaine
```

### API Tracking
```
POST /api/track/event     → Enregistrer événement
POST /api/track/pageview  → Enregistrer page vue
```

---

## 📈 Dashboard Analytics

### 6 Widgets Disponibles

1. **LeadOverviewWidget** - 4 KPIs globaux
2. **TagsStatsWidget** - Stats tags auto/manuels
3. **EngagementStatsWidget** - Courbes engagement 7j
4. **LeadsByDeviceWidget** - Répartition devices
5. **ConversionByCountryWidget** - Top 10 pays
6. **RecentActivityWidget** - 50 derniers événements

---

## 🔐 Rôles & Permissions

| Rôle | Accès |
|------|-------|
| **Super Admin** | Tout accès |
| **Admin** | Filament complet |
| **Commercial** | Dashboard commercial uniquement |

---

## 📝 Documentation

| Document | Description |
|----------|-------------|
| `audit.md` | Audit 360° du projet |
| `LEAD_ATTRIBUTION_GUIDE.md` | Guide attribution leads |
| `CONFIGURATION_WILDCARD_ROYALLEADPRO.md` | Config subdomains |
| `docs/IMPLEMENTATION_PLAN.md` | Plan d'implémentation |

---

## 🚀 Déploiement

### FTP (Script inclus)

```bash
./deploy.sh
```

### Manuel

```bash
# Sur le serveur
git pull origin main
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

---

## 🐛 Troubleshooting

### Géolocalisation échoue (429)

Les APIs de géolocalisation ont des limites. Le système utilise 4 fallbacks :
1. ipapi.co (1000/jour)
2. ip-api.com (45/min)
3. ipwho.is (illimité)
4. freeipapi.com (60/min)

### Leads non visibles pour commerciaux

```bash
php artisan leads:attribute-existing
```

### Cache géolocalisation

```bash
php artisan cache:clear
```

---

## 📞 Support

- **Email** : support@royalleadpro.com
- **Documentation** : Voir dossier `docs/`

---

## 📄 License

Ce projet est sous licence propriétaire. Tous droits réservés.

---

<p align="center">
  Made with ❤️ by <strong>Genius Groups</strong>
</p>

<p align="center">
  <sub>Royal LeadMagnet v1.0.0 - Janvier 2026</sub>
</p>
