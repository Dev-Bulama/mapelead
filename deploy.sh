#!/bin/bash
# =============================================================================
# Mapelead — cPanel Deployment Script
# Run from inside the project directory:  bash deploy.sh
# =============================================================================
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$SCRIPT_DIR"

# Detect PHP binary
PHP_BIN="/opt/alt/php84/usr/bin/php"
[ -f "$PHP_BIN" ] || PHP_BIN=$(which php 2>/dev/null || echo "php")
echo "[deploy] Using PHP: $PHP_BIN ($("$PHP_BIN" -r 'echo PHP_VERSION;' 2>/dev/null || echo 'unknown'))"

echo ""
echo "════════════════════════════════════════════"
echo "  Mapelead Deployment"
echo "════════════════════════════════════════════"

# ── Step 1: Save .env and vendor BEFORE any git operations ───────────────────
PARENT_DIR="$(dirname "$SCRIPT_DIR")"
ENV_BACKUP="$PARENT_DIR/.env.mapelead"
VENDOR_BACKUP_FLAG="$PARENT_DIR/.vendor_ok"

if [ -f ".env" ]; then
    cp .env "$ENV_BACKUP"
    echo "✓ .env saved to $ENV_BACKUP"
fi

# ── Step 2: Pull latest code ──────────────────────────────────────────────────
BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "main")
echo "→ Pulling branch: $BRANCH"
git fetch origin
git reset --hard "origin/$BRANCH"
echo "✓ Code updated to $(git rev-parse --short HEAD)"

# ── Step 3: Restore .env ──────────────────────────────────────────────────────
if [ ! -f ".env" ]; then
    if [ -f "$ENV_BACKUP" ]; then
        cp "$ENV_BACKUP" .env
        echo "✓ .env restored from backup"
    else
        echo ""
        echo "════════════════════════════════════════════"
        echo "  ERROR: .env not found!"
        echo "  Copy your .env.example to .env and fill"
        echo "  in the production values, then re-run."
        echo "════════════════════════════════════════════"
        exit 1
    fi
fi

# ── Step 4: Composer install ──────────────────────────────────────────────────
COMPOSER_BIN=""
for candidate in "$HOME/composer.phar" "$(which composer 2>/dev/null)" "/usr/local/bin/composer"; do
    [ -f "$candidate" ] && COMPOSER_BIN="$candidate" && break
done

if [ -n "$COMPOSER_BIN" ]; then
    echo "→ Running composer install..."
    "$PHP_BIN" "$COMPOSER_BIN" install \
        --no-interaction --prefer-dist \
        --optimize-autoloader --no-dev \
        2>&1
    echo "✓ Vendor dependencies installed"
elif [ -d "vendor" ]; then
    echo "⚠ Composer not found — using existing vendor/ (may be outdated)"
else
    echo ""
    echo "════════════════════════════════════════════"
    echo "  ERROR: No composer binary and no vendor/"
    echo "  Upload vendor/ manually or install composer"
    echo "  from https://getcomposer.org/download/"
    echo "════════════════════════════════════════════"
    exit 1
fi

# ── Step 5: Storage symlink ───────────────────────────────────────────────────
"$PHP_BIN" artisan storage:link --force 2>/dev/null && echo "✓ Storage linked" || true

# ── Step 6: Permissions ───────────────────────────────────────────────────────
chmod -R 755 storage bootstrap/cache
echo "✓ Permissions set (755 on storage + bootstrap/cache)"

# ── Step 7: Run migrations ────────────────────────────────────────────────────
echo "→ Running migrations..."
"$PHP_BIN" artisan migrate --force 2>&1
echo "✓ Migrations complete"

# ── Step 8: Rebuild caches ────────────────────────────────────────────────────
echo "→ Rebuilding caches..."
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache
echo "✓ All caches rebuilt"

# ── Step 9: Restart queue workers (if any) ───────────────────────────────────
"$PHP_BIN" artisan queue:restart 2>/dev/null || true

echo ""
echo "════════════════════════════════════════════"
echo "  ✓ Deployment complete!"
echo "  Branch : $BRANCH"
echo "  Commit : $(git rev-parse --short HEAD)"
echo "════════════════════════════════════════════"
