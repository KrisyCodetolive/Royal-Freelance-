# 📧 Phase 5 - Email Sequences UI Complet

## ✅ Ce Qui a Été Implémenté

**Interface complète Filament** pour gérer les séquences email automatisées avec déclencheurs, emails personnalisés et statistiques.

---

## 🎯 Vue d'Ensemble

### Architecture Email Sequences

```
EmailSequence (Séquence principale)
    ├── Trigger (Déclencheur)
    ├── Status (Brouillon, Active, Pause, Archive)
    ├── Settings (Configuration)
    │
    ├── EmailSequenceEmail (Emails de la séquence)
    │   ├── Subject + Content
    │   ├── send_after_hours (Délai)
    │   ├── Stats (sent, opened, clicked)
    │   └── is_active
    │
    └── EmailSequenceSubscription (Inscriptions leads)
        ├── Lead inscrit
        ├── subscribed_at
        └── EmailSequenceEmailSend (Historique envois)
```

---

## 📁 Composants Créés

### 1. EmailSequenceResource
**Fichier** : `app/Filament/Resources/EmailSequences/EmailSequenceResource.php`

**Configuration** :
- **Navigation** : Group "Marketing", Sort #1
- **Badge** : Nombre de séquences actives (vert)
- **Icône** : 📧 Envelope Open
- **Relations** : EmailsRelationManager

---

### 2. EmailSequenceForm
**Fichier** : `app/Filament/Resources/EmailSequences/Schemas/EmailSequenceForm.php`

#### Section 1 : Informations Générales (2 colonnes)

**Champs** :
- **Nom** : Nom de la séquence (required)
- **Description** : Objectif de la séquence (textarea)
- **Statut** : Draft / Active / Paused / Archived
- **Tunnel** : Lier à un tunnel spécifique (optionnel)

#### Section 2 : Déclenchement (1 colonne)

**Triggers Disponibles** :

| Trigger | Label | Conditions Requises |
|---------|-------|---------------------|
| `FORM_SUBMIT` | Soumission formulaire | Aucune |
| `SCORE_THRESHOLD` | Seuil de score | `min_score` (1-100) |
| `PAGE_VIEW` | Vue page spécifique | `page_id` (select) |
| `TAG_ASSIGNED` | Tag assigné | `tag_id` (select) |
| `INACTIVITY` | Inactivité prolongée | `days` (1-365) |
| `MANUAL` | Déclenchement manuel | Aucune |

**Champs Dynamiques** :
- Affichage conditionnel selon trigger sélectionné
- Helper texts explicatifs
- Validation selon type

**Exemple** :
```
Trigger: "Seuil de score"
→ Affiche: "Score minimum" (TextInput 1-100)

Trigger: "Tag assigné"
→ Affiche: "Tag spécifique" (Select tags)
```

#### Section 3 : Paramètres Avancés (collapsed)

**4 toggles** :
- ✅ **Envoyer le week-end** : Autoriser samedi/dimanche
- ✅ **Heure optimale** : Envoyer selon timezone lead
- ✅ **Arrêter si réponse** : Stopper si lead répond
- ✅ **Arrêter si conversion** : Stopper si lead convertit

---

### 3. EmailSequencesTable
**Fichier** : `app/Filament/Resources/EmailSequences/Tables/EmailSequencesTable.php`

#### Colonnes

| Colonne | Format | Badge/Couleur | Tri |
|---------|--------|---------------|-----|
| **Nom** | Texte + description | - | ✅ |
| **Statut** | Badge | Draft/Active/Paused/Archived | ✅ |
| **Déclencheur** | Badge + icône | Selon trigger | ✅ |
| **Emails** | Count | Badge info + " emails" | ✅ |
| **Abonnés** | Count subscriptions | Badge primary | ✅ |
| **Envoyés** | stats.sent | Badge success | - |
| **Taux ouverture** | % ouverture | Vert ≥20%, Orange 10-20%, Rouge <10% | - |
| **Taux clic** | % clic | Vert ≥5%, Orange 2-5%, Rouge <2% | - |
| **Tunnel** | Relation | - | - (masqué) |
| **Créée le** | Date | - | ✅ (masqué) |

