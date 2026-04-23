# 🏷️ Système de Tags, Timeline et Automatisations

## Vue d'ensemble

Système complet pour **catégoriser, suivre et automatiser** la gestion des leads avec :
- **Tags** : Catégorisation manuelle ou automatique
- **Timeline** : Historique détaillé de tous les événements
- **Automatisations** : Séquences emails et alertes

---

## 🏷️ Système de Tags

### Qu'est-ce qu'un Tag ?

Un **tag** est une étiquette permettant de catégoriser les leads selon différents critères :
- Comportement (VIP, Intéressé Formation, Vidéo Complète)
- État de relance (Relance 7j, Relance 14j)
- Canal d'interaction (WhatsApp Actif, Email Ouvert)
- Custom (tout ce que vous voulez)

### Tags par Défaut

Le système inclut 6 tags pré-configurés :

| Tag | Couleur | Usage |
|-----|---------|-------|
| **VIP** | ⚠️ Warning (Jaune) | Lead à forte valeur |
| **Relance 7j** | ℹ️ Info (Bleu) | À relancer après 7 jours d'inactivité |
| **Relance 14j** | 🔴 Danger (Rouge) | Relance urgente après 14 jours |
| **Intéressé Formation** | ✅ Success (Vert) | A manifesté un intérêt |
| **WhatsApp Actif** | ✅ Success (Vert) | A cliqué sur WhatsApp |
| **Vidéo Complète** | 🔵 Primary (Bleu) | A regardé une vidéo entière |

### Structure du Modèle Tag

```php
Tag {
    id: int
    tenant_id: int
    name: string
    slug: string (auto-généré)
    color: string (gray, info, success, warning, danger, primary)
    description: string
    is_auto: boolean (tag automatique ou manuel)
    auto_trigger: string (condition de déclenchement)
    sort_order: int
}
```

### Relation Lead ↔ Tag

**Table pivot** : `lead_tag`
```php
{
    lead_id: int
    tag_id: int
    assigned_by: int (user qui a assigné le tag)
    assigned_at: timestamp
}
```

### Où Voir les Tags ?

#### 1. Dans LeadsTable
- **Colonne "Tags"** : Affiche jusqu'à 2 tags avec badge coloré
- Toggleable (peut être caché/affiché)
- Couleur badge = couleur du tag

#### 2. Dans LeadInfolist
- **Onglet Informations → Section Tags**
- Tous les tags du lead affichés
- Badges colorés selon la couleur du tag

---

## 🕐 Timeline des Événements

### Vue d'ensemble

La **Timeline** affiche l'historique complet de toutes les actions d'un lead, du plus récent au plus ancien.

### Localisation

**LeadInfolist → Onglet Activité**

### 3 Sections

#### 1. Résumé d'engagement (4 KPIs)

| KPI | Description | Format |
|-----|-------------|--------|
| **Événements totaux** | Nombre total d'événements | Badge bleu |
| **Temps total passé** | Somme du temps sur toutes les pages | H:i:s |
| **Scroll moyen** | Profondeur moyenne de scroll | % |
| **Dernière visite** | Temps écoulé depuis last_activity_at | "Il y a X jours" |

#### 2. Timeline des Événements (50 derniers)

Format Markdown par événement :
```
🔹 **[Label Événement]** sur *[Nom Page]* (⏱️ temps • 📊 scroll • 📱 device)
   📅 Date H:i • +X pts
```

**Exemple réel** :
```
🔹 **Soumission formulaire** sur *Capture Lead* (⏱️ 02:34 • 📊 85% • 📱 Mobile)
   📅 22/01/2026 14:32 • +10 pts

🔹 **Vidéo complétée** sur *Formation Vidéo* (⏱️ 12:45 • 🖥️ Desktop)
   📅 22/01/2026 14:15 • +15 pts

🔹 **Visite de page** sur *Webinar* (⏱️ 01:12 • 📊 45% • 📱 Mobile)
   📅 22/01/2026 13:58 • +1 pt
```

**Données affichées** :
- Type d'événement avec icône
- Page concernée
- Temps passé (si > 0)
- Scroll depth (si > 0)
- Device utilisé avec icône
- Date et heure exacte
- Points gagnés

