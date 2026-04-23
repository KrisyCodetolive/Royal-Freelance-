# Configuration Wildcard pour royalleadpro.com

> Guide de déploiement complet pour activer les sous-domaines automatiques

## 🎯 Objectif

Permettre aux tunnels d'avoir leurs propres sous-domaines automatiquement :

```
mon-tunnel.royalleadpro.com        → Tunnel "Mon Tunnel"
formation-vip.royalleadpro.com     → Tunnel "Formation VIP"
offre-speciale.royalleadpro.com    → Tunnel "Offre Spéciale"
```

---

## ✅ Étape 1 : Configuration du fichier .env sur le serveur

Connectez-vous à votre serveur cPanel et éditez le fichier `.env` :

```bash
# Via File Manager cPanel ou SSH
nano /home/elngbpzd/royalleadpro.com/.env
```

### Variables obligatoires à configurer :

```env
APP_NAME="Royal LeadMagnet"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://royalleadpro.com

# ⭐ CONFIGURATION WILDCARD - CRUCIAL
APP_SUBDOMAIN_BASE=royalleadpro.com
APP_SERVER_IP=102.219.176.30

# ⭐ SESSION - CRUCIAL: Le point (.) devant est OBLIGATOIRE
SESSION_DOMAIN=.royalleadpro.com
SESSION_SECURE_COOKIE=true

# Locale française
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
```

> **⚠️ IMPORTANT** : Le point (`.`) devant `royalleadpro.com` dans `SESSION_DOMAIN` est **obligatoire** pour partager les cookies entre le domaine principal et tous les sous-domaines.

---

## ✅ Étape 2 : Configuration DNS Wildcard

### Via cPanel Zone Editor

1. Connectez-vous à **cPanel** (`https://cp7.zonehosting.com:2083`)
2. Allez dans **Zone Editor** (section Domaines)
3. Cliquez sur **Gérer** à côté de `royalleadpro.com`
4. Cliquez sur **+ Ajouter un enregistrement**

### Ajouter l'enregistrement Wildcard :

| Champ | Valeur |
|-------|--------|
| **Type** | `A` |
| **Nom** | `*` (juste l'astérisque) |
| **Adresse** | `102.219.176.30` (votre IP serveur) |
| **TTL** | `14400` (4 heures) ou `300` (5 min pour tests) |

**Cliquez sur "Ajouter l'enregistrement"**

> 💡 **Résultat** : Cela fait pointer TOUS les sous-domaines (*.royalleadpro.com) vers votre serveur

---

## ✅ Étape 3 : Créer le sous-domaine Wildcard dans cPanel

### Via cPanel > Sous-domaines

1. Dans cPanel, allez dans **Sous-domaines** (Subdomains)
2. Créez un nouveau sous-domaine :

| Champ | Valeur |
|-------|--------|
| **Sous-domaine** | `*` (astérisque) |
| **Domaine** | `royalleadpro.com` |
| **Racine du document** | `/public_html` (même dossier que le domaine principal) |

3. Cliquez sur **Créer**

> 💡 **Résultat** : Tous les sous-domaines pointent vers le même dossier que votre application Laravel

---

## ✅ Étape 4 : Configuration SSL Wildcard

### Option A : AutoSSL cPanel (Si disponible)

1. Dans cPanel, allez dans **SSL/TLS Status**
2. Recherchez `royalleadpro.com`
3. Cochez la case à côté du domaine principal
4. Cliquez sur **Run AutoSSL**
5. Attendez quelques minutes

> ⚠️ Certains hébergeurs mutualisés ne supportent pas le wildcard SSL gratuit

### Option B : Let's Encrypt Wildcard (Recommandé)

Via SSH :

```bash
# Se connecter en SSH
ssh elngbpzd@cp7.zonehosting.com

# Installer acme.sh (si pas déjà installé)
curl https://get.acme.sh | sh
source ~/.bashrc

# Générer le certificat wildcard
~/.acme.sh/acme.sh --issue \
  -d royalleadpro.com \
  -d '*.royalleadpro.com' \
  --dns dns_cf \
  --server letsencrypt

# Suivre les instructions pour la validation DNS
```

> 💡 Pour la validation DNS, vous devrez ajouter un enregistrement TXT `_acme-challenge.royalleadpro.com`

### Option C : Cloudflare (Le plus simple - Recommandé)

Si vous utilisez Cloudflare comme DNS :

1. Le SSL Wildcard est **automatique et gratuit**
2. Assurez-vous que le mode SSL/TLS est sur **"Full (strict)"**
3. Les enregistrements DNS :

```
Type    Nom                 Contenu             Proxy
A       royalleadpro.com    102.219.176.30      Proxied (orange)
A       *                   102.219.176.30      DNS only (gris)
```

> ⚠️ Le wildcard `*` doit être en mode **DNS only** (gris), pas Proxied

---

## ✅ Étape 5 : Vérifier le fichier .htaccess

Dans `/home/elngbpzd/royalleadpro.com/public_html/.htaccess` :

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirection HTTPS (important pour les sous-domaines)
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Gestion standard Laravel
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Sécurité - masquer les fichiers .env
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

## ✅ Étape 6 : Vider les caches Laravel

Via SSH ou Terminal cPanel :

```bash
cd /home/elngbpzd/royalleadpro.com
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Ou tout en un :

```bash
php artisan optimize
```

---

## 🧪 Étape 7 : Tester la configuration

### Test 1 : Vérifier le DNS

Depuis votre terminal local :

```bash
# Tester un sous-domaine aléatoire
nslookup test123.royalleadpro.com

# Devrait retourner : 102.219.176.30
```

Ou utilisez : https://dnschecker.org (entrez `test123.royalleadpro.com`)

### Test 2 : Créer un tunnel test

1. Connectez-vous à l'admin Filament : `https://royalleadpro.com/admin`
2. Allez dans **Tunnels** > **Créer**
3. Remplissez :
   - **Nom** : Test Wildcard
   - **Sous-domaine** : `test-wildcard`
4. Sauvegardez

### Test 3 : Accéder au sous-domaine

Visitez : `https://test-wildcard.royalleadpro.com`

**Résultats attendus :**
- ✅ La page du tunnel s'affiche
- ✅ Le cadenas SSL est vert/fermé
- ✅ Aucune erreur de certificat
- ✅ L'URL dans la barre est bien `test-wildcard.royalleadpro.com`

---

## 📊 Structure actuelle du code

Le système est déjà en place dans Laravel :

### Routes (déjà configurées)

`routes/web.php` :
```php
// Gestion automatique des sous-domaines
Route::domain('{subdomain}.' . config('app.subdomain_base'))->group(function () {
    Route::get('/{page?}', [FunnelController::class, 'show'])
        ->name('funnel.subdomain');
});
```

### Service (déjà implémenté)

`app/Services/SubdomainService.php` :
- ✅ Extraction du sous-domaine depuis la requête
- ✅ Validation du format (3-63 caractères, lettres/chiffres/tirets)
- ✅ Vérification des sous-domaines réservés
- ✅ Génération automatique depuis le nom du tunnel
- ✅ Cache pour les performances

### Filament (déjà configuré)

Les formulaires de création de tunnels incluent déjà le champ sous-domaine avec :
- Auto-génération depuis le nom
- Validation en temps réel
- Vérification de disponibilité

---

## 🔧 Dépannage

### Le sous-domaine affiche "404 Not Found"

**Solutions :**
1. Vérifiez que le sous-domaine `*` est créé dans cPanel
2. Vérifiez que le DNS wildcard (`*`) est configuré
3. Videz les caches : `php artisan optimize:clear`
4. Vérifiez que le tunnel a bien un sous-domaine dans la BDD

### Erreur SSL / Certificat invalide

**Solutions :**
1. Utilisez Cloudflare pour le SSL automatique
2. Installez un certificat wildcard Let's Encrypt
3. Attendez que l'AutoSSL se propage (jusqu'à 24h)

