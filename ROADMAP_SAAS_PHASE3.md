# Roadmap SaaS — Phase 3 (Royal LeadPro)

> Fichier de suivi vivant. À mettre à jour à chaque avancée (case cochée, statut changé, entrée de journal ajoutée).
> Source du cahier des charges : `royal-leadpro-phase3.html` (racine du repo, présentation client Genius Groups SAS).
> Branche de travail : `royalLeadPro`.

## Contexte

Royal LeadPro passe d'outil interne (usage type "royalFreelance", un seul porteur de projet) à une vraie plateforme SaaS multi-tenant, monétisable. La base multi-tenant (modèle `Tenant`, trait `BelongsToTenant`, `TenantService`) existe déjà dans le code — le travail de Phase 3 consiste à finir cette base et à construire ce qu'il manque pour la mise en prod SaaS.

## Les 7 modules de la spec

| # | Module | Statut |
|---|---|---|
| 1 | Architecture Multi-Tenant | ✅ Squelette fonctionnel (inscription self-service, invitations, fuite de données corrigée) |
| 2 | Billing & Abonnements | ✅ Squelette fonctionnel (activation manuelle, pas de gateway de paiement) |
| 3 | Quotas par plan | 🔶 Enforcement partiel (tunnels + listes mailing bloqués ; leads et tunnels partagés calculés mais pas bloqués) |
| 4 | Partage de Tunnels | ⬜ Pas commencé |
| 5 | Rôles & Permissions (Owner/Admin/Editor/Viewer) | ⬜ Pas commencé |
| 6 | Dashboard SaaS utilisateur | ⬜ Pas commencé |
| 7 | Super Admin Royal LeadPro | ⬜ Pas commencé |
| 8 | Pages Front-Office (bonus) | ⬜ Pas commencé |

**Ordre retenu pour attaquer le projet : Module 1 → Module 2 → Module 3.**
Raison : ensemble ils forment le squelette monétisable (inscription → plan → application des limites). Le Billing dépend d'un Multi-Tenant fini (il faut un tenant à la création pour lui attacher un abonnement), et les Quotas dépendent directement du Billing (il faut un plan avec des limites en base). Les modules 4/5/6/7/8 viennent se greffer dessus une fois ce socle posé.

---

## 🔍 Focus actuel : Module 1 — Finaliser le Multi-Tenant

### Ce qui existe déjà et fonctionne
- Table `tenants` complète (uuid, slug, branding/settings JSON, `trial_ends_at`, `suspended_at`, soft deletes)
- `tenant_id` présent sur `users` et sur toutes les tables métier (funnels, leads, tags, offers, alerts, scoring_rules, templates, commercial_groups...)
- `App\Traits\BelongsToTenant` appliqué à 12 modèles : assigne `tenant_id` automatiquement à la création + global scope qui filtre systématiquement par `auth()->user()->tenant_id`
- `App\Services\TenantService::setupWithAdmin()` — crée un tenant + son admin en une fois, avec branding et scoring par défaut. **Fonctionnel mais jamais branché à une route publique.**
- `App\Services\OnboardingService` — checklist complète et opérationnelle par tenant (branding → offre → tunnel → pages → publication → équipe → assignation), avec % de complétion et suggestions contextuelles
- Guard d'auth unique (`web`), redirection après login déjà correcte selon le rôle Spatie (`commercial` → dashboard commercial, `admin`/`manager`/`super_admin` → `/admin`)

### Gaps identifiés (à traiter dans ce module)

1. **🔴 Bug critique — `CommercialAuthController::store()` (ligne ~76)** :
   ```php
   $tenant = Tenant::first();
   ```
   Tout nouveau commercial qui s'inscrit via `/register` est rattaché **au premier tenant de la base, peu importe lequel**. En mono-tenant (royalFreelance) ça passait inaperçu ; dès qu'il y a 2 tenants, n'importe qui peut s'auto-rattacher au mauvais compte. À corriger avant toute ouverture publique de l'inscription — il faut un mécanisme explicite (lien d'invitation avec token, ou sous-domaine/slug du tenant dans l'URL d'inscription).

2. **Pas d'inscription self-service pour créer un nouveau tenant.** Le seul `/register` existant sert à un *commercial* qui rejoint un tenant (et qui plus est, le mauvais). Il n'existe aucune route publique qui appelle `TenantService::setupWithAdmin()` pour qu'un nouveau client crée son propre espace. C'est le cœur du module : il faut un flow distinct, ex. `/demarrer`, avec son propre contrôleur.

