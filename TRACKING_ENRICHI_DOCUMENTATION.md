# 📊 Système de Tracking Enrichi - Documentation

## Vue d'ensemble

Le système de tracking a été considérablement enrichi pour capturer **le maximum de données** sur chaque visiteur et lead. Cette documentation détaille toutes les données capturées et comment les utiliser.

---

## 🎯 Données Capturées

### 1. **Informations Device & Navigateur**

Capturées automatiquement via `UserAgentService` lors de chaque visite :

| Champ | Description | Exemple |
|-------|-------------|---------|
| `device_type` | Type d'appareil | `mobile`, `tablet`, `desktop` |
| `browser` | Navigateur utilisé | `Chrome`, `Safari`, `Firefox`, `Edge` |
| `browser_version` | Version du navigateur | `120.0.6099.199` |
| `os` | Système d'exploitation | `Windows`, `macOS`, `iOS`, `Android`, `Linux` |
| `os_version` | Version de l'OS | `11`, `10.15.7`, `17.2` |
| `user_agent` | User agent complet | Chaîne complète |
| `screen_resolution` | Résolution d'écran | `1920x1080`, `375x667` |
| `language` | Langue du navigateur | `fr-FR`, `en-US` |

### 2. **Géolocalisation Enrichie**

Capturées via `GeolocationService` avec API ipapi.co :

| Champ | Description | Exemple |
|-------|-------------|---------|
| `ip_address` | Adresse IP | `102.219.176.30` |
| `country` | Code pays ISO | `SN`, `FR`, `MA` |
| `city` | Ville | `Dakar`, `Paris`, `Casablanca` |
| `region` | Région/État | `Dakar Region`, `Île-de-France` |
| `timezone` | Fuseau horaire | `Africa/Dakar`, `Europe/Paris` |
| `latitude` | Latitude GPS | `14.693425` |
| `longitude` | Longitude GPS | `-17.447938` |

### 3. **Comportement & Engagement**

Capturées via JavaScript en temps réel :

| Champ | Description | Exemple |
|-------|-------------|---------|
| `time_spent_seconds` | Temps passé sur la page | `45`, `120`, `300` |
| `scroll_depth_percentage` | Profondeur de scroll max | `75`, `100` |

### 4. **Attribution & Source**

Capturées via URL params et cookies :

| Champ | Description | Exemple |
|-------|-------------|---------|
| `source` | UTM Source | `facebook`, `google`, `instagram` |
| `medium` | UTM Medium | `cpc`, `organic`, `email` |
| `campaign` | UTM Campaign | `promo-2024`, `webinar` |
| `referrer` | Page d'origine | `https://facebook.com` |
| `brought_by` | ID du commercial (ref) | `3` |

### 5. **Données Formulaire**

Stockées dans `form_data` (JSON) :

- Email
- Téléphone
- Prénom/Nom
- Champs personnalisés

---

## 📁 Structure des Fichiers

### Nouveaux Fichiers

1. **`database/migrations/2026_01_22_060000_add_enhanced_tracking_fields_to_leads_and_events.php`**
   - Ajoute tous les nouveaux champs aux tables `leads` et `events`

2. **`app/Services/UserAgentService.php`**
   - Parse le User-Agent pour extraire device, browser, OS
   - Méthodes : `parse()`, `detectDeviceType()`, `detectBrowser()`, `detectOS()`, `detectLanguage()`

3. **`app/Http/Controllers/TrackingApiController.php`**
   - Endpoints API pour recevoir les données JavaScript
   - Routes : `/api/tracking/page-engagement`, `/api/tracking/update-lead-data`

### Fichiers Modifiés

1. **`app/Services/TrackingService.php`**
   - `trackVisitor()` : Capture device, browser, OS, language
   - `createLeadFromForm()` : Enrichit les leads formulaire
   - `createEvent()` : Ajoute device/browser/OS à chaque événement

2. **`app/Services/GeolocationService.php`**
   - `updateLeadLocation()` : Capture timezone, region, latitude, longitude

3. **`resources/views/funnel/page.blade.php`**
   - Script JavaScript pour tracker temps passé et scroll depth
   - Envoi automatique via `sendBeacon` avant fermeture

4. **`app/Models/Lead.php`**
   - Ajout de 12 nouveaux champs fillable

5. **`app/Models/Event.php`**
   - Ajout de 5 nouveaux champs fillable

6. **`routes/web.php`**
   - Routes API pour tracking JavaScript

---

## 🚀 Fonctionnement

### Scénario 1 : Nouveau Visiteur Anonyme

1. **Arrivée sur le tunnel** (`capture-de-leads.royalleadpro.com`)
2. **TrackingService::trackVisitor()** s'exécute :
   - Parse User-Agent → device, browser, OS, language
   - Crée Lead anonyme avec toutes ces données
   - Cookie `rlm_visitor` posé (1 an)
3. **GeolocationService::updateLeadLocation()** :
   - Appel API ipapi.co
   - Enrichit avec pays, ville, timezone, coordonnées GPS
4. **JavaScript démarre** :
   - Track temps passé
   - Track scroll depth
   - Capture résolution d'écran

### Scénario 2 : Visiteur Remplit un Formulaire

