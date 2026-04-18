# Faz 1 — Foundation Blueprint

> Faz 0'ın çıktılarını **uygulamaya** köprüleyen plan. Faz 1 başladığında bu doküman adım adım takip edilir.

## 1. Hedef
Çalışan bir Laravel iskeleti, design system'i CSS'e işlenmiş, auth + 2FA tamam, admin shell (boş ama gezilebilir) ayakta. Public anasayfa boş bir layout ile döner. CI yeşil.

**Kapanış kriteri:** `make setup` temiz makinede başarıyla çalışır. Login → 2FA setup → admin dashboard akışı end-to-end çalışır. Lighthouse anasayfa Performance ≥ 95.

## 2. Adım listesi (sıralı)

### 2.1 Laravel iskeleti
```bash
composer create-project laravel/laravel:^12.0 . --prefer-dist
# (boş dizinde olduğumuz için . hedef; mevcut dosyalar conflict'te skip)
# Sonra:
composer require laravel/fortify
composer require spatie/laravel-permission
composer require spatie/laravel-activitylog
composer require spatie/laravel-translatable
composer require spatie/laravel-medialibrary
composer require spatie/laravel-sluggable
composer require spatie/laravel-tags
composer require spatie/laravel-backup
composer require pragmarx/google2fa-laravel
composer require bacon/bacon-qr-code
composer require intervention/image
composer require meilisearch/meilisearch-php

composer require --dev pestphp/pest pestphp/pest-plugin-laravel
composer require --dev larastan/larastan
composer require --dev laravel/pint

php artisan vendor:publish --tag=fortify-config
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider"
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

### 2.2 NPM
```bash
npm init -y
npm install -D vite laravel-vite-plugin tailwindcss @tailwindcss/vite
npm install -D eslint stylelint stylelint-config-standard prettier
npm install alpinejs htmx.org
npm install @tiptap/core @tiptap/starter-kit @tiptap/extension-image @tiptap/extension-link @tiptap/extension-code-block-lowlight lowlight
```

### 2.3 PHP / Vite config
- `php.ini` (lokal): `memory_limit=512M`, `upload_max_filesize=20M`, `post_max_size=22M`.
- `vite.config.js`: ADR-012'deki taslağa göre.
- `tailwind.config.js`: Yok — Tailwind v4 `@theme` direktifi yeterli.
- `resources/css/app.css`: design tokens işle (design-tokens.md).

### 2.4 Migrations (Faz 1 minimum)
```
2026_04_18_000001_create_settings_table.php
2026_04_18_000002_extend_users_table.php       (role, two_factor, audit fields)
2026_04_18_000003_create_audit_logs_table.php   (Spatie default + customizations)
2026_04_18_000004_create_user_devices_table.php (active sessions)
2026_04_18_000005_seed_super_admin.php          (data: ENV'den email + temp password + force-reset flag)
```

Faz 2'de gelecek:
- `posts`, `projects`, `pages`, `categories`, `tags`, `taggables`, `media`, `media_collections`, `redirects`.

### 2.5 Models + Policies
- `User` — HasRoles, HasMediaTrait (avatar), HasActivityLog. Methods: `requiresTwoFactor()`, `hasUnusedRecoveryCodes()`.
- `Setting` — key-value, JSON value, cached.
- `UserDevice` — current login devices.

### 2.6 Routes
```
GET  /                       Landing (boş layout)
GET  /admin/login            Login form
POST /admin/login            Login submit (throttle:5,1)
POST /admin/logout
GET  /admin/2fa              2FA challenge
POST /admin/2fa
GET  /admin/2fa/setup        2FA enrollment (auth only)
POST /admin/2fa/setup
POST /admin/2fa/disable
GET  /admin                  Dashboard (auth + 2fa middleware)
GET  /admin/profile
GET  /admin/profile/sessions
POST /admin/profile/sessions/{id}/destroy
GET  /admin/audit-log
GET  /health                 Health check (public)
```

### 2.7 Middleware stack
- `web` group: cookie, session, csrf, set-locale.
- `admin` group: auth, ensure-2fa, throttle:60,1.
- Global: enforce-https (production), set-security-headers, request-id.

### 2.8 Blade layouts (placeholder)
- `layouts/app.blade.php` — public site layout.
- `layouts/admin.blade.php` — admin shell (sidebar, topbar, content).
- Component'ler: `<x-button>`, `<x-input>`, `<x-card>`, `<x-empty-state>`, `<x-alert>`.

### 2.9 Tests (Faz 1 kapsamı)
- `tests/Feature/Auth/LoginTest.php` — başarılı/başarısız login, throttle, 2FA flow.
- `tests/Feature/Auth/PasswordResetTest.php`.
- `tests/Feature/Auth/TwoFactorTest.php` — setup, challenge, recovery code.
- `tests/Feature/Admin/AccessControlTest.php` — viewer/editor/admin granular access.
- `tests/Unit/HibpCheckTest.php`.
- `tests/Feature/HealthCheckTest.php`.

### 2.10 CI
`.github/workflows/ci.yml` (zaten draft) — Faz 1'de çalışır hale getir, badge README'ye.

## 3. Faz 1 kapanış checklist (bu liste karşılanmadan Faz 2'ye geçilmez)

### Genel
- [ ] `composer audit` + `npm audit` temiz.
- [ ] PHPStan level 8 temiz.
- [ ] Pint + ESLint + Stylelint temiz.
- [ ] `make setup` temiz makinede başarılı.
- [ ] `make test` tüm testler yeşil.
- [ ] CI pipeline yeşil.

### Güvenlik kapısı
- [ ] Tüm route'ların auth + policy kapsamı doğrulandı (test ile).
- [ ] Login flow: throttle, lockout, 2FA, generic error mesajı, audit log girdileri.
- [ ] Session: HttpOnly + Secure + SameSite=Strict, Redis backed, regenerate on login.
- [ ] CSRF: middleware aktif, test ile doğrulandı (csrf-mismatch 419 dönmeli).
- [ ] Security headers tüm response'larda var (HSTS, CSP nonce, COOP, CORP, vb.).
- [ ] `.env` gitignore, secret commit'lenmemiş (gitleaks pass).
- [ ] HIBP check çalışıyor (mock ile test).

### Performans kapısı
- [ ] Lighthouse anasayfa: Perf ≥ 95, Best Practices 100, A11y 100, SEO 100.
- [ ] LCP < 2.5s, CLS < 0.1, INP < 200ms (lab).
- [ ] Critical CSS inline (FOUC yok).
- [ ] Font preload + font-display swap.
- [ ] Public bundle < 25KB CSS, < 15KB JS gzipped.

### Erişilebilirlik kapısı
- [ ] axe-core temiz (anasayfa + login).
- [ ] Klavye-only ile login flow tamam (tab, shift-tab, enter, escape).
- [ ] Focus state'ler görünür.
- [ ] Skip link var.
- [ ] `prefers-reduced-motion` saygılı.

### Tasarım kapısı
- [ ] Design tokens (design-tokens.md) CSS'e tam işlendi.
- [ ] Light/dark mode toggle çalışıyor; FOUC yok (inline pre-hydration script).
- [ ] Login + dashboard mobile (320, 375, 768, 1024, 1440) sınanmış.
- [ ] Empty/error/loading state component'leri var.

### Admin UX kapısı
- [ ] Login error mesajları Türkçe, anlaşılır.
- [ ] 2FA setup adım adım rehberli (QR + manual entry + recovery codes).
- [ ] Recovery codes setup'ta zorunlu confirm checkbox + indir/yazdır.
- [ ] Active sessions sayfasından sign-out çalışıyor.

### Doc
- [ ] README "Setup" bölümü doğrulanmış.
- [ ] `docs/admin-guide.md` ilk taslak (login + 2FA + password reset bölümleri).
- [ ] Yeni ADR'lar eklenmedi (varsa) — Faz 1 sırasında ortaya çıkan kararlar dosyaya geçer.

## 4. Faz 1 sonrası rapor formatı

Faz 1 sonunda kullanıcıya sunulacak özet:
1. **Yapıldı:** ne çalışıyor (ekran görüntüleri / GIF demo).
2. **Atlandı + neden:** kapanış checklist'inden sapma.
3. **Öne çıkan kararlar:** Faz 1 sırasında verilen ek karar(lar) → ADR olarak.
4. **Faz 2 plan:** içerik core'a giriş.
