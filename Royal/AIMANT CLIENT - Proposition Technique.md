# AIMANT CLIENT
## Plateforme de Tunnels de Vente & Prospection Automatisée

---

**Porteur de projet** : Royal  
**Date de réception** : 24 Décembre 2024  
**Échéance souhaitée** : MVP en 8-10 semaines

---

# 1. SYNTHÈSE DU PROJET

## 1.1 Concept

**AIMANT CLIENT** est une plateforme SaaS permettant de créer, dupliquer et piloter des tunnels de vente/prospection pour :
- **Formations** en ligne
- **Livres** et produits numériques
- **Programmes** (communautés, coaching, MLM)

## 1.2 Principe de fonctionnement

```
┌─────────────────────────────────────────────────────────────────┐
│                    PARCOURS PROSPECT                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   1. CAPTURE          2. NURTURING         3. CONVERSION        │
│   ┌─────────┐        ┌─────────────┐       ┌─────────────┐     │
│   │  Page   │───────►│   Vidéos    │──────►│  WhatsApp   │     │
│   │ Capture │        │  1 à 5      │       │  + Paiement │     │
│   │(Formulaire)      │  (Playlist) │       │             │     │
│   └─────────┘        └─────────────┘       └─────────────┘     │
│        │                   │                     │              │
│        ▼                   ▼                     ▼              │
│   ┌─────────┐        ┌─────────────┐       ┌─────────────┐     │
│   │  Page   │        │   Emails    │       │  Adhésion   │     │
│   │  Merci  │        │  Séquence   │       │  / Achat    │     │
│   │(Vidéo 0)│        │  Auto       │       │             │     │
│   └─────────┘        └─────────────┘       └─────────────┘     │
│                                                                 │
│   ════════════════════════════════════════════════════════════ │
│                    TRACKING COMPLET                             │
│   Pages vues • Vidéos % • Clics • Emails • Tags • Score        │
└─────────────────────────────────────────────────────────────────┘
```

## 1.3 Différenciation clé

| Aspect | Outils existants (ClickFunnels, Systeme.io) | **AIMANT CLIENT** |
|--------|---------------------------------------------|-------------------|
| **Cible** | Mondiale, anglophone | **Afrique francophone** |
| **Paiement** | Stripe uniquement | **Mobile Money + GeniusPay** |
| **WhatsApp** | Non intégré | **Intégration native** |
| **Scoring** | Basique ou absent | **Scoring intelligent + alertes** |
| **Prix** | $97-297/mois | **Abordable Afrique** |
| **Langue** | Anglais | **Français natif** |

---

# 2. ARCHITECTURE SYSTÈME

## 2.1 Vue d'ensemble des interfaces

