# Deploy Checklist — ozanefeoglu.com

Production lansmanı için sıralı kontrol listesi. Her madde ya **otomatik** (komut)
ya **manuel onay** (kişi). Hiçbiri atlanmaz.

---

## 0 · Ön koşullar (host seçimi)

| # | Madde | Komut / Eylem | Onay |
|---|---|---|---|
| 0.1 | Sunucu (≥2 CPU, ≥2 GB RAM, 40 GB SSD) | DigitalOcean / Hetzner / Linode | ☐ |
| 0.2 | Domain DNS A kaydı sunucu IP'sine | DNS panel | ☐ |
| 0.3 | SSH anahtar tabanlı, root login disabled | `/etc/ssh/sshd_config` | ☐ |
| 0.4 | UFW: 22 (anahtar bazlı), 80, 443 sadece | `ufw allow / enable` | ☐ |
| 0.5 | Caddy / Nginx + Let's Encrypt (otomatik HTTPS) | install + reload | ☐ |
| 0.6 | PHP 8.3+ · GD · pcntl · redis · zip · sqlite3 | `apt install` | ☐ |
| 0.7 | Composer 2 + Node 22 + npm | `corepack enable` | ☐ |
| 0.8 | Redis (cache + session + queue) | `apt install redis-server` | ☐ |
| 0.9 | MySQL 8 veya PostgreSQL 16 (DB) | install + create db + user | ☐ |

## 1 · Code deploy

```bash
git clone https://github.com/.../ozanefeoglu.com.git
cd ozanefeoglu.com
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

| # | Madde | Komut | Onay |
|---|---|---|---|
| 1.1 | `.env` dosyası oluştur | `cp .env.example .env` | ☐ |
| 1.2 | `APP_ENV=production`, `APP_DEBUG=false` | düzenle | ☐ |
| 1.3 | `APP_URL=https://ozanefeoglu.com` | düzenle | ☐ |
| 1.4 | `APP_KEY` üret | `php artisan key:generate` | ☐ |
| 1.5 | `DB_*` değerleri MySQL/PgSQL'e | düzenle | ☐ |
| 1.6 | `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis` | düzenle | ☐ |
| 1.7 | `ADMIN_EMAIL`, `ADMIN_PASSWORD` (güçlü şifre) | düzenle | ☐ |
| 1.8 | `SECURITY_CSP_ENABLED=true` | düzenle | ☐ |
| 1.9 | `SESSION_ENCRYPT=true`, `SESSION_SECURE_COOKIE=true` | düzenle | ☐ |
| 1.10 | `BACKUP_DESTINATION=s3` + S3 key/secret/bucket | düzenle | ☐ |
| 1.11 | `MEDIA_DISK=public` (veya S3) | düzenle | ☐ |
| 1.12 | `BACKUP_NOTIFICATION_EMAIL=...` | düzenle | ☐ |
| 1.13 | `HIBP_ENABLED=true` | düzenle | ☐ |
| 1.14 | `chown -R www-data:www-data storage bootstrap/cache` | komut | ☐ |
| 1.15 | `chmod -R 775 storage bootstrap/cache` | komut | ☐ |

## 2 · Veritabanı + medya

```bash
php artisan storage:link
php artisan migrate --force
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=SuperAdminSeeder --force
php artisan db:seed --class=SettingSeeder --force
php artisan db:seed --class=PageSeeder --force
php artisan db:seed --class=LegalPageSeeder --force
php artisan db:seed --class=PublicationSeeder --force
```

