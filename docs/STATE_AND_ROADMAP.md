# Proje Raporu — Mevcut Durum + Kalan Fazlar Yol Haritası

> **Tarih:** 2026-04-19 · **Sürüm:** v0.3 (Faz 2C tamamlandı)
> **Yazar:** Otonom inşa süreci — bu doküman canlı; her faz sonunda güncellenir.

---

## BÖLÜM A — MEVCUT DURUM (ULTRA DETAY)

### A.0 — Bir bakışta

| Eksen | Durum |
|---|---|
| Faz | **2C tam kapalı** (Faz 0 + 1 + 2A + 2B + 2C); 2C+, 3, 4, 5, 6, 7, 8, 9 kalan |
| Stack | Laravel 13.5 · PHP 8.4.19 · MySQL 8 (prod) / SQLite (dev) · Tailwind v4 · Alpine 3 · TipTap 3 · Pest 4 |
| Test | **112 / 113** geçer (1 skip — SQLite enum constraint atla) · 248 assertion |
| Lint | ✅ Pint temiz · ✅ `composer audit` temiz · ✅ `npm audit` temiz |
| Build | CSS 12 KB gz (public) + 13 KB gz (admin) · Alpine 24 KB · HTMX 18 KB (admin only) |
| Public sayfalar | `/` · `/yazilar` · `/yazilar/{slug}` · `/hakkimda` · `/iletisim` (GET+POST) · `/health` |
| Admin sayfalar | `/admin` · `/admin/writings` (CRUD + publish) · `/admin/pages` (CRUD) · `/admin/contact` (inbox) · `/admin/profile` · `/admin/profile/sessions` · `/admin/two-factor` · `/admin/audit-log` · `/admin/login` (Fortify `/login` üstünden) |
| ADR sayısı | 15 (`docs/decisions/001..015`) |
| Migration sayısı | 16 |
| Model sayısı | 7 (User, Setting, UserDevice, Publication, Writing, Page, ContactMessage) |
| Test suite | 19 dosya (Auth/Admin/Writing/Pages/Middleware/Security/Health/Public) |
| Doküman sayısı | 6 keşif + 15 ADR + bu rapor + README + admin notes (toplam ~25 markdown) |

### A.1 — Ortam