3. **Isolation = scope applicatif uniquement, pas de tenancy Filament.** `AdminPanelProvider` n'utilise pas le multi-tenancy natif de Filament 4. Un seul panel partagé ; l'isolement dépend à 100% du global scope sur `tenant_id`. Pas bloquant pour avancer, mais tout usage de `withoutGlobalScope('tenant')` (repéré dans `User.php` pour les stats agrégées) doit être audité pour confirmer qu'il ne fuite pas entre tenants.

4. **Le sous-domaine wildcard résout un `Funnel`, pas un `Tenant`.** `ResolveFunnelFromSubdomain` mappe `mon-tunnel.royalleadpro.com` → tunnel public. Il n'y a pas de notion d'espace de travail par sous-domaine (`monentreprise.royalleadpro.com` → panel admin du tenant). À décider : est-ce qu'on en a besoin pour la Phase 3, ou le panel partagé (`/admin` avec scope applicatif) suffit pour le lancement ?

### Décisions prises (2026-07-02)
- [x] Activation du tenant à l'inscription : **plan Gratuit direct**, pas de période d'essai (`trial_ends_at` non utilisé pour ce flow).
- [x] Rattachement d'un commercial à un tenant : **lien d'invitation à token signé**, généré par un admin du tenant, expirable. Règle aussi le bug `Tenant::first()` : `/register` (commercial) exigera un token d'invitation valide au lieu de prendre le premier tenant trouvé.
- [x] Panel admin : **on garde le scope applicatif actuel** (global scope `tenant_id`), pas de tenancy Filament native pour la Phase 3. `AdminPanelProvider` inchangé.