#### Filtres

1. **Statut** : Multiple (Draft, Active, Paused, Archived)
2. **Déclencheur** : Multiple (6 triggers)
3. **Tunnel** : Relation searchable

#### Actions sur Ligne

**1. Toggle Status (Play/Pause)**
- **Si Active** : "Mettre en pause" (⏸️ warning)
- **Si Paused/Draft** : "Activer" (▶️ success)
- Confirmation avec modal explicatif
- Change entre ACTIVE ↔ PAUSED

**2. View** : Consulter la séquence

**3. Edit** : Modifier la séquence

#### Bulk Actions
- **Supprimer** : Delete multiple sequences

**Tri par défaut** : Date création DESC

---

### 4. EmailsRelationManager
**Fichier** : `app/Filament/Resources/EmailSequences/RelationManagers/EmailsRelationManager.php`

**Relation** : `emails` (HasMany EmailSequenceEmail)

#### Form Email

**Section 1 : Contenu de l'Email**

- **Sujet** : TextInput avec variables
  - Placeholder : `{first_name}, avez-vous vu notre offre ?`
  - Variables : `{first_name}`, `{last_name}`, `{funnel_name}`

- **Contenu** : RichEditor avec toolbar
  - Boutons : Bold, Italic, Underline, Link, Lists, H2, H3
  - Variables : `{first_name}`, `{last_name}`, `{full_name}`, `{email}`, `{score}`, `{funnel_name}`

**Section 2 : Paramètres d'Envoi**

- **Délai d'envoi** : TextInput heures
  - 0 = immédiat
  - 24 = 1 jour
  - 168 = 7 jours
  - Helper : "0 = immédiat, 24 = 1 jour, 168 = 7 jours"

- **Email actif** : Toggle (true par défaut)

#### Table Emails

**Colonnes** :

| Colonne | Format | Badge/Couleur |
|---------|--------|---------------|
| **Délai** | Badge formaté | Rouge (0h), Orange (<24h), Bleu (<7j), Vert (≥7j) |
| **Sujet** | Texte limité 50 car | Bold |
| **Actif** | Icône boolean | ✓ Vert / ✗ Rouge |
| **Envoyés** | stats.sent | Badge info |
| **Ouvertures** | % ouverture | Vert |
| **Clics** | % clic | Orange |

**Format Délai** :
```
0h     → ⚡ Immédiat (rouge)
12h    → 12h (orange)
48h    → 2j (bleu)
168h   → 1 sem. (vert)
```

**Reorderable** : ✅ Drag & drop par `send_after_hours`

**Filtres** :
- **Actif** : Ternary (Tous / Actifs / Inactifs)

**Header Actions** :
- **Ajouter un Email** : Crée nouvel email dans séquence

**Actions sur Ligne** :

**1. Dupliquer**
- Copie l'email avec +24h de délai
- Confirmation modal
- Utile pour séquences répétitives

**2. Edit** : Modifier email

**3. Delete** : Supprimer email

**Bulk Actions** :
- **Delete** : Supprimer plusieurs emails

**Empty State** :
- Heading : "Aucun email dans cette séquence"
- Description : "Ajoutez votre premier email pour commencer"
- Icône : 📧

**Tri par défaut** : `send_after_hours ASC`

---

### 5. Pages Filament

#### ListEmailSequences
**Fichier** : `app/Filament/Resources/EmailSequences/Pages/ListEmailSequences.php`

**Header Action** :
- "Nouvelle Séquence" (+ plus icon)

#### CreateEmailSequence
**Fichier** : `app/Filament/Resources/EmailSequences/Pages/CreateEmailSequence.php`