```
┌─────────────────────────────────────────────────────────────────┐
│                      AIMANT CLIENT                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   INTERFACES UTILISATEURS                                       │
│   ┌─────────────────┐  ┌─────────────────┐  ┌───────────────┐  │
│   │   Back-Office   │  │   Dashboard     │  │   Pages       │  │
│   │   Administrateur│  │   Commercial    │  │   Publiques   │  │
│   │                 │  │                 │  │   (Tunnels)   │  │
│   └────────┬────────┘  └────────┬────────┘  └───────┬───────┘  │
│            │                    │                   │          │
│            └────────────────────┼───────────────────┘          │
│                                 │                              │
│   API GATEWAY              ┌────▼────┐                         │
│                            │   API   │                         │
│                            │  REST   │                         │
│                            └────┬────┘                         │
│                                 │                              │
│   SERVICES                      │                              │
│   ┌──────────┐  ┌──────────┐  ┌▼─────────┐  ┌──────────┐      │
│   │  Auth    │  │ Tunnels  │  │  Leads   │  │ Tracking │      │
│   │ Service  │  │ Service  │  │ Service  │  │ Service  │      │
│   └──────────┘  └──────────┘  └──────────┘  └──────────┘      │
│                                                                 │
│   ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐      │
│   │  Email   │  │ WhatsApp │  │ Scoring  │  │ Alertes  │      │
│   │ Service  │  │ Service  │  │ Service  │  │ Service  │      │
│   └──────────┘  └──────────┘  └──────────┘  └──────────┘      │
│                                                                 │
│   DONNÉES                                                       │
│   ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│   │ PostgreSQL  │  │    Redis    │  │ Cloudflare  │            │
│   │   (Data)    │  │(Cache/Queue)│  │  R2 (Media) │            │
│   └─────────────┘  └─────────────┘  └─────────────┘            │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 2.2 Les 3 interfaces principales

### A. Back-Office Administrateur
**Rôle** : Gestion globale de la plateforme et création des tunnels

| Fonctionnalité | Description |
|----------------|-------------|
| Gestion tunnels | Créer, modifier, dupliquer, archiver |
| Page Builder | Éditeur visuel de pages (blocs) |
| Gestion offres | Formations, livres, programmes |
| Gestion équipe | Créer comptes commerciaux, assigner tunnels |
| Analytics global | Vue d'ensemble tous tunnels |
| Configuration | Branding, paiements, emails, WhatsApp |
| Bibliothèque médias | Upload vidéos, images, PDFs |

### B. Dashboard Freelance
**Rôle** : Suivi et gestion des prospects par commercial

| Fonctionnalité | Description |
|----------------|-------------|
| Mes tunnels | Liste des tunnels assignés |
| Pipeline leads | Prospects par statut (Froid → Client) |
| Fiches prospects | Historique complet, tags, score |
| Alertes | Prospects chauds à contacter |
| Relances | Planification emails/WhatsApp |
| Statistiques | Performance personnelle |
| Actions rapides | Changer statut, ajouter tag, noter |

### C. Pages Publiques (Tunnels)
**Rôle** : Pages visibles par les prospects

| Page | Contenu |
|------|---------|
| Page Capture | Formulaire (nom, email, téléphone) |
| Page Merci | Vidéo 0 + message bienvenue |
| Pages Présentation | Vidéos 1-5 en playlist |
| Page Modalités | Vidéo 6 + boutons CTA |
| Page FAQ | Questions fréquentes |
| Page Témoignages | Avis clients |
| Page Paiement | Intégration paiement |

---

# 3. PÉRIMÈTRE FONCTIONNEL MVP

## 3.1 Module Tunnels (Back-Office Admin)

### Fonctionnalités MVP
- ✅ **CRUD Tunnels** : Créer, modifier, dupliquer, archiver
- ✅ **Templates prédéfinis** : MLM, Formation, Livre
- ✅ **Configuration tunnel** :
  - Nom, description, offre associée
  - Prix de l'offre
  - Lien WhatsApp
  - Lien paiement externe
  - Branding (couleurs, logo)
- ✅ **Statuts tunnel** : Brouillon, Actif, Pausé, Archivé
- ✅ **Lien unique** par tunnel (URL personnalisable)
- ✅ **Duplication rapide** avec modification

### Fonctionnalités V2
- ⏳ A/B testing pages
- ⏳ Domaines personnalisés
- ⏳ Tunnels conditionnels (branches)

## 3.2 Page Builder (Back-Office Admin)

### Fonctionnalités MVP
- ✅ **Éditeur par blocs** :
  - Titre (H1, H2, H3)
  - Texte (paragraphe, liste)
  - Image (upload ou URL)
  - Vidéo (YouTube, Vimeo, upload)
  - Bouton CTA (texte, lien, couleur)
  - Formulaire (champs configurables)
  - Espacement / Séparateur
- ✅ **Personnalisation visuelle** :
  - Couleurs (fond, texte, boutons)
  - Logo et favicon
  - Police (sélection limitée)
- ✅ **Responsive mobile** automatique
- ✅ **Prévisualisation** temps réel
- ✅ **Pages types** :
  - Page Capture (formulaire obligatoire)
  - Page Merci (vidéo + texte)
  - Page Présentation (playlist vidéos)
  - Page Modalités (vidéo + CTA WhatsApp)

### Fonctionnalités V2
- ⏳ Drag & drop avancé
- ⏳ Animations et effets
- ⏳ Compte à rebours / Urgence
- ⏳ Pop-ups et exit intent

## 3.3 Gestion Leads & Pipeline

### Fonctionnalités MVP
- ✅ **Liste prospects** par tunnel (filtrable, triable)
- ✅ **Fiche prospect complète** :
  - Informations : nom, email, téléphone, source
  - Tags assignés
  - Score actuel
  - Statut : FROID / TIÈDE / CHAUD / ULTRA CHAUD / CLIENT / MEMBRE
  - Historique actions (timeline)
- ✅ **Actions sur prospect** :
  - Changer statut manuellement
  - Ajouter/retirer tags
  - Ajouter note
  - Envoyer vers WhatsApp
- ✅ **Import/Export CSV**
- ✅ **Recherche** par nom, email, téléphone
- ✅ **Filtres** : statut, tags, score, date, source

### Fonctionnalités V2
- ⏳ Vue Kanban (drag & drop statuts)
- ⏳ Segments dynamiques
- ⏳ Fusion de doublons

## 3.4 Tracking & Analytics

### Événements trackés automatiquement (MVP)
| Événement | Détail |
|-----------|--------|
| `page_view` | Visite d'une page du tunnel |
| `form_submit` | Soumission formulaire capture |
| `video_play` | Lecture vidéo démarrée |
| `video_25` | Vidéo vue à 25% |
| `video_50` | Vidéo vue à 50% |
| `video_75` | Vidéo vue à 75% |
| `video_100` | Vidéo complétée |
| `cta_click` | Clic sur bouton CTA |
| `whatsapp_click` | Clic bouton WhatsApp |
| `payment_click` | Clic bouton paiement |
| `conversion` | Achat/adhésion confirmé |

### Dashboard Analytics (MVP)
- ✅ **Par tunnel** :
  - Trafic (visiteurs uniques, pages vues)
  - Taux conversion capture
  - Taux visionnage par vidéo (graphique)
  - Taux clic WhatsApp
  - Taux conversion finale
  - Répartition prospects par statut
  - Top sources (UTM tracking)
- ✅ **Global** :
  - Vue d'ensemble tous tunnels
  - Comparaison performances
  - Tendances (jour, semaine, mois)

### Fonctionnalités V2
- ⏳ Intégration Pixel Meta
- ⏳ Google Tag Manager
- ⏳ Rapports exportables PDF

## 3.5 Scoring & Tags Automatiques

### Système de scoring (MVP)

| Action | Points |
|--------|--------|
| Visite page capture | +1 |
| Inscription (formulaire) | +10 |
| Vidéo vue 50% | +5 |
| Vidéo complétée | +15 |
| Clic WhatsApp | +25 |
| Réponse WhatsApp (manuel) | +20 |
| Achat/adhésion | +100 |

### Seuils de statut (configurables)

| Score | Statut |
|-------|--------|
| 0–10 | FROID |
| 11–30 | TIÈDE |
| 31–60 | CHAUD |
| 61+ | ULTRA CHAUD |

### Tags automatiques (MVP)
- ✅ Tag selon source (Facebook, TikTok, WhatsApp, etc.)
- ✅ Tag selon tunnel
- ✅ Tag selon actions (ex: "a_vu_video_3", "clic_whatsapp")
- ✅ Tags manuels par commercial

## 3.6 Alertes & Notifications

### Alertes automatiques (MVP)
| Déclencheur | Notification |
|-------------|--------------|
| Prospect devient CHAUD | Push + Email admin/commercial |
| 3 vidéos vues en 24h | Alerte "Prospect engagé" |
| Clic WhatsApp | Alerte immédiate |
| Inactif 7 jours | Alerte relance |
| Inactif 14 jours | Alerte urgente |
| Nouvelle inscription | Notification temps réel |

### Canaux de notification (MVP)
- ✅ Notifications in-app (dashboard)
- ✅ Email (admin et commerciaux)
- ✅ Push navigateur (optionnel)

### Fonctionnalités V2
- ⏳ Notifications WhatsApp
- ⏳ Alertes SMS
- ⏳ Webhooks personnalisés

## 3.7 Relances (Semi-automatiques)

### Fonctionnalités MVP
- ✅ **Bouton "Relancer"** sur fiche prospect
- ✅ **Templates messages** prédéfinis (email + WhatsApp)
- ✅ **Lien WhatsApp** avec message prérempli
- ✅ **Historique relances** sur fiche prospect
- ✅ **Liste "À relancer"** (prospects inactifs)

### Fonctionnalités V2
- ⏳ Séquences emails automatiques par tunnel
- ⏳ Automation complète (si X alors Y)
- ⏳ Intégration WhatsApp Business API

---

# 4. DASHBOARD COMMERCIAL (Détail)

## 4.1 Vue d'ensemble

```
┌─────────────────────────────────────────────────────────────────┐
│  AIMANT CLIENT - Dashboard Commercial                           │
│  Bonjour, Jean 👋                    [Notifications 🔔 3]       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  📊 MES STATISTIQUES (Ce mois)                                  │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐        │
│  │   45     │  │   12     │  │   8      │  │  26%     │        │
│  │ Nouveaux │  │  Chauds  │  │ Convertis│  │ Taux conv│        │
│  │ Prospects│  │          │  │          │  │          │        │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘        │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│  🔥 ALERTES DU JOUR (3)                                         │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ 🔴 Marie Dupont - ULTRA CHAUD - Clic WhatsApp il y a 5min│   │
│  │ 🟠 Paul Martin - CHAUD - 3 vidéos vues aujourd'hui       │   │
│  │ 🟡 Sophie Koffi - TIÈDE - Inactif depuis 7 jours         │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│  📁 MES TUNNELS                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │ Formation MLM   │  │ Livre Leadership│  │ Coaching Pro    │ │
│  │ 23 prospects    │  │ 15 prospects    │  │ 7 prospects     │ │
│  │ 3 chauds        │  │ 2 chauds        │  │ 1 chaud         │ │
│  │ [Voir détails]  │  │ [Voir détails]  │  │ [Voir détails]  │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│  👥 PIPELINE PROSPECTS                                          │
│  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐       │
│  │ FROID  │ │ TIÈDE  │ │ CHAUD  │ │ ULTRA  │ │ CLIENT │       │
│  │   18   │ │   12   │ │   8    │ │   4    │ │   3    │       │
│  └────────┘ └────────┘ └────────┘ └────────┘ └────────┘       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 4.2 Fonctionnalités spécifiques commercial