### Checklist d'implémentation
- [x] Corriger `CommercialAuthController::store()` (retiré `Tenant::first()`, exige un token d'invitation valide et non expiré)
- [x] Modèle + migration `TenantInvitation` (token signé, tenant_id, email cible, expiration 7j, utilisé/non utilisé) — utilise `BelongsToTenant` pour l'isolation
- [x] Génération d'invitation côté admin — implémenté comme **ressource Filament** (`TenantInvitationResource`, groupe nav "Équipe") plutôt qu'un contrôleur+vue dédié : cohérent avec le reste du panel, routé automatiquement sous `/admin/tenant-invitations`
- [x] Nouveau contrôleur + route publique d'inscription tenant : `TenantRegistrationController`, routes dans `routes/tenant.php` (`GET/POST /demarrer`)
- [x] Formulaire d'inscription (nom entreprise, admin: nom/email/mdp) — `resources/views/tenant/register.blade.php`
- [x] Branchement sur `TenantService::setupWithAdmin()`
- [x] Attribution du plan Gratuit à la création — stub `plan_slug` (défaut `'free'`) ajouté sur `tenants`, à remplacer par une vraie relation `Subscription`→`Plan` au Module 2
- [~] Connexion auto + redirection : fait, mais redirection vers `/admin` (pas vers l'onboarding) — la vue `resources/views/filament/pages/onboarding.blade.php` existe déjà mais **n'a pas de Page Filament associée** (orpheline, aucune classe dans `app/Filament/Pages` ne la rend). La brancher est un gap séparé, pas traité ici (relève plutôt du Module 6 — Dashboard SaaS).
- [x] Audit des `withoutGlobalScope('tenant')` existants → **1 vraie fuite trouvée et corrigée** : 6 widgets Filament (`StatsOverview`, `ConversionByCountryWidget`, `LatestLeads`, `RevenuePerformanceWidget`, `LeadsByDeviceWidget`, `LeadsPipelineChart`) bypassaient le scope tenant sans re-filtrer → un admin voyait les stats agrégées de **tous les tenants**. Corrigé (`withoutGlobalScope('tenant')` → `query()`, le scope naturel s'applique). Les autres usages (`CommercialDashboardController`, `User::getCommercialStats()`, `EmailService::processSequences()` en job) sont sûrs (re-filtrés par `brought_by`/user, ou volontairement cross-tenant pour un job de fond).

### Bug trouvé pendant les tests (corrigé)
- `Tenant::$fillable` n'incluait pas `plan_slug` → l'assignation de masse l'ignorait silencieusement. Ajouté.

---

## Module 2 — Billing & Abonnements

### Décisions prises (2026-07-02)
- [x] **Activation manuelle** — pas de gateway de paiement (Mobile Money/CinetPay/PayDunya/Stripe) pour l'instant. Un super_admin active/change le plan d'un tenant depuis le panel. Le vrai paiement viendra une fois le squelette Billing+Quotas validé.
- [x] **Quotas "+X" lus comme cumulatifs par palier** : Leads Gratuit 1000 / Starter 2000 / Prestige 3000 ; Listes mailing Gratuit 1 / Starter 10 / Prestige 20.
- [x] **Tunnels partagés Prestige = illimité** (`null`), cohérent avec les tunnels propres déjà illimités sur ce plan.

### Checklist d'implémentation
- [x] Modèle + migration `Plan` (slug, prix mensuel/annuel, `max_tunnels`/`max_mailing_lists`/`max_leads`/`max_shared_tunnels` nullable = illimité, `available_roles` json, dashboard/support level)
- [x] `PlanSeeder` — 3 plans (Gratuit/Starter/Prestige), appelé depuis `DatabaseSeeder`
- [x] Modèle + migration `Subscription` (tenant_id, plan_id, cycle monthly/yearly, statut active/expired/suspended/trialing, starts_at, ends_at nullable = n'expire jamais)
- [x] `Tenant::subscriptions()`, `activeSubscription()`, `currentPlan()`
- [x] Stub `plan_slug` (Module 1) retiré — `TenantRegistrationController` crée maintenant une vraie `Subscription` (plan Gratuit, statut actif, sans expiration) à l'inscription
- [x] `SubscriptionResource` (Filament) — activation/changement de plan manuel, **restreint à `super_admin`** via `canAccess()` (pas de système de Policy dans ce codebase, donc pas introduit ici — suit la convention existante de checks de rôle inline)
- [x] Commande `subscriptions:expire` — repasse en `expired` les abonnements actifs dont `ends_at` est dépassé, planifiée quotidiennement dans `routes/console.php`

## Module 3 — Quotas par plan

### Checklist d'implémentation
- [x] `QuotaService` : `usage()`, `limit()`, `remaining()`, `hasReachedLimit()` — mappe tunnels (Funnel hors templates), listes mailing (EmailSequence), leads (Lead), tunnels partagés (Funnel avec au moins un `funnel_user`). `null` = illimité ; pas d'abonnement actif = limite 0.
- [x] Enforcement à la création côté Filament : `EnforcesTenantQuota` (trait, `Halt` + notification "limite atteinte") branché sur `CreateFunnel` (quota `tunnels`) et `CreateEmailSequence` (quota `mailing_lists`)
- [ ] **Leads — pas bloqué à la création.** Un `Lead` est créé dès la première vue anonyme d'une page (`TrackingService::trackVisitor`), pas seulement à la soumission d'un formulaire — c'est le cœur du pipeline public de capture, à fort risque business (funnel cassé = prospects perdus) et jamais testé en charge. Bloquer ce chemin mérite sa propre revue dédiée plutôt qu'un ajout hâtif dans ce lot. `QuotaService` calcule déjà l'usage/la limite, prêt à être branché plus tard (ou exposé côté dashboard Module 6 sans bloquer).
- [ ] **Tunnels partagés — pas bloqué.** Aucune UI/route actuelle ne crée réellement un partage (`CommercialService::activateFunnel()` existe mais n'est appelé nulle part) — c'est le Module 4 (Partage de Tunnels), non construit. Rien à enforcer tant que le point d'entrée n'existe pas.
- Testé via tinker : plan Gratuit (2 tunnels) → 2 tunnels créés → `hasReachedLimit('tunnels')` bascule à `true`, `remaining()` à 0. Le chemin Filament (`Halt` + notification) suit le pattern documenté de Filament mais n'a pas été exercé via une vraie requête HTTP/Livewire dans ce lot.

---

## 🐛 Retours de tests utilisateur (2026-07-02)

Tests manuels effectués sur le flow Module 1/2/3. Bugs et questions remontés, **non corrigés** — en attente de go pour traiter :

1. **🔴 Bug — bouton "Mon espace" redirige vers `/commercial`.** Devrait pointer vers l'espace du tenant (admin/owner), pas vers le dashboard commercial. À localiser (probablement un lien codé en dur ou une redirection post-login qui ne distingue pas le rôle).
2. **🔴 Bug — création d'un lien d'invitation en échec.** À reproduire : `TenantInvitationResource` (panel admin, `/admin/tenant-invitations`) — la génération du token/lien ne fonctionne pas comme attendu.
3. **❓ Question — à l'inscription (`/demarrer`), l'admin créé n'est pas explicitement "Owner".** Normal à ce stade : le Module 5 (rôles workspace Owner/Admin/Editor/Viewer) n'est pas construit — seuls les rôles Spatie `admin`/`commercial`/`super_admin` existent. À clarifier si un rôle "Owner" minimal doit être introduit dès maintenant ou si ça attend le Module 5.
4. **⚠️ Demande — choix de plan manquant à l'inscription.** Actuellement `/demarrer` assigne le plan Gratuit automatiquement sans étape de sélection. Le flow souhaité : si l'utilisateur s'inscrit sans avoir choisi de plan, lui demander de choisir un plan **avant validation finale** de l'inscription (proposer le choix de plan avant que le compte ne soit créé).

---

## Journal

- **2026-07-02** — Lecture du cahier des charges Phase 3 (`royal-leadpro-phase3.html`). Analyse du code existant : base multi-tenant déjà en place, aucun des 7 modules SaaS n'est construit. Ordre retenu : Module 1 → 2 → 3. Analyse détaillée du Module 1 : bug critique `Tenant::first()` identifié dans `CommercialAuthController::store()`, absence de flow d'inscription self-service pour créer un tenant. Aucun code modifié — en attente du go pour commencer l'implémentation.
- **2026-07-02** — Go donné. 3 décisions tranchées (plan Gratuit direct sans essai, invitation à token signé, panel Filament partagé conservé). Branche `feature/module1-multitenant-saas` créée depuis `royalLeadPro`. Implémentation complète du Module 1 : migration `plan_slug` (stub Module 2), modèle+migration `TenantInvitation`, `routes/tenant.php` (nouveau fichier dédié aux routes multi-tenant, wiré dans `bootstrap/app.php`), `TenantRegistrationController` (self-service `/demarrer`), `TenantInvitationResource` (Filament, génère les liens d'invitation), fix de `CommercialAuthController::store()` (token d'invitation requis au lieu de `Tenant::first()`). Audit `withoutGlobalScope('tenant')` : fuite cross-tenant réelle trouvée et corrigée dans 6 widgets Filament du dashboard admin. Bug additionnel trouvé aux tests : `plan_slug` manquant de `Tenant::$fillable`, corrigé. Flow testé de bout en bout via tinker (création tenant → invitation → isolation cross-tenant → rattachement commercial) — tout passe. Onboarding : `OnboardingService` est prêt mais la vue Filament associée n'a pas de Page câblée (orpheline) — laissé de côté, hors scope Module 1, redirection actuelle vers `/admin`. Migrations appliquées localement, commit local fait (pas de push).
- **2026-07-02** — Go donné pour Modules 2 & 3. Avant de coder : vérification des chiffres exacts dans `royal-leadpro-phase3.html` (le "+1 000 leads"/"+10 listes" du tableau marketing était ambigu pour des quotas chiffrés) → décisions validées : activation manuelle (pas de gateway), quotas cumulatifs par palier, tunnels partagés Prestige illimité. Branche `feature/module2-3-billing-quotas` créée depuis `feature/module1-multitenant-saas`. Implémenté : `Plan`+`PlanSeeder`, `Subscription`, relations sur `Tenant`, retrait du stub `plan_slug` au profit d'une vraie `Subscription` créée à l'inscription, `SubscriptionResource` Filament réservé au `super_admin`, commande planifiée `subscriptions:expire`, `QuotaService`, enforcement (`Halt` + notification) sur la création de tunnels et de séquences email. Décision de ne pas enforcer les quotas leads (pipeline public de tracking, trop risqué à toucher sans revue dédiée) ni tunnels partagés (Module 4 pas construit, aucun point d'entrée réel) — usage/limite calculés et prêts, juste pas bloquants. Testé via tinker : seed des 3 plans avec les bons chiffres, cycle complet tenant→subscription→quota atteint, et vérification que `SubscriptionResource::canAccess()` bloque bien un admin non-super_admin. Migrations appliquées localement, commit local fait (pas de push).
- **2026-07-02** — Tests manuels utilisateur sur le flow Modules 1/2/3 (hors tinker, usage réel de l'app). 4 retours consignés dans la section "🐛 Retours de tests utilisateur" ci-dessus : 2 bugs (bouton "Mon espace" → `/commercial` au lieu de l'espace tenant ; création de lien d'invitation en échec), 1 question (rôle "Owner" pas explicite à l'inscription — normal, dépend du Module 5 pas construit), 1 demande (ajouter une étape de choix de plan avant validation de l'inscription, actuellement le plan Gratuit est assigné sans demander). Aucun correctif appliqué — en attente de go pour traiter ces points avant de passer au Module 4.