#### 3. Informations système

- Date de création
- Date dernière mise à jour
- Date de conversion (si converti)

---

## 📊 Types d'Événements Trackés

### Vue complète (EventType Enum)

| Type | Label | Points | Icône | Trigger |
|------|-------|--------|-------|---------|
| `PAGE_VIEW` | Visite de page | 1 | 👁️ | À chaque page vue |
| `FORM_SUBMIT` | Soumission formulaire | 10 | 📋 | Formulaire soumis |
| `VIDEO_PLAY` | Lecture vidéo | 2 | ▶️ | Vidéo lancée |
| `VIDEO_25` | Vidéo vue à 25% | 3 | ⏯️ | 25% visionné |
| `VIDEO_50` | Vidéo vue à 50% | 5 | ⏯️ | 50% visionné |
| `VIDEO_75` | Vidéo vue à 75% | 8 | ⏯️ | 75% visionné |
| `VIDEO_100` | Vidéo complétée | 15 | ✅ | 100% visionné |
| `CTA_CLICK` | Clic CTA | 5 | 👆 | Bouton CTA cliqué |
| `WHATSAPP_CLICK` | Clic WhatsApp | 25 | 💬 | Bouton WhatsApp cliqué |
| `PAYMENT_CLICK` | Clic Paiement | 30 | 💳 | Bouton paiement cliqué |
| `CONVERSION` | Conversion | 100 | 🎖️ | Lead devient client |

### Événements de Conversion

Les événements suivants sont considérés comme **conversions** :
- `WHATSAPP_CLICK` (25 pts)
- `PAYMENT_CLICK` (30 pts)
- `CONVERSION` (100 pts)

### Événements Vidéo

Les événements vidéo permettent de tracker précisément l'engagement :
- `VIDEO_PLAY` → 2 pts
- `VIDEO_25` → 3 pts (cumul : 5 pts)
- `VIDEO_50` → 5 pts (cumul : 10 pts)
- `VIDEO_75` → 8 pts (cumul : 18 pts)
- `VIDEO_100` → 15 pts (cumul : 33 pts total)

---

## 🤖 Automatisations Possibles

### 1. Séquences Email (EmailSequence)

#### Triggers Disponibles

| Trigger | Description | Usage |
|---------|-------------|-------|
| `FORM_SUBMIT` | Après soumission formulaire | Emails de bienvenue |
| `SCORE_THRESHOLD` | Quand score atteint X | Relance leads chauds |
| `PAGE_VIEW` | Après visite page spécifique | Nurturing ciblé |
| `TAG_ASSIGNED` | Quand tag assigné | Séquence personnalisée |
| `STATUS_CHANGE` | Changement de statut | Workflow automatisé |
| `VIDEO_WATCHED` | Vidéo vue à X% | Emails de suivi vidéo |
| `INACTIVITY` | X jours sans activité | Relance inactifs |

#### Statuts Séquence

- `DRAFT` : Brouillon (non active)
- `ACTIVE` : Active et envoi emails
- `PAUSED` : En pause
- `ARCHIVED` : Archivée

#### Exemple : Séquence "Relance 7j"

```php
EmailSequence {
    name: "Relance Inactifs 7 jours"
    trigger: INACTIVITY
    trigger_value: 7 (jours)
    status: ACTIVE
    
    Emails:
    1. J+0 : "On a remarqué que vous n'êtes pas revenu..."
    2. J+3 : "Offre spéciale pour vous 🎁"
    3. J+7 : "Dernière chance avant archivage"
}
```

### 2. Alertes Automatiques (Alert)

#### Types d'Alertes

| Type | Déclencheur | Priorité | Action |
|------|-------------|----------|--------|
| `HOT_LEAD` | Score ≥ 31 | 5 | Notification commerciale |
| `ENGAGED` | 3 vidéos en 24h | 3 | Alerte engagement |
| `WHATSAPP_CLICK` | Clic WhatsApp | 5 | Contact immédiat |
| `INACTIVE_7_DAYS` | 7j sans activité | 2 | Relance recommandée |
| `INACTIVE_14_DAYS` | 14j sans activité | 4 | Relance urgente |
| `NEW_REGISTRATION` | Nouvel inscrit | 1 | Info nouvelle lead |

