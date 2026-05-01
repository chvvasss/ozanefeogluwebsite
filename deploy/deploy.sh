#!/usr/bin/env bash
# =============================================================================
# Deploy script — ozanefeoglu.com
# -----------------------------------------------------------------------------
# Idempotent. Safe to run on every push to main.
#   cd /var/www/ozanefeoglu.com && bash deploy/deploy.sh
#
# Steps:
#   1. Pull latest code
#   2. Composer install (no-dev, optimized)
#   3. NPM ci + build assets
#   4. Migrate DB (force, safe with backups in place)
#   5. Cache config / route / view / event
#   6. Refresh storage symlink
#   7. Restart queue + reload caddy/php-fpm
#   8. Smoke test
# =============================================================================

set -euo pipefail

APP_DIR="${APP_DIR:-$(pwd)}"
APP_URL="${APP_URL:-https://ozanefeoglu.com}"

log()  { printf '\033[1;36m▸ %s\033[0m\n' "$*"; }
warn() { printf '\033[1;33m! %s\033[0m\n' "$*"; }
ok()   { printf '\033[1;32m✓ %s\033[0m\n' "$*"; }
die()  { printf '\033[1;31m✘ %s\033[0m\n' "$*"; exit 1; }

cd "$APP_DIR"
[[ -f .env ]] || die ".env not found in $APP_DIR — copy from deploy/.env.production.template"
[[ -f artisan ]] || die "Not in a Laravel project root"

# -----------------------------------------------------------------------------
log "1/8 · Pulling latest code"
git fetch origin main
git reset --hard origin/main

# -----------------------------------------------------------------------------
log "2/8 · Composer install (no-dev, optimized)"
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# -----------------------------------------------------------------------------
log "3/8 · NPM ci + production build"
npm ci --omit=dev --no-audit --no-fund
npm run build

# -----------------------------------------------------------------------------
log "4/8 · Database migrations (force)"
php artisan migrate --force

# Initial seed only on fresh DB — guard against re-seeding existing data
if php artisan tinker --execute='echo App\Models\User::count();' 2>/dev/null | grep -q '^0$'; then
    log "  • Empty DB detected — running initial seeders"
    php artisan db:seed --class=RoleSeeder --force
    php artisan db:seed --class=SuperAdminSeeder --force
    php artisan db:seed --class=SettingSeeder --force
    php artisan db:seed --class=PageSeeder --force
    php artisan db:seed --class=LegalPageSeeder --force
    php artisan db:seed --class=PublicationSeeder --force
fi

# -----------------------------------------------------------------------------
log "5/8 · Caching config / routes / views / events"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# -----------------------------------------------------------------------------
log "6/8 · Refreshing storage symlink"
php artisan storage:link 2>/dev/null || true

# -----------------------------------------------------------------------------
log "7/8 · Restarting queue + reloading PHP-FPM"
php artisan queue:restart
sudo systemctl reload php8.3-fpm 2>/dev/null || warn "(skipped php-fpm reload — no sudo)"
sudo systemctl reload caddy 2>/dev/null || warn "(skipped caddy reload — no sudo)"

# Permissions sanity (storage + bootstrap/cache writable by web user)
sudo chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
sudo chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# -----------------------------------------------------------------------------
log "8/8 · Smoke test"
sleep 2
status=$(curl -s -o /dev/null -w "%{http_code}" "${APP_URL}/" || echo "000")
[[ "$status" == "200" ]] || die "Smoke test FAILED — homepage returned ${status}"

health=$(curl -s -o /dev/null -w "%{http_code}" "${APP_URL}/up" || echo "000")
[[ "$health" == "200" ]] || warn "Health endpoint returned ${health} (check /up route)"

ok "Deploy successful · homepage 200 · health ${health}"