1. **Soumission formulaire**
2. **TrackingService::createLeadFromForm()** :
   - Crée ou met à jour Lead avec email, nom, etc.
   - Capture device, browser, OS à nouveau (cas changement d'appareil)
3. **Event FORM_SUBMIT** créé avec :
   - `time_spent_seconds` : combien de temps avant soumission
   - `scroll_depth_percentage` : jusqu'où il a scrollé
   - Device/Browser/OS du moment

### Scénario 3 : Tracking en Continu

- **Toutes les 30 secondes** : envoi des données (si page active)
- **Avant fermeture page** : envoi final via `sendBeacon`
- **À chaque scroll** : mise à jour du scroll depth max
- **Chaque événement** (clic CTA, vidéo, etc.) : capture contexte complet

---

## 📊 Exemples d'Utilisation

### Dashboard Analytics

```php
// Statistiques par device
$leads->groupBy('device_type')->map->count();
// ['mobile' => 450, 'desktop' => 320, 'tablet' => 45]

// Pays les plus représentés
$leads->groupBy('country')->sortByDesc->count()->take(5);
// ['SN' => 234, 'CI' => 189, 'MA' => 156, ...]

// Temps moyen passé par page
$events->avg('time_spent_seconds'); // 127 secondes

// Scroll depth moyen
$events->avg('scroll_depth_percentage'); // 68%
```

### Segmentation Avancée

```php
// Leads mobiles africains ayant scrollé >75%
Lead::where('device_type', 'mobile')
    ->whereIn('country', ['SN', 'CI', 'MA', 'BF'])
    ->whereHas('events', fn($q) => 
        $q->where('scroll_depth_percentage', '>', 75)
    );

// Leads desktop avec long engagement (>2min)
Lead::where('device_type', 'desktop')
    ->whereHas('events', fn($q) => 
        $q->where('time_spent_seconds', '>', 120)
    );
```

### Remarketing Intelligent

```php
// Visiteurs intéressés (>50% scroll) mais pas convertis
$hotLeads = Lead::where('status', 'WARM')
    ->whereNull('email')
    ->whereHas('events', fn($q) => 
        $q->where('scroll_depth_percentage', '>', 50)
          ->where('time_spent_seconds', '>', 60)
    );
```

---

## 🔧 Déploiement

### 1. Exécuter la Migration

```bash
php artisan migrate
```

### 2. Vider les Caches

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 3. Tester

1. Visitez un tunnel : `https://montunnel.royalleadpro.com`
2. Restez 30 secondes, scrollez
3. Vérifiez en BDD :

```sql
SELECT device_type, browser, os, country, city, timezone, 
       time_spent_seconds, scroll_depth_percentage 
FROM leads 
ORDER BY created_at DESC 
LIMIT 1;
```

---

## 📈 Données Enrichies dans Filament

### Resource LeadResource

Ajoutez ces colonnes dans `table()` :

```php
Tables\Columns\TextColumn::make('device_type')
    ->label('Device')
    ->badge()
    ->color(fn($state) => match($state) {
        'mobile' => 'success',
        'desktop' => 'info',
        'tablet' => 'warning',
        default => 'gray',
    }),

Tables\Columns\TextColumn::make('browser')
    ->label('Navigateur'),

Tables\Columns\TextColumn::make('country')
    ->label('Pays')
    ->formatStateUsing(fn($state) => 
        \Locale::getDisplayRegion("-{$state}", 'fr')
    ),

Tables\Columns\TextColumn::make('city')
    ->label('Ville'),
```

### Infolist Enrichi

```php
Infolists\Components\Section::make('Tracking Comportemental')
    ->schema([
        Infolists\Components\TextEntry::make('events.time_spent_seconds')
            ->label('Temps total passé')
            ->formatStateUsing(fn($state) => gmdate("H:i:s", $state)),
        
        Infolists\Components\TextEntry::make('events.scroll_depth_percentage')
            ->label('Scroll depth moyen')
            ->suffix('%')
            ->badge(),
    ]),
```

---

## 🎯 KPIs Clés à Suivre

### Engagement
- **Temps moyen par visite** : `avg(time_spent_seconds)`
- **Taux de scroll profond** : % visiteurs >75% scroll
- **Taux de rebond** : % visiteurs <10 secondes

### Conversion
- **Device conversion rate** : Taux par type d'appareil
- **Geo conversion rate** : Taux par pays/ville
- **Browser performance** : Quel navigateur convertit le mieux

### Audience
- **Device breakdown** : Mobile vs Desktop vs Tablet
- **Top countries** : Pays générant le plus de leads
- **Timezone distribution** : Heures de pic de trafic

---

## ✅ Résumé des Améliorations

| Avant | Après |
|-------|-------|
| 8 champs par lead | **23 champs par lead** |
| 4 champs par event | **9 champs par event** |
| Géolocalisation basique | **GPS + Timezone + Region** |
| Pas de device info | **Device + Browser + OS + Version** |
| Pas de tracking comportement | **Temps passé + Scroll depth en temps réel** |
| Pas de résolution écran | **Screen resolution capturée** |
| Pas de langue | **Browser language détectée** |

---

## 🔐 Confidentialité & RGPD

Toutes les données capturées sont :
- ✅ Stockées en base de données sécurisée
- ✅ Associées à un consentement implicite (utilisation du service)
- ✅ Anonymes jusqu'à soumission formulaire (cookie UUID)
- ✅ Supprimables via soft-delete

**Note** : Ajouter une mention RGPD dans footer si nécessaire.

---

## 🚀 Prochaines Étapes Suggérées

1. **Dashboard Analytics Dédié** : Visualiser toutes ces données
2. **Segments Automatiques** : Créer des segments basés sur comportement
3. **Scoring Amélioré** : Utiliser temps passé + scroll dans le score
4. **A/B Testing** : Comparer performance par device/geo
5. **Alertes Intelligentes** : Notif si hot lead (>2min + >75% scroll)

---

**Auteur** : Système Royal LeadMagnet  
**Date** : 22 Janvier 2026  
**Version** : 1.0