### Les cookies/sessions ne fonctionnent pas

**Cause :** `SESSION_DOMAIN` mal configuré

**Solution :**
```env
SESSION_DOMAIN=.royalleadpro.com  # Avec le POINT !
```

Puis :
```bash
php artisan config:clear
php artisan config:cache
```

### "Too many redirects"

**Cause :** Conflit HTTPS dans `.htaccess` ou config Cloudflare

**Solutions :**
1. Si Cloudflare : Mode SSL/TLS = "Full (strict)"
2. Vérifiez qu'il n'y a pas de double redirection HTTPS

---

## 📋 Checklist de validation finale

- [ ] `.env` configuré avec `APP_SUBDOMAIN_BASE=royalleadpro.com`
- [ ] `.env` configuré avec `SESSION_DOMAIN=.royalleadpro.com` (avec le point !)
- [ ] Enregistrement DNS `* A 102.219.176.30` créé
- [ ] Sous-domaine `*` créé dans cPanel → `/public_html`
- [ ] SSL Wildcard installé (Cloudflare ou Let's Encrypt)
- [ ] `.htaccess` avec redirection HTTPS
- [ ] Caches Laravel vidés
- [ ] Test avec un sous-domaine aléatoire réussi
- [ ] Test SSL réussi (cadenas vert)
- [ ] Création d'un tunnel test fonctionnel

---

## 🚀 Prochaines étapes après validation

Une fois le wildcard fonctionnel :

### 1. Créer vos premiers tunnels

Via Filament Admin :
- Créez des tunnels depuis les templates
- Le sous-domaine est auto-généré
- Testez chaque tunnel sur son propre sous-domaine

### 2. Configurer les domaines personnalisés (optionnel)

Pour permettre aux clients d'utiliser leur propre domaine :
- Utilisez `SubdomainService::setupCustomDomain()`
- Générez les instructions DNS
- Vérifiez avec `SubdomainService::verifyCustomDomain()`

### 3. Monitoring

Surveillez les logs :
```bash
tail -f storage/logs/laravel.log
```

---

## 💡 Informations complémentaires

### Sous-domaines réservés

Les sous-domaines suivants sont **interdits** pour éviter les conflits :

```
www, app, api, admin, dashboard, panel, mail, email, ftp, sftp,
cdn, assets, static, media, images, files, blog, news, help, support,
docs, dev, staging, test, demo, beta, filament, royal, leadmagnet,
funnel, funnels, tunnel, tunnels
```

### Format valide des sous-domaines

- **Longueur** : 3 à 63 caractères
- **Début** : Doit commencer par une lettre (a-z)
- **Fin** : Lettre ou chiffre
- **Contenu** : Lettres minuscules, chiffres, tirets (pas de tirets consécutifs)

**Exemples valides :**
- `formation-vip`
- `offre2024`
- `webinar-gratuit`

**Exemples invalides :**
- `ab` (trop court)
- `2024-offre` (commence par un chiffre)
- `offre--speciale` (tirets consécutifs)
- `Offre_VIP` (majuscules et underscore interdits)

---

## 📞 Support

En cas de problème :

1. Vérifiez les logs Laravel : `storage/logs/laravel.log`
2. Vérifiez les logs Apache (via cPanel > Raw Access / Error Log)
3. Testez avec `php artisan tinker` :

```php
$service = app(\App\Services\SubdomainService::class);
$service->extractSubdomainFromRequest(); // Doit retourner le sous-domaine
```

---

**✅ Une fois cette configuration terminée, tous vos tunnels auront automatiquement leur propre sous-domaine !**
