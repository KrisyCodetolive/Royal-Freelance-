# Royal LeadMagnet

Plateforme SaaS multi-tenant de génération et gestion de leads avec tunnels de vente, séquences email automatisées et tableau de bord commercial.

**Stack :** Laravel 12 · Filament 4 · PHP 8.2+ · MySQL 8 · Livewire · Tailwind CSS · PWA

---

## Prérequis serveur

- PHP 8.2+ avec extensions : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `zip`
- MySQL 8+
- Composer 2+
- Node.js 18+ et npm
- Nginx
- Certbot pour SSL

---

## Installation initiale (première mise en production)

### 1. Cloner le projet

```bash
cd /var/www
git clone git@github.com:TON_USER/TON_REPO.git royalleadpro
cd royalleadpro
```

### 2. Installer les dépendances

```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
nano .env
```

Valeurs à renseigner obligatoirement :

```env
APP_NAME="Royal LeadMagnet"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://royalleadpro.com
APP_KEY=                          # généré à l'étape suivante

APP_SUBDOMAIN_BASE=royalleadpro.com
APP_SERVER_IP=TON_IP_VPS

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=royal_leadmagnet
DB_USERNAME=ton_user_mysql
DB_PASSWORD=ton_mot_de_passe

SESSION_DRIVER=database
SESSION_DOMAIN=.royalleadpro.com

QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=ton_serveur_smtp
MAIL_PORT=587
MAIL_USERNAME=ton_email@royalleadpro.com
MAIL_PASSWORD=ton_mot_de_passe_smtp
MAIL_FROM_ADDRESS=noreply@royalleadpro.com
MAIL_FROM_NAME="Royal LeadMagnet"

# Clés VAPID pour les notifications push PWA
# Générer avec : php artisan webpush:vapid
VAPID_PUBLIC_KEY=
VAPID_PRIVATE_KEY=
VAPID_SUBJECT=mailto:admin@royalleadpro.com
```

### 4. Générer la clé et préparer la base de données

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

### 5. Optimiser pour la production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 6. Permissions fichiers

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

---

## Configuration DNS

Dans le panneau DNS de ton registrar, ajouter :

| Type  | Nom   | Valeur          |
|-------|-------|-----------------|
| A     | `@`   | IP de ton VPS   |
| A     | `*`   | IP de ton VPS   |
| CNAME | `www` | `royalleadpro.com` |

Le `*` (wildcard) est indispensable — il fait que `comtest01.royalleadpro.com` pointe vers ton VPS.

---

## Configuration Nginx

Créer le fichier `/etc/nginx/sites-available/royalleadpro.com` :

```nginx
server {
    listen 80;
    server_name royalleadpro.com *.royalleadpro.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name royalleadpro.com *.royalleadpro.com;

    ssl_certificate     /etc/letsencrypt/live/royalleadpro.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/royalleadpro.com/privkey.pem;

    root /var/www/royalleadpro/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
ln -s /etc/nginx/sites-available/royalleadpro.com /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
```

### SSL wildcard avec Let's Encrypt

Le certificat wildcard couvre `*.royalleadpro.com` (tous les sous-domaines commerciaux). La validation se fait par DNS :

```bash
certbot certonly --manual --preferred-challenges dns \
  -d royalleadpro.com \
  -d *.royalleadpro.com
```

Certbot affichera un enregistrement `TXT` à ajouter chez ton registrar — ajoute-le puis valide.

---

## Queue Worker

Les séquences email sont envoyées via la queue Laravel. Créer un service systemd pour qu'il tourne en permanence :

```bash
nano /etc/systemd/system/royal-queue.service
```

```ini
[Unit]
Description=Royal LeadMagnet Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/royalleadpro
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=on-failure
RestartSec=5
StandardOutput=append:/var/log/royal-queue.log
StandardError=append:/var/log/royal-queue.log

[Install]
WantedBy=multi-user.target
```

```bash
systemctl enable royal-queue
systemctl start royal-queue
systemctl status royal-queue    # vérifier qu'il tourne
```

---

## Scheduler — séquences email automatiques

Un seul cron suffit. Laravel gère lui-même le planning défini dans `routes/console.php`.

```bash
crontab -e -u www-data
```

Ajouter cette ligne :

```
* * * * * cd /var/www/royalleadpro && php artisan schedule:run >> /dev/null 2>&1
```

**Tâches planifiées automatiquement :**

| Commande | Fréquence | Rôle |
|----------|-----------|------|
| `email:process-sequences` | Toutes les 5 min | Envoie les emails des séquences en attente |
| `leads:check-inactive` | Tous les jours à 09h00 | Détecte les leads inactifs et déclenche les séquences d'inactivité |

**Tester manuellement :**

```bash
php artisan schedule:list                   # voir les tâches et leur prochain déclenchement
php artisan schedule:run                    # simuler un tick du cron
php artisan email:process-sequences         # lancer l'envoi email directement
php artisan leads:check-inactive            # lancer la vérification inactivité
```

---

## Déploiement des mises à jour

À chaque nouvelle version, se connecter au VPS et exécuter :

```bash
cd /var/www/royalleadpro
git pull origin main
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
systemctl restart royal-queue
```

---

## Lancement en local (développement)

```bash
git clone ...
cd royalleadpro
composer install
npm install

cp .env.example .env
# Renseigner DB_*, puis :
# APP_URL=http://lvh.me:8000
# APP_SUBDOMAIN_BASE=lvh.me
# SESSION_DOMAIN=   (laisser vide)

php artisan key:generate
php artisan migrate
php artisan storage:link

# Dans des terminaux séparés :
npm run dev
php artisan serve --host=0.0.0.0 --port=8000
php artisan queue:work
```

Les sous-domaines commerciaux fonctionnent via `lvh.me` (DNS wildcard → `127.0.0.1`) : `http://comtest01.lvh.me:8000/f/mon-tunnel`

Pour émuler le scheduler en local :

```bash
php artisan schedule:work
```

---

## Créer le premier compte admin

```bash
php artisan make:filament-user
```

---

## Structure des accès

| URL | Description |
|-----|-------------|
| `/admin` | Panel administrateur Filament |
| `/commercial` | Dashboard commercial |
| `/f/{slug}` | Tunnel public (sans sous-domaine) |
| `{subdomain}.royalleadpro.com/f/{slug}` | Tunnel via sous-domaine commercial |
