# Guide de Débogage - Attribution des Leads

## 🔍 Logs Implémentés

Des logs détaillés ont été ajoutés à chaque étape du processus d'attribution des leads pour faciliter le débogage.

---

## 📊 Points de Log

### **1. Résolution du Funnel** 🔍

**Fichier** : `FunnelController::resolveFunnel()`

```
🔍 [FUNNEL RESOLVE] Début résolution funnel
```
**Contenu** :
- `subdomain_param` : Paramètre slug/subdomain passé
- `url` : URL complète de la requête
- `host` : Nom d'hôte
- `session_commercial_ref` : ID commercial en session (si présent)

**Scénarios possibles** :

```
✅ [FUNNEL RESOLVE] Funnel trouvé par subdomain
✅ [FUNNEL RESOLVE] Funnel trouvé par slug
✅ [FUNNEL RESOLVE] Funnel trouvé par subdomain extrait
```

---

### **2. Attribution Commerciale** 🎯

**Fichier** : `FunnelController::resolveFunnel()`

```
🎯 [COMMERCIAL ATTRIBUTION] Custom slug trouvé pour commercial
```
**Contenu** :
- `commercial_id` : ID du commercial
- `custom_slug` : Slug personnalisé utilisé
- `funnel_id` : ID du funnel

**OU**

```
⚠️ [COMMERCIAL ATTRIBUTION] Aucun custom_slug trouvé pour ce slug
```
**Contenu** :
- `slug` : Slug recherché
- `funnel_id` : ID du funnel

---

### **3. Stockage en Session** 💾

**Fichier** : `FunnelController::trackCommercialAttribution()`

```
💾 [COMMERCIAL ATTRIBUTION] Stockage en session
```
**Contenu** :
- `commercial_id` : ID du commercial
- `funnel_id` : ID du funnel
- `funnel_name` : Nom du funnel
- `session_id` : ID de session Laravel

```
✅ [COMMERCIAL ATTRIBUTION] Session mise à jour
```
**Contenu** :
- `commercial_ref` : ID commercial stocké
- `commercial_funnel_id` : ID funnel stocké

```
📊 [COMMERCIAL ATTRIBUTION] Compteur incrémenté
```
**Contenu** :
- `commercial_id` : ID du commercial
- `funnel_id` : ID du funnel
- `rows_updated` : Nombre de lignes mises à jour (devrait être 1)

---

### **4. Soumission du Formulaire** 📝

**Fichier** : `FunnelController::handleFormSubmission()`

```
📝 [FORM SUBMISSION] Début traitement formulaire
```
**Contenu** :
- `funnel_id` : ID du funnel
- `page_id` : ID de la page
- `email` : Email du lead
- `session_commercial_ref` : ID commercial en session
- `session_id` : ID de session

**Scénario 1 : Commercial trouvé** ✅

```
👤 [FORM SUBMISSION] Commercial trouvé en session
```
**Contenu** :
- `commercial_id` : ID du commercial
- `commercial_found` : OUI/NON
- `commercial_name` : Nom du commercial

**Scénario 2 : Aucun commercial** ⚠️

```
⚠️ [FORM SUBMISSION] Aucun commercial en session
```
**Contenu** :
- `session_data` : Toutes les données de session

---

### **5. Création du Lead** ✅

```
✅ [FORM SUBMISSION] Lead créé
```
**Contenu** :
- `lead_id` : ID du lead créé
- `lead_email` : Email du lead
- `brought_by` : ID du commercial (devrait correspondre à `commercial_ref`)
- `commercial_name` : Nom du commercial

---

## 🛠️ Comment Utiliser les Logs

### **Sur le Serveur**

```bash
# Se connecter
ssh dev@royalleadpro.com
cd public_html

# Suivre les logs en temps réel
tail -f storage/logs/laravel.log

# Filtrer uniquement les logs d'attribution
tail -f storage/logs/laravel.log | grep "COMMERCIAL ATTRIBUTION\|FORM SUBMISSION\|FUNNEL RESOLVE"

# Rechercher les logs d'un lead spécifique
grep "test@example.com" storage/logs/laravel.log

# Voir les derniers logs d'attribution
grep "COMMERCIAL ATTRIBUTION" storage/logs/laravel.log | tail -20
```

---

## 🔍 Scénario de Test Complet

