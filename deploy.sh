#!/bin/bash
# ═══════════════════════════════════════════════════════════════════════
#  MapeLearn — Deployment Script (run from project root)
#  Usage: bash deploy.sh
# ═══════════════════════════════════════════════════════════════════════

set -e  # Exit on any error

echo "🚀 MapeLearn Deployment Script"
echo "==============================="

# 1. Pull latest code
echo "📥 Pulling latest code..."
git pull origin main

# 2. Install/update PHP dependencies (no dev packages in production)
echo "📦 Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 3. Clear all caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Run database migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# 5. Rebuild caches for production performance
echo "⚡ Building production caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Create storage symlink (if not exists)
echo "🔗 Linking storage..."
php artisan storage:link 2>/dev/null || true

# 7. Set correct file permissions
echo "🔐 Setting permissions..."
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

# 8. Restart queue workers (if supervisor/cron is set up)
echo "⚙️  Restarting queue..."
php artisan queue:restart

echo ""
echo "✅ Deployment complete!"
echo ""
echo "Next steps if this is a fresh deployment:"
echo "  1. php artisan key:generate      (if no APP_KEY in .env)"
echo "  2. php artisan db:seed           (if you want seed data)"
echo "  3. Set up cron job (see DEPLOYMENT.md)"
