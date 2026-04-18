# Mimari — ozanefeoglu.com

> Bu dosya **yüksek seviye** mimariyi anlatır. Her bir teknoloji seçiminin **gerekçesi** `docs/decisions/` altındaki ADR'larda.

## 1. Yaklaşım

Sunucu-tarafı render edilmiş çok-sayfa uygulama (MPA) + ada yaklaşımı (interaktif parçalar Alpine.js + HTMX ile). SPA değil — bu bir içerik sitesi; SSR + progressive enhancement doğru varsayılan.

```
┌───────────────────────────────────────────────────────────────┐
│                          Browser                              │
│  ┌─────────────────────────────────────────────────────────┐  │
│  │  HTML (Blade-rendered)                                  │  │
│  │  + Tailwind CSS v4 (compiled, critical inlined)         │  │
│  │  + Alpine.js (UI state, micro-interactions)             │  │
│  │  + HTMX (admin partial updates, search-as-you-type)     │  │
│  │  + View Transitions API (sayfa geçişleri)               │  │
│  └─────────────────────────────────────────────────────────┘  │
└────────────────────────────────┬──────────────────────────────┘
                                 │ HTTPS (HTTP/2 veya HTTP/3)
                                 ▼
┌───────────────────────────────────────────────────────────────┐
│  Reverse proxy (Nginx / Caddy)                                │
│  - TLS terminasyon, HSTS, statik dosya servisi (immutable)    │
│  - Rate limit (Nginx limit_req)                               │
│  - Cache: full-page cache (anonymous user GET)                │
└────────────────────────────────┬──────────────────────────────┘
                                 │ FastCGI / FrankenPHP worker
                                 ▼
┌───────────────────────────────────────────────────────────────┐
│  PHP 8.4 + Laravel 12 (en güncel stable)                      │
│                                                               │
│  ┌─────────────┐  ┌──────────────┐  ┌────────────────────┐    │
│  │ Public      │  │ Admin        │  │ API (dahili,       │    │
│  │ controllers │  │ controllers  │  │ HTMX/Alpine için)  │    │
│  └──────┬──────┘  └──────┬───────┘  └─────────┬──────────┘    │
│         │                │                    │               │
│         ▼                ▼                    ▼               │
│  ┌─────────────────────────────────────────────────────────┐  │
│  │  Domain layer (Services, Actions, Policies)             │  │
│  │  Eloquent models + Spatie Translatable + Media Library  │  │
│  └─────────────────────────────────────────────────────────┘  │
└────┬────────────────┬───────────────┬──────────────────┬──────┘
     │                │               │                  │
     ▼                ▼               ▼                  ▼
┌─────────┐   ┌───────────────┐  ┌───────────┐   ┌─────────────┐
│ MySQL 8 │   │ Redis/Valkey  │  │ Meilise.  │   │ Local FS /  │
│ (data)  │   │ cache+queue+  │  │ (search)  │   │ S3 uyumlu   │
│         │   │ session       │  │           │   │ (medya)     │
└─────────┘   └───────────────┘  └───────────┘   └─────────────┘
```

## 2. Katmanlar

### 2.1 Sunum
- **Blade** templates, küçük bileşenler (`<x-button />`, `<x-prose />`, `<x-empty-state />`).
- **Tailwind CSS v4** — engine: Lightning CSS, JIT, `@theme` direktifi ile design tokens.
- **Alpine.js 3** — UI state (modal, dropdown, tab, theme toggle).
- **HTMX** — admin'de partial updates (form submit, list filtering, drag-to-reorder).
- **View Transitions API** — sayfa geçişlerinde continuity (progressive enhancement, fallback yumuşak).

### 2.2 Uygulama
- Controller → Action → (Service/Repository?) → Model. Action pattern (Spatie tarzı): tek sorumluluk, test edilebilir, kuyruğa konabilir.
- **Validation:** Form Request sınıfları. Kontrol noktası tek yerde.
- **Authorization:** Policies + Gate. Her admin route'u policy ile korumalı.
- **Events & Listeners:** Yan etkiler için (yeni yazı yayınlandı → cache invalidate, sitemap regen, RSS regen, search index update).

