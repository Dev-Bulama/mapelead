#!/bin/bash
# =============================================================================
# Mapelead — Production Deployment Script
# Usage:  bash deploy.sh
# =============================================================================
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$SCRIPT_DIR"
BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "main")

# ── Detect PHP ──────────────────────────────────────────────────────────────
PHP=""
for p in "/opt/alt/php84/usr/bin/php" "/opt/alt/php83/usr/bin/php" "$(which php 2>/dev/null)"; do
    [ -f "$p" ] || [ -x "$p" ] && PHP="$p" && break
done
[ -z "$PHP" ] && { echo "✗ PHP not found"; exit 1; }
PHP_VER=$("$PHP" -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null)
echo "→ PHP $PHP_VER at $PHP"

# ── Detect Composer ─────────────────────────────────────────────────────────
COMPOSER=""
for c in "$HOME/composer.phar" "$HOME/bin/composer" "$(which composer 2>/dev/null)" "/usr/local/bin/composer"; do
    [ -f "$c" ] && COMPOSER="$c" && break
done

PARENT="$(dirname "$SCRIPT_DIR")"

echo ""
echo "════════════════════════════════════════════"
echo "  Mapelead Deployment  ·  branch: $BRANCH"
echo "════════════════════════════════════════════"

# ── Step 1: Save .env before any git operation ───────────────────────────────
if [ -f ".env" ]; then
    cp .env "$PARENT/.env.mapelead"
    echo "✓ .env saved"
fi

# ── Step 2: Maintenance mode ON ──────────────────────────────────────────────
"$PHP" artisan down --retry=10 2>/dev/null && echo "✓ Maintenance mode ON" || true

# ── Step 3: CLEAR ALL CACHES before pulling (prevents stale-cache 500s) ─────
"$PHP" artisan optimize:clear 2>/dev/null && echo "✓ All caches cleared" || {
    # If artisan itself is broken, clear manually
    rm -f bootstrap/cache/*.php
    rm -f storage/framework/cache/data/*.php 2>/dev/null || true
    find storage/framework/views -name "*.php" -delete 2>/dev/null || true
    echo "✓ Caches cleared manually"
}

# ── Step 4: Pull latest code ──────────────────────────────────────────────────
echo "→ Pulling $BRANCH..."
git fetch origin
git reset --hard "origin/$BRANCH"
echo "✓ Code at $(git rev-parse --short HEAD)"

# ── Step 5: Restore .env ─────────────────────────────────────────────────────
if [ ! -f ".env" ]; then
    if [ -f "$PARENT/.env.mapelead" ]; then
        cp "$PARENT/.env.mapelead" .env
        echo "✓ .env restored"
    else
        "$PHP" artisan up 2>/dev/null || true
        echo "✗ ERROR: No .env found. Copy .env.example to .env and configure it."
        exit 1
    fi
fi

# ── Step 6: Install/update Composer dependencies ─────────────────────────────
if [ -n "$COMPOSER" ]; then
    echo "→ composer install..."
    "$PHP" "$COMPOSER" install \
        --no-interaction --prefer-dist \
        --optimize-autoloader --no-dev 2>&1 | tail -3
    echo "✓ Vendor dependencies up to date"
elif [ -d "vendor" ]; then
    echo "⚠ Composer not found — using existing vendor/ (may be outdated)"
else
    "$PHP" artisan up 2>/dev/null || true
    echo "✗ ERROR: No composer and no vendor/. Upload vendor/ or install composer."
    exit 1
fi

# ── Step 7: Storage symlink ───────────────────────────────────────────────────
"$PHP" artisan storage:link --force 2>/dev/null && echo "✓ Storage symlink OK" || true

# ── Step 8: Permissions ───────────────────────────────────────────────────────
chmod -R 755 storage bootstrap/cache 2>/dev/null && echo "✓ Permissions set" || true

# ── Step 9: Run migrations ────────────────────────────────────────────────────
echo "→ Running migrations..."
"$PHP" artisan migrate --force 2>&1
echo "✓ Migrations complete"

# ── Step 10: Rebuild all caches ──────────────────────────────────────────────
echo "→ Rebuilding caches..."
"$PHP" artisan config:cache
"$PHP" artisan route:cache
"$PHP" artisan view:cache
"$PHP" artisan event:cache 2>/dev/null || true
echo "✓ Caches rebuilt"

# ── Step 11: Queue restart ───────────────────────────────────────────────────
"$PHP" artisan queue:restart 2>/dev/null || true

# ── Step 12: Health check ────────────────────────────────────────────────────
echo ""
echo "→ Running health check..."
if "$PHP" artisan app:health 2>/dev/null; then
    HEALTH_OK=true
else
    HEALTH_OK=false
    echo "⚠ Health check warnings — review above before taking app live"
fi

# ── Step 13: Maintenance mode OFF ────────────────────────────────────────────
"$PHP" artisan up && echo "✓ App is live"

# ── Done ─────────────────────────────────────────────────────────────────────
echo ""
echo "════════════════════════════════════════════"
echo "  ✓ Deployed: $(git log -1 --format='%s')"
echo "  Commit : $(git rev-parse --short HEAD)"
echo "  Branch : $BRANCH"
[ "$HEALTH_OK" = false ] && echo "  ⚠ Review health check warnings above"
echo "════════════════════════════════════════════"