| Fonctionnalité | Description |
|----------------|-------------|
| **Vue "Mes tunnels"** | Uniquement les tunnels assignés |
| **Pipeline visuel** | Répartition prospects par statut |
| **Alertes prioritaires** | Prospects chauds à contacter en premier |
| **Actions rapides** | Contacter WhatsApp, changer statut, noter |
| **Historique personnel** | Mes actions, mes conversions |
| **Objectifs** | Suivi objectifs mensuels (optionnel) |

---

# 5. BACK-OFFICE ADMINISTRATEUR (Détail)

## 5.1 Vue d'ensemble

```
┌─────────────────────────────────────────────────────────────────┐
│  AIMANT CLIENT - Administration                                 │
│  [Dashboard] [Tunnels] [Leads] [Équipe] [Médias] [Config]      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  📊 VUE GLOBALE                                                 │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐        │
│  │   5      │  │  156     │  │   32     │  │ 2.4M     │        │
│  │ Tunnels  │  │ Prospects│  │ Clients  │  │ CA FCFA  │        │
│  │ actifs   │  │ total    │  │ total    │  │ ce mois  │        │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘        │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│  📈 PERFORMANCE TUNNELS                                         │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ Tunnel            │ Visiteurs │ Inscrits │ Conv. │ CA   │   │
│  │───────────────────│───────────│──────────│───────│──────│   │
│  │ Formation MLM     │    450    │    89    │  12%  │ 1.2M │   │
│  │ Livre Leadership  │    280    │    45    │   8%  │ 450K │   │
│  │ Coaching Pro      │    120    │    22    │  15%  │ 750K │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│  👥 ÉQUIPE COMMERCIALE                                          │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ Commercial   │ Tunnels │ Prospects │ Conversions │ Perf │   │
│  │──────────────│─────────│───────────│─────────────│──────│   │
│  │ Jean Mbeki   │    2    │    45     │     8       │ 18%  │   │
│  │ Marie Ondo   │    2    │    38     │     6       │ 16%  │   │
│  │ Paul Nguema  │    1    │    22     │     3       │ 14%  │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 5.2 Fonctionnalités administrateur

| Module | Fonctionnalités |
|--------|-----------------|
| **Tunnels** | CRUD complet, templates, duplication, statistiques |
| **Page Builder** | Éditeur visuel, prévisualisation, publication |
| **Leads** | Vue globale tous prospects, filtres avancés, export |
| **Équipe** | Créer comptes, assigner tunnels, voir performances |
| **Médias** | Bibliothèque vidéos/images, upload, organisation |
| **Configuration** | Branding, paiements, emails, scoring, alertes |
| **Rapports** | Analytics détaillés, export, comparaisons |

---

# 6. ARCHITECTURE TECHNIQUE

## 6.1 Stack technologique

| Composant | Technologie |
|-----------|-------------|
| **Frontend** | React + TailwindCSS + shadcn/ui |
| **BackOffice** | Filament 4 |
| **Backend API** | Laravel 12 |
| **Base de données** | MySql |
| **Cache/Queue** | Redis (sessions, jobs, notifications) |
| **Stockage médias** | Cloudflare R2 (S3-compatible) |
| **Vidéos** | YouTube non listé / Bunny.net Stream |
| **Emails** | Resend / SendGrid |
| **Tracking** | Event tracking interne |
| **Hébergement** | VPS |

## 6.2 Modèle de données

```
┌─────────────────────────────────────────────────────────────────┐
│                    MODÈLE DE DONNÉES                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  UTILISATEURS                                                   │
│  ┌─────────────┐     ┌─────────────┐                           │
│  │    Users    │     │    Roles    │                           │
│  │─────────────│     │─────────────│                           │
│  │ id          │     │ ADMIN       │                           │
│  │ email       │────►│ MANAGER     │                           │
│  │ password    │     │ COMMERCIAL  │                           │
│  │ role        │     └─────────────┘                           │
│  │ created_at  │                                               │
│  └─────────────┘                                               │
│         │                                                       │
│         │ 1:N                                                   │
│         ▼                                                       │
│  OFFRES & TUNNELS                                               │
│  ┌─────────────┐     ┌─────────────┐                           │
│  │   Offers    │     │   Funnels   │                           │
│  │─────────────│     │─────────────│                           │
│  │ id          │◄────│ id          │                           │
│  │ name        │     │ offer_id    │                           │
│  │ type        │     │ name        │                           │
│  │ price       │     │ slug        │                           │
│  │ description │     │ status      │                           │
│  └─────────────┘     │ whatsapp_url│                           │
│                      │ payment_url │                           │
│                      │ branding    │                           │
│                      │ assigned_to │                           │
│                      └─────────────┘                           │
│                             │                                   │
│                             │ 1:N                               │
│                             ▼                                   │
│  PAGES & BLOCS                                                  │
│  ┌─────────────┐     ┌─────────────┐                           │
│  │    Pages    │     │   Blocks    │                           │
│  │─────────────│     │─────────────│                           │
│  │ id          │◄────│ id          │                           │
│  │ funnel_id   │     │ page_id     │                           │
│  │ type        │     │ type        │                           │
│  │ title       │     │ content     │                           │
│  │ slug        │     │ order       │                           │
│  │ order       │     │ settings    │                           │
│  └─────────────┘     └─────────────┘                           │
│         │                                                       │
│         │                                                       │
│  PROSPECTS                                                      │
│  ┌─────────────┐     ┌─────────────┐     ┌─────────────┐       │
│  │    Leads    │     │   Events    │     │    Tags     │       │
│  │─────────────│     │─────────────│     │─────────────│       │
│  │ id          │◄────│ id          │     │ id          │       │
│  │ funnel_id   │     │ lead_id     │     │ name        │       │
│  │ email       │     │ type        │     │ color       │       │
│  │ phone       │     │ page_id     │     └─────────────┘       │
│  │ name        │     │ data        │            │              │
│  │ source      │     │ created_at  │            │              │
│  │ score       │     └─────────────┘     ┌──────▼──────┐       │
│  │ status      │                         │  LeadTags   │       │
│  │ created_at  │◄────────────────────────│─────────────│       │
│  └─────────────┘                         │ lead_id     │       │
│                                          │ tag_id      │       │
│                                          └─────────────┘       │
│                                                                 │
│  AUTOMATISATIONS                                                │
│  ┌─────────────┐     ┌─────────────┐                           │
│  │ Automations │     │   Alerts    │                           │
│  │─────────────│     │─────────────│                           │
│  │ id          │     │ id          │                           │
│  │ trigger     │     │ user_id     │                           │
│  │ condition   │     │ lead_id     │                           │
│  │ action      │     │ type        │                           │
│  │ active      │     │ message     │                           │
│  └─────────────┘     │ read        │                           │
│                      │ created_at  │                           │
│                      └─────────────┘                           │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

