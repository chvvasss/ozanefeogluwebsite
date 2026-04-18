# ADR-009 — Cache & Queue & Session: Redis/Valkey öncelikli, file fallback

## Status
Accepted — 2026-04-18

## Context
Üç ayrı concern, ama tipik olarak aynı altyapıda:
- **Cache:** Page cache (anonim GET), fragment cache, query cache, settings cache.
- **Queue:** Image conversion, search index update, mail send, sitemap regen, RSS regen, newsletter send.
- **Session:** Login state, CSRF token, flash mesajlar.

Adaylar:
- **Redis 7+** veya **Valkey** (Redis fork, BSD, daha "açık" governance)
- **Memcached** (eski, sadece cache, queue/session yok)
- **DragonflyDB** (modern alternatif)
- **File-based** (Laravel default)
- **DB-backed** (Laravel default, queue+session için)

## Decision

### Production (VPS)
- **Cache: Redis/Valkey** — page cache + fragment cache + Eloquent query cache.
- **Queue: Redis/Valkey** — Laravel queue connection `redis`, supervisor altında 2 worker (default + image processing high-prio).
- **Session: Redis/Valkey** — DB session'a göre çok daha hızlı.

### Production (shared hosting fallback)
- **Cache: file** (Laravel `cache.stores.file`)
- **Queue: database** (sync olabilir; ama eventual consistency için en az database queue + cron `queue:work --once`)
- **Session: file**

Karar: tek `.env` toggle (`CACHE_STORE`, `QUEUE_CONNECTION`, `SESSION_DRIVER`) ile geçişlidir.

## Consequences

### Pozitif
- **Redis/Valkey VPS'de standart:** Çoğu image (Ubuntu 24.04 + apt + redis-server) tek komutla.
- **Hızlı:** Page cache hit microseconds.
- **Tek altyapı, üç concern:** Operational footprint küçük.
- **Valkey BSD:** Redis Inc.'in license değişikliğine (RSAL/SSPL) cevaben Linux Foundation forkı; vendor risk yok.

### Negatif / Trade-off
- **Persistence:** Default Redis volatile. Session ve queue için RDB snapshot + AOF açık olmalı (`appendonly yes`). Konfigürasyon item'ı (deploy doc'ta).
- **Memory limit:** `maxmemory` set + `allkeys-lru` policy default. Cache eviction stratejisi.
- **Network port:** Redis 6379 sadece localhost (`bind 127.0.0.1`). Asla public.

### Risk
- **Redis çökerse:** Session reset (kullanıcılar logout), queue içindeki job'lar AOF'tan recover. Karşı önlem: AOF + RDB hybrid persistence + monit/systemd auto-restart.
- **Cache stampede:** Aynı key cache miss olursa 100 request DB'ye gider. Karşı önlem: `Cache::lock()` + `remember()` deduplication.

## Cache stratejisi detay

### Page cache (anonim GET)
- **Anahtar:** `page:{locale}:{path}:{cookie_hash}` — cookie hash login durumunu ayırır.
- **TTL:** Public page 1 saat default, RSS 15 dk, sitemap 1 saat.
- **Invalidation:** Tag-based. `posts` tag'li tüm cache `Cache::tags(['posts'])->flush()` post yayınlandığında.
- **Anonim only:** `auth()->check()` veya `request()->cookie('admin_*')` varsa cache bypass.
- **Header:** `Cache-Control: public, max-age=3600, stale-while-revalidate=86400`.

### Fragment cache
- Hero featured items, etiket bulutu, recent posts widget.
- TTL 1 saat, tag invalidation.

### Query cache
- Sık tekrarlanan lookup: `Setting::all()`, `Category::tree()`, `Tag::popular()`.
- TTL 5-15 dk.

### Settings cache
- `Setting::cached('site_title')` — `forever()` cache, observer'da `Cache::forget()` write'da.

## Queue stratejisi

### Connections
```php
// config/queue.php
'connections' => [
    'redis' => ['driver' => 'redis', 'queue' => 'default', 'retry_after' => 90],
    'database' => ['driver' => 'database', ...],  // shared hosting fallback
],
```

### Queue tiers (priority)
- `high` — image conversion (kullanıcı admin'de bekliyor); süre kritik.
- `default` — sitemap regen, search index update, audit log async write.
- `low` — newsletter bulk send (saatlerce sürebilir; düşük öncelik).

### Worker config (supervisor)
```ini
[program:laravel-worker-default]
command=php artisan queue:work redis --queue=high,default,low --tries=3 --backoff=60 --max-time=3600
numprocs=2
autostart=true
autorestart=true
user=www-data
```

### Failed jobs
- `failed_jobs` table (Laravel default). Admin panel "Failed jobs" sekmesinde görünür; manuel retry/forget.

## Session stratejisi
- Driver: Redis (production), file (fallback).
- Cookie: `__Host-session`, Secure, HttpOnly, SameSite=Strict.
- Lifetime: idle 30 min, absolute 8 hours (admin), 7 days (newsletter unsubscribe link sessionsız).
- Regenerate: login, password change, 2FA enable.

## Alternatives Considered

### Memcached
- **Pro:** Çok hızlı, basit.
- **Con:** Sadece cache. Queue + session için ayrıca Redis/DB lazım. İki sistem; gerek yok.
- **Karar:** Reddedildi.

### DragonflyDB
- **Pro:** Multi-thread, daha hızlı.
- **Con:** Ekosistem ve hosting paket desteği daha az olgun.
- **Karar:** Şimdilik reddedildi (Valkey daha güvenli seçim). Gelecekte yer değiştirilebilir (Redis API uyumlu).

### Tüm queue DB-backed (Redis yok)
- **Pro:** Bir bağımlılık az.
- **Con:** Queue throughput sınırlı (DB lock contention), session DB write yükü.
- **Karar:** Sadece fallback.

### Horizon (Laravel queue dashboard)
- **Pro:** Güzel queue UI.
- **Con:** Redis-only, ekstra paket. Bizim ölçek için overkill — basit "Failed jobs" admin sayfası yeter.
- **Karar:** Reddedildi (ilk lansman); gelecekte değerlendirilebilir.

## References
- Laravel cache: https://laravel.com/docs/cache
- Laravel queue: https://laravel.com/docs/queues
- Valkey: https://valkey.io/
- Cache stampede mitigation: https://en.wikipedia.org/wiki/Cache_stampede

## İlgili ADR'lar
- ADR-001: Laravel framework (cache/queue/session abstraction)
- ADR-007: Media (variant generation queued)
- ADR-008: Search (re-index queued)