### 3. Tags Automatiques

**Tags auto-assignés selon comportement** :

```php
// Exemple : Auto-tag "Vidéo Complète"
if ($event->type === EventType::VIDEO_100) {
    $tag = Tag::where('slug', 'video-complete')->first();
    $lead->tags()->attach($tag->id, [
        'assigned_by' => null, // Auto
        'assigned_at' => now()
    ]);
}
```

**Scénarios d'auto-tagging** :
- ✅ Vidéo 100% → Tag "Vidéo Complète"
- 💬 Clic WhatsApp → Tag "WhatsApp Actif"
- ⏰ 7j inactivité → Tag "Relance 7j"
- ⏰ 14j inactivité → Tag "Relance 14j"
- 🔥 Score > 61 → Tag "Ultra Hot"

---

## 🎯 Workflows Recommandés

### Workflow 1 : Lead Chaud → Conversion

```
1. Lead atteint score 31+ (HOT)
   → Alerte HOT_LEAD envoyée
   → Tag "VIP" assigné automatiquement
   
2. Commercial contacte le lead
   → Assign lead à commercial
   → Note ajoutée dans CRM
   
3. Lead clique WhatsApp
   → Event WHATSAPP_CLICK (+25 pts)
   → Alerte WHATSAPP_CLICK envoyée
   → Tag "WhatsApp Actif" assigné
   
4. Lead devient CLIENT
   → Status changé à CLIENT
   → Event CONVERSION (+100 pts)
   → Séquence "Onboarding Client" démarrée
```

### Workflow 2 : Lead Inactif → Relance

```
1. Lead sans activité depuis 7j
   → Tag "Relance 7j" assigné auto
   → Alerte INACTIVE_7_DAYS créée
   → Séquence "Relance 7j" démarrée
   
2. Si toujours inactif après 7j de plus (14j total)
   → Tag "Relance 14j" assigné auto
   → Tag "Relance 7j" retiré
   → Alerte INACTIVE_14_DAYS créée
   → Séquence "Relance 14j Urgente" démarrée
   
3. Si activité détectée
   → Tags relance retirés
   → Score recalculé
   → Statut mis à jour
```

### Workflow 3 : Engagement Vidéo → Nurturing

```
1. Lead visite page avec vidéo
   → Event PAGE_VIEW (+1 pt)
   
2. Lead lance vidéo
   → Event VIDEO_PLAY (+2 pts)
   
3. Lead regarde 50%
   → Event VIDEO_50 (+5 pts)
   → Séquence "Vidéo 50%" démarrée
   
4. Lead regarde 100%
   → Event VIDEO_100 (+15 pts)
   → Tag "Vidéo Complète" assigné
   → Séquence "Vidéo Complète" démarrée
   → Status peut passer à WARM
```

---

## 📁 Fichiers Modifiés/Créés

### Modifiés

1. **`app/Filament/Resources/Leads/Tables/LeadsTable.php`**
   - Ajout colonne "Tags" avec badges colorés

2. **`app/Filament/Resources/Leads/Schemas/LeadInfolist.php`**
   - Section "Tags" dans onglet Informations
   - Timeline complète dans onglet Activité
   - Résumé d'engagement (4 KPIs)
   - Historique événements (50 derniers)

### Existants (déjà implémentés)

3. **`app/Models/Tag.php`**
   - Modèle Tag avec relations
   - Scopes : auto, manual, ordered

4. **`app/Models/Lead.php`**
   - Relation tags() (BelongsToMany)
   - Relation events() (HasMany)

5. **`app/Enums/EventType.php`**
   - 11 types d'événements
   - Méthodes : label(), defaultPoints(), icon(), isVideoEvent(), isConversionEvent()

6. **`app/Enums/AlertType.php`**
   - 6 types d'alertes
   - Méthodes : label(), description(), color(), icon(), priority()

