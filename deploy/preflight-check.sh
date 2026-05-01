#!/usr/bin/env bash
# =============================================================================
# Preflight checks — verify the codebase is production-ready BEFORE deploying.
# Run on dev machine: bash deploy/preflight-check.sh
# Exit code 0 = ready to ship. Non-zero = fix before deploying.
# =============================================================================

set -uo pipefail

CHECKS_PASSED=0
CHECKS_FAILED=0
CHECKS_WARNED=0

ok()   { CHECKS_PASSED=$((CHECKS_PASSED+1)); printf '\033[1;32m✓\033[0m  %s\n' "$*"; }
fail() { CHECKS_FAILED=$((CHECKS_FAILED+1)); printf '\033[1;31m✘\033[0m  %s\n' "$*"; }
warn() { CHECKS_WARNED=$((CHECKS_WARNED+1)); printf '\033[1;33m!\033[0m  %s\n' "$*"; }
section() { printf '\n\033[1m── %s ──\033[0m\n' "$*"; }

cd "$(dirname "$0")/.."

# -----------------------------------------------------------------------------
section "Repository state"
if git diff-index --quiet HEAD -- 2>/dev/null; then
    ok "Working tree clean (no uncommitted changes)"
else
    warn "Uncommitted changes present"
fi

current_branch=$(git rev-parse --abbrev-ref HEAD)
[[ "$current_branch" == "main" ]] && ok "On main branch" || warn "On '$current_branch' (deploy from main)"

unpushed=$(git log --oneline @{u}.. 2>/dev/null | wc -l || echo "0")
[[ "$unpushed" == "0" ]] && ok "All commits pushed to origin" || warn "$unpushed unpushed commits"

# -----------------------------------------------------------------------------
section "Dependencies + security"
if composer audit --no-dev --format=plain 2>&1 | grep -q "No security vulnerability"; then
    ok "composer audit: clean"
else
    fail "composer audit found vulnerabilities — run 'composer audit'"
fi

if npm audit --omit=dev --json 2>/dev/null | grep -q '"vulnerabilities":{"info":0,"low":0,"moderate":0,"high":0,"critical":0'; then
    ok "npm audit (prod): clean"
elif [[ $(npm audit --omit=dev --json 2>/dev/null | grep -oE '"total":[0-9]+' | head -1) == '"total":0' ]]; then
    ok "npm audit (prod): clean"
else
    warn "npm audit may report findings — check 'npm audit --omit=dev'"
fi

# -----------------------------------------------------------------------------
section "Build artifacts"
if [[ -f public/build/manifest.json ]]; then
    ok "Vite manifest present"
else
    fail "Missing public/build/manifest.json — run 'npm run build'"
fi

asset_count=$(find public/build/assets -type f 2>/dev/null | wc -l)
[[ "$asset_count" -gt 0 ]] && ok "$asset_count built asset(s) present" || fail "No built assets found"

# -----------------------------------------------------------------------------
section "Test suite"
if php artisan test --parallel 2>&1 | grep -q "Tests:.*passed"; then
    summary=$(php artisan test --parallel 2>&1 | grep -E "Tests:" | head -1 | tr -s ' ')
    ok "Pest:$summary"
else
    fail "Pest test suite failed — run 'php artisan test'"
fi

# -----------------------------------------------------------------------------
section "Configuration"
if [[ -f .env ]]; then
    ok ".env present"
    if grep -q "^APP_DEBUG=true" .env; then
        warn "APP_DEBUG=true (production must be false)"
    fi
    if grep -q "^APP_ENV=local" .env; then
        warn "APP_ENV=local (production should be 'production')"
    fi
fi

[[ -f deploy/.env.production.template ]] && ok "Production env template ready"
[[ -f deploy/Caddyfile ]] && ok "Caddyfile ready"
[[ -f deploy/nginx.conf.example ]] && ok "Nginx config example ready"
[[ -f deploy/laravel-queue.service ]] && ok "Queue worker systemd unit ready"
[[ -f deploy/deploy.sh ]] && ok "Deploy script ready"
[[ -f deploy/provision.sh ]] && ok "Provision script ready"

# -----------------------------------------------------------------------------
section "Brand assets"
required_brand=(
    "public/favicon.svg"
    "public/branding/logo-mark.svg"
    "public/branding/logo-mark-inverse.svg"
    "public/branding/logo-wordmark.svg"
    "public/branding/logo-horizontal.svg"
    "public/branding/logo-horizontal-inverse.svg"
    "public/branding/apple-touch-icon.svg"
    "public/branding/og-mark.svg"
    "public/site.webmanifest"
)
for f in "${required_brand[@]}"; do
    [[ -f "$f" ]] && ok "$f" || fail "$f missing"
done

# -----------------------------------------------------------------------------
section "Documentation"
[[ -f docs/DEPLOY_CHECKLIST.md ]] && ok "DEPLOY_CHECKLIST.md present"
[[ -f docs/branding/LOGO_PHILOSOPHY.md ]] && ok "LOGO_PHILOSOPHY.md present"
[[ -f docs/decisions ]] || [[ -d docs/decisions ]] && ok "ADR directory present"

# -----------------------------------------------------------------------------
echo ""
printf '\033[1m═══════════════════════════════════════════════\033[0m\n'
printf '\033[1m  Preflight Summary\033[0m\n'
printf '\033[1m═══════════════════════════════════════════════\033[0m\n'
printf '  \033[1;32m✓\033[0m Passed:  %d\n' "$CHECKS_PASSED"
printf '  \033[1;33m!\033[0m Warned:  %d\n' "$CHECKS_WARNED"
printf '  \033[1;31m✘\033[0m Failed:  %d\n' "$CHECKS_FAILED"
echo ""

if [[ "$CHECKS_FAILED" -gt 0 ]]; then
    printf '\033[1;31m✘ NOT READY for production. Fix failures above.\033[0m\n'
    exit 1
fi

if [[ "$CHECKS_WARNED" -gt 0 ]]; then
    printf '\033[1;33m! Ready, but %d warning(s) — review before deploying.\033[0m\n' "$CHECKS_WARNED"
    exit 0
fi

printf '\033[1;32m✓ READY TO SHIP.\033[0m\n'
exit 0