### **Étape 1 : Accès à l'URL**

Visiteur accède : `https://groups.royalleadpro.com/f/test`

**Logs attendus** :
```
🔍 [FUNNEL RESOLVE] Début résolution funnel
  subdomain_param: "test"
  url: "https://groups.royalleadpro.com/f/test"
  session_commercial_ref: null

✅ [FUNNEL RESOLVE] Funnel trouvé par slug
  funnel_id: 2
  funnel_name: "Mon Funnel"
  slug: "test"

🎯 [COMMERCIAL ATTRIBUTION] Custom slug trouvé pour commercial
  commercial_id: 5
  custom_slug: "test"
  funnel_id: 2

💾 [COMMERCIAL ATTRIBUTION] Stockage en session
  commercial_id: 5
  funnel_id: 2
  session_id: "abc123..."

✅ [COMMERCIAL ATTRIBUTION] Session mise à jour
  commercial_ref: 5
  commercial_funnel_id: 2

📊 [COMMERCIAL ATTRIBUTION] Compteur incrémenté
  rows_updated: 1
```

---

### **Étape 2 : Soumission du Formulaire**

Visiteur soumet le formulaire avec `email: "john@example.com"`

**Logs attendus** :
```
📝 [FORM SUBMISSION] Début traitement formulaire
  funnel_id: 2
  page_id: 10
  email: "john@example.com"
  session_commercial_ref: 5
  session_id: "abc123..."

👤 [FORM SUBMISSION] Commercial trouvé en session
  commercial_id: 5
  commercial_found: "OUI"
  commercial_name: "Jean Dupont"

✅ [FORM SUBMISSION] Lead créé
  lead_id: 42
  lead_email: "john@example.com"
  brought_by: 5
  commercial_name: "Jean Dupont"
```

---

## ❌ Problèmes Courants

### **Problème 1 : Session perdue entre visite et soumission**

**Symptômes** :
```
🔍 [FUNNEL RESOLVE] session_commercial_ref: 5  ✅
📝 [FORM SUBMISSION] session_commercial_ref: null  ❌
```

**Causes possibles** :
- Cookie de session bloqué
- Domaine de session mal configuré
- Session expirée

**Solution** :
Vérifier `config/session.php` :
```php
'domain' => env('SESSION_DOMAIN', '.royalleadpro.com'),
'same_site' => 'lax',
```

---

### **Problème 2 : Custom slug non trouvé**

**Symptômes** :
```
✅ [FUNNEL RESOLVE] Funnel trouvé par slug
⚠️ [COMMERCIAL ATTRIBUTION] Aucun custom_slug trouvé pour ce slug
```

**Causes** :
- Le `custom_slug` n'existe pas dans `funnel_user`
- Le `custom_slug` est inactif (`is_active = false`)

**Solution** :
```sql
SELECT * FROM funnel_user 
WHERE custom_slug = 'test' 
AND is_active = 1;
```

---

### **Problème 3 : Commercial non trouvé en session**

**Symptômes** :
```
⚠️ [FORM SUBMISSION] Aucun commercial en session
  session_data: {...}
```

**Vérifier** :
- La clé `commercial_ref` existe dans `session_data`
- L'utilisateur avec cet ID existe dans la table `users`

---

## 📈 Vérification Finale

Après un test complet, vérifier en base de données :

```sql
-- Vérifier le lead créé
SELECT id, email, brought_by, created_at 
FROM leads 
WHERE email = 'john@example.com' 
ORDER BY created_at DESC 
LIMIT 1;

-- Vérifier l'attribution
SELECT u.name, l.email, l.brought_by 
FROM leads l
JOIN users u ON l.brought_by = u.id
WHERE l.email = 'john@example.com';

-- Vérifier le compteur
SELECT user_id, funnel_id, leads_count 
FROM funnel_user 
WHERE user_id = 5 AND funnel_id = 2;
```

---

## 🚀 Commandes Utiles

```bash
# Vider les logs
> storage/logs/laravel.log

# Compter les attributions réussies aujourd'hui
grep "Lead créé" storage/logs/laravel.log | grep "$(date +%Y-%m-%d)" | wc -l

# Voir tous les commerciaux qui ont reçu des leads
grep "Lead créé" storage/logs/laravel.log | grep "brought_by" | sort | uniq
```