# 7. PLANNING DE DÉVELOPPEMENT

## 7.1 Phase MVP (2 mois)

| Semaine | Sprint | Livrables |
|---------|--------|-----------|
| **S1-S2** | Cadrage & Design | Architecture, maquettes UI/UX, BDD, charte graphique |
| **S3-S4** | Backend Core | API Auth, Users, Offers, Funnels, Pages |
| **S5-S6** | Page Builder + Pages publiques | Éditeur blocs, rendu pages, tracking |
| **S7-S8** | Leads + Scoring + Alertes | Pipeline, fiches, scoring auto, notifications |
| **S9** | Dashboards | Back-Office Admin + Dashboard Commercial |
| **S10** | Tests & Lancement | QA, corrections, déploiement production |

**Livrable MVP** : Plateforme fonctionnelle avec création tunnels, tracking complet, gestion leads et dashboards.

## 7.2 Phase Développement Continu (V2 - 1 mois)

| Semaine | Focus | Fonctionnalités |
|---------|-------|-----------------|
| **S1** | Email Marketing + Automations | Séquences automatiques, règles "si X alors Y" |
| **S2** | Intégrations | Paiements (Wave, CinetPay), WhatsApp Business API |
| **S3** | Analytics avancés | Pixel Meta, GTM, rapports PDF |
| **S4** | A/B Testing + Optimisations | Tests pages, optimisations performances |

