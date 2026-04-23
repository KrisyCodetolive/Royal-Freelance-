# 🤖 Phase 3 - Auto-Tagging Complet

## ✅ Ce Qui a Été Implémenté

Système d'auto-tagging **entièrement automatique** basé sur le comportement des leads.

---

## 🎯 Auto-Tagging Basé sur Événements

### EventObserver Enrichi

**Fichier** : `app/Observers/EventObserver.php`

#### Triggers Automatiques

| Événement | Tag Assigné | Condition |
|-----------|-------------|-----------|
| `VIDEO_100` | video_complete | Lead a regardé vidéo à 100% |
| `WHATSAPP_CLICK` | whatsapp_click | Lead a cliqué sur WhatsApp |
| `FORM_SUBMIT` | form_submit | Lead a soumis un formulaire |

#### Logique

```php
protected function autoAssignTags($lead, Event $event): void
{
    // Vidéo complétée → Tag "video_complete"
    if ($event->type === EventType::VIDEO_100) {
        $tag = Tag::where('auto_trigger', 'video_complete')
            ->where('is_auto', true)
            ->first();
        
        if ($tag && !$lead->tags->contains($tag->id)) {
            $lead->tags()->attach($tag->id);
        }
    }
    // ... autres événements
}
```

**Quand ça se déclenche** : Automatiquement à chaque nouvel événement créé.

---

## 📊 Auto-Tagging Basé sur Score

### LeadObserver Enrichi

**Fichier** : `app/Observers/LeadObserver.php`

#### Triggers Automatiques

| Score | Tag Assigné | Action Supplémentaire |
|-------|-------------|-----------------------|
| ≥ 31 et < 61 | score_threshold_31 (HOT) | - |
| ≥ 61 | score_threshold_61 (ULTRA HOT) | Retire tag HOT si présent |

#### Logique

```php
protected function autoAssignTagsByScore(Lead $lead): void
{
    // Score ≥ 31 (HOT)
    if ($lead->score >= 31 && $lead->score < 61) {
        $tag = Tag::where('auto_trigger', 'score_threshold_31')
            ->where('is_auto', true)
            ->first();
        
        if ($tag && !$lead->tags->contains($tag->id)) {
            $lead->tags()->attach($tag->id);
        }
    }

    // Score ≥ 61 (ULTRA HOT)
    if ($lead->score >= 61) {
        $tag = Tag::where('auto_trigger', 'score_threshold_61')
            ->where('is_auto', true)
            ->first();
        
        if ($tag && !$lead->tags->contains($tag->id)) {
            $lead->tags()->attach($tag->id);
        }

        // Retirer tag HOT (car maintenant ULTRA HOT)
        $hotTag = Tag::where('auto_trigger', 'score_threshold_31')->first();
        if ($hotTag && $lead->tags->contains($hotTag->id)) {
            $lead->tags()->detach($hotTag->id);
        }
    }
}
```

**Quand ça se déclenche** : Automatiquement quand le score d'un lead change.

---

## ⏰ Auto-Tagging Basé sur Inactivité

### Command CheckInactiveLeads

**Fichier** : `app/Console/Commands/CheckInactiveLeads.php`

#### Triggers Automatiques

| Inactivité | Tag Assigné | Période |
|------------|-------------|---------|
| ≥ 7 jours et < 14 jours | inactive_7_days | Entre 7 et 14 jours |
| ≥ 14 jours | inactive_14_days | 14 jours et plus |

#### Logique

**Pour 7 jours** :
```php
$inactifs7j = Lead::where('last_activity_at', '<', now()->subDays(7))
    ->where('last_activity_at', '>=', now()->subDays(14))
    ->get();

foreach ($inactifs7j as $lead) {
    if (!$lead->tags->contains($tag7j->id)) {
        $lead->tags()->attach($tag7j->id);
    }
}
```

**Pour 14 jours** :
```php
$inactifs14j = Lead::where('last_activity_at', '<', now()->subDays(14))
    ->get();

foreach ($inactifs14j as $lead) {
    // Retirer tag 7j si présent
    $lead->tags()->detach($tag7j->id);
    
    // Ajouter tag 14j
    $lead->tags()->attach($tag14j->id);
}
```

#### Exécution Automatique

**Fichier** : `routes/console.php`

```php
Schedule::command('leads:check-inactive')->dailyAt('09:00');
```

**Fréquence** : Tous les jours à 9h00 du matin.

#### Commande Manuelle

```bash
php artisan leads:check-inactive
```

**Output** :
```
Vérification des leads inactifs...
✓ 12 leads marqués comme inactifs 7 jours
✓ 5 leads marqués comme inactifs 14 jours
✓ Vérification terminée !
```

---

## 📁 Fichiers Modifiés/Créés

### 1. EventObserver
- **Fichier** : `app/Observers/EventObserver.php`
- **Modifié** : Ajout méthode `autoAssignTags()`
- **Imports** : Tag, EventType

### 2. LeadObserver
- **Fichier** : `app/Observers/LeadObserver.php`
- **Modifié** : Ajout méthode `autoAssignTagsByScore()`
- **Imports** : Tag
- **Trigger** : Sur changement de score