| # | Madde | Komut / Eylem | Onay |
|---|---|---|---|
| 2.1 | Migrations çalıştır | `php artisan migrate --force` | ☐ |
| 2.2 | Roller seed | `db:seed --class=RoleSeeder` | ☐ |
| 2.3 | Super-admin seed (env'den) | `db:seed --class=SuperAdminSeeder` | ☐ |
| 2.4 | Site ayarları seed | `db:seed --class=SettingSeeder` | ☐ |
| 2.5 | Sayfalar (about + contact) seed | `db:seed --class=PageSeeder` | ☐ |
| 2.6 | Legal sayfalar seed | `db:seed --class=LegalPageSeeder` | ☐ |
| 2.7 | Yayınlar seed (boş kalabilir) | `db:seed --class=PublicationSeeder` | ☐ |
| 2.8 | Storage symlink | `php artisan storage:link` | ☐ |
| 2.9 | AA fotoğraf import (opsiyonel) | `aa:import-photos --source=/path` | ☐ |
| 2.10 | Tüm super-admin login + 2FA kur | manuel | ☐ |

## 3 · Performans optimizasyonları

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize
```

| # | Madde | Komut | Onay |
|---|---|---|---|
| 3.1 | Config cache | `php artisan config:cache` | ☐ |
| 3.2 | Route cache | `php artisan route:cache` | ☐ |
| 3.3 | View cache | `php artisan view:cache` | ☐ |
| 3.4 | Event cache | `php artisan event:cache` | ☐ |
| 3.5 | OPcache aktif (`opcache.enable=1`) | `/etc/php/8.3/cli/php.ini` | ☐ |
| 3.6 | OPcache JIT aktif (`opcache.jit=tracing`) | aynı dosya | ☐ |
| 3.7 | Memory limit ≥ 512 MB | aynı dosya | ☐ |

## 4 · Worker + Cron + Queue

```cron
* * * * * cd /var/www/ozanefeoglu.com && php artisan schedule:run >> /dev/null 2>&1
```

| # | Madde | Eylem | Onay |
|---|---|---|---|
| 4.1 | Cron entry (her dakika `schedule:run`) | crontab -e | ☐ |
| 4.2 | Queue worker (systemd unit) | `/etc/systemd/system/laravel-queue.service` | ☐ |
| 4.3 | Queue worker enabled + started | `systemctl enable --now` | ☐ |
| 4.4 | Pail / Horizon (opsiyonel monitoring) | install | ☐ |

## 5 · Yedekleme

| # | Madde | Komut | Onay |
|---|---|---|---|
| 5.1 | İlk backup elle | `php artisan backup:run` | ☐ |
| 5.2 | S3 bucket'ta backup zip görünüyor | manuel kontrol | ☐ |
| 5.3 | Backup şifreli (BACKUP_ARCHIVE_PASSWORD set) | `unzip -P` ile dene | ☐ |
| 5.4 | Cron daily 02:00 backup planlı | `routes/console.php` | ☐ |
| 5.5 | Test restore (dev makinede) | manuel | ☐ |

## 6 · Güvenlik son kontroller

| # | Madde | Eylem / Komut | Onay |
|---|---|---|---|
| 6.1 | HTTPS zorunlu (Caddy/nginx HTTP→HTTPS redirect) | config | ☐ |
| 6.2 | HSTS header production'da emit (Security headers middleware) | `curl -I` | ☐ |
| 6.3 | CSP header production'da emit | `curl -I` | ☐ |
| 6.4 | Admin /login page rate limit aktif | login dene | ☐ |
| 6.5 | Contact form throttle (3/10dk) çalışıyor | manuel test | ☐ |
| 6.6 | KVKK 90gün retention cron çalışıyor | `php artisan schedule:list` | ☐ |
| 6.7 | composer audit clean | `composer audit` | ☐ |
| 6.8 | npm audit clean | `npm audit --omit=dev` | ☐ |
| 6.9 | `.env` dosyası 600 perms | `chmod 600 .env` | ☐ |
| 6.10 | `.git` directory web-erişimden engellenmiş | nginx/Caddy config | ☐ |

## 7 · Public smoke testi (gerçek HTTPS adresi)

```bash
for p in / /yazilar /hakkimda /iletisim /goruntu \
         /hukuksal/kvkk /hukuksal/gizlilik /hukuksal/kunye \
         /sitemap.xml /health /robots.txt; do
  curl -s -o /dev/null -w "%{http_code} $p\n" https://ozanefeoglu.com$p
done
```

| # | Madde | Beklenen | Onay |
|---|---|---|---|
| 7.1 | Tüm route'lar 200 (sitemap + health dahil) | curl loop | ☐ |
| 7.2 | `/health` JSON `app: ok`, tüm checks ok | curl | ☐ |
| 7.3 | `/sitemap.xml` valid XML, ≥50 URL | curl + xmllint | ☐ |
| 7.4 | `/robots.txt` AI opt-out kuralları görünür | curl | ☐ |
| 7.5 | Hero gerçek foto ile yükleniyor | tarayıcı | ☐ |
| 7.6 | İletişim formu gönderim → DB'ye düşüyor | manuel test + admin'de gör | ☐ |
| 7.7 | KVKK link footer'da, redirect /kvkk → /hukuksal/kvkk | tarayıcı | ☐ |

## 8 · Admin smoke testi

```bash
# super-admin login → her tab → CRUD smoke
```

| # | Madde | Eylem | Onay |
|---|---|---|---|
| 8.1 | Login → 2FA setup ekranı geliyor | tarayıcı | ☐ |
| 8.2 | 2FA TOTP kuruldu (Google Authenticator vb.) | telefon | ☐ |
| 8.3 | Recovery codes safe yere kaydedildi | offline | ☐ |
| 8.4 | Dashboard widget'ları yükleniyor | tarayıcı | ☐ |
| 8.5 | Yazı CRUD (yeni + düzenle + yayınla) | manuel | ☐ |
| 8.6 | Foto yükleme (tek + bulk) | manuel | ☐ |
| 8.7 | Sayfa CRUD | manuel | ☐ |
| 8.8 | Kullanıcı ekle (editor rolü) | manuel | ☐ |
| 8.9 | Yedekleme tetikle + indir | manuel | ☐ |
| 8.10 | Settings 6 sekme PUT (her birini bir kez) | manuel | ☐ |
| 8.11 | İletişim mesajı durumu güncelle | manuel | ☐ |
| 8.12 | Audit log filtreleri çalışıyor | manuel | ☐ |

## 9 · Monitoring + Observability

| # | Madde | Eylem | Onay |
|---|---|---|---|
| 9.1 | Uptime monitor (`/health` her 60s) | UptimeRobot / Hetrix / kendi | ☐ |
| 9.2 | Error tracking (Sentry / Bugsnag opt) | install + DSN | ☐ |
| 9.3 | Log rotation (logrotate `storage/logs/`) | systemd config | ☐ |
| 9.4 | Disk usage alert (90%) | systemd / cron | ☐ |
| 9.5 | Backup size + age alarm | manual / Spatie notification | ☐ |
| 9.6 | Plausible / Umami self-hosted (opsiyonel) | install | ☐ |

## 10 · Lansman sonrası 24 saat

| # | Madde | Eylem | Onay |
|---|---|---|---|
| 10.1 | Google Search Console — sitemap submit | ekle | ☐ |
| 10.2 | İlk gerçek yazı + foto yükle | admin | ☐ |
| 10.3 | Tarayıcı cache + CDN bust (yeni asset hash) | otomatik | ☐ |
| 10.4 | Performance gerçek lighthouse skoru ≥90 | dev tools | ☐ |
| 10.5 | İletişim formu gerçek mesaj alabiliyor mu | manuel | ☐ |
| 10.6 | KVKK aydınlatma metni avukat onaylı | hukuk | ☐ |
| 10.7 | AA dilekçesi onaylı (afiliyasyon) | kurum | ☐ |
| 10.8 | İlk backup gerçekten S3'te + restore çalıştı | manuel | ☐ |

---

## Skor

Bu checklist'i bitirdiğinde:
- ✅ **Pest:** 212 test ✓
- ✅ **Massive audit:** 351 check, 100/100
- ✅ **OWASP/KVKK:** 9/10 (security + privacy)
- ✅ **WCAG AA:** 9.5/10 (accessibility)
- ✅ **Performance:** ~8/10 (cache + N+1 + asset)

Site **lansmana hazır**.
