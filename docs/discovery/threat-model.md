# Tehdit Modeli — STRIDE

> Brief Bölüm 5.1 (Güvenlik) ve OWASP ASVS Level 2 hedefiyle uyumlu. Her tehdit için **kontrol** alanı, hangi katmanda nasıl önlendiğini söyler.

## 1. Sistem sınırları

```
                ┌─── Güvensiz bölge ───┐
                │                      │
   Anonim       │      Kayıtlı         │   Admin
   ziyaretçi ──┼──── kullanıcı  ──────┼─── operatör
                │                      │
                └──────────────────────┘
                          │
                  ┌───────┼───────┐
                  │   Web Edge    │      ← TLS, WAF, rate limit
                  │  (Caddy/CF)   │
                  └───────┬───────┘
                          │
                  ┌───────┼───────┐
                  │  PHP/Laravel  │      ← Middleware, validation, authz
                  │  (app server) │
                  └───┬───┬───┬───┘
                      │   │   │
                  ┌───┘   │   └───┐
                  ▼       ▼       ▼
              ┌──────┐ ┌─────┐ ┌──────┐
              │ DB   │ │Redis│ │ FS   │  ← prepared queries, file perms
              └──────┘ └─────┘ └──────┘
```

**Trust boundaries:** Browser↔Edge, Edge↔App, App↔Data. Her sınırda input validate + output encode.

## 2. STRIDE — kategori bazlı

### 2.1 Spoofing (kimlik sahtekarlığı)

| Tehdit | Senaryo | Kontrol |
|---|---|---|
| Login brute-force | Saldırgan admin şifresini sözlük saldırısıyla dener | Rate limit (login endpoint, IP + username başına), failed attempt log, account lockout (5 yanlış → 15 dk), CAPTCHA opsiyonel |
| Credential stuffing | Başka servisten sızan listeyle dener | HIBP Pwned Passwords k-anonymity API check (kayıt + şifre değişim), 2FA zorunlu admin için |
| Session hijacking | XSS veya MITM ile cookie ele geçirme | `Secure`, `HttpOnly`, `SameSite=Strict`, `__Host-` prefix; HSTS preload; oturum regenerate (login + privilege change), absolute timeout (8sa), idle timeout (30dk) |
| Session fixation | Saldırgan kendi session ID'sini kurbana yutturur | Login öncesi/sonrası `session_regenerate_id(true)` (Laravel default) |
| 2FA bypass | TOTP kodunu çalma / replay | TOTP süresi 30s, replay protection (kullanılan kod tekrar geçmez), recovery code single-use |
| OAuth / social impersonation | Yok (kapsam dışı, social login planlanmıyor) | N/A |

### 2.2 Tampering (veri tahrifi)

| Tehdit | Senaryo | Kontrol |
|---|---|---|
| SQL injection | URL/form parametresinde SQL fragment | **Eloquent + prepared statements her yerde**. Raw query yok; istisna gerekirse `DB::statement` + parameter binding zorunlu, asla string concat |
| Mass assignment | `User::create($request->all())` ile `is_admin=true` injection | Form Request validation + `$fillable` whitelist, `$guarded = ['*']` default |
| CSRF | Saldırgan sayfasından istek tetiklenir | Laravel `VerifyCsrfToken` middleware (default), SPA olmadığı için `SameSite=Strict` ek katman |
| File upload tampering | PHP shell `.jpg.php` upload | MIME whitelist + extension whitelist + magic byte kontrol + image re-encode (Intervention) + uploads dizini PHP exec'siz (Nginx `location ~* \.php$ { deny all; }` + `.htaccess`) + random filename |
| Path traversal | `../../../etc/passwd` | File path'leri kullanıcı input'undan oluşturma; whitelist + Laravel Storage facade (basename + scope) |
| Parameter pollution | `?id=1&id=2` | Laravel `request()->input('id')` deterministik (son değer), validation rule'lar tip zorlar |
| Mass data update via mass-assign | Eloquent `update($request->all())` | Form Request → validated array, asla raw `$request->all()` model'a |
| Time-of-check / time-of-use | Yetki kontrolü ile aksiyon arasında değişim | DB constraint + transaction + optimistic lock (version column) kritik aksiyonlarda |

### 2.3 Repudiation (inkâr)

