# 🧪 Guide de Tests Fonctionnels - Royal LeadMagnet

**Équipe QA - GENIUS GROUPS SAS**  
**Version** : 1.0  
**Date** : 28 Janvier 2026  
**Projet** : Royal LeadMagnet v1.0.0

---

## 📋 Table des Matières

1. [Environnement de Test](#1-environnement-de-test)
2. [Comptes de Test](#2-comptes-de-test)
3. [Tests Pages Publiques (Funnels)](#3-tests-pages-publiques-funnels)
4. [Tests Dashboard Commercial](#4-tests-dashboard-commercial)
5. [Tests Admin Filament](#5-tests-admin-filament)
6. [Tests Tracking & Géolocalisation](#6-tests-tracking--géolocalisation)
7. [Tests Attribution Leads](#7-tests-attribution-leads)
8. [Tests Alertes](#8-tests-alertes)
9. [Tests Tags & Auto-Tagging](#9-tests-tags--auto-tagging)
10. [Tests Email Sequences](#10-tests-email-sequences)
11. [Tests Responsivité](#11-tests-responsivité)
12. [Tests Performance](#12-tests-performance)
13. [Checklist Finale](#13-checklist-finale)
14. [Rapport de Bugs](#14-rapport-de-bugs)

---

## 1. Environnement de Test

### URLs de Test

| Environnement | URL |
|---------------|-----|
| **Production** | https://royalleadpro.com |
| **Admin Filament** | https://royalleadpro.com/admin |
| **Dashboard Commercial** | https://royalleadpro.com/commercial/dashboard |
| **Funnel Test** | https://groups.royalleadpro.com/f/test |

### Navigateurs à Tester

| Navigateur | Versions | Priorité |
|------------|----------|----------|
| Chrome | Dernière + N-1 | 🔴 Haute |
| Safari | Dernière | 🔴 Haute |
| Firefox | Dernière | 🟠 Moyenne |
| Edge | Dernière | 🟡 Basse |

### Devices à Tester

| Device | Résolution | Priorité |
|--------|------------|----------|
| iPhone 14 Pro | 393 x 852 | 🔴 Haute |
| Samsung Galaxy S23 | 360 x 780 | 🔴 Haute |
| iPad Pro | 1024 x 1366 | 🟠 Moyenne |
| Desktop HD | 1920 x 1080 | 🔴 Haute |
| Desktop 4K | 2560 x 1440 | 🟡 Basse |

---

## 2. Comptes de Test

### Créer les Comptes Suivants

| Rôle | Email | Mot de passe | Notes |
|------|-------|--------------|-------|
| Super Admin | admin@royalleadpro.com | `Test123!` | Accès total |
| Commercial 1 | commercial1@test.com | `Test123!` | Commercial standard |
| Commercial 2 | commercial2@test.com | `Test123!` | Pour tests multi-commerciaux |

### Leads de Test

Créer au moins 10 leads de test avec :
- Différents statuts (NEW, WARM, HOT, ULTRA_HOT, CONVERTED)
- Différentes sources (funnels variés)
- Différents pays
- Différents devices

---

## 3. Tests Pages Publiques (Funnels)

### TC-F001 : Affichage Page Funnel

| ID | TC-F001 |
|----|---------|
| **Titre** | Affichage correct d'une page funnel |
| **Prérequis** | Funnel "test" existe et est publié |
| **URL** | https://groups.royalleadpro.com/f/test |

**Étapes :**
1. Ouvrir l'URL du funnel
2. Vérifier le chargement de la page

**Résultats Attendus :**
- [ ] Page se charge en moins de 3 secondes
- [ ] Logo/branding visible
- [ ] Vidéo présente et cliquable
- [ ] Formulaire visible
- [ ] CTA WhatsApp visible
- [ ] Pas d'erreur console JavaScript

---

### TC-F002 : Soumission Formulaire

| ID | TC-F002 |
|----|---------|
| **Titre** | Soumission formulaire de capture lead |
| **Prérequis** | Funnel actif avec formulaire |

**Étapes :**
1. Accéder à la page funnel
2. Remplir le formulaire :
   - Prénom : `TestQA`
   - Email : `testqa+{timestamp}@test.com`
   - Téléphone : `+221701234567`
3. Cliquer sur "Envoyer" / "S'inscrire"
4. Vérifier la redirection

**Résultats Attendus :**
- [ ] Formulaire accepté sans erreur
- [ ] Redirection vers page Merci
- [ ] Lead créé dans la base (vérifier Admin)
- [ ] Données correctement enregistrées
- [ ] Session commercial_ref préservée

---

### TC-F003 : Tracking Vidéo

| ID | TC-F003 |
|----|---------|
| **Titre** | Tracking progression vidéo |
| **Outils** | Console navigateur (F12) |

**Étapes :**
1. Accéder à une page funnel avec vidéo
2. Ouvrir la console navigateur (F12 > Console)
3. Lancer la vidéo
4. Regarder jusqu'à 25%, 50%, 75%, 100%
5. Vérifier les appels API dans l'onglet Network

**Résultats Attendus :**
- [ ] Événement `VIDEO_25` enregistré
- [ ] Événement `VIDEO_50` enregistré
- [ ] Événement `VIDEO_75` enregistré
- [ ] Événement `VIDEO_100` enregistré
- [ ] Pas d'erreurs console

---

### TC-F004 : Clic WhatsApp

| ID | TC-F004 |
|----|---------|
| **Titre** | Tracking clic bouton WhatsApp |

**Étapes :**
1. Accéder à une page funnel
2. Soumettre le formulaire (pour créer le lead)
3. Cliquer sur le bouton WhatsApp/CTA

**Résultats Attendus :**
- [ ] Redirection vers WhatsApp (ou lien configuré)
- [ ] Événement `WHATSAPP_CLICK` créé
- [ ] Score du lead augmenté (+25 pts)
- [ ] Tag auto assigné (si configuré)

---

### TC-F005 : Sous-domaine Wildcard

| ID | TC-F005 |
|----|---------|
| **Titre** | Accès funnel via sous-domaine |
| **URL** | https://{subdomain}.royalleadpro.com |

**Étapes :**
1. Créer un funnel avec sous-domaine "demo-test"
2. Accéder à https://demo-test.royalleadpro.com
3. Vérifier l'affichage

**Résultats Attendus :**
- [ ] Page du bon funnel s'affiche
- [ ] SSL valide (cadenas vert)
- [ ] Pas d'erreur de certificat
- [ ] Formulaire fonctionne

---

## 4. Tests Dashboard Commercial

### TC-C001 : Connexion Commercial

| ID | TC-C001 |
|----|---------|
| **Titre** | Authentification dashboard commercial |
| **URL** | https://royalleadpro.com/commercial/login |

**Étapes :**
1. Accéder à la page de connexion
2. Entrer email : `commercial1@test.com`
3. Entrer mot de passe : `Test123!`
4. Cliquer "Se connecter"

**Résultats Attendus :**
- [ ] Connexion réussie
- [ ] Redirection vers /commercial/dashboard
- [ ] Nom du commercial affiché
- [ ] Pas d'accès à /admin (403)

---

### TC-C002 : Dashboard Stats

| ID | TC-C002 |
|----|---------|
| **Titre** | Affichage statistiques dashboard |

**Étapes :**
1. Se connecter en tant que commercial
2. Aller sur /commercial/dashboard
3. Vérifier les 4 cartes stats

**Résultats Attendus :**
- [ ] Card "Total Leads" affiche un nombre
- [ ] Card "Leads Chauds" affiche un nombre
- [ ] Card "Conversions" affiche un nombre
- [ ] Card "Tunnels Actifs" affiche un nombre
- [ ] Graphique 30 jours affiché
- [ ] Widget alertes visible

---

### TC-C003 : Liste Leads

| ID | TC-C003 |
|----|---------|
| **Titre** | Affichage et filtrage liste leads |
| **URL** | /commercial/leads |

**Étapes :**
1. Accéder à /commercial/leads
2. Vérifier l'affichage de la table
3. Tester les filtres (statut, funnel)
4. Tester la recherche

**Résultats Attendus :**
- [ ] Table affiche les leads du commercial
- [ ] Colonnes : Lead, Source, Score, Date, Contact
- [ ] Filtre par statut fonctionne
- [ ] Filtre par funnel fonctionne
- [ ] Recherche par nom/email fonctionne
- [ ] Pagination fonctionne

---

### TC-C004 : Vue Kanban

| ID | TC-C004 |
|----|---------|
| **Titre** | Pipeline Kanban leads |
| **URL** | /commercial/leads?view=kanban |

**Étapes :**
1. Accéder à /commercial/leads
2. Cliquer sur "Vue Kanban"
3. Vérifier les 4 colonnes
4. Drag & drop un lead vers une autre colonne

**Résultats Attendus :**
- [ ] 4 colonnes affichées (FROID, TIÈDE, CHAUD, CONVERTI)
- [ ] Cards leads dans les bonnes colonnes
- [ ] Drag & drop fonctionne
- [ ] Statut lead mis à jour après drop
- [ ] Compteurs colonnes corrects

---

### TC-C005 : Actions Rapides

| ID | TC-C005 |
|----|---------|
| **Titre** | Actions sur lead (WhatsApp, Email) |

**Étapes :**
1. Dans la liste des leads, trouver un lead avec téléphone
2. Cliquer sur l'icône WhatsApp
3. Vérifier la redirection
4. Cliquer sur l'icône Email

**Résultats Attendus :**
- [ ] Bouton WhatsApp ouvre wa.me/{numéro}
- [ ] Bouton Email ouvre mailto:{email}
- [ ] Numéro formaté correctement

---

### TC-C006 : Configuration Lien Commercial

| ID | TC-C006 |
|----|---------|
| **Titre** | Personnalisation lien funnel |
| **URL** | /commercial/funnels |

**Étapes :**
1. Accéder à /commercial/funnels
2. Choisir un funnel
3. Cliquer "Configurer"
4. Modifier le slug personnalisé (ex: "mon-lien-test")
5. Sauvegarder
6. Tester le lien généré

**Résultats Attendus :**
- [ ] Formulaire de configuration s'affiche
- [ ] Slug personnalisé sauvegardé
- [ ] Lien généré accessible
- [ ] Leads via ce lien attribués au commercial

---

## 5. Tests Admin Filament

### TC-A001 : Connexion Admin

| ID | TC-A001 |
|----|---------|
| **Titre** | Authentification admin Filament |
| **URL** | https://royalleadpro.com/admin |

**Étapes :**
1. Accéder à /admin
2. Entrer credentials admin
3. Vérifier l'accès dashboard

**Résultats Attendus :**
- [ ] Page login Filament s'affiche
- [ ] Connexion réussie
- [ ] Dashboard avec widgets visible
- [ ] Menu navigation complet

---

### TC-A002 : CRUD Leads

| ID | TC-A002 |
|----|---------|
| **Titre** | Gestion leads dans Filament |
| **URL** | /admin/leads |

**Étapes :**
1. Lister les leads
2. Ouvrir un lead (View)
3. Vérifier tous les onglets
4. Modifier un lead (Edit)
5. Sauvegarder

**Résultats Attendus :**
- [ ] Liste affiche tous les leads
- [ ] Filtres fonctionnent (statut, device, pays, tags)
- [ ] Vue détail : 4 onglets présents
- [ ] Onglet Tracking : données affichées
- [ ] Onglet Activité : timeline visible
- [ ] Modification sauvegardée

---

### TC-A003 : CRUD Tags

| ID | TC-A003 |
|----|---------|
| **Titre** | Gestion tags |
| **URL** | /admin/tags |

**Étapes :**
1. Créer un nouveau tag
2. Définir nom, couleur, description
3. Sauvegarder
4. Vérifier dans la liste
5. Assigner à un lead (bulk action)

**Résultats Attendus :**
- [ ] Tag créé avec succès
- [ ] Couleur correcte dans la liste
- [ ] Bulk action "Assigner Tags" fonctionne
- [ ] Tag visible sur le lead

---

### TC-A004 : CRUD Email Sequences

| ID | TC-A004 |
|----|---------|
| **Titre** | Gestion séquences email |
| **URL** | /admin/email-sequences |

**Étapes :**
1. Créer une nouvelle séquence
2. Définir nom, trigger, statut
3. Ajouter 2-3 emails à la séquence
4. Activer la séquence
5. Vérifier dans la liste

**Résultats Attendus :**
- [ ] Séquence créée
- [ ] Emails ajoutés (RelationManager)
- [ ] Délais configurables
- [ ] Toggle Active/Pause fonctionne
- [ ] Stats affichées (envoyés, ouvertures)

---

### TC-A005 : CRUD Funnels

| ID | TC-A005 |
|----|---------|
| **Titre** | Gestion tunnels de vente |
| **URL** | /admin/funnels |

**Étapes :**
1. Créer un nouveau funnel
2. Configurer nom, sous-domaine
3. Ajouter des pages
4. Publier le funnel
5. Tester l'accès public

**Résultats Attendus :**
- [ ] Funnel créé
- [ ] Sous-domaine généré automatiquement
- [ ] Pages ajoutables
- [ ] Funnel accessible via URL publique

---

### TC-A006 : Widgets Dashboard

| ID | TC-A006 |
|----|---------|
| **Titre** | Widgets analytics dashboard |
| **URL** | /admin |

**Étapes :**
1. Accéder au dashboard admin
2. Vérifier les 6 widgets
3. Interagir avec les graphiques

**Résultats Attendus :**
- [ ] LeadOverviewWidget : 4 stats visibles
- [ ] TagsStatsWidget : stats tags
- [ ] EngagementStatsWidget : courbes visibles
- [ ] LeadsByDeviceWidget : donut chart
- [ ] ConversionByCountryWidget : bar chart
- [ ] RecentActivityWidget : table cliquable

---

## 6. Tests Tracking & Géolocalisation

### TC-T001 : Géolocalisation Lead

| ID | TC-T001 |
|----|---------|
| **Titre** | Données géolocalisation enregistrées |
| **Prérequis** | VPN ou test depuis différents pays |

**Étapes :**
1. Soumettre un formulaire depuis une IP connue
2. Vérifier le lead créé dans Admin
3. Onglet "Tracking" > Section "Géolocalisation"

**Résultats Attendus :**
- [ ] Pays détecté correctement
- [ ] Ville détectée
- [ ] Région détectée
- [ ] Timezone correct
- [ ] Latitude/Longitude présents
- [ ] Logs serveur : "ip-api.com success" ou autre API

---

### TC-T002 : Device Detection

| ID | TC-T002 |
|----|---------|
| **Titre** | Détection appareil/navigateur |

**Étapes :**
1. Soumettre formulaire depuis Mobile
2. Soumettre formulaire depuis Desktop
3. Vérifier les leads créés

**Résultats Attendus :**
- [ ] device_type correct (mobile/desktop)
- [ ] browser correct (Chrome, Safari...)
- [ ] browser_version présent
- [ ] os correct (iOS, Android, Windows...)
- [ ] screen_resolution présent

---

### TC-T003 : Temps Passé & Scroll

| ID | TC-T003 |
|----|---------|
| **Titre** | Tracking comportemental |

**Étapes :**
1. Accéder à un funnel
2. Attendre 2 minutes sur la page
3. Scroller jusqu'en bas (100%)
4. Soumettre le formulaire
5. Vérifier l'événement créé

**Résultats Attendus :**
- [ ] time_on_page ≈ 120 secondes
- [ ] scroll_depth = 100 ou proche
- [ ] Événement PAGE_VIEW enregistré

---

## 7. Tests Attribution Leads

### TC-AT001 : Attribution via URL Commercial

| ID | TC-AT001 |
|----|---------|
| **Titre** | Lead attribué au bon commercial |

**Étapes :**
1. Obtenir le lien personnalisé du Commercial 1
2. Ouvrir ce lien en navigation privée
3. Soumettre le formulaire
4. Vérifier dans Admin → lead → "brought_by"
5. Vérifier Dashboard Commercial 1

**Résultats Attendus :**
- [ ] Lead créé avec brought_by = ID Commercial 1
- [ ] Lead visible dans dashboard Commercial 1
- [ ] Lead NON visible pour Commercial 2
- [ ] Stats Commercial 1 incrémentées

---

### TC-AT002 : Attribution Session Perdue

| ID | TC-AT002 |
|----|---------|
| **Titre** | Attribution même si session perdue |

**Étapes :**
1. Ouvrir lien commercial
2. Fermer le navigateur
3. Rouvrir le même lien
4. Soumettre formulaire

**Résultats Attendus :**
- [ ] Lead attribué au commercial (hidden input fallback)
- [ ] Pas de perte d'attribution

---

## 8. Tests Alertes

### TC-AL001 : Widget Alertes Dashboard

| ID | TC-AL001 |
|----|---------|
| **Titre** | Affichage widget alertes |

**Étapes :**
1. Se connecter en commercial
2. Dashboard → Widget Alertes
3. Vérifier le contenu

**Résultats Attendus :**
- [ ] Widget visible
- [ ] Badge compteur (si alertes)
- [ ] 5 dernières alertes affichées
- [ ] Lien "Tout voir" fonctionne

---

### TC-AL002 : Page Liste Alertes

| ID | TC-AL002 |
|----|---------|
| **Titre** | Liste complète alertes |
| **URL** | /commercial/alerts |

**Étapes :**
1. Accéder à /commercial/alerts
2. Vérifier les filtres
3. Marquer une alerte comme lue
4. Marquer toutes comme lues

**Résultats Attendus :**
- [ ] Summary cards (4 KPIs)
- [ ] Filtre statut fonctionne
- [ ] "Marquer comme lu" fonctionne (AJAX)
- [ ] "Tout marquer comme lu" fonctionne
- [ ] Badge navigation mis à jour

---

### TC-AL003 : Création Alerte Automatique

| ID | TC-AL003 |
|----|---------|
| **Titre** | Alerte créée sur lead HOT |

**Étapes :**
1. Modifier un lead : score = 35 (HOT)
2. Sauvegarder
3. Vérifier les alertes du commercial

**Résultats Attendus :**
- [ ] Alerte "Lead devient CHAUD" créée
- [ ] Alerte visible dans widget
- [ ] Type : HOT_LEAD

---

## 9. Tests Tags & Auto-Tagging

### TC-TAG001 : Assignation Manuelle Tag

| ID | TC-TAG001 |
|----|---------|
| **Titre** | Bulk action assigner tag |

**Étapes :**
1. Admin → Leads
2. Sélectionner 3 leads (checkboxes)
3. Bulk Actions → "Assigner Tags"
4. Choisir tag "VIP"
5. Confirmer

**Résultats Attendus :**
- [ ] 3 leads ont maintenant le tag VIP
- [ ] Badge tag visible sur chaque lead
- [ ] Compteur tag incrémenté

---

### TC-TAG002 : Auto-Tag Vidéo Complete

| ID | TC-TAG002 |
|----|---------|
| **Titre** | Tag auto sur vidéo 100% |
| **Prérequis** | Tag "video_complete" existe et is_auto=true |

**Étapes :**
1. Accéder à un funnel avec vidéo
2. Soumettre formulaire (créer lead)
3. Regarder la vidéo jusqu'à 100%
4. Vérifier le lead dans Admin

**Résultats Attendus :**
- [ ] Tag "Vidéo Complète" assigné automatiquement
- [ ] Visible dans onglet Informations du lead

---

### TC-TAG003 : Auto-Tag Score HOT

| ID | TC-TAG003 |
|----|---------|
| **Titre** | Tag auto sur score ≥ 31 |
| **Prérequis** | Tag "score_threshold_31" existe |

**Étapes :**
1. Créer/modifier lead avec score = 35
2. Sauvegarder
3. Vérifier les tags du lead

**Résultats Attendus :**
- [ ] Tag "Lead HOT" assigné automatiquement

---

## 10. Tests Email Sequences

### TC-ES001 : Création Séquence

| ID | TC-ES001 |
|----|---------|
| **Titre** | Créer séquence email complète |

**Étapes :**
1. Admin → Email Sequences → Créer
2. Nom : "Test QA Sequence"
3. Trigger : FORM_SUBMIT
4. Ajouter 3 emails avec délais (0h, 24h, 72h)
5. Activer

**Résultats Attendus :**
- [ ] Séquence créée
- [ ] 3 emails listés
- [ ] Statut "Active"

---

### TC-ES002 : Inscription Lead à Séquence

| ID | TC-ES002 |
|----|---------|
| **Titre** | Bulk action démarrer séquence |

**Étapes :**
1. Admin → Leads
2. Sélectionner 3 leads
3. Bulk Actions → "Démarrer Séquence Email"
4. Choisir "Test QA Sequence"
5. Confirmer

**Résultats Attendus :**
- [ ] Notification "3 leads inscrits"
- [ ] Compteur "Abonnés" incrémenté
- [ ] Pas de doublon si lead déjà inscrit

---

## 11. Tests Responsivité

### TR-001 : Dashboard Commercial Mobile

| ID | TR-001 |
|----|---------|
| **Titre** | Dashboard responsive mobile |
| **Device** | iPhone 14 (393x852) |

**Vérifications :**
- [ ] Menu hamburger visible
- [ ] Stats cards empilées verticalement
- [ ] Graphique lisible
- [ ] Widget alertes fonctionnel
- [ ] Tableau leads scrollable horizontalement

---

### TR-002 : Funnel Mobile

| ID | TR-002 |
|----|---------|
| **Titre** | Page funnel responsive |
| **Device** | Mobile |

**Vérifications :**
- [ ] Vidéo 100% largeur
- [ ] Formulaire affichage correct
- [ ] Boutons cliquables (taille suffisante)
- [ ] Pas de scroll horizontal
- [ ] Textes lisibles

---

### TR-003 : Kanban Tablet

| ID | TR-003 |
|----|---------|
| **Titre** | Kanban sur tablette |
| **Device** | iPad |

**Vérifications :**
- [ ] 4 colonnes visibles
- [ ] Drag & drop fonctionne au touch
- [ ] Cards lisibles

---

## 12. Tests Performance

### TP-001 : Temps de Chargement

| ID | TP-001 |
|----|---------|
| **Titre** | Performance chargement pages |
| **Outil** | Chrome DevTools > Lighthouse |

**Pages à tester :**

| Page | Objectif LCP | Objectif FCP |
|------|--------------|--------------|
| Funnel public | < 2.5s | < 1.8s |
| Dashboard Commercial | < 3s | < 2s |
| Admin Filament | < 4s | < 2.5s |

---

### TP-002 : Requêtes API

| ID | TP-002 |
|----|---------|
| **Titre** | Performance API tracking |

**Étapes :**
1. Ouvrir DevTools > Network
2. Naviguer sur un funnel
3. Vérifier les appels /api/track/*

**Résultats Attendus :**
- [ ] Réponses < 500ms
- [ ] Pas d'erreurs 500
- [ ] Pas de requêtes bloquantes

---

## 13. Checklist Finale

### Avant Livraison Client ✅

#### Fonctionnalités Core
- [ ] Connexion admin fonctionne
- [ ] Connexion commercial fonctionne
- [ ] Création lead via formulaire
- [ ] Tracking device/géoloc correct
- [ ] Attribution commerciaux correcte

#### Dashboard Commercial
- [ ] Stats affichées correctement
- [ ] Liste leads fonctionnelle
- [ ] Kanban drag & drop OK
- [ ] Alertes visibles et cliquables
- [ ] Configuration liens OK

#### Admin Filament
- [ ] CRUD Leads complet
- [ ] CRUD Tags complet
- [ ] CRUD Séquences Email
- [ ] CRUD Funnels
- [ ] 6 Widgets dashboard

#### Qualité
- [ ] Pas d'erreurs console JS
- [ ] Pas d'erreurs PHP (logs propres)
- [ ] Responsive mobile OK
- [ ] Performance acceptable

#### Sécurité
- [ ] HTTPS actif
- [ ] CSRF protection active
- [ ] Rôles/permissions respectés
- [ ] Pas d'accès non autorisé

---

## 14. Rapport de Bugs

### Template de Rapport Bug

```markdown
## Bug Report

**ID** : BUG-XXX
**Date** : JJ/MM/AAAA
**Rapporteur** : [Nom QA]
**Sévérité** : 🔴 Critique / 🟠 Majeur / 🟡 Mineur / 🟢 Cosmétique

### Description
[Description claire du problème]

### Étapes de Reproduction
1. [Étape 1]
2. [Étape 2]
3. [Étape 3]

### Résultat Obtenu
[Ce qui se passe réellement]

### Résultat Attendu
[Ce qui devrait se passer]

### Environnement
- **Navigateur** : Chrome 120
- **OS** : macOS 14.2
- **Device** : Desktop
- **URL** : https://...

### Captures d'écran
[Joindre screenshots si applicable]

### Logs Console
```
[Copier les erreurs console si présentes]
```

### Priorité Fix
- [ ] Bloquant livraison
- [ ] À corriger avant livraison
- [ ] Peut être livré, fix ultérieur
```

---

## 📞 Contacts

| Rôle | Nom | Contact |
|------|-----|---------|
| **Lead QA** | [Nom] | qa@geniusgroups.com |
| **Lead Dev** | [Nom] | dev@geniusgroups.com |
| **Chef de Projet** | [Nom] | pm@geniusgroups.com |

---

## 📋 Historique des Tests

| Date | Testeur | Version | Résultat |
|------|---------|---------|----------|
| 28/01/2026 | | v1.0.0 | En cours |

---

<p align="center">
  <strong>GENIUS GROUPS SAS</strong><br>
  <sub>Département Qualité - Guide de Tests Fonctionnels</sub>
</p>
