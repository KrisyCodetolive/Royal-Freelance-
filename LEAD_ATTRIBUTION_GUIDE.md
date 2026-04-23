# Attribution des Leads aux Commerciaux - Guide de Résolution

## 🔍 Problème Identifié

Les commerciaux ne voient pas leurs leads dans le dashboard commercial (`/commercial/dashboard` et `/commercial/leads`) même si les leads sont bien capturés.

### **Cause Racine** :
Les leads créés **avant** la mise en place du système d'attribution automatique n'ont pas le champ `brought_by` renseigné, donc ils n'apparaissent pas dans les requêtes filtrées par `where('brought_by', $user->id)`.

---

## ✅ Solutions Implémentées

### **1. Attribution Automatique (Nouveaux Leads)**

**Fichier** : `app/Http/Controllers/FunnelController.php`

Depuis notre correction précédente, tous les **nouveaux leads** sont automatiquement attribués au commercial :

```php
// Récupérer le commercial si présent en session
$broughtBy = null;
if ($commercialId = session('commercial_ref')) {
    $broughtBy = \App\Models\User::find($commercialId);
}

// Création avec attribution correcte
$lead = $this->trackingService->createLeadFromForm($funnel, $request->all(), $broughtBy);
```

**Flux** :
1. Visiteur accède via URL personnalisée (`/f/custom-slug`)
2. `trackCommercialAttribution()` stocke `commercial_ref` en session
3. Lors de la soumission du formulaire, le commercial est récupéré
4. Le lead est créé avec `brought_by` = ID du commercial

✅ **Tous les nouveaux leads sont correctement attribués**

---

### **2. Attribution Rétroactive (Leads Existants)**

**Fichier** : `app/Console/Commands/AttributeExistingLeads.php`

Commande Artisan pour attribuer les leads existants sans `brought_by` :

#### **Utilisation** :

```bash
# Mode dry-run (aperçu sans modification)
php artisan leads:attribute-existing --dry-run

# Attribution réelle
php artisan leads:attribute-existing

# Filtrer par funnel spécifique
php artisan leads:attribute-existing --funnel=5
```

#### **Stratégie d'Attribution** :
Pour chaque lead sans `brought_by` :
1. Récupère le `funnel_id` du lead
2. Trouve le premier commercial actif sur ce funnel
3. Attribue le lead à ce commercial
4. Incrémente le compteur `leads_count` dans `funnel_user`

#### **Exemple de Sortie** :

```
🔍 Searching for leads without brought_by attribution...
Found 42 leads to process.
 42/42 [============================] 100%

┌─────────────────────────────────────┬───────┐
│ Status                              │ Count │
├─────────────────────────────────────┼───────┤
│ ✅ Would be attributed              │ 38    │
│ ⏭️  Skipped (no commercial found)   │ 4     │
│ 📊 Total processed                  │ 42    │
└─────────────────────────────────────┴───────┘

✅ Successfully attributed 38 leads!
```

---

## 📊 Vérification du Système

### **Relations et Scopes Utilisés** :

#### **Modèle User** :
```php
// Relation
public function broughtLeads(): HasMany
{
    return $this->hasMany(Lead::class, 'brought_by');
}

// Stats
public function getCommercialStats(): array
{
    return [
        'total_leads' => $this->broughtLeads()->count(),
        'hot_leads' => $this->broughtLeads()->hot()->count(),
        'conversions' => $this->broughtLeads()->converted()->count(),
        'active_funnels' => $this->usableFunnels()->wherePivot('is_active', true)->count(),
    ];
}
```

#### **Modèle Lead** :
```php
// Relation inverse
public function broughtBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'brought_by');
}

// Scopes
public function scopeHot($query)
{
    return $query->whereIn('status', [LeadStatus::HOT, LeadStatus::ULTRA_HOT]);
}

public function scopeConverted($query)
{
    return $query->whereNotNull('converted_at');
}

public function scopeBroughtBy($query, User|int $user)
{
    $userId = $user instanceof User ? $user->id : $user;
    return $query->where('brought_by', $userId);
}
```

---

## 🚀 Procédure de Résolution Complète

### **Sur le Serveur de Production** :

```bash
# 1. Se connecter au serveur
ssh dev@royalleadpro.com
cd public_html

# 2. Vider le cache (pour l'erreur Tab)
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 3. Vérifier les leads sans attribution
php artisan leads:attribute-existing --dry-run

# 4. Si tout est OK, attribuer les leads
php artisan leads:attribute-existing

# 5. Vérifier les résultats
php artisan tinker
>>> \App\Models\Lead::whereNotNull('brought_by')->count()
>>> \App\Models\User::role('commercial')->first()->broughtLeads()->count()
```

---

## 📈 Requêtes de Vérification

### **Compter les leads par statut d'attribution** :

```php
// Total des leads
Lead::count()

// Leads avec attribution
Lead::whereNotNull('brought_by')->count()

// Leads sans attribution
Lead::whereNull('brought_by')->count()

// Leads par commercial
User::role('commercial')->get()->map(function($user) {
    return [
        'name' => $user->name,
        'leads' => $user->broughtLeads()->count(),
        'hot_leads' => $user->broughtLeads()->hot()->count(),
    ];
})
```

### **Dashboard Commercial** :

```php
$user = User::find(2); // ID du commercial
$stats = $user->getCommercialStats();

// Affiche :
// [
//     'total_leads' => 38,
//     'hot_leads' => 12,
//     'conversions' => 5,
//     'active_funnels' => 3,
// ]
```

---

## 🎯 Points de Contrôle

✅ **Nouveaux leads** : Automatiquement attribués via `session('commercial_ref')`
✅ **Leads existants** : Commande `leads:attribute-existing` disponible
✅ **Relations** : `broughtLeads()` et `broughtBy()` fonctionnelles
✅ **Scopes** : `hot()`, `converted()`, `broughtBy()` opérationnels
✅ **Dashboard** : Affiche les stats correctes une fois les leads attribués
✅ **Compteurs** : `funnel_user.leads_count` mis à jour

---

## 🔧 Maintenance

### **Commande Planifiée (Optionnel)** :

Pour attribuer automatiquement les leads orphelins chaque nuit :

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('leads:attribute-existing')
        ->daily()
        ->at('02:00');
}
```

---

## 📝 Résumé

**Problème** : Leads non visibles pour les commerciaux
**Cause** : Champ `brought_by` NULL sur les anciens leads
**Solution** : 
1. ✅ Attribution automatique pour les nouveaux leads
2. ✅ Commande Artisan pour les leads existants
3. ✅ Cache vidé pour corriger l'erreur Tab

**Commande à exécuter sur le serveur** :
```bash
php artisan optimize:clear && php artisan leads:attribute-existing
```