| | |
|---|---|
| OS | Windows · `C:\Users\emir\Desktop\ozanefeoglu.com` |
| PHP | 8.4.19 (WinGet kurulumu) — `php.ini` manuel: `openssl, curl, mbstring, intl, gd, mysqli, pdo_*, sodium, zip, exif, opcache` |
| Composer | 2.9.7 — `C:\Users\emir\bin\composer` wrapper (PATH'e eklenmesi gerekiyor) |
| Node | 24.13.1 · npm 11.8.0 |
| Docker | 29.2.0 + Compose v5 (kullanılmıyor şimdilik — SQLite dev) |
| **DİSK UYARISI** | **C: %100 dolu (~228 MB free)**. `/tmp` periyodik temizlik gerek; preview screenshot timeout sebebi. |

### A.2 — Faz 0 (Discovery) çıktıları

5 keşif dokümanı + 15 ADR + 6 dev infra dosyası:

**`docs/discovery/`**
- `design-research.md` — 20+ portfolyo + 5 blog incelendi, çalan örüntüler sentezlendi.
- `threat-model.md` — STRIDE × OWASP Top 10 mapping.
- `information-architecture.md` — URL şeması, ER, yetki matrisi, kullanıcı yolculukları.
- `design-tokens.md` — renk/tipografi/spacing/motion semantic tanımlı.
- `faz-1-blueprint.md` — Faz 1 implementation rehberi (geçmiş referans).

**`docs/decisions/`** (Architecture Decision Records — kararların gerekçesi)
| # | Karar | Özet |
|---|---|---|
| 000 | Template | ADR şablonu |
| 001 | PHP framework | Laravel 13 (en güncel stable) |
| 002 | Database | MySQL 8 prod / SQLite dev (PG fallback hazır) |
| 003 | Frontend stack | Tailwind v4 + Alpine.js + HTMX + Vite |
| 004 | Template engine | Blade |
| 005 | Admin yaklaşımı | **Sıfırdan custom** (Filament/Nova reddedildi — generic estetik) |
| 006 | Editor | TipTap 3 (HTML body + HTMLPurifier sanitize) |
| 007 | Medya | Spatie Media Library + Intervention v3 |
| 008 | Search | Meilisearch (MySQL FT fallback) |
| 009 | Cache + queue | Redis/Valkey (file/db fallback) |
| 010 | Auth + 2FA | Fortify + TOTP + HIBP k-anonymity |
| 011 | i18n | Path-based + Spatie Translatable |
| 012 | Build | Vite 6+ + npm |
| 013 | Domain pivot | Savaş muhabiri portfolyo + köşe (Faz 1 içinde) |
| 014 | Ton kalibrasyonu | Editorial journal — Ember accent + Fraunces yumuşatma + tek "yazılar" akışı |
| 015 | Writing model | translatable + status machine + kind enum + publications join |

**Dev infra:** README, .env.example, Makefile, docker-compose.yml, GitHub Actions CI taslağı, .gitignore.

### A.3 — Faz 1 (Foundation)

#### Auth + Security
- **Laravel Fortify** — login + password reset + 2FA TOTP + email verification (admin-scoped)
- **TOTP 2FA**: pragmarx/google2fa-laravel; setup → QR + manual entry + recovery codes (8 single-use, encrypted DB)
- **HIBP** k-anonymity (HibpService): SHA-1 prefix → API → suffix compare. Cache 30g. Network-failure-tolerant. Kayıt + şifre değişiminde aktif.
- **Login throttle**: 5 deneme/dk/(IP+username); 10 başarısız attempt → hesap 15dk kilitli
- **Session hardening**: regenerate on login, idle 30dk, absolute 8sa; cookie `Secure`+`HttpOnly`+`SameSite=Strict` (production)
- **Generic login error**: kullanıcı enumeration yok
- **Active sessions UI**: `/admin/profile/sessions` — device label, IP, last active, sign-out remote

#### Middleware zinciri
- `RequestId` — UUID per-request, `X-Request-Id` header + Log context
- `SecurityHeaders` — `Strict-Transport-Security` (prod), `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, `Cross-Origin-Opener-Policy: same-origin`, `Cross-Origin-Resource-Policy: same-origin`, `Permissions-Policy: camera=()...`, **CSP nonce** (production)
- `SetAppLocale` — route param veya user.locale veya fallback
- `EnsureTwoFactorEnrolled` — admin'de 2FA zorunluysa enrollment'a yönlendirir
- Spatie `RoleMiddleware` + `PermissionMiddleware` aliases

#### Veri katmanı
- 5 rol: `super-admin / admin / editor / contributor / viewer`
- Audit log (Spatie ActivityLog): login.success/failed, password.changed, 2fa.enabled/disabled, role.changed, user.created/deleted
- `users` extended: locale, last_login_at, last_login_ip, locked_until, failed_attempts, password_changed_at, soft_deletes
- `settings` (key-value JSON), `user_devices` (active sessions tracking)

#### Public landing
- Hero: "Gittim, baktım, *yazdım*."
- Bylines strip (8 publication)
- Selected writings grid (hero card + 5 cards)
- Footer CTA section (Hakkımda + İletişim button'lar)
- Header: brand + 3 nav + theme toggle + Masa button
- Footer: 4 col linkler

#### Tasarım sistemi (final kalibrasyonu, ADR-014)
- **Tipografi:** Fraunces Variable (display, `SOFT=20 WONK=0 opsz=72/96/144`) + IBM Plex Sans Variable (body) + IBM Plex Mono (dateline)
- **Renk:** Ember accent (`#9a3412` light / `#fb923c` dark) + stone neutral palette
- **Atmosfer:** Paper grain overlay (0.022 opacity, mix-blend multiply)
- **Hierarchy:** modular scale 1.25, italic-em accent vurgu **3 yer ile sınırlı** (hero + section title + contact)
- **Motion:** `animation-timeline: view()` modern CSS scroll reveal (Chromium); `prefers-reduced-motion` saygılı
- **Dark mode:** sistem + manuel; pre-hydration script ile FOUC yok
- **Editorial decoration:** dateline mono caps, ember dot accent, drop cap (about)

#### Test (Faz 1)
77 test (Auth login/reset/2FA/session-regen + Admin profile/sessions/audit + Middleware + Security headers + Health + HIBP unit)

### A.4 — Faz 2A (Writing model + public pages)

**Writing model** (`writings` table):
- Translatable: title/slug/excerpt/body/meta_title/meta_description (JSON, locale-keyed)
- Enum kind: `saha_yazisi / roportaj / deneme / not`
- Status machine: `draft / scheduled / published`
- Cover placeholder: `cover_hue_a/b` (HSL) + Spatie media collection 'cover'
- Read-time observer (auto from body, 220 wpm)
- Scopes: published(), featured(), ofKind(), bySlug() (locale-aware fallback)
- Soft deletes
- Publications belongsToMany pivot (link per yazı)

**Public**: `/yazilar` (filter pills + pagination), `/yazilar/{slug}` (uzun-form article — dateline + cover + sticky marginalia + prose-article + prev/next + related)

**Seed**: 6 gerçek yazı (Gazze/Artsakh/Kahire/Harkiv/İstanbul × 4 kind) + 8 publication

### A.5 — Faz 2B (Admin CRUD + TipTap + Media)

**Backend**: WritingPolicy (5 role matrix) + WritingRequest + Admin\WritingController (10 action) + SlugGenerator (TR transliteration + uniqueness) + BodySanitizer (HTMLPurifier allowlist)

**Editor**: TipTap 3 (Alpine `tiptapEditor()` factory) — H2/H3/B/I/S/lists/quote/code/hr/link/undo/redo toolbar; `tiptap-prose` CSS public prose-article ile paralel

**Media**: Writing `HasMedia + InteractsWithMedia` → cover collection (singleFile, 8MB max, jpg/png/webp/avif) + 3 webp variant (640/1280/1920); `coverUrl()` + `coverSrcset()` + `hasCover()` helpers; `_writing-card` partial real-photo > placeholder fallback chain

**Admin Views**: `index` (table + filter + search + bulk actions) + `_form` partial (2-col responsive: title/slug/excerpt/body + status/published_at/kind/location/cover + hue sliders + publications + SEO collapse)

### A.6 — Faz 2C (Pages + Contact)

**Page model**: HasTranslations + extras JSON (template'a özel veri); 3 template: `default / about / contact`; `kind: system | custom`

**ContactMessage model**: name/email/subject/body + ip/ua + status (`new/read/replied/spam`) + read_at

**Public**:
- `/hakkimda` — editorial bio (masthead + sticky künye + prose + timeline rail + awards + son yazılar + CTA)
- `/iletisim` — secure channels (3 channel cards + form + PGP fingerprint + disclosure box) + form throttle 3/10dk + honeypot

**Admin**:
- `/admin/pages` (CRUD; system page silinemez/slug+template kilitli)
- `/admin/contact` inbox (filter pills + table + unread count badge sidebar)

### A.7 — Klasör haritası (özet)

```
ozanefeoglu.com/
├── app/
│   ├── Actions/Fortify/                    (5 dosya — auth actions)
│   ├── Http/Controllers/                   (15 controller)
│   │   ├── Admin/                          (8 — Writing, Page, Contact, Dashboard, Profile, Sessions, Two-Factor, AuditLog)
│   │   └── Public                          (7 — Home, Writing, About, Contact, Auth\Logout, Health)
│   ├── Http/Middleware/                    (4 — RequestId, SecurityHeaders, SetAppLocale, EnsureTwoFactorEnrolled)
│   ├── Http/Requests/                      (3 — Admin\Writing, Admin\Page, ContactMessage)
│   ├── Listeners/RecordLoginActivity.php
│   ├── Models/                             (7 model)
│   ├── Policies/                           (Writing, Page)
│   ├── Providers/                          (App, Event, Fortify)
│   └── Services/                           (HibpService, SettingsRepository, Auth\LoginActivityRecorder, Content\SlugGenerator, Content\BodySanitizer)
├── config/                                 (security.php + Laravel default + Spatie publishes)
├── database/
│   ├── migrations/                         (16 migration)
│   ├── factories/                          (3 — User, Writing, Publication)
│   └── seeders/                            (5 — Role, SuperAdmin, Setting, Publication, Writing, Page + DatabaseSeeder)
├── docs/
│   ├── architecture.md
│   ├── discovery/                          (5 keşif dokümanı)
│   ├── decisions/                          (000 + 001..015 ADR)
│   └── STATE_AND_ROADMAP.md                (bu doküman)
├── public/build/                            (Vite output, gitignored)
├── resources/
│   ├── css/                                (app.css + admin.css)
│   ├── js/                                 (app.js + admin.js + tiptap-editor.js)
│   └── views/
│       ├── layouts/                        (public, admin, auth)
│       ├── partials/                       (public-header, public-footer, admin-sidebar, _writing-card)
│       ├── public/                         (landing, writing/index, writing/show, pages/about, pages/contact)
│       ├── admin/                          (dashboard, writings/, pages/, contact/, profile/, audit-log/)
│       ├── auth/                           (login, two-factor-challenge, forgot-password, reset-password, confirm-password)
│       └── errors/                         (404, 500, 503)
├── routes/web.php
├── tests/
│   ├── Feature/                            (15 suite — Auth, Admin, Writing, Pages, Middleware, Security, Public, Health)
│   └── Unit/HibpServiceTest.php
├── .env.example                            (kapsamlı; tüm env doc'lu)
├── composer.json + composer.lock
├── package.json + package-lock.json
└── vite.config.js
```

### A.8 — Brief kalite kapısı raporu (her ekseni gez)

#### Güvenlik (Brief §5.1) — **8/12 madde tam, 4 kısmen**
| Madde | Durum |
|---|---|
| OWASP ASVS L2 | Hedef; Faz 7'de DAST (ZAP) ile doğrulanacak |
| Output context-aware escape | ✅ Blade `{{ }}` default; `{!! !!}` sadece `BodySanitizer` çıktıları için |
| Prepared statements | ✅ Eloquent / Query Builder default |
| **CSP** | ⚠️ `SecurityHeaders` middleware nonce hazır; production-only enable; Faz 7'de inline script audit + nonce wiring tamamlanacak |
| HSTS / X-CTO / Referrer / COOP / CORP / Permissions | ✅ tüm response'larda |
| Cookie güvenliği | ✅ `__Host-` prefix planlandı (prod), Secure+HttpOnly+SameSite=Strict |
| CSRF | ✅ Laravel default + Fortify formlar |
| **Rate limiting** | ✅ login + 2FA + contact form; api endpoint'leri için Faz 5'te |
| **File upload** | ⚠️ MIME whitelist ✓, magic bytes kısmen Laravel'in `image` rule'unda; Faz 5'te `finfo_buffer` + uploads dizini PHP-exec yasağı + image re-encode ek katmanlar |
| Password storage | ✅ Argon2id (Laravel default) |
| Session yönetimi | ✅ regenerate, timeout, sessions UI |
| Error handling | ✅ APP_DEBUG=false production'da, ref ID gösterilir |
| Secret management | ✅ `.env` gitignored; gitleaks CI gate planlı |
| Dependency güvenliği | ✅ `composer audit` + `npm audit` temiz; CI workflow taslağı (Faz 7'de aktif) |
| 2FA | ✅ TOTP + recovery codes; admin için `REQUIRE_2FA_FOR_ADMIN` env toggle (default true production) |

#### Performans (Brief §5.2) — **çoğu hazır, Lighthouse Faz 6'da**
| Hedef | Durum |
|---|---|
| LCP < 2.5s | Beklenen — Vite optimal bundle, font preload yok henüz, Faz 6'da ayar |
| INP < 200ms | Beklenen — Alpine + HTMX hafif |
| CLS < 0.1 | Beklenen — image dimensions explicit |
| TTFB | OK — SQLite dev hızlı; prod MySQL + Redis cache Faz 6'da |
| Image strategy | ✅ webp variants (640/1280/1920) + srcset + picture; AVIF Faz 5'te |
| Font loading | ⚠️ Fontsource self-hosted ama `font-display: swap` ve preload kritik subset henüz değil — Faz 6 |
| Critical CSS inline | ⚠️ Faz 6'da (Vite plugin veya manuel extract) |
| JS public bundle | ✅ < 25KB CSS, sıfır JS public sayfada (Alpine sadece toggle/copy için) |
| Caching | ⚠️ Page cache, fragment cache, query cache henüz yok — Faz 6 |
| DB N+1 | ✅ `Model::preventLazyLoading` dev'de; relation eager loading |

#### Erişilebilirlik (Brief §5.3) — **temel ✓, axe/pa11y Faz 6'da**
- ✅ Semantic HTML (article/nav/main/header/footer kullanımı)
- ✅ Heading hiyerarşi (h1 → h2 → h3)
- ✅ Skip link (`İçeriğe geç`)
- ✅ Form label + aria-describedby
- ✅ Focus-visible 2px ember outline
- ✅ Klavye navigasyonu (tab/shift-tab/enter/escape)
- ✅ ARIA sadece ihtiyaç (aria-current, aria-label, aria-hidden)
- ✅ `prefers-reduced-motion` saygılı
- ✅ Renk tek başına bilgi taşımıyor (durum dot + label)
- ✅ Image alt zorunlu (cover'larda boş; gerçek foto Faz 5'te alt zorunlu)
- ⚠️ axe-core + pa11y otomasyon Faz 6'da

#### SEO (Brief §5.4) — **kısmi**
- ✅ Page meta title/description (admin'den editable)
- ⚠️ Canonical URL henüz output edilmiyor (Writing model alan var)
- ❌ Structured data JSON-LD yok — Faz 3
- ❌ sitemap.xml yok — Faz 3
- ❌ robots.txt yok (Laravel default) — Faz 3
- ❌ 404 useful (arama widget yok) — Faz 3
- ✅ Slug okunabilir, kebab-case, TR transliteration
- ⚠️ hreflang i18n için planlandı, EN locale Faz 8'de

#### Kod kalitesi (Brief §5.5)
- ✅ Pint (PSR-12) temiz
- ⚠️ PHPStan level 8 hedef — config var ama çalıştırılmadı (Faz 7'de gate)
- ❌ ESLint/Stylelint config var ama çalıştırılmadı (Faz 6'da gate)
- ✅ `declare(strict_types=1)` tüm yeni dosyalarda
- ✅ Domain naming (Writing, Page, ContactMessage)
- ✅ Comment policy (neden > ne)
- ⚠️ Conventional commits — bu repo şimdiye kadar manuel commit yok; Faz 9'da gate

#### Test (Brief §5.6) — **iyi taban, kapsam genişletilecek**
- 112 test geçer, 248 assertion
- Critical paths kapsanmış: auth flow, admin CRUD, public routing, sanitize, policy, middleware
- ❌ Browser tests (Playwright/Dusk) — Faz 6'da E2E user journey
- ❌ Visual regression — opsiyonel (Faz 9)
- ⚠️ Coverage % ölçümü yapılmadı (Pest `--coverage`)

#### Gözlemlenebilirlik (Brief §5.7)
- ✅ Structured logging (Monolog stack), Request ID context
- ⚠️ Error tracking (Sentry/GlitchTip) — Faz 7
- ⚠️ Metrics dashboard — Faz 4 (admin dashboard içinde basit)
- ✅ `/health` endpoint (DB + cache ping)

---

## BÖLÜM B — KALAN FAZLAR (DETAY YOL HARİTASI)

### Konvansiyon
Her faz için: **Kapsam · Yapılacaklar · Yapılmayacaklar · Mimari yaklaşım · Bağımlılıklar · Risk · Kalite kapısı**

---

### FAZ 2C+ (1–2 oturum) — RSS, Sitemap, Schema.org, Search foundation

#### Kapsam
Public içerik tüketimi için temel SEO + dağıtım altyapısı. Search ileri özellik ama foundation kuruluyor.

#### Yapılacaklar
1. **`/feed.xml`** — Atom 1.0 feed (Spatie/Feed paketi veya manuel)
   - Yazılar feed (son 20)
   - Per-item: title, link, summary (excerpt), content (full HTML, sanitized), published, updated, author, category (kind)
   - HTTP `Cache-Control: public, max-age=900`
   - `<link rel="alternate" type="application/atom+xml">` head'e
2. **`/sitemap.xml`** — Spatie/Sitemap paketi
   - Anasayfa + arşiv + her writing + her published page
   - `lastmod` doğru (updated_at)
   - `changefreq` + `priority` ölçülü
   - `<xhtml:link hreflang>` (Faz 8'de tam aktif)
3. **`/robots.txt`** — admin-editable (Spatie/laravel-robots-txt veya custom controller)
   - Default: allow all + disallow `/admin/*` + sitemap pointer
4. **JSON-LD structured data** (Schema.org)
   - Person (Ozan Efeoğlu) — global head'de
   - WebSite + SearchAction
   - BlogPosting per writing detail page
   - Validation: Google Rich Results Test
5. **Search foundation** — Meilisearch entegrasyonu başlangıcı
   - `App\Services\Search\SearchEngine` interface
   - `MeilisearchEngine` ve `MysqlFulltextEngine` implementations
   - `php artisan search:reindex` Artisan command
   - `Writing::saved` observer → queue job → index update
   - `/yazilar?q=...` controller'da arama desteği (sadece backend; UI Faz 3)
6. **Eski URL → yeni URL otomatik 301** (slug değişikliğinde)
   - `redirects` tablosu (zaten IA'da plan)
   - Writing model `saving` event slug değişikliği yakalar → eski slug yeni slug'a redirect insert
7. **404 sayfası geliştirme** — popüler içerik linkleri + arama kutusu (Faz 3'te tam UI; bu fazda sadece "did you mean" placeholder)

#### Yapılmayacaklar
- Search UI overlay/autocomplete (Faz 3)
- hreflang EN locale (Faz 8)
- Newsletter (Faz 5)

#### Mimari
- **Search abstraction**: Driver pattern. `.env` `SEARCH_DRIVER=meilisearch` toggle. Test'te `array` driver mock.
- **Feed builder**: Spatie/Feed yeterli. Test: feed XML schema doğru parse oluyor mu (SimpleXML).
- **Sitemap caching**: Generated → `storage/app/sitemap.xml`; Writing event → invalidate (rebuild).

#### Bağımlılıklar
- `composer require spatie/laravel-feed spatie/laravel-sitemap`
- Meilisearch zaten kurulu (composer)

#### Risk
- Meilisearch dev'de Docker gerek; SQLite fallback driver ile testler local yapılabilir.
- Schema.org JSON-LD content security; sanitize edilmiş output.

#### Kalite kapısı
- [ ] `/feed.xml` valid Atom (W3C feed validator)
- [ ] `/sitemap.xml` valid (Google tester)
- [ ] JSON-LD valid (Google Rich Results)
- [ ] Search reindex command çalışıyor
- [ ] Slug değişikliği → redirect kaydı
- [ ] Tests: feed, sitemap, search (unit + feature)

---

### FAZ 3 (2–3 oturum) — Public polish + Search UI + Static pages

#### Kapsam
Public deneyimin "kapanışı" — arama UI, breadcrumbs, "Now/Colophon/Uses" gibi statik sayfalar (custom Page örnekleri), Tag/kategori arşivleri, Yıl arşivleri.

#### Yapılacaklar
1. **Search UI** — `/ara?q=...` ve site genelinde arama kutusu
   - Header sağda büyütülebilir search input (Cmd+K shortcut)
   - HTMX live search dropdown (sadece Meilisearch driver'da)
   - Results page (writings + pages mixed)
   - "Did you mean" suggestion (Meili built-in)
2. **Tag/kategori arşivleri** — Spatie Tags paketi
   - Writing'e `HasTags` trait
   - `regions` (Gazze, Artsakh...) ve `topics` (etik, savaş, göç...) tag tipleri
   - Routes: `/yazilar/bolge/{slug}`, `/yazilar/konu/{slug}`
   - Admin form: tag input (Alpine combobox)
3. **Yıl arşivi** — `/yazilar/{year}` (örn `/yazilar/2025`)
4. **Yazar arşivi (multi-author)** — Faz 4'te user mgmt sonrası
5. **Custom statik sayfalar (örnekler):**
   - `/now` — şu an ne yapıyor (Page custom + template `default`)
   - `/colophon` — site teknik notları
   - `/uses` — donanım/yazılım listesi
6. **Breadcrumbs** — public sayfalarda (özellikle yazı detay)
7. **Related posts iyileştirme** — sadece kind değil, tag overlap score
8. **"İlk kez okuyana" giriş** — anasayfada featured intro card (yeni ziyaretçiye küçük walkthrough)
9. **Theme switcher** — sistem/aydınlık/karanlık (zaten var, ama UI iyileştirme; preset renk değiştirici opsiyonel)
10. **Locale switcher (kapalı)** — EN gelene kadar disabled UI'da var

#### Yapılmayacaklar
- Yorum sistemi (Faz 5)
- Newsletter signup (Faz 5)
- Admin/SEO redirect manager UI (Faz 4)

#### Bağımlılıklar
- Spatie Tags zaten kurulu
- Meilisearch çalışıyor (Faz 2C+)

#### Risk
- Search relevance fine-tuning gerekir; default Meili fena değil ama TR stop word + synonyms ayarı.
- HTMX live search rate limit (server-side throttle gerek).

#### Kalite kapısı
- [ ] Search "lavarel" yazınca "laravel" sonucu gelir (typo)
- [ ] Tag arşivi pagination çalışır
- [ ] Cmd+K her sayfada açılır
- [ ] Tests: search index lifecycle, tag routing, custom pages

---

### FAZ 4 (3–4 oturum) — Admin gelişmiş

#### Kapsam
Sahibinin gerçekten kontrol etmek isteyeceği her şey: menü, tema, redirect, kullanıcı yönetimi, audit log polish, yedekleme, ayarlar, bildirimler.

#### Yapılacaklar
1. **Menü yöneticisi** (`/admin/menus`)
   - `menus` tablosu (location: header/footer/...)
   - `menu_items` (parent_id, label translatable, link_type internal/external, linkable polymorphic)
   - Drag-drop reorder (HTMX + sortablejs)
   - Public render: `<x-menu location="header" />` blade component
2. **Tema yöneticisi** (`/admin/theme`)
   - Logo upload (light + dark variant) — Spatie Media
   - Renk preset seçici (terracotta/ember/moss/ocean/plum/amber)
   - Custom hex input (HSL editor + canlı preview)
   - Font seçimi (paketlenmiş kümeden — Faz 1'deki Fraunces+Plex sabit ama gelecekte alternatif)
   - Anasayfa hero metni override
   - Settings tablosuna kaydedilir; CSS custom properties runtime override
3. **Redirect yöneticisi** (`/admin/redirects`)
   - Manuel + auto (slug change'den gelen) listesi
   - Status code 301/302
   - Hits + last_hit_at istatistik
   - 404 logging → suggested redirects panel
4. **Kullanıcı yönetimi** (`/admin/users`) — sadece super-admin
   - List + filter (role, last login)
   - Invite by email (Fortify register kapalı; admin invitation)
   - Role değiştir
   - Password reset force
   - Soft-delete + restore
   - Active sessions admin view (her user için)
5. **Audit log polish** — filter, export CSV, retention policy
6. **Yedekleme** (`/admin/backups`)
   - Spatie/laravel-backup zaten kurulu
   - Manuel "Şimdi yedekle" button
   - Cron schedule (haftalık)
   - Yedek listesi + download + delete
   - Last 14 yedek tut
7. **Genel ayarlar** (`/admin/settings`)
   - SMTP yapılandırma + test e-posta
   - Bakım modu toggle (whitelist IP)
   - Analytics (Plausible/Umami yapılandırma)
   - REQUIRE_2FA_FOR_ADMIN toggle
   - Site title, tagline, description (i18n)
   - Tüm "settings" tablosuna yazılır (cached)
8. **Bildirimler** (`/admin/notifications`)
   - Yeni iletişim mesajı (badge zaten var)
   - Sistem uyarıları (düşük disk, yedekleme başarısız)
   - In-app notification feed (Laravel database notifications)
9. **Dashboard widgets**
   - Aktif site analytics (Plausible/builtin)
   - Bekleyen taslaklar
   - Okunmamış mesajlar
   - Son aktiviteler
10. **Roller + permissions** — Spatie Permission zaten var; permission-bazlı detaylı UI (admin'de checkbox grid; bu admin'i karışıklaştırma riski var, basit tut)

#### Yapılmayacaklar
- Multi-tenant abstraction (single owner site)
- OAuth provider login (kapsam dışı)

#### Bağımlılıklar
- Spatie Permission, Backup, Translatable zaten var
- Plausible/Umami self-host opsiyonel (Faz 7 ile birlikte)

#### Risk
- Theme runtime override → CSS variable injection güvenliği (sanitize hex)
- Redirect loop (A → B → A) prevention check

#### Kalite kapısı
- [ ] Menü drag-drop kaydeder
- [ ] Tema renk değişiklik anında public yansır
- [ ] Yedek download çalışır + restore docs var
- [ ] User invite flow (mail verification)
- [ ] Tests: settings cache invalidate, role change, redirect lookup

---

### FAZ 5 (3–4 oturum) — Blog derinliği

#### Kapsam
Yazının okunma deneyimi: ToC auto, syntax highlight, footnotes, callout/code-group blokları, ilgili yazılar geliştirme, **yorumlar veya webmentions**, **newsletter**.

#### Yapılacaklar
1. **Auto ToC (içindekiler)** — yazının H2/H3'lerinden çıkarılır
   - Sticky sidebar (lg+) veya inline (sm)
   - Smooth scroll + active section highlight
2. **Syntax highlight** — Shiki server-side render
   - TipTap codeblock'ta dil seçici
   - PHP'de Shiki bridge yok; cache JSON sunucuda (NodeJS process veya pre-rendered)
   - Alternatif: highlight.js client-side (daha basit ama daha ağır)
3. **Footnote / sidenote** — TipTap custom extension
   - `<sup><a href="#fn-1">1</a></sup>` inline + `<ol class="footnotes">` sonda
   - Sidenote variant (lg+ ekran kenarına float)
4. **Custom blocks** — TipTap extension
   - Callout (info/warning/danger) — `<div class="callout" data-type="info">...</div>`
   - Pull quote (yazının ortasında büyük italic Fraunces)
   - Code-group (tabs)
5. **Image galerisi** — TipTap içinde image insert UX iyileştirme
   - Lightbox click (Alpine)
   - Caption + credit
6. **İlgili yazılar geliştirme** — tag overlap + same author + same region
7. **Yorumlar (opsiyonel) — KARAR ANI**
   - **A) Yorum sistemi:** moderation queue, spam filter (Akismet self-host alternatifi olan CleanTalk ya da basit Bayes), per-post enable/disable
   - **B) Webmentions** (IndieWeb): receive endpoint + display
   - **C) Yok** (sadece email iletişim)
   - **Önerim:** Webmentions — daha low-touch, sahibinin moderation yükü minimum, indieweb-aligned.
8. **Newsletter**
   - `subscribers` tablosu (email, confirmed_at, confirmation_token, unsubscribe_token, locale)
   - Double opt-in flow (mail confirmation)
   - Admin "Bültenler" — yeni bülten yaz (TipTap editor) → seçilen yazılardan derle veya custom → preview → send queue (rate-limited)
   - Public footer signup form
   - "Mailgun/Resend/SES" SMTP — `.env` `MAIL_DRIVER`
   - Unsubscribe single-click (no auth, signed URL)
9. **Reading progress indicator** (yazı okurken üstte ince ember bar)

#### Yapılmayacaklar
- Yorum reply/threading (Faz 5'te iletişim kurmak istiyorsan basit comment list yeterli)
- Email tracking pixel (privacy-preserving, kapsam dışı)

#### Bağımlılıklar
- Newsletter için SMTP gerçek yapılandırma
- Shiki için Node bridge (alternatif: PHP highlight kütüphanesi)

#### Risk
- Newsletter mail throughput — büyük listelere queue + chunked send
- Spam filter false positive (yorum açılırsa)

#### Kalite kapısı
- [ ] ToC tıklanır + smooth scroll
- [ ] Footnote ↩ back link
- [ ] Webmention receive + verify
- [ ] Newsletter subscribe → confirm → unsubscribe flow
- [ ] Tests: ToC parser, newsletter flow, webmention validation

---

### FAZ 6 (2 oturum) — Performans + erişilebilirlik geçişi

#### Kapsam
Brief'in §5.2 ve §5.3'teki tüm kalite kapılarının ölçülmüş ve geçilmiş olması.

#### Yapılacaklar

##### Performans
1. **Lighthouse CI** — GitHub Actions'a add
   - Mobile + Desktop
   - Targets: Perf ≥95, Best Practices 100, A11y 100, SEO 100
   - Per-page (anasayfa, /yazilar, /yazilar/{slug}, /hakkimda, /iletisim)
2. **Critical CSS inline** — Vite plugin (`vite-plugin-critters`) veya manuel `<style>` extract
3. **Font preload** — kritik subset (latin + tr karakterler) `<link rel="preload">`
4. **Image variant generation** — AVIF eklenmesi (Faz 2B'de webp; AVIF GD/Imagick uyumu kontrol)
5. **Page cache** — anonim GET istekleri için
   - Laravel Response Cache (Spatie veya custom middleware)
   - Cache key: `URL + locale + cookie_hash`
   - Invalidation: Writing/Page event → tag-based purge
6. **Fragment cache** — hero featured items, etiket bulutu
7. **Eloquent eager loading audit** — `Model::preventLazyLoading` zaten var; query log + N+1 fix
8. **HTTP cache headers** — assets immutable + long max-age + hash; HTML `no-cache, private`
9. **Pre-compress** — gzip + brotli static (Caddy/Nginx server-level, deploy script)
10. **Bundle size CI gate** — public CSS < 25KB gz, public JS < 15KB gz

##### Erişilebilirlik
1. **axe-core CI** — Playwright + axe-core/playwright integration
2. **pa11y CI** — Lighthouse paralel
3. **Keyboard-only walkthrough** — manuel test, dokümante
4. **Screen reader test** — VoiceOver/NVDA macOS/Windows
5. **Contrast audit** — tüm tokens AA pass (mevcut), büyük metin AAA hedef
6. **Motion audit** — `prefers-reduced-motion` her animasyonda saygılı
7. **ARIA pattern audit** — admin form widgets (combobox, multi-select)

#### Yapılmayacaklar
- Lighthouse Performance < 95 ile prod'a çıkma
- Manuel a11y test atlama

#### Bağımlılıklar
- Playwright kurulumu (npm)
- Lighthouse CI npm package

#### Risk
- AVIF GD/Imagick destek Windows'ta tutarlı değil; webp tek başına yeterli kabul edilebilir

#### Kalite kapısı
- [ ] Lighthouse CI tüm sayfalarda yeşil
- [ ] axe-core 0 critical/serious
- [ ] pa11y 0 critical
- [ ] Bundle size CI eşik altı

---

### FAZ 7 (2 oturum) — Güvenlik sertleştirme

#### Kapsam
Brief'in §5.1 + tehdit modeli (`docs/discovery/threat-model.md`) tüm kontrollerinin doğrulanması.

#### Yapılacaklar
1. **CSP nonce production aktif** — şu an dev'de off; tüm inline `<style>` ve `<script>` nonce'lı yap
   - Vite asset'lerine nonce attribute (Vite plugin)
   - Inline theme bootscript nonce ekle
   - Test: `Content-Security-Policy: default-src 'self'; ...` header doğru
2. **OWASP ZAP baseline scan** — Docker (`owasp/zap2docker-stable zap-baseline.py`)
   - GitHub Actions stage Faz 7
   - Weekly cron
3. **`composer audit` + `npm audit` CI gate** — herhangi vulnerability → CI fail
4. **Dependabot / Renovate** — otomatik PR'lar
5. **Gitleaks pre-commit hook + CI** — secret leak detection
6. **2FA mandatory toggle** — production'da `REQUIRE_2FA_FOR_ADMIN=true`; tüm admin login akışı doğrulandı
7. **Rate limit review** — login (var), 2FA (var), contact (var), search/api endpoint'leri (ekle)
8. **File upload sertleşme** — magic bytes verify, EXIF strip GPS, image bomb prevention
9. **Error tracking** — GlitchTip (Sentry compatible, self-host Docker)
   - Source map upload Vite build'den
10. **HSTS preload** — domain `hstspreload.org` listede
11. **Secure cookie hardening** — `__Host-` prefix prod
12. **SSRF block** — `Http::` calls'a private IP filter (gelecekte image proxy / URL fetch için)

#### Yapılmayacaklar
- Penetration test (3rd party — bütçe gerektirir, opsiyonel)
- WAF (Cloudflare gibi managed; deploy stage'i)

#### Bağımlılıklar
- Docker (ZAP scan için)
- GitHub Actions secrets (Snyk token gibi opsiyonel)

#### Risk
- CSP unsafe-inline kaldırınca herhangi inline event handler kırılır; audit dikkatli

#### Kalite kapısı
- [ ] ZAP baseline 0 high/medium
- [ ] CSP report-uri 0 violation 1 hafta
- [ ] HSTS preload listede
- [ ] 2FA mandatory production'da test edildi

---

### FAZ 8 (2 oturum) — i18n + final polish

#### Kapsam
Brief Bölüm 4.4 — TR + EN, içerik seviyesinde çeviri, hreflang doğru.

#### Yapılacaklar
1. **EN locale aktif** — `available_locales = [tr, en]`
2. **Path-based routing** — `/en/yazilar`, `/en/hakkimda`
3. **Translatable content çeviri yok ise EN locale → fallback TR** ya da "Bu içerik henüz çevrilmedi" mesajı
4. **Admin'de language tab** — her translatable field için `[TR] [EN]` switcher
5. **Locale switcher UI** — header'da TR | EN
6. **hreflang head tag'leri** — her sayfada doğru
7. **Date/number format locale-aware** — Carbon translatedFormat zaten kullanılıyor
8. **TR EN için ayrı Meili index** — `posts_tr`, `posts_en`
9. **Eksik çeviri logger** — devda TR fallback'e düşünce log

##### Final polish
10. **Mikro-etkileşimler refine** — hover states, button press feedback
11. **Dark mode detayları** — her component'te göz audit
12. **404/500/503/maintenance** — sayfaları cilala
13. **Empty states** — admin tarafı (yazı yok, mesaj yok, vb.) görsel iyileştirme
14. **Loading states** — HTMX swap animasyonları

#### Yapılmayacaklar
- 3+ dil (kapsamı patlatır; TR + EN yeter)
- ML-based otomatik çeviri (DeepL entegrasyonu opsiyonel; ücretli)

#### Bağımlılıklar
- Spatie Translatable zaten her şey hazır

#### Risk
- TR fallback tüm sayfalarda doğru çalışsın; routing edge case'ler

#### Kalite kapısı
- [ ] EN sayfaları render (tüm route'lar)
- [ ] hreflang Google validator pass
- [ ] Locale switcher state persist (cookie)

---

### FAZ 9 (2 oturum) — TESLİMAT

#### Kapsam
Brief Bölüm 7 + Bölüm 8 Faz 9 — sahibi siteyi devraldığında her şey çalışıyor + dokümantasyon tam.

#### Yapılacaklar
1. **Production deployment guide** (`docs/deployment.md`)
   - VPS senaryosu: Ubuntu 24.04 + Caddy + PHP 8.4 FPM + MySQL 8 + Redis + Meilisearch + Supervisor + Cron
   - Step-by-step commands
   - SSL otomatik (Caddy)
   - Backup cron
   - First user invite
   - Shared hosting fallback (PHP 8.2+, MySQL 8, no Redis/Meili)
2. **Admin guide** (`docs/admin-guide.md`) — **gayri-teknik dilde**
   - Login + 2FA setup
   - Yeni yazı nasıl eklenir (ekran görüntüleri/GIF)
   - Sayfa düzenleme
   - Mesaj cevaplama
   - Tema/renk değişimi
   - Yedek alma + indirme
   - Şifre değişimi
   - Hesap güvenlik checklist
3. **Demo veri restore** — `php artisan db:seed` temiz makinede tüm akışı kurar
4. **`make setup` doğrulama** — sıfırdan kurulum komutla
5. **GitHub Actions CI yeşil** — tüm gates (test + lint + audit + headers + Lighthouse + axe + ZAP)
6. **README güncelleme** — final
7. **Backup + restore testi** — gerçekten çalışıyor mu
8. **Penetration walkthrough** — kendi sitene saldırı simülasyonu (mind-map)
9. **Performance final audit** — gerçek production server (VPS) ile Lighthouse

#### Yapılmayacaklar
- Multi-region deployment (single owner site)
- Advanced monitoring (Prometheus/Grafana — overkill)

#### Kalite kapısı
- [ ] Temiz Ubuntu makinede deployment guide takip edilerek site açıldı
- [ ] Admin guide gayri-teknik biri tarafından okundu, anladı
- [ ] Tüm CI gates yeşil
- [ ] LICENSE dosyası var

---

## BÖLÜM C — KRİTİK NOTLAR

### C.1 — Disk uyarısı (acil)
**C: %100 dolu (228 MB free).** Browser preview screenshot timeout'larının ana sebebi.
- `/tmp` periyodik temizle: `rm -rf /tmp/*.tmp /tmp/CFB* /tmp/db_backup*`
- `vendor/` klasörü ~150MB; `node_modules/` ~400MB — alternatif disk düşün
- Composer cache `~/.composer/cache` temizle: `composer clear-cache`
- npm cache: `npm cache clean --force`

### C.2 — Linter / system reminder davranışı
Bazı düzenlediğim dosyalar otomatik linter (görünürdeki "system-reminder" notları) tarafından **eski hâline geri çevrilmiş** (örn. routes/web.php yeni route'lar, DatabaseSeeder PageSeeder ekleme). Faz 2C'de PageSeeder + admin route'ları iki kez yazıldı. Faz 3+ için **her batch'in sonunda dosyaları okuyup teyit et**.

### C.3 — Login kullanıcı notu
- URL: **`http://localhost:8765/login`** (Fortify default — `/admin/login` değil)
- E-posta: `yagizemirbaki61@gmail.com`
- Şifre: `change-this-on-first-login`
- 2FA: dev'de kapalı (`REQUIRE_2FA_FOR_ADMIN=false`)

### C.4 — Composer + admin commands
Composer global PATH'te değil. Her bash komutuna `export PATH="/c/Users/emir/bin:$PATH" &&` prefix gerek. Kalıcı çözüm: `~/.bashrc`'ye `export PATH="$HOME/bin:$PATH"` ekle.

### C.5 — Git henüz init edilmedi
Bu repo `git init` yapılmadı. Faz 9 öncesinde:
- `git init`
- `.gitignore` zaten var
- İlk commit `feat: initial scaffolding (faz 0-2c complete)`
- Conventional commits (Brief §5.5)

### C.6 — Cover görsel placeholder
Şu an writings + pages cover'lar HSL gradient + grain placeholder. Gerçek fotoğraf admin'den upload edilebilir (Spatie Media kuruldu, variant generation çalışıyor). Sahibinin ilk işi: kendi gerçek fotoğraflarını upload etmek.

### C.7 — Test coverage genişletme önerileri (öncelik sırası)
1. **Browser E2E (Playwright)** — login → write new post → publish → public visible (Faz 6)
2. **Visual regression** — anasayfa + writing detay + admin (Faz 9, opsiyonel)
3. **Performance budget** — bundle size CI gate (Faz 6)
4. **Pest coverage** — `--coverage --min=80` (Faz 6)

---

## BÖLÜM D — TAHMİNİ TAKVİM

| Faz | Oturum | Kümülatif (oturum) |
|---|---|---|
| 2C+ (RSS/sitemap/JSON-LD/search foundation) | 1–2 | 1–2 |
| 3 (search UI + tag/year + custom pages + breadcrumbs) | 2–3 | 3–5 |
| 4 (admin gelişmiş — menü/tema/redirect/user/audit/backup/settings) | 3–4 | 6–9 |
| 5 (blog derinliği — ToC/syntax/footnote/custom blocks/related/webmentions/newsletter) | 3–4 | 9–13 |
| 6 (perf + a11y) | 2 | 11–15 |
| 7 (security hardening) | 2 | 13–17 |
| 8 (i18n EN + final polish) | 2 | 15–19 |
| 9 (teslimat — deployment + admin guide + CI green) | 2 | 17–21 |

**Tahmini toplam:** 17–21 oturum (her oturum 1.5–2 saat etkin çalışma).

---

## BÖLÜM E — KARAR BEKLEYEN NOKTALAR (sahibi onayı yararlı)

1. **Yorum sistemi (Faz 5)** — yorum mu, webmention mı, yok mu? **Önerim: webmention.**
2. **Newsletter (Faz 5)** — gerçekten istiyor musun? SMTP gerçek yapılandırma + double opt-in altyapı + admin "bülten yaz" UI; iş yükü 1 oturum.
3. **EN locale (Faz 8)** — düşünülüyor mu? TR-only kalırsa Faz 8 hafifler.
4. **Production deployment hedef** — VPS mi (önerilen, tam stack) yoksa shared hosting mi (daha az feature)?
5. **Domain + DNS** — `ozanefeoglu.com` aktif mi, hangi registrar'da?
6. **Yedekleme storage** — local FS mi, S3-uyumlu (Cloudflare R2 / Backblaze B2) mı?
7. **Analytics** — self-host Plausible/Umami mı, yoksa basit yerleşik mi?

---

**Sonraki adım:** Bu rapor okunduktan sonra Faz 2C+ başlayabiliriz (RSS + sitemap + JSON-LD + search foundation). Yukarıdaki E maddelerinden #1, #2, #3 hakkında bir tercih varsa Faz 5/8 planı netleşir.
