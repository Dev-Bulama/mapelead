# MapeLearn — cPanel Deployment Guide

A beginner-friendly, step-by-step guide to deploying MapeLearn on a shared cPanel hosting account (e.g., Namecheap, Hostinger, A2Hosting).

---

## Prerequisites

- cPanel hosting with PHP 8.2+ support
- MySQL database
- SSH access (strongly recommended) or cPanel File Manager
- Your domain pointed to the hosting (e.g., `mapelead.org`)
- Composer installed (or use the cPanel composer tool)

---

## Step 1 — Upload Your Files

### Option A: Git Clone via SSH (Recommended)
```bash
ssh your_user@your_server.com
cd public_html
git clone https://github.com/your-username/mapelead.git .
```

### Option B: cPanel File Manager
1. Zip your project locally (exclude `node_modules`, `.git`, `vendor`)
2. Upload to `public_html` via File Manager
3. Extract the zip

> **Important:** The Laravel `public/` folder contents go into `public_html/`, and the rest of the project goes one level **above** `public_html/` for security.

### Recommended Folder Structure on cPanel:
```
/home/your_cpanel_user/
├── mapelead/          ← your Laravel app (NOT inside public_html)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   └── ...
└── public_html/       ← ONLY contains the contents of Laravel's public/ folder
    ├── index.php      ← modified (see Step 4)
    ├── .htaccess
    └── ...
```

---

## Step 2 — Create MySQL Database

1. Log in to cPanel → **MySQL Databases**
2. Create a new database (e.g., `youraccount_mapelead`)
3. Create a database user with a strong password
4. Add the user to the database with **All Privileges**
5. Note: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

---

## Step 3 — Configure .env

```bash
# SSH into your server
cd ~/mapelead
cp .env.production.example .env

# Edit the file
nano .env
```

Fill in at minimum:
```env
APP_KEY=          # Will be generated in step 5
APP_URL=https://mapelead.org
DB_CONNECTION=mysql
DB_DATABASE=youraccount_mapelead
DB_USERNAME=youraccount_dbuser
DB_PASSWORD=YourStrongPassword
MAIL_MAILER=smtp
# ... fill in all other values
```

---

## Step 4 — Adjust public/index.php Paths

Copy the contents of `public/` to `public_html/`, then open `public_html/index.php` and update the paths:

```php
// Change these two lines:
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// To point to your Laravel root:
require __DIR__.'/../mapelead/vendor/autoload.php';
$app = require_once __DIR__.'/../mapelead/bootstrap/app.php';
```

Also copy `public/.htaccess` to `public_html/.htaccess`.

---

## Step 5 — Install Dependencies & Run Setup

```bash
cd ~/mapelead

# Install PHP packages
composer install --no-dev --optimize-autoloader

# Generate app encryption key
php artisan key:generate

# Run database migrations
php artisan migrate --force

# Seed initial data (roles, permissions, settings, admin user)
php artisan db:seed --force

# Create the storage symlink
php artisan storage:link

# Cache everything for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## Step 6 — Set File Permissions

```bash
cd ~/mapelead
chmod -R 775 storage bootstrap/cache
find storage -type f -exec chmod 664 {} \;
```

---

## Step 7 — Set Up Cron Job (Required for Queues & Scheduled Tasks)

In cPanel → **Cron Jobs**, add this cron job (every minute):

```
* * * * * /usr/local/bin/php /home/your_cpanel_user/mapelead/artisan schedule:run >> /dev/null 2>&1
```

This runs Laravel's task scheduler which handles:
- Queue processing (email notifications, certificate generation)
- Any scheduled cleanup tasks

> If your host doesn't support `php artisan queue:work` as a background process (most shared hosts don't), the `schedule:run` approach with a cron will process queued jobs every minute automatically.

---

## Step 8 — Configure SSL

1. In cPanel → **SSL/TLS** → **AutoSSL** (Let's Encrypt) — click "Run AutoSSL"
2. Or install your own SSL certificate
3. Force HTTPS via `.htaccess` in `public_html/`:

```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## Step 9 — Configure Mail

Use one of these options:

### Option A: Mailgun (Recommended for production)
1. Create account at mailgun.com
2. Verify your domain
3. Set in .env:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@yourdomain.mailgun.org
MAIL_PASSWORD=your-mailgun-smtp-password
MAIL_ENCRYPTION=tls
```

### Option B: cPanel SMTP
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.mapelead.org
MAIL_PORT=587
MAIL_USERNAME=noreply@mapelead.org
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
```

---

## Step 10 — Post-Deployment Checklist

- [ ] Site loads at `https://mapelead.org`
- [ ] Admin login works: `admin@mapelead.org` / `Admin@123456` (change this immediately!)
- [ ] Google OAuth redirect URI set to `https://mapelead.org/auth/google/callback` in Google Cloud Console
- [ ] Paystack webhook URL configured: `https://mapelead.org/enroll/callback/{reference}`
- [ ] Storage symlink working (profile photos, thumbnails load)
- [ ] Contact form sends emails
- [ ] SSL certificate active (padlock in browser)
- [ ] `APP_DEBUG=false` in .env
- [ ] Change all default passwords immediately
- [ ] Set up regular database backups in cPanel

---

## Common Issues

**500 Error after deploy:**
```bash
php artisan config:clear && php artisan cache:clear
# Check storage permissions:
chmod -R 775 storage bootstrap/cache
```

**Migrations fail:**
```bash
# Check DB credentials in .env
php artisan migrate:status
```

**Images not loading:**
```bash
php artisan storage:link
# If symlink exists but broken, delete and re-run
```

**Queue jobs not running:**
```bash
# Manually process:
php artisan queue:work --once
# Check cron job is set up correctly
```

---

## Performance Tips

1. **PHP OPcache** — Enable in cPanel PHP settings (huge speed boost)
2. **Redis** — If your host offers Redis, switch `CACHE_STORE=redis` and `SESSION_DRIVER=redis` in .env
3. **CDN** — Use Cloudflare (free) for static assets and global CDN
4. **Database indexes** — All critical queries are indexed in the migrations
5. **Image optimization** — Thumbnails are stored at upload time; use WebP format

---

*Deployment guide for MapeLearn v1.0 — Built with Laravel 13*