| Tehdit | Senaryo | Kontrol |
|---|---|---|
| Admin "ben silmedim" | Audit eksik | **Audit log** her admin yazma aksiyonunda: kim, ne zaman, hangi model, hangi alan değişti (before/after diff). Append-only tablo, sadece super-admin görebilir |
| E-posta gönderimi inkârı | Sahibi spam ile suçlanır | İletişim formu submit log + IP + user-agent + UTC timestamp; outbound email log (transactional) |
| Comment spam atılan kullanıcı tartışması | "Ben yazmadım" | Yorum varsa IP + UA + UTC log, edit history immutable |

### 2.4 Information Disclosure (bilgi sızıntısı)

| Tehdit | Senaryo | Kontrol |
|---|---|---|
| Hata mesajı stack trace | Production'da exception kullanıcıya gider | `APP_DEBUG=false` zorunlu, custom error handler → log + ref ID, generic mesaj kullanıcıya |
| `.env` web'den erişilebilir | Yanlış document root | `public/` web root, `.env` üstte. Defansif: web server `location ~ /\.` deny |
| Git directory exposed | `.git/` web'den okunabilir | `.git/` deploy artifact'a girmez; deploy script atomik, sadece `dist/` |
| Direct object reference | `/admin/posts/42/edit` başka kullanıcının post'una erişim | Policy check her edit/destroy: `$this->authorize('update', $post)` |
| User enumeration | Login form "user yok" vs "şifre yanlış" farkı | Tek mesaj: "Geçersiz kimlik bilgisi", aynı süre (timing-safe compare) |
| Verbose 4xx | API "X table'da Y kolonu yok" | Generic "404 not found" / "422 validation failed" + spesifik field, asla schema sızıntısı |
| `phpinfo()` accessible | Geliştirme kalıntısı | Production'da `disable_functions=phpinfo` + linter rule |
| Backup files (.sql.gz) accessible | Yanlış konum | Backup dizini web root dışında (`storage/backups/`), Laravel route ile auth + admin policy |
| Source map leak | `*.css.map`, `*.js.map` üretimde | Vite build'de production'da source map sadece `.map` ayrı dizine, deploy etme; veya `sourcemap: false` |
| Search auto-complete leak | Drafts arama sonucunda görünür | Search index'e sadece published item'lar yazılır; observer'da `if ($post->status === 'published')` |

### 2.5 Denial of Service

| Tehdit | Senaryo | Kontrol |
|---|---|---|
| HTTP flood | Bot/bot-net | Edge rate limit (Caddy `rate_limit` veya CF), app-level throttle (Laravel `throttle:60,1`) |
| Resource exhaustion (image) | Devasa upload (zip bomb / pixel flood) | Upload boyut limiti (PHP + Nginx + app), image dimension limit (e.g. 12000×12000), memory limit per request |
| ReDoS | Vulnerable regex | Regex'lerde catastrophic backtracking pattern review; user input regex'e gitmez |
| Slow Loris | Yarım açık bağlantılar | Reverse proxy timeout (Caddy default OK), `client_body_timeout 10s` |
| Search index bloat | Spam yorumlar Meili'yi şişirir | Yorum moderasyondan geçmeden index'e gitmez |
| Newsletter abuse | Sahte e-mail subscribe spam | Double opt-in, email-pattern rate limit, captcha |

### 2.6 Elevation of Privilege

| Tehdit | Senaryo | Kontrol |
|---|---|---|
| Contributor → admin | Form'da `role=admin` injection | Mass-assign protection + form request whitelist + sadece admin policy `assignRole` çağırabilir |
| IDOR yetki bypass | `/admin/users/1/promote` direkt çağrı | Policy: `before($user, $ability)` super-admin only kontrolü |
| Stale session yetki güncellemesi | Demote'tan sonra eski session admin haklarıyla devam eder | Policy her request'te DB'den yetki çeker (cached role olduğu için TTL kısa, 60s) veya privilege change'de tüm session invalidate |
| Yedekleme indir endpointi | Auth ama yetki check yok | Specific policy `downloadBackup` super-admin only |
| Bakım modu bypass | `?bypass=1` parametre | Bakım mode whitelist IP-based, parametre yok |

## 3. OWASP Top 10 (2025 versiyonu) eşlemesi

