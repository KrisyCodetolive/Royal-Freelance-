# Configuration Wildcard DNS sur cPanel

> Guide étape par étape pour configurer les sous-domaines automatiques sur un hébergement mutualisé cPanel

## 🎯 Objectif

Permettre à chaque tunnel d'avoir son propre sous-domaine **automatiquement**, sans intervention manuelle :

```
mon-tunnel.votredomaine.com     → Tunnel "Mon Tunnel"
formation-vip.votredomaine.com  → Tunnel "Formation VIP"
offre-speciale.votredomaine.com → Tunnel "Offre Spéciale"
```

---

## 📋 Prérequis

- Accès cPanel à votre hébergement
- Accès à la Zone DNS (souvent dans cPanel ou chez le registrar)
- Certificat SSL Wildcard (Let's Encrypt gratuit via cPanel ou payant)
- Laravel déployé sur l'hébergement

---

## Étape 1 : Configuration DNS Wildcard

### Option A : Via cPanel (Zone Editor)

1. Connectez-vous à **cPanel**
2. Allez dans **Zone Editor** (section Domaines)
3. Cliquez sur **Gérer** à côté de votre domaine
4. Cliquez sur **+ Ajouter un enregistrement**
5. Configurez :

| Champ | Valeur |
|-------|--------|
| Type | A |
| Nom | `*` (juste l'astérisque) |
| Adresse | L'IP de votre serveur (visible dans cPanel) |
| TTL | 14400 (ou 300 pour tests) |

6. **Sauvegardez**

### Option B : Via le Registrar (Cloudflare, OVH, etc.)

Si votre DNS est géré par Cloudflare, OVH, ou autre :

```
Type    Nom     Contenu             Proxy
A       *       xxx.xxx.xxx.xxx     DNS only (gris)
A       @       xxx.xxx.xxx.xxx     Proxied (orange) ou DNS only
```

> ⚠️ **Important** : Si vous utilisez Cloudflare en mode Proxy, vous aurez le SSL automatiquement. Sinon, suivez l'étape 2.

---

## Étape 2 : Certificat SSL Wildcard

### Option A : Let's Encrypt via cPanel (Gratuit)

1. Dans cPanel, allez dans **SSL/TLS Status**
2. Recherchez votre domaine
3. Si disponible, cliquez sur **AutoSSL** ou **Run AutoSSL**
4. Certains cPanel supportent le wildcard automatiquement

### Option B : Let's Encrypt manuel avec validation DNS

Si cPanel ne supporte pas le wildcard automatique :

1. Connectez-vous en SSH ou utilisez le Terminal cPanel
2. Installez Certbot si disponible, ou utilisez acme.sh :

```bash
# Installation acme.sh (si pas déjà installé)
curl https://get.acme.sh | sh

# Génération du certificat wildcard (validation DNS manuelle)
~/.acme.sh/acme.sh --issue -d votredomaine.com -d '*.votredomaine.com' --dns --yes-I-know-dns-manual-mode-enough-go-ahead-please
```

3. Suivez les instructions pour ajouter l'enregistrement TXT DNS
4. Une fois vérifié, installez le certificat dans cPanel > SSL/TLS > Installer

### Option C : Cloudflare (Recommandé - Gratuit et Automatique)

1. Utilisez Cloudflare comme DNS
2. Le SSL Wildcard est **automatique et gratuit**
3. Activez le mode "Full (Strict)" dans SSL/TLS

---

## Étape 3 : Configuration cPanel - Sous-domaine Wildcard

### Créer le sous-domaine wildcard dans cPanel

1. Allez dans **Sous-domaines** (Subdomains)
2. Créez un nouveau sous-domaine :

| Champ | Valeur |
|-------|--------|
| Sous-domaine | `*` (astérisque) |
| Domaine | votredomaine.com |
| Racine du document | `/public_html` (même dossier que le domaine principal) |

3. **Créer**

> 💡 Cela fait pointer TOUS les sous-domaines vers le même dossier que votre application Laravel.

---

## Étape 4 : Configuration Apache (.htaccess)

Assurez-vous que votre `.htaccess` dans `/public_html` (ou `/public`) gère correctement les sous-domaines :

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

# Sécurité
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

## Étape 5 : Configuration Laravel (.env)

```env
# URL de base de l'application (domaine principal)
APP_URL=https://votredomaine.com

# Domaine de base pour les sous-domaines (SANS le protocole)
APP_SUBDOMAIN_BASE=votredomaine.com

# IP du serveur (pour les domaines personnalisés futurs)
APP_SERVER_IP=xxx.xxx.xxx.xxx

# CRUCIAL : Domaine des cookies avec le point devant !
SESSION_DOMAIN=.votredomaine.com

# Cookies sécurisés
SESSION_SECURE_COOKIE=true
```

### 🔴 Point crucial : SESSION_DOMAIN

Le point (`.`) devant le domaine est **obligatoire** pour que les cookies soient partagés entre :
- `votredomaine.com`
- `tunnel1.votredomaine.com`
- `tunnel2.votredomaine.com`

---

## Étape 6 : Configuration Laravel (config/session.php)

Vérifiez que la configuration session utilise bien les variables d'environnement :

```php
// config/session.php

return [
    // ...
    
    'domain' => env('SESSION_DOMAIN'),
    
    'secure' => env('SESSION_SECURE_COOKIE', true),
    
    // ...
];
```

---

## Étape 7 : Vider les caches

Après toutes les modifications :

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Ou tout en un
php artisan optimize:clear
```

---

## 🧪 Test de la configuration

### 1. Vérifier le DNS

```bash
# Depuis votre terminal local
nslookup test-random.votredomaine.com

# Devrait retourner l'IP de votre serveur
```

Ou utilisez : https://dnschecker.org

### 2. Créer un tunnel test

1. Dans l'admin Filament, créez un tunnel avec le nom "Test Wildcard"
2. Définissez le sous-domaine : `test-wildcard`
3. Accédez à : `https://test-wildcard.votredomaine.com`

### 3. Vérifier le SSL

Visitez : `https://test-wildcard.votredomaine.com`
- 🔒 Le cadenas doit être vert/fermé
- Aucune erreur de certificat

---

## 📁 Structure des fichiers sur cPanel

```
/home/votreuser/
├── public_html/                    ← Document Root (tous les domaines/sous-domaines)
│   ├── index.php                   ← Point d'entrée Laravel
│   ├── .htaccess                   ← Règles Apache
│   └── ...
├── app/
├── config/
├── routes/
├── storage/
├── vendor/
├── .env                            ← Configuration
└── ...
```

---

## 🔧 Dépannage

### Le sous-domaine affiche une erreur 404 ou page parking

**Cause** : Le sous-domaine wildcard n'est pas configuré dans cPanel
**Solution** : Vérifiez l'étape 3 (création du sous-domaine `*`)

### Erreur SSL / Certificat invalide

**Cause** : Le certificat wildcard n'est pas installé
**Solutions** :
1. Utilisez Cloudflare (SSL automatique)
2. Installez un certificat wildcard Let's Encrypt
3. Certains hébergeurs offrent le wildcard SSL en option payante

### Les cookies ne sont pas partagés entre sous-domaines

**Cause** : `SESSION_DOMAIN` mal configuré
**Solution** : 
```env
SESSION_DOMAIN=.votredomaine.com  # Avec le POINT !
```

### Le tunnel n'est pas trouvé (404)

**Cause** : Le sous-domaine n'est pas enregistré dans la base de données
**Solution** : Vérifiez que le champ `subdomain` du funnel est bien rempli

### Redirection infinie

**Cause** : Conflit entre les règles .htaccess et la config Apache
**Solution** : Vérifiez qu'il n'y a pas de double redirection HTTPS

---

## 📊 Monitoring

### Logs utiles

```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Logs Apache (accès cPanel)
# Allez dans : cPanel > Metrics > Raw Access / Error Log
```

### Vérification du sous-domaine actif

Dans Laravel, vous pouvez logger le sous-domaine pour debug :

```php
// Dans un middleware ou controller
Log::info('Subdomain accessed', [
    'host' => request()->getHost(),
    'subdomain' => app(SubdomainService::class)->extractSubdomainFromRequest(),
]);
```

---

## 🚀 Checklist finale

- [ ] Enregistrement DNS `*` (A record) créé
- [ ] Sous-domaine `*` créé dans cPanel
- [ ] Certificat SSL Wildcard installé (ou Cloudflare)
- [ ] `.htaccess` configuré avec redirection HTTPS
- [ ] `.env` avec `APP_SUBDOMAIN_BASE` correct
- [ ] `.env` avec `SESSION_DOMAIN=.votredomaine.com` (avec le point !)
- [ ] Caches Laravel vidés
- [ ] Test avec un sous-domaine aléatoire réussi
- [ ] Test SSL réussi (cadenas vert)

---

## 💡 Avantages de cette approche

| Avantage | Description |
|----------|-------------|
| ✅ Automatique | Aucune action manuelle pour chaque tunnel |
| ✅ Instantané | Le sous-domaine fonctionne dès sa création |
| ✅ Économique | Pas de frais supplémentaires par sous-domaine |
| ✅ Scalable | Supporte des milliers de tunnels |
| ✅ SEO Friendly | Chaque tunnel a son propre domaine |

---

## 📞 Support hébergeurs populaires

| Hébergeur | Wildcard DNS | Wildcard SSL | Notes |
|-----------|--------------|--------------|-------|
| o2switch | ✅ | ✅ (Let's Encrypt) | Très bien supporté |
| OVH Web | ✅ | ⚠️ (Payant) | Utilisez Cloudflare |
| Hostinger | ✅ | ✅ | Bien supporté |
| PlanetHoster | ✅ | ✅ | Excellent support |
| LWS | ✅ | ⚠️ | Vérifiez le plan |
| Infomaniak | ✅ | ✅ | Suisse, excellent |

---

*Guide mis à jour : Janvier 2026*