### 2.3 Veri
- **Eloquent ORM** + read-replica desteği geleceğe hazır olsun (config'te ayrı read connection).
- **Migrations** atomik, tek konsept başına. Asla destructive change üretim DB'sine inmez (rename → add+copy+drop pattern).
- **Translatable alanlar** Spatie/laravel-translatable: tek tablo, JSON kolon, locale-aware accessor.
- **Soft deletes** içerik tablolarında (proje, yazı, sayfa, medya, kullanıcı). Audit + geri alma.
- **Versioning** uzun-form içerik için (post_versions tablosu, revision history).

### 2.4 Cache
- **Page cache:** Anonim GET istekleri için tam sayfa cache. Cache key: URL + locale + cookie hash. Invalidation: yazı/proje publish/update event'inde tag-based purge.
- **Fragment cache:** Pahalı view parçaları (öne çıkanlar listesi, etiket bulutu).
- **DB query cache:** Sık erişilen lookup'lar (categories, tags, settings).
- **HTTP cache:** Statik asset'ler `Cache-Control: public, max-age=31536000, immutable` + content hash.

### 2.5 Search
- **Meilisearch** self-host (Docker container).
- Index: `posts`, `projects`, `pages`. Locale başına ayrı index.
- Update: Eloquent observer → queue job → Meilisearch upsert.

### 2.6 Queue
- **Redis-backed queue** production. Database queue dev/staging.
- Workerlar `php artisan queue:work --tries=3 --backoff=60` supervisor altında.
- Job'lar: search index update, image variant generation, sitemap regen, RSS regen, newsletter send, email send.

### 2.7 Medya
- **Spatie Media Library** + **Intervention Image v3**.
- Upload → magic byte verify → re-encode (metadata strip) → variant generation (responsive: 320, 640, 1024, 1920w; format: AVIF + WebP + original fallback).
- Storage: `local` dev, S3-uyumlu (Cloudflare R2 / Backblaze B2) production.
- Public URL: CDN front (Cloudflare). Path obfuscation (random uuid).

## 3. Güvenlik mimarisi

Detay: `docs/discovery/threat-model.md` ve `docs/security.md` (Faz 7'de yazılacak).

Özet katmanlar:
1. **Edge:** Cloudflare WAF (opsiyonel) + reverse proxy rate limit + HTTPS yalnız.
2. **App:** Middleware zinciri — `EnforceHttps`, `SetSecurityHeaders`, `VerifyCsrf`, `Authenticate`, `ThrottleRequests`, `Authorize`.
3. **Veri:** Prepared statements (Eloquent zaten), output escape (Blade `{{ }}` default), file-system permission asgari.
4. **Auth:** Argon2id (Laravel hash default), TOTP 2FA, recovery codes, session regeneration.
5. **Secrets:** `.env` gitignored, production'da OS env veya Vault. CI'da masked secrets.

## 4. Deployment topolojisi

İki hedef destekleniyor — sahibi karar verecek:

### 4.1 VPS (önerilen)
- Ubuntu 24.04 LTS
- Caddy (otomatik TLS, HTTP/3)
- PHP 8.4 + FrankenPHP veya PHP-FPM
- MySQL 8
- Redis/Valkey
- Meilisearch (Docker)
- Supervisor (queue worker)
- Cron (scheduler, backup)

### 4.2 Shared hosting (fallback)
- PHP 8.2+ olan paylaşımlı host
- MySQL 8
- Redis yoksa file cache, queue: `sync` (gerçek zamanlı job yok), search: MySQL FULLTEXT (Meilisearch'e karşı fallback)
- Bu mod için `config/site.php` flag: `SITE_DEPLOY_MODE=shared` → bağımlılıklar düşer.

## 5. Build pipeline

- **Vite 6+** — Tailwind v4 + ES modules + asset hashing.
- Manifest `public/build/manifest.json`. Blade `@vite([...])` directive.
- Production build: `npm run build` → CI artifact → atomik symlink swap deploy.

## 6. CI/CD

GitHub Actions:
1. **PR open:** lint (PHP-CS-Fixer, ESLint, Stylelint), static analysis (PHPStan level 8), unit + feature tests (Pest), security audit (`composer audit`, `npm audit`), secrets scan (gitleaks).
2. **Merge to main:** build assets, run migrations against staging, smoke test, deploy.

## 7. Gözlemlenebilirlik

- **Logs:** JSON-line, Monolog handler, `storage/logs/`. Production'da Loki veya basit dosya rotation.
- **Errors:** GlitchTip (Sentry uyumlu, self-host) — Faz 7'de.
- **Metrics:** Yerleşik basit panel (request count, p50/p95 latency, hata oranı) admin dashboard'a.
- **Uptime:** `/health` endpoint (DB ping + cache ping + queue depth).

## 8. Dikkat edilmesi gereken şeyler

- **Migration güvenliği:** `create_posts_table` gibi migrations idempotent olmasa da, prod'a inecek değişiklikler **rename pattern** ile yapılmalı (add new col → backfill → switch reads → drop old). Otomatik destructive migration yok.
- **Multi-tenant değil:** Tek sahip, tek site. Soyutlamalar buna göre — gereksiz tenant abstraction yok.
- **Kullanıcı yorumları:** Faz 5'te. İlk lansmanda kapalı; webmentions tercih edilebilir.
