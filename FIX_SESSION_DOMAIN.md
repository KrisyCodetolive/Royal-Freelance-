# 🔧 Fix Session - Attribution des Leads

## ❌ Problème Identifié

Les logs montrent que l'attribution fonctionne **PARFAITEMENT** lors de la visite :

```
✅ [COMMERCIAL ATTRIBUTION] Session mise à jour
  commercial_ref: 2
  commercial_funnel_id: 2
  session_id: "do9wkwZZy5eDk5mZZRGXK0GCwaAtzNm65kg9Nq8P"
```

**MAIS** : La session est probablement perdue entre la visite et la soumission du formulaire.

---

## 🔍 Cause Probable

**Configuration `.env`** :
```env
SESSION_DOMAIN=null
```

Avec `null`, le cookie de session est limité au domaine exact (`groups.royalleadpro.com`) et peut ne pas persister correctement.

---

## ✅ Solution

### **Option 1 : Domaine Wildcard (Recommandé)**

Modifier `.env` sur le serveur :

```env
SESSION_DOMAIN=.royalleadpro.com
```

**Avantage** : Le cookie fonctionne sur tous les sous-domaines :
- `groups.royalleadpro.com`
- `www.royalleadpro.com`
- `admin.royalleadpro.com`

### **Option 2 : Domaine Spécifique**

```env
SESSION_DOMAIN=groups.royalleadpro.com
```

**Avantage** : Plus sécurisé, mais limité à un seul sous-domaine.

---

## 🚀 Commandes à Exécuter

### **Sur le Serveur** :

```bash
# 1. Se connecter
ssh dev@royalleadpro.com
cd public_html

# 2. Modifier .env
nano .env

# Changer :
# SESSION_DOMAIN=null
# En :
# SESSION_DOMAIN=.royalleadpro.com

# 3. Vider le cache de configuration
php artisan config:clear
php artisan cache:clear

# 4. Vérifier la configuration
php artisan tinker
>>> config('session.domain')
=> ".royalleadpro.com"
```

---

## 🧪 Test Après Correction

### **1. Vider les cookies du navigateur**

Dans Chrome/Firefox :
- Ouvrir DevTools (F12)
- Application > Cookies
- Supprimer tous les cookies de `royalleadpro.com`

### **2. Tester le flux complet**

1. **Visiter** : `https://groups.royalleadpro.com/f/test`
2. **Vérifier** les logs :
   ```
   ✅ [COMMERCIAL ATTRIBUTION] Session mise à jour
     commercial_ref: 2
   ```
3. **Soumettre** le formulaire
4. **Vérifier** les logs :
   ```
   👤 [FORM SUBMISSION] Commercial trouvé en session
     commercial_id: 2
     commercial_found: "OUI"
   
   ✅ [FORM SUBMISSION] Lead créé
     brought_by: 2
   ```

### **3. Vérifier en base de données**

```sql
SELECT id, email, brought_by, created_at 
FROM leads 
ORDER BY created_at DESC 
LIMIT 1;
```

Le champ `brought_by` devrait être `2`.

---

## 🔍 Vérification du Cookie

### **Dans le Navigateur (DevTools)** :

1. Ouvrir DevTools (F12)
2. Aller dans **Application** > **Cookies**
3. Chercher le cookie `royal-leadpro-session`
4. Vérifier :
   - **Domain** : `.royalleadpro.com` (avec le point)
   - **Path** : `/`
   - **SameSite** : `Lax`
   - **Secure** : `Yes` (si HTTPS)

---

## 📊 Autres Paramètres de Session à Vérifier

### **Dans `.env`** :

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_EXPIRE_ON_CLOSE=false
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true  # Important pour HTTPS
SESSION_SAME_SITE=lax
SESSION_DOMAIN=.royalleadpro.com  # ← FIX PRINCIPAL
```

---

## ⚠️ Si le Problème Persiste

### **Vérifier que la session est bien stockée** :

```sql
-- Vérifier la table sessions
SELECT * FROM sessions 
WHERE id = 'do9wkwZZy5eDk5mZZRGXK0GCwaAtzNm65kg9Nq8P';

-- Décoder le payload
SELECT 
  id,
  user_id,
  FROM_UNIXTIME(last_activity) as last_activity_time,
  payload
FROM sessions
ORDER BY last_activity DESC
LIMIT 5;
```

### **Ajouter un log pour vérifier le cookie** :

Dans `FunnelController::handleFormSubmission()`, après la ligne 286 :

```php
\Log::info('🍪 [DEBUG COOKIE]', [
    'cookies' => $request->cookies->all(),
    'session_cookie_name' => config('session.cookie'),
    'session_id_from_cookie' => $request->cookie(config('session.cookie')),
]);
```

---

## 📝 Résumé

**Problème** : Session perdue entre visite et soumission
**Cause** : `SESSION_DOMAIN=null` dans `.env`
**Solution** : `SESSION_DOMAIN=.royalleadpro.com`
**Commande** : `php artisan config:clear`

**Après correction, les leads seront correctement attribués !** ✅