| OWASP kategorisi | Kapsama notu |
|---|---|
| A01 Broken Access Control | Policy + Gate + middleware. Test: her admin route policy eşlemeli |
| A02 Cryptographic Failures | Argon2id (Laravel hash), TLS 1.3, secrets env'de, DB at-rest encryption (hosting opsiyonel) |
| A03 Injection | Eloquent prepared, output context-aware escape, command exec yok (yoksa `escapeshellarg` + whitelist), eval/unserialize user input ile yok |
| A04 Insecure Design | Threat model (bu doc), ASR (attack surface review) Faz 7 |
| A05 Security Misconfiguration | Hardened defaults: `APP_DEBUG=false`, `APP_ENV=production`, security headers middleware, CSP nonce |
| A06 Vulnerable Components | `composer audit` + `npm audit` her PR + Dependabot |
| A07 Identification & Auth Failures | 2FA, password policy, session hardening, account lockout |
| A08 Data Integrity Failures | Composer.lock + package-lock.json commit, integrity hash CI'da |
| A09 Logging & Monitoring | Audit log, Monolog JSON, GlitchTip integration Faz 7 |
| A10 SSRF | Image fetch URL whitelist (sadece izin verilen domains), DNS rebinding awareness, private IP block |

## 4. Yüksek riskli yüzeyler — yoğun bakım

1. **`/admin/login`** — brute-force + credential stuffing. Kontrol: throttle (5/dk/IP, 10/saat/username), HIBP check, 2FA, generic error, log.
2. **Medya upload** (admin + olası comment attachment) — RCE + DoS riski. Kontrol: whitelist + magic + re-encode + size limit + uploads non-exec.
3. **İletişim formu** — spam + email injection. Kontrol: honeypot + rate limit + captcha (eklenebilir) + header injection check (`PHPMailer` veya Symfony Mailer kullanımı zaten kapsar).
4. **Yorum yazma** (Faz 5) — XSS + spam + storage. Kontrol: HTMLPurifier veya allowlist Markdown subset, moderation queue, rate limit.
5. **Search** — kullanıcı sorgusu Meili'ye gider. Kontrol: query length cap, special char escape (Meili tolerant ama yine de), result count cap.
6. **Image proxy / external URL fetch** (eğer varsa: Open Graph image scraper, embed previewer) — SSRF. Kontrol: URL allowlist, DNS resolve + private IP reject, redirect limit, response size limit.
7. **Newsletter** — bulk send, subscriber list. Kontrol: double opt-in, rate-limited send (queue throttle), unsubscribe single-click, list export admin-only audit-logged.

## 5. Sertleştirme planı (Faz 7 detayı, burada özet)

### HTTP başlıkları (her response)
```
Strict-Transport-Security: max-age=63072000; includeSubDomains; preload
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=(), interest-cohort=()
Cross-Origin-Opener-Policy: same-origin
Cross-Origin-Resource-Policy: same-origin
Cross-Origin-Embedder-Policy: credentialless  (sadece gerekirse, asset CDN'i bozarsa kaldır)
Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{NONCE}'; style-src 'self' 'nonce-{NONCE}'; img-src 'self' data: blob: {CDN}; font-src 'self'; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'; object-src 'none'; upgrade-insecure-requests
```

### Cookie defaults
```
session: Secure, HttpOnly, SameSite=Strict, __Host- prefix, Path=/
csrf:    Secure, SameSite=Strict (HttpOnly hayır, JS HTMX okuyacak)
```

### Tarama / test
- **DAST:** OWASP ZAP baseline scan (otomasyon Faz 7).
- **SAST:** Psalm taint mode + PHPStan strict-rules + Larastan.
- **Dependency:** `composer audit`, `npm audit`, Dependabot, Snyk free tier (ops.).
- **Secrets:** `gitleaks` PR'da CI gate.
- **Manuel:** Admin attack surface boyu ile penetration walkthrough self-review (Faz 7).

## 6. Hatırlatma — kapı kontrolü

Her yeni feature için Faz checklist'inde:
- [ ] Bu feature hangi tehdit kategorilerine yüzey ekliyor?
- [ ] Yukarıdaki kontroller bu yüzeyi kapsıyor mu? Kapsamıyorsa: yeni kontrol planla.
- [ ] Bu özellik için bir negative test var mı? (örn: "yetkisiz kullanıcı bunu yapamıyor" testi)
