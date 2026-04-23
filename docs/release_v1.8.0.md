# 📋 Release Notes — v1.8.0

**Date :** 06 mars 2026  
**Branche :** `feature/lead-tagging-pipeline`  
**Auteur :** Équipe Dev

---

## 🔖 Résumé des changements

Cette release contient **3 blocs fonctionnels majeurs** :

1. 🏷️ **Tags Automatiques — Progression Tunnel** (auto-tagging des leads)
2. 📊 **Gestion Leads & Pipeline** (Kanban 5 colonnes, segments dynamiques, fusion doublons)
3. 📧 **Relances Mails & Séquences Automatiques** (Postmark, tests & déclencheurs)

---

## 1. 🏷️ Tags Automatiques — Progression Tunnel

### Description
Les leads sont désormais **automatiquement taggés** en fonction de leur avancée dans les pages du tunnel. Chaque visite de page attribue un tag et met à jour le statut du lead.

### Mapping Page → Tag

| Page visitée | Tag assigné | Couleur | Statut Lead |
|-------------|-------------|---------|-------------|
| Page 1 | ❄️ **Froid** | 🔵 Bleu | `cold` |
| Page 2 | ☀️ **Tiède** | 🟡 Orange | `warm` |
| Page 3 | 🔥 **Chaud** | 🔴 Rouge | `hot` |
| Page 4 | 🟣 **Ultra Chaud** | 🟣 Violet | `ultra_hot` |
| Clic "Rejoindre" (Page 4) | ✅ **Client** | 🟢 Vert | `client` |

### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| [app/Models/Page.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Models/Page.php) | Ajout [getPositionInFunnel()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Models/Page.php#141-157) et [isLastPage()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Models/Page.php#158-169) |
| [app/Services/TrackingService.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/TrackingService.php) | Logique d'auto-tagging ([assignFunnelProgressionTag](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/TrackingService.php#411-466), [markAsClient](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/TrackingService.php#488-545)) |
| [app/Http/Controllers/TrackingApiController.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Http/Controllers/TrackingApiController.php) | Endpoint API [trackCtaClick()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/TrackingService.php#170-187) |
| [routes/web.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/routes/web.php) | Route `POST /api/tracking/cta-click` |
| [resources/views/funnel/blocks/button.blade.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/resources/views/funnel/blocks/button.blade.php) | Tracking JS sur les boutons CTA |

### Scénarios de test

> [!IMPORTANT]
> **Pré-requis** : Avoir un tunnel de 4 pages actives, accessible via URL publique.

#### Test 1 — Tag automatique à la visite de page
1. Ouvrir un tunnel de test (navigation privée ou sans cookie existant)
2. Arriver sur la **Page 1** → Le lead doit être créé avec :
   - Tag : `Froid` (bleu)
   - Statut : `cold`
3. Naviguer vers la **Page 2** → vérifier :
   - Tag `Froid` **supprimé**, tag `Tiède` (orange) ajouté
   - Statut : `warm`
4. Naviguer vers la **Page 3** → vérifier :
   - Tag `Chaud` (rouge), statut `hot`
5. Naviguer vers la **Page 4** → vérifier :
   - Tag `Ultra Chaud` (violet), statut `ultra_hot`

#### Test 2 — Pas de rétrogradation
1. Après avoir visité la Page 3 (statut `hot`)
2. Retourner sur la Page 1
3. **Vérifier que le statut reste `hot`** (le tag `Froid` est ajouté en tracking mais le statut ne descend pas)

#### Test 3 — Conversion Client via CTA
1. Naviguer jusqu'à la **Page 4**
2. Cliquer sur le **bouton CTA** ("Rejoindre" ou équivalent)
3. Vérifier dans l'admin :
   - Tag : [Client](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/TrackingService.php#488-545) (vert)
   - Statut : `client`
   - `converted_at` renseigné
   - Les anciens tags de progression (Froid/Tiède/Chaud/Ultra Chaud) sont **supprimés**
4. Vérifier dans les logs : `🎉 [FUNNEL PROGRESSION] Lead converti en CLIENT`

#### Test 4 — Double clic CTA (idempotence)
1. Cliquer 2 fois sur le CTA de la Page 4
2. Vérifier qu'il n'y a **pas de doublon** de tag [Client](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/TrackingService.php#488-545)
3. Vérifier que `converted_at` n'est **pas écrasé**

---

## 2. 📊 Gestion Leads & Pipeline

### 2.1 Vue Kanban — 5 colonnes avec Drag & Drop

#### Description
Le Kanban passe de **4 colonnes** (basées sur le score) à **5 colonnes** (basées sur le [LeadStatus](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Http/Controllers/CommercialDashboardController.php#283-337) enum) :

| Colonne | Statut | Couleur bordure |
|---------|--------|-----------------|
| ❄️ Froid | `cold` | Bleu |
| ☀️ Tiède | `warm` | Orange |
| 🔥 Chaud | `hot` | Rouge |
| 🟣 Ultra Chaud | `ultra_hot` | Violet |
| ✅ Client | `client` | Vert |

#### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| [app/Http/Controllers/CommercialDashboardController.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Http/Controllers/CommercialDashboardController.php) | Kanban basé sur [LeadStatus](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Http/Controllers/CommercialDashboardController.php#283-337), ajout colonne Ultra Chaud, refactoring [updateLeadStatus()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Http/Controllers/CommercialDashboardController.php#283-337) |
| [resources/views/commercial/leads/kanban.blade.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/resources/views/commercial/leads/kanban.blade.php) | 5 colonnes, animation drag, compteurs live |

#### Scénarios de test

##### Test 5 — Affichage des 5 colonnes
1. Aller sur `/commercial/leads/kanban`
2. Vérifier que les **5 colonnes** sont visibles : Froid, Tiède, Chaud, Ultra Chaud, Client
3. Chaque colonne affiche son **compteur** de leads

##### Test 6 — Drag & Drop
1. Prendre un lead de la colonne **Froid**
2. Le glisser vers **Chaud**
3. Vérifier :
   - Toast de succès affiché
   - Les compteurs des 2 colonnes sont mis à jour
   - En base, le `status` du lead est `hot`
   - Le tag `chaud` est attribué, l'ancien tag `froid` est retiré

##### Test 7 — Drag vers Client
1. Glisser un lead vers la colonne **Client**
2. Vérifier que `converted_at` est renseigné
3. Vérifier que le tag [Client](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/TrackingService.php#488-545) est attribué

##### Test 8 — Dé-conversion (Client → Chaud)
1. Glisser un lead de la colonne **Client** vers **Chaud**
2. Vérifier que :
   - Le `converted_at` est **remis à null**
   - Le statut est `hot`
   - Le tag [Client](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/TrackingService.php#488-545) est retiré, `Chaud` est ajouté

##### Test 9 — Protection accès
1. Essayer de modifier un lead qui n'appartient **pas** au commercial connecté
2. Vérifier le retour **403 Accès refusé**

---

### 2.2 Segments Dynamiques

#### Description
Des segments intelligents pré-définis permettent de filtrer les leads automatiquement. Disponibles à **2 niveaux** :

- **Admin Filament** : Onglets (tabs) dans la page Leads avec badges de comptage
- **Service API** : `LeadService::getSegments()` et [getLeadsBySegment()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/LeadService.php#394-443)

#### Segments disponibles

| Segment | Critère |
|---------|---------|
| 🔥 **Chauds sans contact** | Leads HOT ou ULTRA_HOT jamais contactés via WhatsApp |
| 💤 **Inactifs 7j+** | Sans activité depuis 7 jours (hors clients) |
| 🎬 **Engagés vidéo** | Ont regardé ≥75% d'une vidéo |
| 🆕 **Nouveaux 24h** | Créés dans les dernières 24 heures |
| 🟣 **Prêts à convertir** | Ultra Chauds pas encore clients |
| 📧 **Sans email** | Identifiés par téléphone seulement |

#### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| [app/Services/LeadService.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/LeadService.php) | [getSegments()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/LeadService.php#306-393), [getLeadsBySegment()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/LeadService.php#394-443) |
| [app/Filament/Resources/Leads/Pages/ListLeads.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Filament/Resources/Leads/Pages/ListLeads.php) | Tabs Filament avec [getTabs()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Filament/Resources/Leads/Pages/ListLeads.php#32-96) |

#### Scénarios de test

##### Test 10 — Onglets Filament
1. Aller sur la page **Leads** dans le panel admin Filament
2. Vérifier que les **7 onglets** sont visibles (Tous + 6 segments)
3. Chaque onglet affiche un **badge** avec le nombre de leads
4. Cliquer sur "🔥 Chauds sans contact" → seuls les leads chauds/ultra chauds sans événement WhatsApp

##### Test 11 — Segment "Inactifs 7j+"
1. Cliquer sur l'onglet "💤 Inactifs 7j+"
2. Vérifier que tous les leads affichés ont une `last_activity_at` > 7 jours
3. Vérifier que les leads `client` et `member` ne sont **pas** inclus

##### Test 12 — Segment "Nouveaux 24h"
1. Créer un lead via un tunnel
2. Aller sur l'onglet "🆕 Nouveaux 24h"
3. Le lead doit apparaître immédiatement
4. Attendre (ou modifier `created_at` en base) > 24h → le lead doit disparaître

---

### 2.3 Fusion de Doublons

#### Description
Détection et fusion de leads en doublon basés sur **email** ou **téléphone**. La fusion conserve le lead principal (score le plus élevé) et transfère toutes les données des secondaires.

#### Logique de fusion

```
Lead Principal (score le plus élevé)
├── Données manquantes comblées depuis les secondaires
├── Score = MAX(tous les scores)
├── Statut = le plus avancé (priorité LeadStatus)
├── Tags = UNION de tous les tags (sans doublons)
├── Événements = tous transférés
├── Notes = toutes transférées + note de fusion
├── Alertes = toutes transférées
└── Leads secondaires = soft-deleted
```

#### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| [app/Services/LeadService.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/LeadService.php) | [findDuplicates()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/LeadService.php#450-515), [mergeLeads()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/LeadService.php#516-623) |
| [app/Filament/Resources/Leads/Tables/LeadsTable.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Filament/Resources/Leads/Tables/LeadsTable.php) | Bulk action "Fusionner les doublons" |

#### Scénarios de test

##### Test 13 — Fusionner via Filament
1. Créer (ou avoir) 2 leads avec le **même email** dans un tunnel
2. Aller dans la liste des Leads admin
3. Sélectionner les 2 leads (checkbox)
4. Cliquer sur **"Fusionner les doublons"**
5. Confirmer dans la modale
6. Vérifier :
   - Le lead avec le **meilleur score** est conservé
   - Le lead secondaire est **soft-deleted** (visible dans Corbeille)
   - Les tags des 2 leads sont fusionnés
   - Les événements sont tous rattachés au lead principal
   - Une **note de fusion** est créée sur le lead principal (🔀)

##### Test 14 — Fusion impossible avec 1 seul lead
1. Sélectionner un seul lead
2. Cliquer sur "Fusionner les doublons"
3. Vérifier le message d'alerte : "Sélectionnez au moins 2 leads"

##### Test 15 — Fusion de données manquantes
1. Avoir 2 leads :
   - Lead A : email uniquement (score 50)
   - Lead B : téléphone uniquement (score 30)
2. Fusionner les 2
3. Vérifier que le lead final a **email + téléphone** et un score de **50**

##### Test 16 — Service [findDuplicates()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/LeadService.php#450-515)
1. En Tinker ou via un test unitaire, appeler :
   ```php
   $service = app(LeadService::class);
   $duplicates = $service->findDuplicates($tenant);
   ```
2. Vérifier que les groupes de doublons sont correctement détectés

---

## 📁 Récapitulatif des fichiers modifiés

| # | Fichier | Changement |
|---|---------|------------|
| 1 | [app/Models/Page.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Models/Page.php) | [getPositionInFunnel()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Models/Page.php#141-157), [isLastPage()](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Models/Page.php#158-169) |
| 2 | [app/Services/TrackingService.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/TrackingService.php) | Auto-tagging progression, CTA client |
| 3 | [app/Http/Controllers/TrackingApiController.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Http/Controllers/TrackingApiController.php) | Endpoint CTA click tracking |
| 4 | [routes/web.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/routes/web.php) | Route API `/api/tracking/cta-click` |
| 5 | [resources/views/funnel/blocks/button.blade.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/resources/views/funnel/blocks/button.blade.php) | Tracking JS clics CTA |
| 6 | [app/Http/Controllers/CommercialDashboardController.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Http/Controllers/CommercialDashboardController.php) | Kanban 5 colonnes, drag & drop enum |
| 7 | [resources/views/commercial/leads/kanban.blade.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/resources/views/commercial/leads/kanban.blade.php) | UI 5 colonnes + animations |
| 8 | [app/Services/LeadService.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Services/LeadService.php) | Segments dynamiques + fusion doublons |
| 9 | [app/Filament/Resources/Leads/Tables/LeadsTable.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Filament/Resources/Leads/Tables/LeadsTable.php) | Bulk action fusion |
| 10 | [app/Filament/Resources/Leads/Pages/ListLeads.php](file:///Users/apple/Desktop/ProjetClient/Royal-LeadMagnet/app/Filament/Resources/Leads/Pages/ListLeads.php) | Tabs segments dynamiques |

---

> [!WARNING]
> **Points d'attention pour les testeurs :**
> - La conversion "Client" via drag & drop Kanban et via CTA tunnel sont deux chemins différents vers le même résultat — tester les 2
> - Les segments utilisent `last_activity_at` — si ce champ n'est pas renseigné sur d'anciens leads, ils apparaîtront dans "Inactifs 7j+"
> - La fusion est **irréversible** (soft-delete) — les leads supprimés restent en corbeille mais les événements sont déjà transférés

---

## 3. 📧 Relances Mails & Séquences Automatiques

### Description
Création et intégration complète d'un système de **séquences d'emails automatiques** pour la conversion des leads, rattaché au provider **Postmark API**. Les séquences se déclenchent sur actions directes ou inactivité.

### Déclencheurs supportés
| Nom | Explication | Fichiers Impactés |
|---|---|---|
| `FORM_SUBMIT` | Soumission de formulaire / Nouveau lead | `TrackingService` |
| `PAGE_VIEW` | Le lead visite une page spécifique du tunnel | `TrackingService` |
| `SCORE_THRESHOLD` | Le lead atteint un certain palier d'engagement | `LeadObserver` |
| `TAG_ASSIGNED` | Un webhook, l'auto-progression ou un Kanban tag le lead | `TrackingService` & `LeadObserver` |
| `INACTIVITY` | Le prospect n'a rien fait depuis 7 ou 14 jours | `CheckInactiveLeads` (Command) |

### Dashboard & Statistiques Apportées
- 📊 **Widget `StatsOverview`** (Filament Admin) : Affichage direct du **Total d'emails envoyés** provenant des `EmailSequenceEmailSend`.

### Fichiers modifiés pour l'Email Automation

| Fichier | Modification |
|---------|-------------|
| `.env.example`, `config/mail.php`, `config/services.php` | Raccordement à `symfony/postmark-mailer` via API. |
| `app/Services/TrackingService.php` | Injection de `EmailService` et déclenchements sur form/page/tag. |
| `app/Observers/LeadObserver.php` | Déclenchement séquence automatique sur palier de score (`SCORE_THRESHOLD`). |
| `app/Console/Commands/CheckInactiveLeads.php` | Evaluation quotidienne des inactifs + trigger des séquences de relance (`INACTIVITY`). |
| `routes/console.php` | Raccordement Cron Horaire (`email:process-sequences`). |
| `app/Filament/Widgets/StatsOverview.php` | Ajout Stat des emails envoyés. |

### Scénarios de test (Emails & Séquences)

##### Test 17 — Déclenchement automatique par formulaire
1. Créer une nouvelle séquence active depuis Filament (ex: Séquence de Bienvenue).
2. Régler le trigger sur `Soumission formulaire`.
3. S'inscrire via un tunnel avec un email valide.
4. Lancer la cron forcée : `php artisan email:process-sequences`
5. Vérifier que la table `email_sequence_subscriptions` a bien intercepté l'inscription.
6. L'email a dû arriver sur `postmark` (vérifier l'UI Postmark ou les logs laravel si en mode dev/log).

##### Test 18 — Processus Scheduler
1. Ajouter plusieurs leads ou tags pour qu'ils soient qualifiés pour des emails dans 24h.
2. Modifier exceptionnellement la date de création de l'abonnement en base pour anticiper le délai (-24h).
3. Lancer `php artisan email:process-sequences`.
4. Vérifier que le nombre "Emails Envoyés" au niveau du Dashboard Admin (StatsOverview) s'incrémente.

> [!TIP]
> Pour vérifier les logs des séquences emails en tâche de fond :
> ```bash
> tail -f storage/logs/laravel.log | grep -i "Email sequence"
> ```