---

# 8. PROPOSITION FINANCIÈRE

## 8.1 Phase MVP (2 mois) – Forfait

| Poste | Description | Montant |
|-------|-------------|---------|
| **Cadrage & Design** | Architecture, maquettes, prototypes | 200 000 XOF |
| **Backend API Core** | Auth, Users, Offers, Funnels, Pages | 350 000 XOF |
| **Page Builder** | Éditeur blocs, prévisualisation, publication | 250 000 XOF |
| **Pages Publiques** | Rendu pages, tracking événements | 150 000 XOF |
| **Module Leads** | Pipeline, fiches, scoring, tags | 200 000 XOF |
| **Alertes & Notifications** | Système alertes, notifications temps réel | 100 000 XOF |
| **Dashboard Commercial** | Interface commerciaux | 150 000 XOF |
| **Back-Office Admin** | Interface administration complète | 200 000 XOF |
| **Tests & Déploiement** | QA, mise en production | 100 000 XOF |
| **TOTAL MVP** | | **1 650 000 XOF** |

## 8.2 Phase Développement Continu (V2 - 1 mois) – Forfait

| Poste | Description | Montant |
|-------|-------------|---------||
| **Email Marketing + Automations** | Séquences auto, règles conditionnelles | 250 000 XOF |
| **Features V2 - Intégrations** | WhatsApp API | 350 000 XOF |
| **Analytics avancés** | Pixel Meta, GTM, rapports PDF, A/B testing | 250 000 XOF |
| **TOTAL V2** | | **850 000 XOF** |