**Comportement** :
- Après création → Redirect vers Edit
- Notification : "Séquence créée avec succès"

#### EditEmailSequence
**Fichier** : `app/Filament/Resources/EmailSequences/Pages/EditEmailSequence.php`

**Header Actions** :
- **Prévisualiser** : Voir la séquence (icône 👁️)
- **Supprimer** : Delete séquence

**Notification** : "Séquence mise à jour"

---

### 6. Bulk Action dans LeadsTable

**Fichier** : `app/Filament/Resources/Leads/Tables/LeadsTable.php`

#### Nouvelle Bulk Action : "Démarrer Séquence Email"

**Icône** : 📧 Envelope  
**Couleur** : Primary (bleu)

**Form** :
```php
Select::make('sequence_id')
    ->label('Séquence Email')
    ->options(EmailSequence::where('status', 'active')->pluck('name', 'id'))
    ->required()
    ->searchable()
    ->helperText('Les leads seront inscrits à cette séquence automatiquement')
```

**Action** :
1. Récupère la séquence sélectionnée
2. Pour chaque lead sélectionné :
   - Vérifie s'il n'est pas déjà inscrit
   - Si non inscrit → `subscribeLead()`
3. Compte le nombre d'inscriptions
4. Notification : "{count} leads inscrits à la séquence"

**Protection** : Évite les doublons (ne réinscrit pas si déjà inscrit)

---

## 🎬 Scénarios d'Utilisation

### Scénario 1 : Créer Séquence "Relance Inactifs"

**Étapes** :

1. **Marketing → Séquences Email → Nouvelle Séquence**

2. **Remplir formulaire** :
   ```
   Nom: Relance Leads Inactifs 7 jours
   Description: Réengager les leads qui n'ont pas ouvert depuis 1 semaine
   Statut: Brouillon (pour tester)
   Tunnel: (vide - toutes sources)
   
   Déclencheur: Inactivité prolongée
   Nombre de jours: 7
   
   Paramètres:
   ☑ Envoyer le week-end
   ☑ Heure optimale
   ☑ Arrêter si réponse
   ☑ Arrêter si conversion
   ```

3. **Sauvegarder** → Redirect vers Edit

4. **Onglet "Emails de la Séquence"**

5. **Ajouter Email #1** (Rappel doux) :
   ```
   Sujet: {first_name}, vous nous manquez ! 👋
   Contenu:
   Bonjour {first_name},
   
   Nous avons remarqué que vous n'avez pas visité {funnel_name} depuis quelques jours.
   
   Y a-t-il quelque chose que nous pouvons faire pour vous aider ?
   
   [BOUTON: Reprendre où j'en étais]
   
   Délai: 0 heures (immédiat)
   Actif: ✓
   ```

6. **Ajouter Email #2** (Offre spéciale) :
   ```
   Sujet: {first_name}, offre exclusive pour vous 🎁
   Contenu:
   Bonjour {first_name},
   
   Pour vous remercier de votre intérêt, voici une offre exclusive :
   -20% sur votre première commande !
   
   Code: RETOUR20
   Valable 48h uniquement.
   
   Délai: 72 heures (3 jours après email 1)
   Actif: ✓
   ```

7. **Ajouter Email #3** (Dernière tentative) :
   ```
   Sujet: Dernière chance {first_name} ⏰
   Contenu:
   Bonjour {first_name},
   
   Votre offre exclusive expire dans 24h !
   
   Ne manquez pas cette opportunité.
   
   Délai: 168 heures (7 jours après email 1)
   Actif: ✓
   ```

8. **Changer statut → ACTIVE**

9. **Résultat** : Séquence activée, s'exécute automatiquement sur leads inactifs 7j+

---

### Scénario 2 : Inscription Manuelle de Leads

**Objectif** : Démarrer séquence sur 50 leads spécifiques.

**Étapes** :

1. **Gestion Leads → Leads**

