# ADR-010 — Auth & 2FA: Laravel Fortify + TOTP (RFC 6238) + Recovery Codes + HIBP

## Status
Accepted — 2026-04-18

## Context
Brief Bölüm 5.1 ve 4.3 (Admin) gereksinimleri:
- **Password hash:** Argon2id (PASSWORD_ARGON2ID).
- **2FA:** TOTP, ayarlardan zorunlu yapılabilir. Recovery codes.
- **Password policy:** NIST 2024 (uzunluk öncelikli, zorunlu karakter sınıfı yok). HIBP Pwned Passwords k-anonymity check.
- **Session yönetimi:** Login regenerate, idle + absolute timeout, aktif oturumları admin'den kapatma.
- **Brute-force koruması:** Rate limit + lockout.
- **Audit log:** Login, logout, failed login, password change, 2FA enable/disable.
- **Multi-user (gelecek):** Roller — Super Admin, Admin, Editor, Contributor, Viewer.

## Decision

### Auth foundation
**Laravel Fortify** — headless auth backend; UI biz yazıyoruz (Blade).
- Login, registration (registration kapalı default — sadece super-admin yeni user oluşturur), password reset, email verification, 2FA — Fortify hazır verir.
- **No Jetstream/Breeze:** Onlar UI getiriyor; biz custom (ADR-005 tarzı) tema istiyoruz.

### Password policy
- **Minimum 12 karakter** (NIST SP 800-63B § 5.1.1.2).
- **Maksimum 128** (DoS önlemi).
- **Karakter sınıfı zorunlu değil.**
- **HIBP Pwned Passwords k-anonymity API check:** Kayıt + şifre değişimde, ilk 5 karakter SHA-1 hash → API → response içinde tam hash var mı? Varsa reddet, "bu şifre veri sızıntılarında görülmüş" mesajı.
- **Common password list local:** En sık 10000 şifre lokal listede (offline hızlı kontrol).
- **Rate limit HIBP API:** Network bağımlı; cache hit lokal'de 30 gün.

### 2FA (TOTP — RFC 6238)
- **Library:** `pragmarx/google2fa-laravel` + `bacon/bacon-qr-code`.
- **Setup akışı:** Profile → "Enable 2FA" → secret generate (server-side, base32) → QR (otpauth URI) → user kodu girer + onaylar → enable.
- **Recovery codes:** 8 adet single-use (32 char hex) — şifrelenmiş DB'de (`encrypted` cast). Setup tamamlandığında bir kez gösterilir.
- **TOTP window:** ±1 step (30s) — clock drift toleransı.
- **Replay protection:** Son kullanılan code DB'de cache (key: `2fa:used:{user_id}:{code_hash}`, TTL 90s).
- **Zorunluluk:** `Settings::require_2fa_for_admin = true` toggle. Açıksa Admin/Super Admin login sonrası 2FA enable etmeden hiçbir admin sayfaya erişemez.

### Brute-force koruması
- **Login throttle:** `throttle:5,1` (5 deneme / dakika / IP+username kombinasyonu). 6. attempt → 429 + "5 dakika sonra dene" mesaj + Retry-After header.
- **Account lockout:** 10 başarısız attempt / 24sa → hesap 15 dakika kilitli. Admin manual unlock.
- **Generic error message:** "Geçersiz e-posta veya şifre" — kullanıcı enumeration önleme.
- **Timing-safe compare:** Laravel hash::check zaten sabit zaman.

### Session hardening
- **Cookie:** `__Host-session`, Secure, HttpOnly, SameSite=Strict.
- **Driver:** Redis (ADR-009).
- **Regenerate:** `Auth::login` sonrası, password change, 2FA enable, role change.
- **Idle timeout:** 30 dakika (admin), 8 saat absolute.
- **Active sessions UI:** Profile → "Sessions" → device, IP, last active, "Sign out" button (her oturumu remote close).
  - DB'de `sessions` tablosu (Laravel default) Redis kullanırken aktif değil; `personal_access_tokens` muadili `user_devices` tablosu tutarız (login sırasında insert, logout/expire sırasında delete).

### Login flow (sıralı)
```
POST /admin/login
  → throttle middleware
  → validate (email, password)
  → check user exists + not locked
  → bcrypt/argon2id verify (constant time)
  → if fail: log + counter++ + 401
  → if success:
      → check 2FA enabled
        → no: if require_2fa → redirect setup; else login OK
        → yes: redirect /admin/2fa challenge
  → 2FA challenge POST:
      → throttle 5/1
      → verify TOTP or recovery code
      → if fail: log + 401
      → if success:
        → session_regenerate_id
        → audit log "login"
        → redirect intended URL
```