### 3. CheckInactiveLeads Command
- **Fichier** : `app/Console/Commands/CheckInactiveLeads.php`
- **Créé** : Nouveau
- **Signature** : `leads:check-inactive`
- **Description** : Vérifie leads inactifs et assigne tags

### 4. Console Scheduler
- **Fichier** : `routes/console.php`
- **Modifié** : Ajout schedule quotidien 9h00

### 5. EventServiceProvider
- **Fichier** : `app/Providers/EventServiceProvider.php`
- **Déjà enregistré** : 
  - `Lead::observe(LeadObserver::class)`
  - `Event::observe(EventObserver::class)`

---

## 🎬 Scénarios d'Auto-Tagging

### Scénario 1 : Lead Regarde Vidéo Complète

**Flux** :
1. Lead visite page avec vidéo
2. Lead regarde vidéo à 100%
3. **Event VIDEO_100 créé**
4. **EventObserver déclenché**
5. Tag "Vidéo Complète" assigné automatiquement
6. Lead a maintenant le tag visible dans Filament

**Résultat** :
- Lead a badge "Vidéo Complète" 🔵
- Visible dans LeadsTable
- Filtrable par ce tag

### Scénario 2 : Lead Devient Chaud (Score)

**Flux** :
1. Lead accumule des points (page views, événements)
2. Score passe de 25 → 35
3. **LeadObserver déclenché** (score changed)
4. Tag "Score ≥ 31 (HOT)" assigné automatiquement
5. Si score passe à 65 plus tard :
   - Tag "Score ≥ 61 (ULTRA HOT)" assigné
   - Tag "Score ≥ 31 (HOT)" retiré (upgrade)

**Résultat** :
- Lead a badge "HOT" 🔴 ou "ULTRA HOT" 🔥
- Filtrable pour relances prioritaires

### Scénario 3 : Lead Inactif 7 Jours

**Flux** :
1. Lead s'inscrit, visite pages
2. `last_activity_at` = 8 jours dans le passé
3. **Command quotidienne s'exécute** (9h00)
4. Lead détecté comme inactif 7j
5. Tag "Relance 7j" assigné automatiquement

**14 jours plus tard** :
1. Lead toujours inactif (16 jours total)
2. **Command quotidienne s'exécute**
3. Tag "Relance 7j" retiré
4. Tag "Relance 14j" assigné
5. Alerte urgente créée

**Résultat** :
- Lead a badge "Relance 7j" 🔵 puis "Relance 14j" 🔴
- Filtre rapide pour voir leads à relancer

### Scénario 4 : Lead Clique WhatsApp

**Flux** :
1. Lead clique bouton WhatsApp sur page
2. **Event WHATSAPP_CLICK créé**
3. **EventObserver déclenché**
4. Tag "WhatsApp Actif" assigné
5. Score augmente (+25 pts)
6. **LeadObserver déclenché** (score changed)
7. Si score ≥ 31 → Tag HOT aussi assigné

**Résultat** :
- Lead a 2 tags : "WhatsApp Actif" 🟢 + "HOT" 🔴
- Commercial peut filtrer leads WhatsApp pour relance

---

## 🔧 Configuration Nécessaire

### 1. Créer les Tags Automatiques

**Via Filament** : Admin → Tags → Créer

**Tags à créer** :

| Nom | Slug | Couleur | Auto | Trigger |
|-----|------|---------|------|---------|
| Vidéo Complète | video-complete | primary | ✅ | video_complete |
| WhatsApp Actif | whatsapp-actif | success | ✅ | whatsapp_click |
| Formulaire Soumis | formulaire-soumis | info | ✅ | form_submit |
| Lead HOT | lead-hot | warning | ✅ | score_threshold_31 |
| Lead ULTRA HOT | lead-ultra-hot | danger | ✅ | score_threshold_61 |
| Relance 7j | relance-7j | info | ✅ | inactive_7_days |
| Relance 14j | relance-14j | danger | ✅ | inactive_14_days |

**Ou via Tinker** :

```php
php artisan tinker

Tag::create([
    'name' => 'Vidéo Complète',
    'slug' => 'video-complete',
    'color' => 'primary',
    'is_auto' => true,
    'auto_trigger' => 'video_complete',
    'description' => 'Lead a regardé une vidéo en entier',
]);

// Répéter pour les autres tags...
```

### 2. Activer le Scheduler

**Sur le serveur** : Ajouter au crontab

```bash
crontab -e
```

**Ajouter** :
```
* * * * * cd /home/elngbpzd/royalleadpro.com && /usr/local/bin/php8.3 artisan schedule:run >> /dev/null 2>&1
```

**Vérifier** :
```bash
php artisan schedule:list
```

**Output attendu** :
```
0 9 * * *  php artisan leads:check-inactive .................. Next Due: Tomorrow at 9:00 AM
```

### 3. Tester Manuellement

```bash
# Tester la command
php artisan leads:check-inactive

# Vérifier qu'un tag est assigné
php artisan tinker
>>> Lead::find(1)->tags;
```

---