2. **Filtrer leads** :
   - Statut: NEW
   - Tags: Intéressé Formation
   - Device: Mobile

3. **Sélectionner 50 leads** (checkboxes)

4. **Bulk Actions → Démarrer Séquence Email**

5. **Choisir séquence** : "Formation Mobile - Onboarding"

6. **Confirmer**

7. **Notification** : "50 leads inscrits à la séquence"

8. **Résultat** :
   - 50 EmailSequenceSubscription créées
   - subscribed_at = now()
   - Premier email envoyé selon délai (ex: immédiat)

---

### Scénario 3 : Séquence Déclenchée par Tag

**Objectif** : Envoyer séquence quand tag "VIP" assigné.

**Étapes** :

1. **Créer séquence "Onboarding VIP"** :
   ```
   Déclencheur: Tag assigné
   Tag spécifique: VIP
   ```

2. **Ajouter 3 emails** (bienvenue, avantages, offre exclusive)

3. **Activer séquence**

4. **Quand tag "VIP" assigné** :
   - Automatiquement (via auto-tagging)
   - OU manuellement (bulk action)
   
5. **Résultat** :
   - Observer dans EmailSequence vérifie `checkTriggerConditions()`
   - Si lead a tag "VIP" → `subscribeLead()`
   - Séquence démarre automatiquement

**Note** : Nécessite Observer pour fonctionner (Phase 6 - à implémenter)

---

### Scénario 4 : Analyser Performance Séquence

**Objectif** : Voir quelle séquence convertit le mieux.

**Étapes** :

1. **Marketing → Séquences Email**

2. **Tableau affiche** :
   ```
   Nom                      | Abonnés | Envoyés | Ouverture | Clic
   -------------------------|---------|---------|-----------|------
   Relance Inactifs 7j      | 234     | 702     | 22.5% ✅  | 8.1% ✅
   Formation Mobile         | 156     | 468     | 18.2% ⚠️  | 4.3% ⚠️
   Offre Flash VIP          | 89      | 267     | 31.2% ✅  | 12.4% ✅
   Onboarding Général       | 1,024   | 3,072   | 15.7% 🔴  | 2.9% 🔴
   ```

3. **Analyser** :
   - ✅ "Offre Flash VIP" : Meilleur taux (31% ouverture, 12% clic)
   - 🔴 "Onboarding Général" : Faible taux (15% ouverture)

4. **Actions** :
   - Dupliquer "Offre Flash VIP" pour autres segments
   - Améliorer "Onboarding Général" (sujet, contenu)
   - Tester A/B variants

---

### Scénario 5 : Dupliquer Email dans Séquence

**Objectif** : Créer rapidement variant d'un email.

**Étapes** :

1. **Éditer séquence**

2. **Onglet "Emails"**

3. **Email "Offre 20%" (délai 72h)**

4. **Action → Dupliquer**

5. **Confirmation** : "L'email sera dupliqué avec +24h"

6. **Résultat** :
   - Nouvel email créé
   - Délai = 96h (72h + 24h)
   - Même sujet et contenu
   - Peut être modifié pour variant

7. **Modifier** : Changer sujet pour tester A/B

---

## 📊 KPIs & Statistiques

### Au Niveau Séquence

**Métriques Globales** :
- **Abonnés** : Nombre de leads inscrits
- **Envoyés** : Total emails envoyés
- **Taux ouverture** : % emails ouverts
- **Taux clic** : % emails cliqués

**Calculs** :
```php
$openRate = ($stats['opened'] / $stats['sent']) * 100;
$clickRate = ($stats['clicked'] / $stats['sent']) * 100;
```

**Couleurs** :
- Vert : ≥ 20% (ouverture) / ≥ 5% (clic)
- Orange : 10-20% (ouverture) / 2-5% (clic)
- Rouge : < 10% (ouverture) / < 2% (clic)

### Au Niveau Email