### Password reset
- **Token:** `random_bytes(32)` hex, hashed in DB (Laravel default).
- **Lifetime:** 60 dakika.
- **Email link:** signed URL `/admin/password/reset/{token}?email=`.
- **Single-use:** kullanılınca delete.
- **Throttle:** `throttle:3,15` (3 reset/15 dk).

### Audit log entries
| Event | Logged data |
|---|---|
| `login.success` | user_id, ip, ua, device_id |
| `login.failed` | email_attempted, ip, ua, reason (`bad_password`, `unknown_user`, `locked`, `2fa_failed`) |
| `logout` | user_id, ip |
| `password.change` | user_id, ip |
| `password.reset.requested` | email, ip |
| `password.reset.completed` | user_id, ip |
| `2fa.enabled` | user_id |
| `2fa.disabled` | user_id, by_user_id |
| `2fa.recovery_used` | user_id, code_index |
| `role.changed` | user_id, by_user_id, before, after |
| `user.created`, `user.deleted` | actor + target |

Spatie Activity Log paketi kullanılır.

## Consequences

### Pozitif
- **Standartlara uygun:** OWASP ASVS V2, NIST SP 800-63B, RFC 6238 — çerçeve içinde.
- **Fortify olgun:** Laravel resmi, sürekli güncel.
- **Defense in depth:** Throttle + lockout + 2FA + HIBP + audit — birden fazla katman.
- **No external auth provider lock-in:** OAuth opsiyonel olarak gelecekte (gerekirse) eklenebilir; ama bu single-tenant sahibinin sitesi, bu kadar yeter.

### Negatif / Trade-off
- **HIBP API bağımlılığı:** Network gerektirir. Mitigation: cache + offline fallback (yaygın 10000 şifre listesi).
- **2FA UX friction:** Sahibinin telefonu yanında olmazsa giriş yapamaz. Mitigation: recovery codes + admin "set bypass" emergency procedure (doc'ta).
- **Recovery code yönetimi:** Tek seferlik gösterim; sahibi unutursa? Mitigation: setup'ta "Lütfen güvenli yere kaydedin" zorunlu confirm checkbox + admin-side regenerate (eski codeları invalidate eder).

### Risk
- **2FA secret leak:** DB encrypted ama backup'ta accidentally plaintext'e dönüşmesin. Mitigation: backup öncesi encrypted-at-rest (Spatie Backup encryption support).
- **Throttle bypass via IP rotation:** Bot proxy farm'ları. Mitigation: account-level lockout (IP'den bağımsız).

## Alternatives Considered

### Laravel Breeze / Jetstream
- **Pro:** UI hazır.
- **Con:** UI ile geliyor — bizim custom design system'a uymuyor.
- **Karar:** Reddedildi.

### WebAuthn (passkeys) only
- **Pro:** Modern, phishing-proof.
- **Con:** Single-tenant sahibinin telefonu/cihazı kaybolursa fallback gerekir; passkey ekosistemi kullanıcı için confusing.
- **Karar:** Şimdilik reddedildi. Gelecekte 2FA yerine veya yanına eklenebilir (`web-auth/webauthn-lib`).

### SMS 2FA
- **Pro:** Tanıdık.
- **Con:** SIM swap saldırıları, SMS interception. NIST SP 800-63B SMS'i deprecated kabul eder.
- **Karar:** Reddedildi.

### Email link / magic link (passwordless)
- **Pro:** UX iyi.
- **Con:** Mail account kompromize ise hesap kompromize. Admin için yetersiz.
- **Karar:** Reddedildi (admin'de). Newsletter unsubscribe gibi non-auth contextlerde OK.

### Sosyal login (Google/GitHub OAuth)
- **Pro:** Kolay.
- **Con:** Single-tenant — sahibi tek admin. OAuth provider lock-in (provider devre dışı kalırsa kapı kapanır). Ek attack surface.
- **Karar:** Reddedildi.

## References
- NIST SP 800-63B Digital Identity Guidelines: https://pages.nist.gov/800-63-3/sp800-63b.html
- HIBP Pwned Passwords API: https://haveibeenpwned.com/API/v3#PwnedPasswords
- RFC 6238 TOTP: https://datatracker.ietf.org/doc/html/rfc6238
- Laravel Fortify: https://laravel.com/docs/fortify
- OWASP ASVS V2 Authentication: https://owasp.org/www-project-application-security-verification-standard/

## İlgili ADR'lar
- ADR-005: Admin yaklaşımı (custom UI)
- ADR-009: Cache & queue (session driver)