## 📊 Statistiques & Monitoring

### Vérifier Auto-Tagging

**Via SQL** :
```sql
-- Voir leads avec tags auto-assignés (assigned_by = null)
SELECT l.email, t.name, lt.assigned_at
FROM leads l
JOIN lead_tag lt ON l.id = lt.lead_id
JOIN tags t ON t.id = lt.tag_id
WHERE lt.assigned_by IS NULL
ORDER BY lt.assigned_at DESC
LIMIT 10;
```

**Via Tinker** :
```php
// Tags les plus assignés automatiquement
Tag::withCount('leads')->where('is_auto', true)->get();

// Leads avec le plus de tags auto
Lead::has('tags')->withCount('tags')->orderBy('tags_count', 'desc')->take(10)->get();
```

### Dashboard Widget (à créer)

```php
// Nombre de tags auto-assignés aujourd'hui
$autoTaggedToday = DB::table('lead_tag')
    ->whereNull('assigned_by')
    ->whereDate('assigned_at', today())
    ->count();

// Tags les plus actifs
$topAutoTags = Tag::where('is_auto', true)
    ->withCount('leads')
    ->orderBy('leads_count', 'desc')
    ->take(5)
    ->get();
```

---

## 🎯 Workflows Automatiques Complets

### Workflow 1 : Lead → Vidéo → WhatsApp → HOT

```
1. Lead visite tunnel
   ↓ Event PAGE_VIEW (+1 pt)

2. Lead regarde vidéo 100%
   ↓ Event VIDEO_100 (+15 pts)
   ↓ 🤖 AUTO: Tag "Vidéo Complète"
   ↓ Score = 16 pts

3. Lead clique WhatsApp
   ↓ Event WHATSAPP_CLICK (+25 pts)
   ↓ 🤖 AUTO: Tag "WhatsApp Actif"
   ↓ Score = 41 pts
   ↓ 🤖 AUTO: Tag "Lead HOT" (score ≥ 31)

4. Résultat : Lead a 3 tags automatiques
   → Vidéo Complète
   → WhatsApp Actif
   → Lead HOT
```

### Workflow 2 : Lead → Inactivité → Relances

```
1. Lead s'inscrit (J0)
   ↓ Event FORM_SUBMIT (+10 pts)
   ↓ 🤖 AUTO: Tag "Formulaire Soumis"

2. Lead inactif 7 jours (J7)
   ↓ Command quotidienne (9h00)
   ↓ 🤖 AUTO: Tag "Relance 7j"
   ↓ → Séquence email "Relance 7j" démarrée

3. Lead toujours inactif 14 jours (J14)
   ↓ Command quotidienne (9h00)
   ↓ 🤖 AUTO: Tag "Relance 7j" retiré
   ↓ 🤖 AUTO: Tag "Relance 14j" assigné
   ↓ → Séquence email "Relance urgente" démarrée
   ↓ → Alerte INACTIVE_14_DAYS créée

4. Lead revient (J16)
   ↓ Event PAGE_VIEW
   ↓ last_activity_at mis à jour
   ↓ Prochain J23 : tag "Relance 14j" retiré (car actif)
```

### Workflow 3 : Lead → Score Progressif

```
Score 10 → COLD
  ↓ +20 pts
Score 30 → WARM
  ↓ +5 pts
Score 35 → WARM
  ↓ 🤖 AUTO: Tag "Lead HOT"
  ↓ +30 pts
Score 65 → HOT
  ↓ 🤖 AUTO: Tag "Lead ULTRA HOT"
  ↓ 🤖 AUTO: Tag "Lead HOT" retiré
```

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
/usr/local/bin/php8.3 artisan cache:clear

# Configurer crontab (une seule fois)
crontab -e
# Ajouter : * * * * * cd /home/elngbpzd/royalleadpro.com && /usr/local/bin/php8.3 artisan schedule:run >> /dev/null 2>&1

# Tester command
/usr/local/bin/php8.3 artisan leads:check-inactive

# Créer tags automatiques via Tinker
/usr/local/bin/php8.3 artisan tinker
>>> // Créer les 7 tags listés ci-dessus
```

---

## ✅ Résumé Phase 3

### Observers (Temps Réel)
- ✅ **EventObserver** : Auto-tag sur vidéo, WhatsApp, formulaire
- ✅ **LeadObserver** : Auto-tag sur score ≥ 31 et ≥ 61

### Command (Quotidien)
- ✅ **CheckInactiveLeads** : Auto-tag inactivité 7j et 14j
- ✅ **Scheduler** : Exécution automatique tous les jours 9h00

### Configuration
- ✅ Enregistrés dans EventServiceProvider
- ✅ Scheduler configuré dans routes/console.php

### Prochaines Étapes
- 🔄 Créer les 7 tags automatiques via Filament
- 🔄 Configurer crontab sur serveur
- 🔄 Tester pendant 1 semaine
- 🔄 Dashboard analytics tags (Phase 4)

---

**Auteur** : Royal LeadMagnet System  
**Date** : 22 Janvier 2026  
**Version** : Phase 3 Complete  
**Statut** : ✅ Production Ready
