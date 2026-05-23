#!/bin/bash
# Mapelead Production Deployment Script
# Run from project root: bash deploy.sh

set -e

echo "=== Mapelead Deployment ==="

# Backup .env if it exists
if [ -f ".env" ]; then
    cp .env .env.backup
    echo "✓ .env backed up"
fi

# Pull latest code
git fetch origin
git reset --hard origin/$(git rev-parse --abbrev-ref HEAD)
echo "✓ Code updated"

# Restore .env (git reset may have removed it if somehow tracked)
if [ -f ".env.backup" ] && [ ! -f ".env" ]; then
    cp .env.backup .env
    echo "✓ .env restored"
fi

# Install/update composer dependencies (preserve vendor if network fails)
if command -v composer &> /dev/null; then
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
    echo "✓ Composer dependencies updated"
elif [ -d "vendor" ]; then
    echo "⚠ Composer not found, using existing vendor/"
else
    echo "✗ ERROR: No composer and no vendor directory. Deployment cannot proceed."
    exit 1
fi

# Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo "✓ Caches rebuilt"

# Run migrations (safe - only runs new ones)
php artisan migrate --force
echo "✓ Migrations ran"

# Create storage symlink (if not exists)
php artisan storage:link 2>/dev/null || true
echo "✓ Storage linked"

# Restart queue workers (if supervisor is set up)
php artisan queue:restart 2>/dev/null || true

# Set proper permissions
chmod -R 755 storage bootstrap/cache
echo "✓ Permissions set"

echo "=== Deployment Complete ==="
