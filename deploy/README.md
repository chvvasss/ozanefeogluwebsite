# Deploy Rehberi — ozanefeoglu.com

Üretim sürümünü dünyaya açma adımları. Önce **hosting kararını ver**, sonra ona göre ilerle.

---

## 0 · Karar matrisi — Nereye deploy?

| Seçenek | Aylık | Kurulum süresi | Teknik yük | Ne zaman seçilmeli |
|---|---|---|---|---|
| **A · Laravel Forge + Hetzner CPX21** | $12 + €6 ≈ **€16/ay** | 30 dk | Düşük | Tek sahip, az teknik dert, en pratik |
| **B · Manuel VPS (Hetzner CPX21)** | **€6/ay** | 2-3 saat | Orta | Bütçe sıkı, terminal sevenler |
| **C · Laravel Vapor (AWS)** | **€40-100/ay** | 1 saat | Düşük | Trafik patlaması beklentisi varsa |
| **D · DigitalOcean App Platform** | **$15/ay+** | 45 dk | Düşük | DO ekosistemi tercih ediliyorsa |
| **E · Shared hosting (Plesk/cPanel)** | €3-5/ay | Değişken | Yüksek | Önerilmez (queue + Redis sınırı) |

**Önerim: A (Laravel Forge + Hetzner)**
- Forge git push'la otomatik deploy yapar
- Tek tıkla SSL (Let's Encrypt)
- Built-in Redis, queue worker, scheduler yönetimi
- DEPLOY_CHECKLIST'in 80 maddesinin yarısını otomatik çözer
- Bizim deploy/ klasöründeki script'ler buna paralel çalışır

İkinci tercih: B (Manuel VPS) — bütçe önceliği varsa. `provision.sh` + `deploy.sh` çıplak Ubuntu sunucuyu çalıştırır.

---

## 1 · Önkoşullar (her seçenek için ortak)

- [ ] **Domain** — `ozanefeoglu.com` ile registrar (GoDaddy / Namecheap / vs.)
- [ ] **DNS yöneticisi** — önerilen: Cloudflare (free plan, DNS + DDoS + analytics)
- [ ] **Object storage** — fotoğraflar için. Önerilen: **Cloudflare R2** (zero egress, ~$0.015/GB)
- [ ] **SMTP provider** — şifre sıfırlama mailleri için. Seçenekler:
  - **Brevo** (free plan: 300 mail/gün)
  - **Postmark** ($10/ay, transactional, en güvenilir)
  - **Mailgun** ($35/ay)
- [ ] **Sentry hesabı** (opsiyonel ama önerilir, free plan: 5K event/ay)
- [ ] **Google reCAPTCHA Enterprise** — iletişim formu için (free quota yeterli)

---

## 2 · Deploy klasörü içeriği

```
deploy/
├── README.md                        ← bu dosya
├── .env.production.template         ← prod env iskeleti (secrets ile doldurulacak)
├── Caddyfile                        ← Caddy 2 config (önerilen web server)
├── nginx.conf.example               ← Nginx alternatifi
├── laravel-queue.service            ← systemd queue worker unit
├── provision.sh                     ← Ubuntu 24 sıfırdan kurulum (B seçeneği için)
├── deploy.sh                        ← her deploy'da çalışan zero-downtime script
└── preflight-check.sh               ← deploy ÖNCESİ codebase audit
```

---

## 3 · Adım-adım — A seçeneği (Laravel Forge)

### 3.1 Sunucu oluştur
1. https://forge.laravel.com → giriş yap
2. **Create Server** → DigitalOcean / Hetzner / Linode (Hetzner önerilir, Avrupa hızlı)
3. Server tipi: **CX22** (€4.5/ay) veya **CPX21** (€6.5/ay, daha hızlı CPU)
4. Region: **Nuremberg / Helsinki** (en yakın TR'ye)
5. PHP 8.3 (default ✓), MySQL 8 (default ✓), Redis (✓ ekle)

### 3.2 Site ekle
1. Forge → Sites → **New Site**
2. Root domain: `ozanefeoglu.com`
3. Aliases: `www.ozanefeoglu.com`
4. PHP version: 8.3
5. **Deploy from Git**: `chvvasss/ozanefeogluwebsite`
6. Branch: `main`

### 3.3 Environment
1. Site → **Environment** sekmesi
2. `deploy/.env.production.template`'in içeriğini kopyala
3. `<PLACEHOLDER>` değerleri doldur (passwords, API keys)
4. Kaydet

### 3.4 SSL
1. Site → **SSL** → Let's Encrypt → Obtain Certificate
2. Force HTTPS ✓

### 3.5 Daemon (queue worker)
1. Site → **Queue** → New Daemon
2. Connection: `redis`
3. Queue: `high,default,low`
4. Memory: 192 MB
5. Tries: 3

### 3.6 Scheduler
Forge zaten `* * * * * php artisan schedule:run` cron'unu otomatik kurar. ✓

### 3.7 Database + initial seed
1. Forge → Databases → ozanefeoglu_prod oluştur
2. SSH'la sunucuya gir veya Forge web SSH:
```bash
cd /home/forge/ozanefeoglu.com
php artisan migrate --force
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=SuperAdminSeeder --force
php artisan db:seed --class=SettingSeeder --force
php artisan db:seed --class=PageSeeder --force
php artisan db:seed --class=LegalPageSeeder --force
php artisan db:seed --class=PublicationSeeder --force
```

### 3.8 İlk deploy
Forge sitesinde **Deploy Now** butonu. Site canlıya çıkar.

### 3.9 Smoke test
```bash
for p in / /yazilar /goruntu /hakkimda /iletisim /sitemap.xml /up; do
  curl -s -o /dev/null -w "%{http_code}  $p\n" https://ozanefeoglu.com$p
done
```
Hepsi 200 olmalı.

### 3.10 Login + 2FA setup
1. https://ozanefeoglu.com/login
2. ADMIN_EMAIL + ADMIN_PASSWORD ile giriş
3. **Profil** → şifreyi yeni güçlü şifrene çevir
4. **İki faktör** → QR kod tara, recovery codes'u offline yere kaydet
5. .env'deki `ADMIN_PASSWORD`'u sil (secret manager'a taşı)

---

## 4 · Adım-adım — B seçeneği (Manuel VPS)

### 4.1 Sunucu oluştur
- Hetzner Cloud Console → New Server
- Image: Ubuntu 24.04
- Type: CPX21 (3 vCPU, 4 GB RAM, €6.5/ay)
- Region: Nuremberg
- SSH key ekle (passwordless login)

### 4.2 Provision
```bash
ssh root@<server-ip>
adduser ozan
usermod -aG sudo ozan
su - ozan
```

```bash
curl -fsSL https://raw.githubusercontent.com/chvvasss/ozanefeogluwebsite/main/deploy/provision.sh | bash
```

### 4.3 DB + secret kurulumu
```bash
sudo mysql_secure_installation

DB_PWD=$(openssl rand -base64 24)
echo "DB password: $DB_PWD" > ~/secrets.txt
chmod 600 ~/secrets.txt

sudo mysql <<SQL
CREATE DATABASE ozanefeoglu_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ozan_app'@'127.0.0.1' IDENTIFIED BY '$DB_PWD';
GRANT ALL ON ozanefeoglu_prod.* TO 'ozan_app'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL
```

### 4.4 Code + env
```bash
cd /var/www
sudo git clone https://github.com/chvvasss/ozanefeogluwebsite.git ozanefeoglu.com
sudo chown -R $USER:www-data ozanefeoglu.com
cd ozanefeoglu.com

cp deploy/.env.production.template .env
nano .env   # fill in DB_PASSWORD, ADMIN_*, SMTP_*, AWS_*, SENTRY_DSN, etc.
chmod 600 .env

php artisan key:generate --force
```

### 4.5 Caddy
```bash
sudo cp deploy/Caddyfile /etc/caddy/Caddyfile
sudo systemctl reload caddy
```

DNS A kaydı sunucu IP'sine baktıktan sonra Caddy otomatik Let's Encrypt çeker.

### 4.6 Deploy
```bash
cd /var/www/ozanefeoglu.com && bash deploy/deploy.sh
```

### 4.7 Queue worker
```bash
sudo cp deploy/laravel-queue.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now laravel-queue
sudo systemctl status laravel-queue   # active (running) ✓
```

### 4.8 Cron
```bash
sudo crontab -u www-data -e
# Add this line:
* * * * * cd /var/www/ozanefeoglu.com && php artisan schedule:run >> /dev/null 2>&1
```

### 4.9 Smoke + login (3.9 ve 3.10 ile aynı)

---

## 5 · DNS kayıtları (Cloudflare)

```
Type   Name    Content              Proxy   TTL
A      @       <server-ip>          ✓       Auto
A      www     <server-ip>          ✓       Auto
TXT    @       v=spf1 include:mailgun.org -all   ✗   Auto    (SMTP provider'a göre)
MX     @       <mailgun-mx>         ✗       Auto    (eğer maile cevap alınacaksa)
```

Cloudflare proxy AÇIK olduğunda gerçek client IP `CF-Connecting-IP` header'ından gelir → bizim TrustProxies middleware'ımız zaten `*` set edilmiş, IP doğru yakalanır.

---

## 6 · Post-deploy doğrulama

1. **Lighthouse skoru** ≥ 90 her kategori için
2. **SecurityHeaders.com** A+ rating (https://securityheaders.com/?q=ozanefeoglu.com)
3. **SSL Labs** A+ rating (https://www.ssllabs.com/ssltest/analyze.html?d=ozanefeoglu.com)
4. **Mozilla Observatory** A+ (https://observatory.mozilla.org)
5. **Google Search Console** sitemap submit
6. **Sentry** ilk hata yakalama testi (kasıtlı 500'le)

---

## 7 · Acil durum playbook

| Durum | Komut |
|---|---|
| Site açılmıyor | `tail -100 storage/logs/laravel.log` |
| Cache'i temizle | `php artisan optimize:clear` |
| Queue tıkandı | `sudo systemctl restart laravel-queue` |
| Caddy düştü | `sudo systemctl restart caddy` |
| DB connect hatası | `sudo systemctl status mysql` + `mysql -u ozan_app -p` |
| Önceki sürüme dön | `git reset --hard HEAD~1 && bash deploy/deploy.sh` |
| Backup'tan restore | `unzip latest.zip -d /tmp && mysql < /tmp/db.sql` |

---

## 8 · Maintenance mode (planlı bakım)

```bash
php artisan down --secret="emergency-access-token" --refresh=15
# work...
php artisan up
```

Sahibi `https://ozanefeoglu.com/emergency-access-token` ile siteyi normal görür, başkalarına 503.

---

## 9 · Saklı tut

- ADMIN_PASSWORD (ilk login sonrası değiştir, sil)
- 2FA recovery codes (offline, kasada)
- DB_PASSWORD (Forge gizli alanı VEYA secrets.txt yedeği)
- AWS_SECRET_ACCESS_KEY
- BACKUP_ARCHIVE_PASSWORD (backup zip şifresi — bu olmadan zip açılmaz)
- SENTRY_LARAVEL_DSN

Hepsini bir password manager'a (1Password / Bitwarden / KeePassXC) ekle.