**Métriques par Email** :
- **Envoyés** : Nombre d'envois
- **Ouvertures** : % ouverture
- **Clics** : % clic

**Affichage** :
```
Email #1 (⚡ Immédiat): 234 envoyés, 22.5% ouverture, 8.1% clic
Email #2 (3j):          189 envoyés, 18.2% ouverture, 4.3% clic
Email #3 (7j):          156 envoyés, 15.7% ouverture, 2.9% clic
```

---

## 🔧 Fonctionnalités Avancées

### Variables de Personnalisation

**Dans Sujet** :
- `{first_name}` → Prénom lead
- `{last_name}` → Nom lead
- `{funnel_name}` → Nom du tunnel

**Dans Contenu** :
- `{first_name}`, `{last_name}`, `{full_name}`
- `{email}` → Email lead
- `{score}` → Score lead
- `{funnel_name}` → Nom tunnel
- `{tenant_name}` → Nom tenant

**Exemple** :
```
Sujet: {first_name}, votre score est de {score} points !

Contenu:
Bonjour {full_name},

Félicitations ! Vous avez atteint {score} points dans {funnel_name}.

Vous êtes presque prêt pour la prochaine étape.

L'équipe {tenant_name}
```

**Rendu** :
```
Sujet: Marie, votre score est de 45 points !

Contenu:
Bonjour Marie Dupont,

Félicitations ! Vous avez atteint 45 points dans Formation Mobile.

Vous êtes presque prêt pour la prochaine étape.

L'équipe Royal LeadMagnet
```

### Conditions d'Arrêt

**4 paramètres configurables** :

1. **send_on_weekends** (false par défaut)
   - Si false : Emails repoussés au lundi si tombent samedi/dimanche
   - Si true : Envoi 7j/7

2. **send_at_optimal_time** (true par défaut)
   - Si true : Envoyer selon timezone lead (ex: 10h locale)
   - Si false : Envoyer immédiatement selon délai

3. **stop_on_reply** (true par défaut)
   - Si lead répond à un email → Arrêter séquence
   - Évite spam si lead engage

4. **stop_on_conversion** (true par défaut)
   - Si lead passe à statut CONVERTED → Arrêter séquence
   - Inutile de relancer un client converti

---

## 🚧 Phase 6 Suggérée - Automatisations

### À Implémenter

**1. Observer pour Auto-Inscription** :

```php
// app/Observers/EmailSequenceObserver.php

class EmailSequenceObserver
{
    public function created(Event $event): void
    {
        // Trouver séquences avec trigger correspondant
        $sequences = EmailSequence::where('status', 'active')
            ->where('trigger', /* selon event type */)
            ->get();
        
        foreach ($sequences as $sequence) {
            if ($sequence->checkTriggerConditions($event->lead)) {
                $sequence->subscribeLead($event->lead);
            }
        }
    }
}
```

**2. Job Quotidien pour Envoi Emails** :

```php
// app/Console/Commands/SendSequenceEmails.php

public function handle()
{
    $subscriptions = EmailSequenceSubscription::whereNotNull('subscribed_at')
        ->where('completed', false)
        ->get();
    
    foreach ($subscriptions as $subscription) {
        $sequence = $subscription->emailSequence;
        
        foreach ($sequence->emails as $email) {
            if ($email->shouldSendTo($subscription)) {
                // Envoyer email
                Mail::to($subscription->lead)->send(new SequenceEmail($email, $subscription->lead));
                
                // Logger envoi
                EmailSequenceEmailSend::create([
                    'email_id' => $email->id,
                    'subscription_id' => $subscription->id,
                    'sent_at' => now(),
                ]);
            }
        }
    }
}
```

**3. Webhook pour Tracking Ouvertures/Clics** :