## 8.3 Récapitulatif global

| Phase | Durée | Montant |
|-------|-------|---------||
| MVP | 2 mois | 1 650 000 XOF |
| V2 (Email, Automations, Paiements, Analytics) | 1 mois | 850 000 XOF |
| **TOTAL PROJET** | **~3 mois** | **2 500 000 XOF** |

## 8.4 Échéancier de paiement

### Phase MVP (1 650 000 XOF)

| Jalon | % | Montant | Condition |
|-------|---|---------|-----------||
| Signature | 60% | 990 000 XOF | À la commande |
| Livraison finale MVP | 40% | 660 000 XOF | Recette définitive |

### Phase V2 (850 000 XOF)

| Modalité | Montant |
|----------|---------||
| Paiement unique | 850 000 XOF à la livraison V2 |

---

# 9. LIVRABLES

## 9.1 Livrables MVP

| Type | Détail |
|------|--------|
| **Plateforme Web** | Application Web responsive |
| **Back-Office Admin** | Interface administration complète |
| **Dashboard Commercial** | Interface équipe commerciale |
| **Pages Tunnels** | Pages publiques générées |
| **Backend** | API REST complète, BDD |
| **Documentation** | Technique, utilisateur, API |
| **Code source** | Accès repository Git complet |

## 9.2 Services inclus