7. **`app/Enums/LeadStatus.php`**
   - 6 statuts (COLD → ULTRA_HOT → CLIENT → MEMBER)
   - Méthode fromScore() pour auto-calcul

8. **`app/Enums/EmailSequenceStatus.php`**
   - 4 statuts séquences

9. **`app/Enums/EmailSequenceTrigger.php`**
   - 7 triggers d'automatisation

10. **`app/Events/LeadStatusChanged.php`**
    - Event déclenché lors du changement de statut
    - Méthodes : isNowHot(), isNowClient()

---

## 🚀 Utilisation dans Filament

### Assigner un Tag Manuellement

**Via LeadResource** :
1. Ouvrir un lead
2. Aller dans l'onglet "Informations"
3. Section "Tags" → Gérer les tags
4. Sélectionner tag(s) → Sauvegarder

**Via Bulk Actions** (à implémenter) :
1. Sélectionner plusieurs leads
2. Actions de masse → "Assigner Tags"
3. Choisir tag(s) → Appliquer

### Créer un Nouveau Tag

**Via TagResource** (à créer si nécessaire) :
```php
Tag::create([
    'name' => 'Lead Premium',
    'color' => 'warning',
    'description' => 'Lead avec potentiel élevé',
    'is_auto' => false,
]);
```

### Filtrer par Tags

**Dans LeadsTable** :
```php
SelectFilter::make('tags')
    ->relationship('tags', 'name')
    ->multiple()
    ->preload();
```

---

## 📊 Statistiques & Analytics

### KPIs Exploitables

```php
// Leads par tag
$leadsByTag = Tag::withCount('leads')
    ->ordered()
    ->get();

// Tags les plus utilisés
$topTags = Tag::withCount('leads')
    ->orderBy('leads_count', 'desc')
    ->limit(10)
    ->get();

// Leads avec tag "VIP" et score > 50
$vipHotLeads = Lead::whereHas('tags', fn($q) => 
        $q->where('slug', 'vip')
    )
    ->where('score', '>', 50)
    ->get();

// Leads inactifs 7j sans tag de relance
$needsRelance = Lead::where('last_activity_at', '<', now()->subDays(7))
    ->whereDoesntHave('tags', fn($q) => 
        $q->whereIn('slug', ['relance-7j', 'relance-14j'])
    )
    ->get();
```

---

## ✅ Résumé des Fonctionnalités

### Tags
- ✅ Modèle Tag existant avec relations
- ✅ 6 tags par défaut pré-configurés
- ✅ Affichage dans LeadsTable (colonne toggleable)
- ✅ Affichage dans LeadInfolist (section dédiée)
- ⏳ Gestion tags (création, édition) via TagResource (à créer)
- ⏳ Assignment tags via bulk actions (à implémenter)
- ⏳ Auto-tagging selon comportement (à implémenter)

### Timeline
- ✅ Timeline complète des événements (50 derniers)
- ✅ Résumé engagement (4 KPIs)
- ✅ Formatage Markdown avec détails enrichis
- ✅ Affichage device, temps, scroll par événement
- ✅ Points gagnés affichés

### Automatisations
- ✅ 11 types d'événements trackés
- ✅ 6 types d'alertes configurées
- ✅ 7 triggers séquences emails
- ✅ Event LeadStatusChanged pour workflow
- ⏳ Listeners pour auto-tagging (à créer)
- ⏳ Jobs pour relances automatiques (à créer)

---

## 🎓 Prochaines Étapes Suggérées

1. **TagResource Filament** : Créer l'interface de gestion des tags
2. **Auto-tagging Listeners** : Implémenter les listeners pour auto-assignment
3. **Bulk Actions Tags** : Ajouter actions de masse pour tags
4. **Dashboard Analytics Tags** : Widget stats par tag
5. **Email Sequences UI** : Interface pour créer/gérer les séquences
6. **Alert Center** : Tableau de bord des alertes actives
7. **Workflow Builder** : Interface visuelle pour créer workflows

---

**Auteur** : Système Royal LeadMagnet  
**Date** : 22 Janvier 2026  
**Version** : 1.0
