#!/usr/bin/env bash
# =============================================================================
# Provision script — Ubuntu 24.04 LTS / 22.04 LTS for ozanefeoglu.com
# -----------------------------------------------------------------------------
# Idempotent. Run once on a fresh VPS as a sudo user (NOT root):
#     curl -fsSL https://raw.githubusercontent.com/.../provision.sh | bash
# Or:
#     scp deploy/provision.sh user@server:~/ && ssh user@server "bash provision.sh"
#
# What it does:
#   · Hardens SSH (key auth only, root login off)
#   · UFW firewall (22, 80, 443)
#   · PHP 8.3 + extensions
#   · Composer 2 + Node 22 + npm
#   · Redis (cache + session + queue)
#   · MySQL 8 (DB)
#   · Caddy 2 (web server + auto HTTPS)
#   · Laravel app skeleton + permissions + systemd queue worker
# =============================================================================

set -euo pipefail

DOMAIN="${DOMAIN:-ozanefeoglu.com}"
APP_USER="www-data"
APP_DIR="/var/www/${DOMAIN}"
DB_NAME="${DB_NAME:-ozanefeoglu_prod}"
DB_USER="${DB_USER:-ozan_app}"

log() { printf '\033[1;36m▸ %s\033[0m\n' "$*"; }
warn() { printf '\033[1;33m! %s\033[0m\n' "$*"; }
die() { printf '\033[1;31m✘ %s\033[0m\n' "$*"; exit 1; }

[[ $EUID -eq 0 ]] && die "Don't run as root. Use a sudo user."
sudo -v || die "Need sudo."

log "Updating apt cache + base packages"
sudo apt-get update -qq
sudo apt-get upgrade -y -qq
sudo apt-get install -y -qq curl wget git unzip software-properties-common ca-certificates gnupg lsb-release

# -----------------------------------------------------------------------------
log "Installing PHP 8.3 + extensions"
sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update -qq
sudo apt-get install -y -qq \
    php8.3 php8.3-fpm php8.3-cli php8.3-common \
    php8.3-mysql php8.3-pgsql php8.3-sqlite3 \
    php8.3-redis php8.3-curl php8.3-mbstring php8.3-zip \
    php8.3-gd php8.3-bcmath php8.3-intl php8.3-xml php8.3-soap

# OPcache + JIT — production sane defaults
sudo tee /etc/php/8.3/fpm/conf.d/99-prod.ini > /dev/null <<'INI'
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.jit_buffer_size=128M
opcache.jit=tracing
memory_limit=512M
upload_max_filesize=80M
post_max_size=100M
max_execution_time=120
INI
sudo systemctl restart php8.3-fpm

# -----------------------------------------------------------------------------
log "Installing Composer 2"
EXPECTED_CHECKSUM=$(curl -fsSL https://composer.github.io/installer.sig)
curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php
ACTUAL_CHECKSUM=$(php -r "echo hash_file('sha384', '/tmp/composer-setup.php');")
[[ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]] && die "Composer checksum mismatch"
sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer --quiet
rm /tmp/composer-setup.php

# -----------------------------------------------------------------------------
log "Installing Node 22 + npm"
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt-get install -y -qq nodejs

# -----------------------------------------------------------------------------
log "Installing Redis"
sudo apt-get install -y -qq redis-server
sudo sed -i 's/^# requirepass .*/requirepass CHANGE-ME-IN-ENV/' /etc/redis/redis.conf || true
sudo systemctl enable --now redis-server

# -----------------------------------------------------------------------------
log "Installing MySQL 8"
if ! command -v mysql >/dev/null; then
    sudo DEBIAN_FRONTEND=noninteractive apt-get install -y -qq mysql-server
fi
sudo systemctl enable --now mysql

warn "Run 'sudo mysql_secure_installation' AFTER provisioning."
warn "Then create DB + user manually:"
cat <<EOF

  sudo mysql -e "CREATE DATABASE ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  sudo mysql -e "CREATE USER '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '<STRONG_PASSWORD>';"
  sudo mysql -e "GRANT ALL ON ${DB_NAME}.* TO '${DB_USER}'@'127.0.0.1';"
  sudo mysql -e "FLUSH PRIVILEGES;"

EOF

# -----------------------------------------------------------------------------
log "Installing Caddy 2"
sudo apt-get install -y -qq debian-keyring debian-archive-keyring apt-transport-https
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-stable.list >/dev/null
sudo apt-get update -qq
sudo apt-get install -y -qq caddy

# -----------------------------------------------------------------------------
log "Configuring UFW firewall"
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow OpenSSH
sudo ufw allow http
sudo ufw allow https
yes | sudo ufw enable

# -----------------------------------------------------------------------------
log "Hardening SSH"
sudo sed -i 's/^#*PermitRootLogin.*/PermitRootLogin no/' /etc/ssh/sshd_config
sudo sed -i 's/^#*PasswordAuthentication.*/PasswordAuthentication no/' /etc/ssh/sshd_config
sudo systemctl reload ssh

# -----------------------------------------------------------------------------
log "Creating app directory + permissions"
sudo mkdir -p "${APP_DIR}"
sudo chown -R "${USER}:${APP_USER}" "${APP_DIR}"
sudo chmod -R 775 "${APP_DIR}"

cat <<EOF

═══════════════════════════════════════════════════════════════════
✅ Provisioning complete.

Next:
  1. Clone the repository:
       cd /var/www && sudo -u ${USER} git clone https://github.com/chvvasss/ozanefeogluwebsite.git ${DOMAIN}

  2. Configure Caddy:
       sudo cp ${APP_DIR}/deploy/Caddyfile /etc/caddy/Caddyfile
       sudo systemctl reload caddy

  3. Run deploy script:
       cd ${APP_DIR} && sudo -u ${USER} bash deploy/deploy.sh

  4. Install systemd queue worker:
       sudo cp ${APP_DIR}/deploy/laravel-queue.service /etc/systemd/system/
       sudo systemctl daemon-reload
       sudo systemctl enable --now laravel-queue

  5. Add cron entry for scheduler:
       sudo crontab -u ${APP_USER} -e
       Add:  * * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1

═══════════════════════════════════════════════════════════════════
EOF
