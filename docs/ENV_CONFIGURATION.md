# Configuration Environnement (.env)

> Guide complet pour configurer Royal LeadMagnet

## 📋 Table des matières

1. [Configuration de base](#configuration-de-base)
2. [Base de données](#base-de-données)
3. [Sous-domaines & Domaines personnalisés](#sous-domaines--domaines-personnalisés)
4. [Services Email](#services-email)
5. [Stockage & Médias](#stockage--médias)
6. [Tracking & Analytics](#tracking--analytics)
7. [Géolocalisation](#géolocalisation)
8. [Paiements](#paiements)
9. [File d'attente (Queue)](#file-dattente-queue)
10. [Cache & Sessions](#cache--sessions)
11. [Sécurité](#sécurité)

---

## Configuration de base

```env
# Nom de l'application (affiché dans l'admin et les emails)
APP_NAME="Royal LeadMagnet"

# Environnement : local, staging, production
APP_ENV=production

# Mode debug (JAMAIS en production !)
APP_DEBUG=false

# Clé de chiffrement (générer avec: php artisan key:generate)
APP_KEY=base64:VOTRE_CLE_GENEREE

# URL principale de l'application
APP_URL=https://app.royalleadmagnet.com

# Timezone (important pour les séquences email et analytics)
APP_TIMEZONE=Africa/Dakar

# Langue par défaut
APP_LOCALE=fr
APP_FALLBACK_LOCALE=en
```

---

## Base de données

```env
# Type de base de données
DB_CONNECTION=mysql

# Serveur de base de données
DB_HOST=127.0.0.1
DB_PORT=3306

# Nom de la base de données
DB_DATABASE=royal_leadmagnet

# Identifiants
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe_securise

# Options avancées (optionnel)
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

### 💡 Conseils Base de Données

| Environnement | Recommandation |
|---------------|----------------|
| Local | MySQL 8.0+ ou MariaDB 10.5+ |
| Production | MySQL 8.0 avec réplication |
| Hébergé | PlanetScale, DigitalOcean Managed DB |

---

## Sous-domaines & Domaines personnalisés

> ⚠️ **Nouveau !** Configuration requise pour les URLs de tunnels personnalisées.

```env
# Domaine de base pour les sous-domaines de tunnels
# Ex: pour "mon-tunnel.votreapp.com", mettre "votreapp.com"
APP_SUBDOMAIN_BASE=votreapp.com

# Adresse IP du serveur (pour la configuration DNS des domaines personnalisés)
APP_SERVER_IP=123.456.789.000
```

### 🌐 Configuration DNS requise

Pour que les sous-domaines fonctionnent, vous devez configurer un **wildcard DNS** :

```
Type    Host    Value                   TTL
A       *       123.456.789.000         300
A       @       123.456.789.000         300
```

### 📊 Exemples d'URLs

| Type | Format |
|------|--------|
| Classique | `https://app.com/f/mon-tunnel` |
| Sous-domaine | `https://mon-tunnel.app.com` |
| Domaine perso | `https://www.monsite.com` |

---

## Services Email

### Option 1: SMTP Standard

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@mg.votredomaine.com
MAIL_PASSWORD=votre_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votredomaine.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Option 2: Mailgun (Recommandé pour l'Afrique)

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.votredomaine.com
MAILGUN_SECRET=key-xxxxxxxxxxxxxxxxxxxx
MAILGUN_ENDPOINT=api.eu.mailgun.net  # EU endpoint recommandé
```

### Option 3: Sendinblue/Brevo

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=votre_email@exemple.com
MAIL_PASSWORD=votre_cle_smtp
MAIL_ENCRYPTION=tls
```

### Option 4: Amazon SES

```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=AKIAIOSFODNN7EXAMPLE
AWS_SECRET_ACCESS_KEY=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
AWS_DEFAULT_REGION=eu-west-1
```

---

## Stockage & Médias

### Local (Développement)

```env
FILESYSTEM_DISK=local
```

### Amazon S3 (Production)

```env
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=AKIAIOSFODNN7EXAMPLE
AWS_SECRET_ACCESS_KEY=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
AWS_DEFAULT_REGION=eu-west-1
AWS_BUCKET=royal-leadmagnet-media
AWS_URL=https://royal-leadmagnet-media.s3.eu-west-1.amazonaws.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### DigitalOcean Spaces (Alternative économique)

```env
FILESYSTEM_DISK=do_spaces

DO_SPACES_KEY=XXXXXXXXXXXXXXXXX
DO_SPACES_SECRET=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
DO_SPACES_ENDPOINT=https://fra1.digitaloceanspaces.com
DO_SPACES_REGION=fra1
DO_SPACES_BUCKET=royal-leadmagnet
```

---

## Tracking & Analytics

```env
# Facebook Pixel (optionnel, peut aussi être configuré par tunnel)
FACEBOOK_PIXEL_ID=123456789012345

# Google Analytics (optionnel)
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX

# Google Tag Manager
GTM_CONTAINER_ID=GTM-XXXXXXX
```

---

## Géolocalisation

> Utilisé pour identifier les marchés africains et adapter les prix/paiements.

### Option 1: ipapi.co (Gratuit - 1000 req/jour)

```env
# Pas de configuration nécessaire, utilisé par défaut
GEOIP_PROVIDER=ipapi
```

### Option 2: MaxMind GeoIP2 (Recommandé Production)

```env
GEOIP_PROVIDER=maxmind
MAXMIND_LICENSE_KEY=XXXXXXXXXXXXXXXX
MAXMIND_ACCOUNT_ID=123456
```

### Option 3: ipinfo.io

```env
GEOIP_PROVIDER=ipinfo
IPINFO_TOKEN=xxxxxxxxxxxxxxxxxxxx
```

---

## Paiements

### Mobile Money (Afrique via CinetPay)

```env
CINETPAY_API_KEY=xxxxxxxxxxxxxxxxxxxxx
CINETPAY_SITE_ID=123456
CINETPAY_SECRET_KEY=xxxxxxxxxxxxxxxxxxxxxxx
CINETPAY_MODE=PRODUCTION  # ou TEST
```

### Stripe (International)

```env
STRIPE_KEY=pk_live_xxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_live_xxxxxxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxx
```

### PayPal

```env
PAYPAL_CLIENT_ID=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
PAYPAL_CLIENT_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
PAYPAL_MODE=live  # ou sandbox
```

---

## File d'attente (Queue)

> Pour les séquences email, notifications et tâches asynchrones.

### Développement (Synchrone)

```env
QUEUE_CONNECTION=sync
```

### Production avec Redis (Recommandé)

```env
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_CLIENT=phpredis
```

### Production avec Database

```env
QUEUE_CONNECTION=database
```

> ⚠️ N'oubliez pas de lancer le worker en production :
> ```bash
> php artisan queue:work --daemon --tries=3
> ```

---

## Cache & Sessions

### Développement

```env
CACHE_DRIVER=file
SESSION_DRIVER=file
```

### Production (Redis recommandé)

```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=votre_mot_de_passe_redis
REDIS_PORT=6379
```

### Alternative avec Database

```env
CACHE_DRIVER=database
SESSION_DRIVER=database
```

---

## Sécurité

```env
# Durée de vie du token CSRF (minutes)
SESSION_LIFETIME=120

# Domaine des cookies (important pour les sous-domaines !)
# Mettre un point devant pour autoriser les sous-domaines
SESSION_DOMAIN=.votreapp.com

# Cookies sécurisés (HTTPS uniquement)
SESSION_SECURE_COOKIE=true

# Protection CORS (optionnel, pour API)
CORS_ALLOWED_ORIGINS=https://votreapp.com,https://*.votreapp.com

# Rate limiting (requêtes par minute)
RATE_LIMIT_PER_MINUTE=60
```

---

## 🚀 Configuration Rapide par Environnement

### Développement Local (.env.local)

```env
APP_NAME="Royal LeadMagnet DEV"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_SUBDOMAIN_BASE=localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=royal_leadmagnet_dev
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

MAIL_MAILER=log
```

### Staging (.env.staging)

```env
APP_NAME="Royal LeadMagnet STAGING"
APP_ENV=staging
APP_DEBUG=true
APP_URL=https://staging.royalleadmagnet.com
APP_SUBDOMAIN_BASE=staging.royalleadmagnet.com

DB_CONNECTION=mysql
DB_HOST=db.staging.internal
DB_DATABASE=royal_leadmagnet_staging
DB_USERNAME=staging_user
DB_PASSWORD=staging_password_secure

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

MAIL_MAILER=smtp
# ... config email de test
```

### Production (.env.production)

```env
APP_NAME="Royal LeadMagnet"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.royalleadmagnet.com
APP_SUBDOMAIN_BASE=royalleadmagnet.com
APP_SERVER_IP=xxx.xxx.xxx.xxx

DB_CONNECTION=mysql
DB_HOST=db.production.internal
DB_DATABASE=royal_leadmagnet
DB_USERNAME=prod_user
DB_PASSWORD=SUPER_SECURE_PASSWORD_GENERATED

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_DOMAIN=.royalleadmagnet.com
SESSION_SECURE_COOKIE=true

FILESYSTEM_DISK=s3
# ... config S3

MAIL_MAILER=ses
# ... config SES
```

---

## 📝 Checklist de Déploiement

- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` générée (`php artisan key:generate`)
- [ ] `APP_URL` correcte (avec HTTPS)
- [ ] `APP_SUBDOMAIN_BASE` configuré
- [ ] `SESSION_DOMAIN` avec le point (`.votredomaine.com`)
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] Base de données configurée et migrée
- [ ] Redis installé et configuré
- [ ] Queue worker démarré avec Supervisor
- [ ] SSL/TLS configuré (Let's Encrypt)
- [ ] Wildcard DNS configuré pour les sous-domaines
- [ ] Stockage S3/Spaces configuré pour les médias
- [ ] Service email configuré et testé
- [ ] Backup automatique de la BDD configuré

---

## 🆘 Dépannage

### Les sous-domaines ne fonctionnent pas

1. Vérifiez le wildcard DNS (`*.votredomaine.com`)
2. Vérifiez `APP_SUBDOMAIN_BASE`
3. Vérifiez `SESSION_DOMAIN` (avec le point !)
4. Videz le cache : `php artisan config:clear`

### Les cookies ne sont pas partagés entre sous-domaines

```env
SESSION_DOMAIN=.votredomaine.com  # Le point est CRUCIAL !
```

### Erreurs de CORS

```env
CORS_ALLOWED_ORIGINS=https://votredomaine.com,https://*.votredomaine.com
```

### Emails non reçus

1. Vérifiez les logs : `storage/logs/laravel.log`
2. Testez avec : `php artisan tinker` puis `Mail::raw('Test', fn($m) => $m->to('test@email.com'));`
3. Vérifiez les SPF/DKIM de votre domaine

---

## 📞 Support

Pour toute question sur la configuration :
- Documentation : `/docs`
- Issues : GitHub Issues
- Email : support@royalleadmagnet.com

---

*Dernière mise à jour : Janvier 2026*
