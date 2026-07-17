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
| 3 | Quotas par plan | ✅ Enforcement complet (tunnels, listes mailing, tunnels partagés et leads bloqués) |
| 4 | Partage de Tunnels | ✅ Squelette fonctionnel (permission lecture/édition, quota enforcé) |
| 5 | Rôles & Permissions (Owner/Admin/Editor/Viewer) | ✅ Squelette fonctionnel (rôles Spatie réels, scoping panel + actions) |
| 6 | Dashboard SaaS utilisateur | ✅ Squelette fonctionnel (widget d'usage + CTA upgrade sur le dashboard principal) |
| 7 | Super Admin Royal LeadPro | ✅ Squelette fonctionnel (liste/fiche tenants, suspension/réactivation avec enforcement panel+public+commercial, usage par tenant, analytics globale) |
| 8 | Pages Front-Office (bonus) | 🔶 Page de présentation RoyalLeadPro faite (accueil `/`) ; page "Fonctionnalités" et "Tarifs" dédiées pas encore séparées (tout est sur une seule page à ancres) |

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
- [x] **Leads — bloqué à la création (2026-07-17).** Décision utilisateur explicite d'aller jusqu'au blocage du chemin de tracking anonyme malgré le risque noté ci-dessus (repris tel quel pour mémoire). `TrackingService::trackVisitor()`/`createLeadFromForm()` retournent désormais `null` (au lieu de `Lead`) quand `QuotaService::hasReachedLimit($tenant, 'leads')` est vrai **et** qu'il s'agirait d'un nouveau lead (visiteur anonyme sans cookie, ou soumission sans lead existant) — un visiteur déjà identifié par cookie continue d'être suivi/mis à jour normalement (déjà compté, pas de nouvelle unité de quota). `FunnelController::renderPage()` tolère un lead nul (déjà le comportement défensif des vues `funnel/page.blade.php`/`button.blade.php` : `{{ $currentLead->id ?? 'null' }}`) ; `handleFormSubmission()` retourne un message d'erreur clair (`session('error')`, déjà affiché par la vue) si la soumission est refusée.
- [x] **Tunnels partagés — bloqué au partage (Module 4).** Voir section Module 4 ci-dessous.
- Testé via tinker : plan Gratuit (2 tunnels) → 2 tunnels créés → `hasReachedLimit('tunnels')` bascule à `true`, `remaining()` à 0. Le chemin Filament (`Halt` + notification) suit le pattern documenté de Filament mais n'a pas été exercé via une vraie requête HTTP/Livewire dans ce lot.
- Leads : 5 tests (`LeadsQuotaEnforcementTest`) — nouveau visiteur anonyme non tracké une fois le quota atteint (aucun `Lead` créé, page toujours 200), visiteur déjà identifié par cookie toujours suivi, soumission de formulaire refusée avec message clair (`session('error')`) sans créer de lead, mise à jour d'un lead existant toujours autorisée, création normale sous le quota. Suite complète 110 tests verte. Vérifié manuellement via un vrai serveur (`php artisan serve`) avec un tenant et un plan dédiés (`max_leads = 0`) : la visite ne crée aucun lead, la soumission du formulaire affiche bien le message d'erreur dans la page réelle — données de test nettoyées après coup (plan et tenant dédiés supprimés, pas touché au plan "Gratuit" partagé).

---

## Module 4 — Partage de Tunnels

### Constat de départ
Le mécanisme attendu par le CDC existait déjà partiellement : pivots `funnel_user` (partage individuel) et `commercial_group_funnel` (partage par groupe), déjà comptés (partiellement) dans `QuotaService::usage()['shared_tunnels']`. Manquaient : un point d'entrée UI réellement branché, une granularité lecture/édition, et l'enforcement du quota au partage. En creusant, l'action `assign_to_commercials` de `ViewFunnel` faisait déjà ce travail de point d'entrée (attache individuelle ou par groupe) — repérée après coup, une première implémentation en doublon (nouvelle RelationManager) a été jetée pour consolider dans cette action existante plutôt que dupliquer.

### Checklist d'implémentation
- [x] Migration : colonne `can_edit` (boolean, défaut `false`) sur `funnel_user` et `commercial_group_funnel`
- [x] `Funnel::users()`/`commercialGroups()`, `User::usableFunnels()`, `CommercialGroup::funnels()` — `can_edit` ajouté aux `withPivot()`
- [x] Action `assign_to_commercials` (`ViewFunnel`) : toggle `can_edit` ajouté au formulaire (individuel + groupe), libellés mis à jour ("Partager" au lieu d'"Attribuer"). Sélection individuelle élargie : n'importe quel membre du tenant (plus seulement `role('commercial')`), pour rester compatible avec les rôles Editor/Viewer du Module 5 à venir
- [x] `QuotaService::canShareFunnel()` + `isFunnelAlreadyShared()` : bloque le partage d'un tunnel **encore privé** une fois `shared_tunnels` atteint ; un tunnel déjà partagé peut recevoir d'autres partages sans limite supplémentaire
- [x] `QuotaService::usage()['shared_tunnels']` corrigé pour compter aussi les partages par groupe (avant : seulement `whereHas('users')`, les partages groupe n'étaient pas comptés)
- [x] Même enforcement ajouté sur le point d'entrée "groupe" (`CommercialGroups\RelationManagers\FunnelsRelationManager`), pour rester cohérent des deux côtés de la relation
- Testé : 7 tests (`TunnelSharingTest`, extensions `QuotaServiceTest`) + vérification manuelle via serveur (page `ViewFunnel` s'affiche sans erreur, bouton "Partager" présent)

---

## Module 5 — Rôles & Permissions workspace (Owner/Admin/Editor/Viewer)

### Décision retenue
Editor/Viewer implémentés comme **vrais rôles Spatie** (cohérent avec `owner`/`admin` déjà faits au Module 1), avec accès au **panel Filament existant, scope restreint** — pas un espace dédié type `/commercial`. Question posée explicitement à l'utilisateur avant de coder, restée sans réponse ; option recommandée retenue par défaut plutôt que de bloquer l'avancement. À revalider si l'utilisateur relève un désaccord.

### Mapping permissions (CDC → code)
| Permission CDC | Editor | Viewer |
|---|---|---|
| Créer/éditer tunnels | tunnels assignés (`assigned_to`) ou partagés avec `can_edit` | ✕ (jamais, même si `can_edit` est vrai sur son pivot — rôle strictement lecture seule) |
| Partager des tunnels | ✕ | ✕ |
| Ajouter des membres | ✕ | ✕ |
| Accès leads | leads des tunnels accessibles (assignés/partagés) | leads des tunnels partagés, lecture seule |

### Checklist d'implémentation
- [x] `DatabaseSeeder::createRoles()` : rôles `editor` (`view_funnels`, `update_funnels`, `view_pages`, `create_pages`, `update_pages`, `view_leads`, `update_leads`, `view_tags`, `view_alerts`, `view_analytics`) et `viewer` (uniquement les `view_*` équivalents)
- [x] `User::canAccessPanel()` élargi à `isAdmin() || isEditor() || isViewer()` ; nouveaux helpers `isEditor()`/`isViewer()`
- [x] `Funnel::canBeEditedBy(User $user)` — logique centralisée : Admin toujours, Viewer jamais, Editor si assigné directement ou partagé avec `can_edit` (individuel ou via groupe)
- [x] `FunnelResource::getEloquentQuery()` — Editor/Viewer restreints à `Funnel::availableToUser()` (déjà existante), Owner/Admin voient tout le tenant
- [x] `FunnelResource::canCreate()`/`canEdit()`/`canDelete()` — create/delete réservés à Owner/Admin, edit délégué à `Funnel::canBeEditedBy()`
- [x] `LeadResource::getEloquentQuery()` — un lead est visible si son tunnel l'est ; `canCreate()`/`canEdit()`/`canDelete()` sur le même principe que `FunnelResource`
- [x] Actions Filament de `ViewFunnel` : "Partager" masquée si non-admin, "Créer une Page" masquée si le tunnel n'est pas éditable par l'utilisateur courant
- [x] `TenantInvitationForm` : rôles `editor`/`viewer` ajoutés au `Select` (en plus d'`admin`/`commercial`)
- [x] `CommercialAuthController` : `authenticate()` et `store()` redirigent `editor`/`viewer` vers `/admin` comme les autres rôles panel
- [x] `SetsUpSaasTestData` (tests) : rôles `editor`/`viewer` ajoutés à `seedRolesAndPlans()`, helpers `createEditorForTenant()`/`createViewerForTenant()`
- Bug trouvé pendant les tests (corrigé) : `canBeEditedBy()` ignorait le rôle Viewer et se basait uniquement sur le pivot `can_edit` — un Viewer avec un partage marqué `can_edit=true` pouvait éditer. Corrigé : le rôle Viewer bloque l'édition avant toute vérification de pivot.
- Testé : 8 tests (`WorkspaceRolesTest`) + vérification manuelle via serveur (un Editor connecté ne voit qu'1 tunnel sur plusieurs dans la liste, bouton "Partager" absent de la page de détail)

---

## Module 6 — Dashboard SaaS utilisateur

### Constat de départ
La page "Mon abonnement" (Module 2, réservée à l'Owner) affichait déjà l'essentiel de ce que demande le CDC pour ce module (`MySubscription::getUsage()` : usage/limite par quota) — mais seulement à l'Owner, sur une page à part dédiée au changement de plan. Le CDC demande un vrai "Dashboard SaaS utilisateur" visible au quotidien par toute l'équipe, pas seulement au moment de gérer la facturation. Décision : ne pas dupliquer cette logique dans une nouvelle page séparée, mais l'extraire dans `QuotaService` et l'exposer comme widget sur le dashboard principal existant (`app/Filament/Pages/Dashboard.php`), visible de tous les rôles panel.

### Checklist d'implémentation
- [x] `QuotaService::usageWithLimits(Tenant $tenant)` — extrait la logique jusque-là dupliquée dans `MySubscription::getUsage()` (usage + limite pour chaque quota, prêt à afficher)
- [x] `MySubscription::getUsage()` simplifié pour appeler cette méthode partagée (pas de duplication entre la page billing et le nouveau widget)
- [x] `App\Filament\Widgets\QuotaUsageWidget` — usage/limite des 4 quotas avec barre de progression, badge "illimité" si `null`, couleur d'alerte si un quota est à ≥80% de sa limite
- [x] CTA upgrade contextuel : si un quota est proche/atteint et l'utilisateur est Owner → lien vers `MySubscription` ; sinon (Admin/Editor/Viewer) → message informatif sans lien (seul l'Owner gère l'abonnement, cf. Module 5)
- [x] Widget ajouté en premier sur l'onglet "Vue d'ensemble" de `Dashboard.php`, visible de tous les rôles ayant accès au panel (Owner/Admin/Editor/Viewer) — la donnée est au niveau du tenant, pas de l'utilisateur
- [ ] **Onboarding orphelin (gap du Module 1) — délibérément pas traité ici.** La vue `resources/views/filament/pages/onboarding.blade.php` attend un `$this->form` (wizard multi-étapes) qui n'existe dans aucune classe Page — la construire correctement est un travail à part entière (définir les étapes du wizard, la validation, le câblage aux services existants), hors du périmètre précis du CDC pour ce module ("vue tunnels/leads/listes/plan + CTA upgrade"). Reste un gap ouvert, à traiter dans une session dédiée si demandé.
- Testé : 3 tests (`QuotaUsageWidgetTest`) + vérification manuelle via serveur (dashboard répond 200, widget correctement enregistré et monté sans erreur — rendu Livewire lazy comme les autres widgets du dashboard, cohérent avec le comportement existant)

---

## 🐛 Retours de tests utilisateur (2026-07-02)

Tests manuels effectués sur le flow Module 1/2/3. Bugs et questions remontés :

1. **✅ Corrigé — bouton "Mon espace" redirigeait vers `/commercial`.** Cause : `resources/views/components/landing/nav.blade.php:23` testait `Auth::user()->isSuperAdmin()` (rôle `super_admin` uniquement) au lieu de `isAdmin()` (couvre `admin` + `super_admin`) — un admin de tenant tombait donc dans le `else` et atterrissait sur le dashboard commercial. Corrigé.
2. **✅ Corrigé — création d'un lien d'invitation en échec.** Cause réelle (confirmée dans `storage/logs/laravel.log`) : erreur fatale `Class "Filament\Forms\Components\Section" not found` dans `TenantInvitationForm.php` — Filament 4 a déplacé les composants de layout (`Section`, `Grid`, ...) vers le namespace `Filament\Schemas\Components\*`. La page de création plantait entièrement (pas un échec silencieux). En cherchant le même import cassé ailleurs : trouvé aussi dans `SubscriptionForm.php` (Module 2 — formulaire d'activation de plan par le super_admin, jamais exercé par un test) et dans `TagForm.php` (pré-existant, hors Phase 3). Les 3 corrigés. **Gap de test identifié et comblé** : les tests existants (`CommercialInvitationRegistrationTest`) créaient l'invitation directement via `TenantInvitation::create()`, sans jamais rendre le formulaire Livewire/Filament — donc ce bug ne pouvait pas être détecté. Ajout de `tests/Feature/TenantInvitationCreationTest.php` qui exerce réellement `CreateTenantInvitation` et `CreateSubscription` via `Livewire::test(...)->fillForm(...)->call('create')`.
3. **✅ Traité — rôle "Owner" introduit.** Discussion avec l'utilisateur pour clarifier la hiérarchie (voir `royal-leadpro-phase3.html`, section "4 Rôles") : Super Admin = propriétaire de la plateforme Royal LeadPro (existe déjà) ; Owner = propriétaire d'un workspace/tenant, seul rôle habilité à "Gérer l'abonnement" (identique à Admin sur le reste). Rôle Spatie `owner` ajouté (mêmes permissions qu'`admin`), assigné à la personne qui crée le tenant via `/demarrer` (en plus du rôle `admin`, pour ne rien casser des checks `isAdmin()` existants). `User::isAdmin()`/`isManager()` incluent désormais `owner` ; nouveau helper `isOwner()`.
4. **✅ Traité — choix de plan à l'inscription.** `/demarrer` est maintenant en 2 étapes (identification puis plan). Décision utilisateur : les 3 plans sont affichés y compris les payants, activation immédiate avec **paiement mocké** (pas de vraie passerelle) mais application réelle des quotas/restrictions du plan choisi dès la création. Plans payants : `ends_at` calculé selon le cycle (mensuel/annuel) pour que `subscriptions:expire` ait un comportement réel plus tard ; plan Gratuit : toujours sans expiration.
5. **✅ Bonus traité — self-service upgrade par l'Owner.** Le CDC donne explicitement "Gérer l'abonnement" à l'Owner seul : ajout d'une page Filament `MySubscription` ("Mon abonnement", réservée à `hasRole('owner')`) qui affiche le plan/usage courant et permet de changer de plan (même mécanisme de paiement mocké qu'à l'inscription). Complète `SubscriptionResource` qui reste l'outil du `super_admin` pour intervenir sur n'importe quel tenant.
6. **✅ Bonus traité — choix du rôle à l'invitation.** `TenantInvitationResource` forçait `role = 'commercial'` en dur : l'Owner/Admin qui invite choisit maintenant Admin ou Commercial dans le formulaire. `CommercialAuthController::store()` redirige l'invité vers `/admin` ou l'espace commercial selon le rôle reçu (avant : toujours vers l'espace commercial, même pour un admin invité).

---

## Module 8 (bonus) — Page de présentation RoyalLeadPro

### Décision prise (2026-07-03)
- La page d'accueil `/` bascule de la landing "Royal Freelance" (vente du coaching, RoyalLeadPro offert en bonus) vers une vraie landing SaaS pour Royal LeadPro : hero, comment ça marche, fonctionnalités, tarifs (dynamique depuis le modèle `Plan`), FAQ, CTA vers `/demarrer`.
- L'ancienne landing "Royal Freelance" n'est pas supprimée : elle est déplacée sur `/royal-freelance` (fichier `resources/views/landing.blade.php` et ses composants `components/landing/*` inchangés).

### Checklist d'implémentation
- [x] Nouvelle vue `resources/views/royalleadpro-landing.blade.php` + composants dédiés dans `resources/views/components/leadpro/*` (nav, hero, how, features, pricing, faq, cta, footer), branding avec les assets `public/assets/RoyalLeadPro/*`
- [x] Section Tarifs dynamique : lit `Plan::where('is_active', true)->orderBy('price_monthly')->get()`, passé par la route `/` — pas de données statiques dupliquées avec le seeder
- [x] `routes/web.php` : `/` (domaine principal) pointe vers la nouvelle landing nommée `home` ; nouvelle route `/royal-freelance` (nommée `royal-freelance`) pour l'ancienne landing, inchangée
- [x] CTA de la nouvelle landing pointent vers `route('tenant.register')` (`/demarrer`), pas vers `route('register')` (qui sert l'inscription commercial sur invitation)
- [x] `tests/Feature/ExampleTest.php` — `RefreshDatabase` réactivé (commenté avant) : le test `GET /` échouait car la nouvelle page interroge la table `plans`, absente en sqlite mémoire sans migrations. Suite complète repassée verte (49 tests).
- [ ] Pages "Fonctionnalités" et "Tarifs" séparées (pour l'instant tout est sur `/` avec des ancres `#fonctionnalites` / `#tarifs`) — à voir si le CDC exige des URLs dédiées ou si les ancres suffisent.
- [ ] Cross-lien depuis l'ancienne landing Royal Freelance vers `/royal-freelance` → nouvelle landing RoyalLeadPro : pas ajouté (fichier volontairement non touché), à décider si utile.

---

## Module 7 — Super Admin Royal LeadPro

### Constat de départ
Une partie du module existait déjà avant même d'être attaqué explicitement : `UserResource` donne déjà au `super_admin` une vue plateforme (bypass du scope tenant), et `SubscriptionResource` lui permet déjà de créer/modifier l'abonnement de n'importe quel tenant. Une première version de `TenantResource` (liste + fiche en lecture seule) avait aussi été commencée hors session (working tree, jamais committée). Il manquait : l'activation/suspension de comptes (les champs `is_active`/`suspended_at` existaient sur `Tenant` mais ne bloquaient rien nulle part), le monitoring d'usage par tenant, et l'analytics SaaS globale.

### Décision prise (2026-07-17)
Question posée à l'utilisateur avant de coder : quand un tenant est suspendu, qu'est-ce qui doit être bloqué ? Réponse : **panel admin ET tunnels publics** (pas seulement le panel — un tenant suspendu ne doit plus capturer de leads).

### Checklist d'implémentation
- [x] `Tenant::suspend()`/`reactivate()` — écrivent/vident `suspended_at`
- [x] `User::canAccessPanel()` — bloque si `tenant->isSuspended()` (sauf `super_admin`, garde-fou si jamais rattaché à un tenant suspendu)
- [x] `Funnel::isPubliclyAccessible()` — distinct de `isActive()` (statut du tunnel), ajoute la vérification `tenant->isSuspended()`. Branché sur les 5 points d'entrée public existants (`FunnelController::showRoot/showPage/submit`, `ResolveFunnelFromSubdomain::handleCustomDomain/handleSubdomain`) — `showFromSubdomain`/`showPageFromSubdomain`/`submitFromSubdomain` en héritent gratuitement puisqu'ils dépendent de l'attribut posé par le middleware
- [x] Actions Filament "Suspendre"/"Réactiver" avec confirmation, sur `TenantsTable` (liste) et `ViewTenant` (fiche)
- [x] Section "Usage & quotas" sur `TenantInfolist` — réutilise `QuotaService::usageWithLimits()` (même source que le widget dashboard du Module 6)
- [x] Page `SaasAnalytics` (groupe nav "Super Admin") avec 3 widgets : `SaasAnalyticsOverview` (nb tenants/actifs/suspendus, MRR mocké ramené au mensuel), `TenantsByPlanChart` (répartition par plan), `TenantGrowthChart` (nouveaux tenants/30j)
- [x] 11 tests (`SuperAdminTenantManagementTest`) : suspension/réactivation (modèle + actions Livewire réelles), enforcement panel, enforcement tunnels publics (HTTP réel avant/après suspension), accès `SaasAnalytics` réservé au super_admin, calcul MRR
- Testé : suite complète (103 tests) verte + vérification manuelle via serveur réel (login super_admin, `/admin/tenants` liste le tenant QA, `/admin/tenants/{id}` affiche la section Usage et le bouton Suspendre, `/admin/saas-analytics` répond 200 avec le contenu attendu). Données de test nettoyées après vérification.

### Gap comblé (2026-07-17) — espace commercial
L'espace commercial (`/commercial/*`) est maintenant couvert par la suspension, au même titre que le panel Filament et les tunnels publics. `App\Http\Middleware\EnsureTenantNotSuspended` (alias `tenant.active`) ajouté au groupe de routes `/commercial/*` (aux côtés de `RoleMiddleware::class . ':commercial'`, qui ne vérifiait que le rôle Spatie, pas le tenant) : bloque avec un 403 explicite si le tenant du commercial est suspendu. `CommercialAuthController::authenticate()` (le login générique `/login`, partagé avec les autres rôles) reçoit la même correction que côté Filament : identifiants valides mais tenant suspendu → message explicite au lieu du générique "identifiants incorrects". 4 tests (`CommercialSuspensionTest`), suite complète 114 tests verte. Vérifié manuellement via un vrai serveur : login réussi puis tenant suspendu → `/commercial` renvoie 403 avec le message ; nouvelle tentative de login avec le bon mot de passe → message explicite affiché, utilisateur bien resté déconnecté — données de test nettoyées après coup.

---

## Journal

- **2026-07-02** — Lecture du cahier des charges Phase 3 (`royal-leadpro-phase3.html`). Analyse du code existant : base multi-tenant déjà en place, aucun des 7 modules SaaS n'est construit. Ordre retenu : Module 1 → 2 → 3. Analyse détaillée du Module 1 : bug critique `Tenant::first()` identifié dans `CommercialAuthController::store()`, absence de flow d'inscription self-service pour créer un tenant. Aucun code modifié — en attente du go pour commencer l'implémentation.
- **2026-07-02** — Go donné. 3 décisions tranchées (plan Gratuit direct sans essai, invitation à token signé, panel Filament partagé conservé). Branche `feature/module1-multitenant-saas` créée depuis `royalLeadPro`. Implémentation complète du Module 1 : migration `plan_slug` (stub Module 2), modèle+migration `TenantInvitation`, `routes/tenant.php` (nouveau fichier dédié aux routes multi-tenant, wiré dans `bootstrap/app.php`), `TenantRegistrationController` (self-service `/demarrer`), `TenantInvitationResource` (Filament, génère les liens d'invitation), fix de `CommercialAuthController::store()` (token d'invitation requis au lieu de `Tenant::first()`). Audit `withoutGlobalScope('tenant')` : fuite cross-tenant réelle trouvée et corrigée dans 6 widgets Filament du dashboard admin. Bug additionnel trouvé aux tests : `plan_slug` manquant de `Tenant::$fillable`, corrigé. Flow testé de bout en bout via tinker (création tenant → invitation → isolation cross-tenant → rattachement commercial) — tout passe. Onboarding : `OnboardingService` est prêt mais la vue Filament associée n'a pas de Page câblée (orpheline) — laissé de côté, hors scope Module 1, redirection actuelle vers `/admin`. Migrations appliquées localement, commit local fait (pas de push).
- **2026-07-02** — Go donné pour Modules 2 & 3. Avant de coder : vérification des chiffres exacts dans `royal-leadpro-phase3.html` (le "+1 000 leads"/"+10 listes" du tableau marketing était ambigu pour des quotas chiffrés) → décisions validées : activation manuelle (pas de gateway), quotas cumulatifs par palier, tunnels partagés Prestige illimité. Branche `feature/module2-3-billing-quotas` créée depuis `feature/module1-multitenant-saas`. Implémenté : `Plan`+`PlanSeeder`, `Subscription`, relations sur `Tenant`, retrait du stub `plan_slug` au profit d'une vraie `Subscription` créée à l'inscription, `SubscriptionResource` Filament réservé au `super_admin`, commande planifiée `subscriptions:expire`, `QuotaService`, enforcement (`Halt` + notification) sur la création de tunnels et de séquences email. Décision de ne pas enforcer les quotas leads (pipeline public de tracking, trop risqué à toucher sans revue dédiée) ni tunnels partagés (Module 4 pas construit, aucun point d'entrée réel) — usage/limite calculés et prêts, juste pas bloquants. Testé via tinker : seed des 3 plans avec les bons chiffres, cycle complet tenant→subscription→quota atteint, et vérification que `SubscriptionResource::canAccess()` bloque bien un admin non-super_admin. Migrations appliquées localement, commit local fait (pas de push).
- **2026-07-02** — Tests manuels utilisateur sur le flow Modules 1/2/3 (hors tinker, usage réel de l'app). 4 retours consignés dans la section "🐛 Retours de tests utilisateur" ci-dessus : 2 bugs (bouton "Mon espace" → `/commercial` au lieu de l'espace tenant ; création de lien d'invitation en échec), 1 question (rôle "Owner" pas explicite à l'inscription — normal, dépend du Module 5 pas construit), 1 demande (ajouter une étape de choix de plan avant validation de l'inscription, actuellement le plan Gratuit est assigné sans demander). Aucun correctif appliqué — en attente de go pour traiter ces points avant de passer au Module 4.
- **2026-07-02** — Go donné pour corriger les 2 bugs. Branche `fix/module1-tests-utilisateur` créée depuis `royalLeadPro`. Bug "Mon espace" : `nav.blade.php` testait `isSuperAdmin()` au lieu de `isAdmin()`, corrigé. Bug invitation : root cause trouvée dans `storage/logs/laravel.log` — `Class "Filament\Forms\Components\Section" not found` (Filament 4 a déplacé les composants de layout vers `Filament\Schemas\Components\*`), page de création en échec total. Même import cassé trouvé et corrigé dans 2 autres fichiers : `SubscriptionForm.php` (Module 2, formulaire d'activation de plan — jamais exercé par un test, potentiellement cassé en silence depuis sa création) et `TagForm.php` (pré-existant, hors Phase 3). Gap de test comblé : les tests d'invitation existants ne rendaient jamais le formulaire Livewire réel, donc ne pouvaient pas voir ce bug — ajout de `TenantInvitationCreationTest.php` qui passe par `Livewire::test(...)->fillForm(...)->call('create')` pour `CreateTenantInvitation` et `CreateSubscription`. Suite complète (40 tests) verte. Pas de push, questions 3/4 toujours en attente d'arbitrage.
- **2026-07-02** — Discussion approfondie sur les points 3/4 en attente : clarification de la hiérarchie de rôles (Super Admin = plateforme, Owner = workspace/tenant) via relecture de `royal-leadpro-phase3.html`, décision de passer `/demarrer` en 2 étapes avec paiement mocké pour les plans payants, et extension du périmètre à la demande de l'utilisateur pour inclure le self-service upgrade par l'Owner et le choix du rôle à l'invitation. Go donné. Branche `feature/owner-role-plan-selection` créée depuis `fix/module1-tests-utilisateur` (pour hériter des 2 corrections de bugs). Implémenté : rôle Spatie `owner` (seedé dans `DatabaseSeeder` + re-seedé sur la base locale existante, idempotent), `User::isAdmin()`/`isManager()` incluent `owner`, helper `isOwner()` ; `/demarrer` réécrit en 2 étapes (`storeIdentity` stocke en session un mot de passe déjà haché — jamais en clair — puis `choosePlan`/`store`), `TenantService::setupWithAdmin()` ne re-hache plus le mot de passe (délégué au cast `hashed` du modèle, qui détecte via `Hash::isHashed()` s'il faut hacher ou non — évite un double-hash) ; `Subscription::computeEndsAt()` centralise le calcul de fin de cycle mocké (null pour Gratuit, +1 mois/an sinon) ; page Filament `MySubscription` ("Mon abonnement", réservée à `hasRole('owner')`) avec action `changePlan` ; `TenantInvitationForm` a maintenant un `Select` de rôle (Admin/Commercial) au lieu de `commercial` forcé, `CommercialAuthController::store()` redirige selon le rôle reçu. Testé : suite complète (49 tests, 126 assertions) verte, et flow complet rejoué manuellement via tinker contre la vraie base MySQL locale (transaction annulée après coup) pour confirmer l'absence de double-hash et le bon fonctionnement de `canAccessPanel()`/`MySubscription::canAccess()`. Pas de push.
- **2026-07-02** — Fusion : `feature/owner-role-plan-selection` fusionnée (fast-forward) dans `royalLeadPro`, branches `feature/owner-role-plan-selection` et `fix/module1-tests-utilisateur` supprimées localement (tout leur contenu est déjà dans `royalLeadPro`).
- **2026-07-03** — Go donné pour le Module 8 (bonus, page de présentation front-office). Ressources graphiques RoyalLeadPro trouvées dans `public/assets/RoyalLeadPro/` (logos noir/or/blanc/transparent). Décision : `/` devient la nouvelle landing RoyalLeadPro (produit SaaS), l'ancienne landing "Royal Freelance" est déplacée sur `/royal-freelance` sans suppression de fichier/composant. Branche `feature/royalleadpro-landing-page` créée depuis `royalLeadPro`. Implémenté : nouvelle vue + composants `leadpro/*` (hero, comment ça marche, fonctionnalités, tarifs dynamiques depuis `Plan`, FAQ, CTA, footer), route `/` repointée + nouvelle route nommée `royal-freelance`. Effet de bord détecté et corrigé : `tests/Feature/ExampleTest.php` (`GET /`) n'utilisait pas `RefreshDatabase` et échouait car `/` interroge maintenant la table `plans` — trait réactivé, conforme à la convention des autres tests Feature. Vérifié en conditions réelles : serveur `php artisan serve` lancé, `/`, `/royal-freelance` et les assets logo RoyalLeadPro répondent 200, contenu attendu présent (3 plans affichés). Suite complète (49 tests) verte. Restent en attente : pages "Fonctionnalités"/"Tarifs" séparées si besoin (actuellement ancres sur `/`), et éventuel lien croisé depuis l'ancienne landing Royal Freelance vers la nouvelle.
- **2026-07-04** — Go donné pour enchaîner Modules 4 → 5 → 6, avec revue du plan avant chaque implémentation (demande explicite de l'utilisateur). Plan détaillé pour les 3 modules écrit et approuvé. Module 4 (Partage de Tunnels) implémenté sur `feature/module4-partage-tunnels` (depuis `royalLeadPro`) : `can_edit` ajouté aux pivots `funnel_user`/`commercial_group_funnel`, `QuotaService::canShareFunnel()`/`isFunnelAlreadyShared()`, correction du calcul `usage()['shared_tunnels']` (les partages par groupe n'étaient pas comptés). Auto-correction en cours de route : le plan initial proposait une nouvelle `RelationManager` de partage sur `FunnelResource`, mais l'exploration plus poussée du code a révélé que `ViewFunnel::getHeaderActions()` avait déjà une action `assign_to_commercials` faisant ce travail (attache individuelle/groupe) — repérée après avoir déjà écrit le doublon. Doublon supprimé, travail consolidé dans cette action existante (renommée "Partager", élargie à tout membre du tenant plutôt que `role('commercial')` seul, pour rester compatible avec les rôles Editor/Viewer du Module 5 à venir). 7 tests ajoutés (`TunnelSharingTest` + extensions `QuotaServiceTest`), suite complète 56 tests verte, vérifié manuellement via serveur que la page `ViewFunnel` s'affiche sans erreur. Commit local fait sur `feature/module4-partage-tunnels`, pas de fusion ni de push. Module 5 (rôles Editor/Viewer) à suivre : décision retenue par défaut (rôles Spatie réels + accès panel scoped, cohérent avec Owner/Admin) faute de réponse utilisateur à la question posée — à confirmer si besoin avant de merger.
- **2026-07-04** — Module 5 (Rôles & Permissions workspace) implémenté sur `feature/module5-roles-workspace` (depuis `feature/module4-partage-tunnels`). Confirmation de la décision par défaut (rôles Spatie réels + panel scoped) reposée à l'utilisateur avant de coder, restée sans réponse une seconde fois — implémentation faite sur cette base, à revalider si besoin. Rôles `editor`/`viewer` ajoutés (`DatabaseSeeder`), `User::canAccessPanel()` élargi, helpers `isEditor()`/`isViewer()`. Logique d'édition centralisée dans `Funnel::canBeEditedBy()` (réutilisée par `FunnelResource` et `LeadResource`), scoping des listes via `Funnel::availableToUser()` déjà existante. Actions Filament masquées selon le rôle ("Partager", "Créer une Page"). `TenantInvitationForm` et `CommercialAuthController` mis à jour pour permettre l'invitation et la connexion d'un Editor/Viewer. Bug trouvé et corrigé pendant les tests : `canBeEditedBy()` ne vérifiait pas le rôle Viewer avant de regarder le pivot `can_edit`, un Viewer avec un partage marqué en édition pouvait donc éditer — corrigé en bloquant l'édition pour Viewer avant toute autre vérification. 8 tests ajoutés (`WorkspaceRolesTest`), suite complète 64 tests verte. Vérifié manuellement via serveur : un compte Editor créé en base, connecté en HTTP réel, ne voit qu'1 tunnel sur plusieurs dans la liste (scoping confirmé en conditions réelles, pas seulement via les tests), et le bouton "Partager" est bien absent de sa vue détail. Données de test nettoyées après vérification. Commit local fait sur `feature/module5-roles-workspace`, pas de fusion ni de push. Module 6 (Dashboard SaaS) à suivre.
- **2026-07-04** — Module 6 (Dashboard SaaS utilisateur) implémenté sur `feature/module6-dashboard-saas` (depuis `feature/module5-roles-workspace`). Avant de coder : constat que `MySubscription::getUsage()` (Module 2) affichait déjà l'essentiel de ce que demande ce module, mais réservé à l'Owner sur une page de billing — décision de ne pas dupliquer une 3e fois la même logique (après le doublon repéré et corrigé au Module 4) : extraction dans `QuotaService::usageWithLimits()`, réutilisée à la fois par `MySubscription` et par le nouveau `QuotaUsageWidget` posé sur le dashboard principal (`Dashboard.php`, onglet "Vue d'ensemble"), visible de tous les rôles panel (Owner/Admin/Editor/Viewer). CTA upgrade contextuel vers `MySubscription` pour l'Owner uniquement si un quota est ≥80% ou atteint ; message informatif sans lien pour les autres rôles. Décision explicite de ne pas brancher l'onboarding orphelin (gap noté au Module 1) : la vue attend un wizard multi-étapes complet qui n'existe dans aucune classe Page, un chantier à part entière et hors du périmètre précis de ce module — laissé en gap ouvert plutôt que bâclé. 3 tests ajoutés (`QuotaUsageWidgetTest`), suite complète 67 tests verte, vérifié manuellement via serveur (dashboard répond 200, widget monté sans erreur). Commit local fait sur `feature/module6-dashboard-saas`, pas de fusion ni de push. Modules 4/5/6 tous implémentés : reste à décider si l'utilisateur veut fusionner les 3 branches dans `royalLeadPro` maintenant ou continuer sur les modules 7/8 avant de fusionner.
- **2026-07-17** — Reprise de session : état des lieux complet (`git log`/`git status`/tests) après une longue pause. Constat : Modules 1/2/4/5/6 mergés sur `royalLeadPro` entre-temps, Module 8 (landing) en bonne partie fait, du travail non committé était présent dans le working tree (ébauche `TenantResource` en lecture seule, refonte thème admin) — committé hors session par l'utilisateur ("commit express") avant le début des travaux du jour. Go donné pour attaquer le Module 7 (dernier module CDC). Avant de coder : scope discuté et question posée sur la portée de la suspension d'un tenant → réponse **panel admin + tunnels publics**. Branche `feature/module7-super-admin` créée depuis `royalLeadPro`. Implémenté : `Funnel::isPubliclyAccessible()` (distinct de `isActive()`) branché sur les 5 points d'entrée public + `User::canAccessPanel()` qui bloque si le tenant est suspendu (garde-fou explicite pour le super_admin) ; `Tenant::suspend()/reactivate()` + actions Filament "Suspendre"/"Réactiver" (table et fiche) ; section "Usage & quotas" sur `TenantInfolist` (réutilise `QuotaService::usageWithLimits()`) ; page `SaasAnalytics` + 3 widgets (vue d'ensemble, répartition par plan, croissance) réservée au super_admin. 11 tests ajoutés (`SuperAdminTenantManagementTest`), suite complète 103 tests verte. Vérifié manuellement via un vrai serveur (`php artisan serve`) avec un compte super_admin réel : login réussi, `/admin/tenants` liste bien le tenant de test, la fiche tenant affiche la section Usage et le bouton Suspendre, `/admin/saas-analytics` répond 200 avec le contenu attendu — données de test nettoyées après coup. Gap volontairement laissé ouvert : l'espace commercial (`/commercial/*`) n'est pas bloqué par la suspension (hors du scope confirmé, cf. section Module 7 ci-dessus). Les 7 modules du CDC ont maintenant un squelette fonctionnel ; restent en gap connus : quotas leads non bloqués (Module 3), pages Fonctionnalités/Tarifs séparées (Module 8), onboarding wizard orphelin (Module 1), espace commercial non couvert par la suspension (Module 7). Pas de fusion ni de push — en attente de revue utilisateur.
