# ADR-007 — Medya Yönetimi: Spatie Media Library + Intervention Image v3, S3-uyumlu storage opsiyonel

## Status
Accepted — 2026-04-18

## Context
Medya işleme gereksinimleri:
- **Upload akışı:** Drag-drop, progress, alt text zorunlu, kategoriler/koleksiyonlar.
- **Variant generation:** Responsive (320, 640, 1024, 1920w) × format (AVIF + WebP + original fallback) = ortalama 12 derived asset/orig.
- **Re-encoding (güvenlik):** EXIF strip, magic byte verify, double extension koruması.
- **Storage:** Lokal FS dev; production'da S3-uyumlu (Cloudflare R2 / Backblaze B2 / MinIO) opsiyonu.
- **CDN front:** Cloudflare hostname mapping; image transformation isteğe bağlı.
- **Public URL:** Path obfuscation (UUID), guessable değil.

## Decision

### Veri katmanı
**Spatie Media Library 11+** — polymorphic media tablosu, model attach/detach, koleksiyonlar, conversion definitions.

### İşleme katmanı
**Intervention Image 3** (yeni nesil, Imagick veya GD driver) — variant generation, format conversion, EXIF strip.

### Format stratejisi
- **Output formats:** AVIF (modern, küçük), WebP (yaygın), original (JPEG/PNG fallback).
- **Picture element:**
  ```html
  <picture>
    <source type="image/avif" srcset="...">
    <source type="image/webp" srcset="...">
    <img src="..." alt="..." width="..." height="..." loading="lazy" decoding="async">
  </picture>
  ```
- **AVIF for art (illustration, photography), WebP for screenshots, JPEG fallback.**

### Storage stratejisi
- **Dev:** `disk: 'local'`, `storage/app/public` symlinked to `public/storage`.
- **Production seçenek A (default):** `disk: 'local'` + Cloudflare CDN. Origin = sunucumuz, CF cache.
- **Production seçenek B:** `disk: 's3'` (R2/B2/MinIO). Bucket public; CF in front; origin sunucumuza dokunmaz.
- Karar admin → "Settings → Storage" altında. Migration komut: `php artisan media:move-to-s3`.

### Conversion pipeline (queue'da)
Upload → original kaydet → queue job:
1. Magic bytes + MIME verify (`getimagesize` + finfo).
2. Re-encode (Intervention) — EXIF strip + ICC profile keep + max dimension cap (12000×12000).
3. Generate variants: `[320, 640, 1024, 1920]` widths × `[avif, webp]` formats (original size + format de korunur).
4. Upload variants disk'e.
5. `media.conversions_disk_files` kaydı güncelle, observer trigger.

Eğer `imagick` yoksa GD fallback (AVIF GD'de PHP 8.1+ var).

### Path stratejisi
```
storage/app/public/media/{uuid:0:2}/{uuid}/original.jpg
                                          /320.avif
                                          /640.avif
                                          /1024.avif
                                          /1920.avif
                                          /320.webp
                                          /...
```
İlk 2 char prefix (1024 dizin shard) — FS performansı için.

### Security checks
1. **Magic bytes:** `finfo_buffer($firstBytes, FILEINFO_MIME_TYPE)`. Whitelist: `image/jpeg`, `image/png`, `image/webp`, `image/avif`, `image/gif` (no SVG by default — ayrı toggle, sanitize ile).
2. **Extension whitelist:** Aynı liste.
3. **Filename:** UUID, original adı sadece DB'de.
4. **Upload dir non-exec:** Web server `location ~* \.php$ { return 403; }` veya `.htaccess`.
5. **Size limit:** PHP `upload_max_filesize=20M`, Nginx `client_max_body_size 20M`, app validation 20M.
6. **Image bomb:** Dimension cap pre-decode (`getimagesize` width/height check ≤ 12000).
7. **SVG:** İsteğe bağlı; on ise `enshrined/svg-sanitize` zorunlu, script/foreignObject strip.

## Consequences

### Pozitif
- **Olgun ekosistem:** Spatie Media Library 6+ yıl bakımda, geniş topluluk.
- **Polymorphic:** Model'a `interface HasMedia` ekleyip ilişkilendir; Project, Post, Page, ContactMessage hep medya alır.
- **Variant declarative:** `registerMediaConversions()` model methodda; tek yerden yönetilir.
- **Queue-friendly:** Conversion'lar background job, upload bloklamaz.
- **Modern format desteği:** AVIF + WebP — Core Web Vitals LCP'yi yarıya düşürür (yüksek kalite/küçük boyut).

### Negatif / Trade-off
- **Disk space:** Variant generation 10×+ alan yer. Mitigation: `media:cleanup` komutu, kullanılmayan media için soft-delete + 30g sonra purge cron.
- **Imagick gereksinim:** AVIF için modern Imagick (libheif desteğiyle) veya GD 8.1+. Hosting kontrolü gerekli; check command CI'da.
- **First-upload latency:** Variant generation queue'a düştüğü için "ilk gösterim" için fallback original. Çoğu admin durumunda kabul edilir.

### Risk
- **EXIF strip eksiği = privacy leak:** GPS koordinatları, kamera info. Karşı önlem: `Image::orient()->encode()` re-encode default'tur, EXIF kaybolur. Test: feature test "uploaded image has no EXIF GPS".
- **AVIF browser support:** ~95% modern (2026), ama IE/eski Safari için fallback gerekli. Picture element çözer.
- **Image proxy SSRF (gelecekte URL fetch eklersek):** Whitelist + private IP block.

## Alternatives Considered

### Plain Laravel Storage + custom variant logic
- **Pro:** No dep, full control.
- **Con:** Reinventing variant management, conversion definitions, polymorphic relationship — Spatie zaten elinde tutuyor.
- **Karar:** Reddedildi.

### Glide (`league/glide`) on-demand image processing
- **Pro:** URL'de boyut/format param → on-demand variant.
- **Con:** First request slow; cache invalidation karmaşık; signed URL gerekli.
- **Karar:** Yardımcı olabilir ama ana pattern değil. Pre-generation tercih edildi (predictable performance).

### Cloudinary / Imgix (managed)
- **Pro:** Hiçbir şey kurmuyoruz, mükemmel performans.
- **Con:** Vendor lock-in, ücret, gizlilik (third-party'ye media gidiyor).
- **Karar:** Reddedildi.

### Filament Media plugin
- **Pro:** UI hazır.
- **Con:** Filament kullanmıyoruz (ADR-005).
- **Karar:** Reddedildi.

## References
- Spatie Media Library: https://spatie.be/docs/laravel-medialibrary
- Intervention Image v3: https://image.intervention.io/v3
- AVIF support 2026: https://caniuse.com/avif
- Image bomb / DoS prevention: https://owasp.org/www-community/vulnerabilities/Denial_of_Service

## İlgili ADR'lar
- ADR-006: TipTap (image insert from media library)
- ADR-009: Cache & queue (variant generation queued)