- ✅ Design UI/UX moderne
- ✅ Tests qualité (QA)
- ✅ Déploiement et configuration
- ✅ Formation utilisateur (1 jour)
- ✅ Support technique 2 mois post-MVP
- ✅ Corrections de bugs pendant garantie

---

# 10. COÛTS D'INFRASTRUCTURE

## 10.1 Hébergement VPS Tout-en-un

| Formule | Caractéristiques | Coût Annuel |
|---------|-----------------|-------------|
| **VPS Standard** | 4 vCPU, 8 GB RAM, 200 GB SSD, tout inclus | **350 000 XOF/an** |

## 10.2 Services tiers (estimations mensuelles)

| Service | Coût estimé |
|---------|-------------|
| Emails transactionnels (Resend) | ~5 000 XOF/mois |
| Stockage vidéos (Bunny.net) | ~10 000 XOF/mois |
| Domaine .com | ~15 000 XOF/an |

---

# 11. GARANTIE ET SUPPORT

## 11.1 Garantie MVP

| Élément | Durée |
|---------|-------|
| Correction de bugs | 1 mois |
| Support technique | 1 mois |
| Mises à jour sécurité | 2 mois |

## 11.2 Maintenance optionnelle

| Service | Montant mensuel |
|---------|-----------------|
| Maintenance corrective | 80 000 à 300 000 XOF/mois |
| Maintenance évolutive | Sur devis |

---

# 12. AVANTAGES DE NOTRE PROPOSITION

| Avantage | Détail |
|----------|--------|
| **MVP rapide** | 2 mois pour lancer |
| **Budget maîtrisé** | 1,65M XOF pour un outil complet |
| **Double interface** | Admin + Commercial séparés |
| **Tracking complet** | Chaque action prospect trackée |
| **Scoring intelligent** | Priorisation automatique des leads |
| **Code propriétaire** | Vous possédez 100% du code |
| **Évolutif** | Architecture prête pour V2 |

---

# 13. PROCHAINES ÉTAPES

1. **Validation de la proposition** : Retour du porteur de projet
2. **Ajustements éventuels** : Modifications selon besoins
3. **Signature du contrat** : Engagement mutuel + acompte 30%
4. **Kick-off** : Réunion de lancement, cadrage détaillé
5. **Démarrage Sprint 1** : Design et architecture

---

*Document confidentiel – Projet AIMANT CLIENT*  
*GENIUS GROUPS – Décembre 2024*