```php
// routes/web.php
Route::get('/email/open/{send_id}', [EmailTrackingController::class, 'trackOpen']);
Route::get('/email/click/{send_id}/{link_id}', [EmailTrackingController::class, 'trackClick']);

// Dans email HTML
<img src="{{ route('email.open', $send->id) }}" width="1" height="1" />
<a href="{{ route('email.click', [$send->id, 1]) }}">Lien</a>
```

---

## 📁 Structure Fichiers Créée

```
app/Filament/Resources/EmailSequences/
├── EmailSequenceResource.php
├── Schemas/
│   └── EmailSequenceForm.php
├── Tables/
│   └── EmailSequencesTable.php
├── Pages/
│   ├── ListEmailSequences.php
│   ├── CreateEmailSequence.php
│   └── EditEmailSequence.php
└── RelationManagers/
    └── EmailsRelationManager.php

app/Filament/Resources/Leads/Tables/
└── LeadsTable.php (modifié - bulk action ajoutée)
```

**Total** : 8 fichiers (7 créés + 1 modifié)

---

## 🚀 Déploiement

```bash
# Sur le serveur
ssh elngbpzd@royalleadpro.com
cd ~/royalleadpro.com
git pull origin main

# Vider caches
/usr/local/bin/php8.3 artisan config:clear
/usr/local/bin/php8.3 artisan route:clear
/usr/local/bin/php8.3 artisan view:clear
/usr/local/bin/php8.3 artisan cache:clear

# Vérifier migrations (si tables manquantes)
/usr/local/bin/php8.3 artisan migrate --force

# Tester
# Aller dans Admin → Marketing → Séquences Email
```

**Vérifications** :
1. ✅ Navigation "Marketing" visible
2. ✅ Badge affiche nombre séquences actives
3. ✅ Créer nouvelle séquence fonctionne
4. ✅ RelationManager emails fonctionne
5. ✅ Bulk action leads visible

---

## ✅ Résumé Phase 5

### Interface Complète
- ✅ **EmailSequenceResource** : CRUD complet
- ✅ **Form** : 3 sections (Info, Trigger, Settings)
- ✅ **Table** : 10 colonnes + 3 filtres + toggle status
- ✅ **RelationManager** : Gestion emails avec drag & drop
- ✅ **Pages** : List, Create, Edit
- ✅ **Bulk Action** : Démarrer séquences sur leads

### Triggers Supportés
- ✅ Formulaire soumis
- ✅ Seuil de score
- ✅ Page vue
- ✅ Tag assigné
- ✅ Inactivité
- ✅ Manuel

### Features
- ✅ Variables personnalisation (8 variables)
- ✅ Rich text editor (7 boutons)
- ✅ Stats ouverture/clic par email
- ✅ Drag & drop emails (reorder)
- ✅ Dupliquer emails
- ✅ Toggle Active/Pause
- ✅ Protection doublons inscriptions

### Prochaines Étapes
- 🔄 **Phase 6** : Automatisations (Observers + Job envoi + Tracking)
- 🔄 Dashboard widget séquences
- 🔄 A/B testing variants
- 🔄 Templates emails prédéfinis

---

## 📚 Récapitulatif Global (Phases 1-5)

| Phase | Composant | Fichiers | Statut |
|-------|-----------|----------|--------|
| **1. Tracking Enrichi** | 23 champs + JS tracking | 7 | ✅ |
| **2. TagResource** | Form + Table + Bulk Actions | 4 | ✅ |
| **3. Auto-Tagging** | 3 Observers + Command | 4 | ✅ |
| **4. Dashboard Analytics** | 6 Widgets complets | 6 | ✅ |
| **5. Email Sequences UI** | Resource + RelationManager | 8 | ✅ |

**Total** : **29 fichiers** pour un CRM ultra-complet ! 🎉

---

**Auteur** : Royal LeadMagnet System  
**Date** : 22 Janvier 2026  
**Version** : Phase 5 Complete  
**Statut** : ✅ Production Ready (UI uniquement - automatisations Phase 6)
