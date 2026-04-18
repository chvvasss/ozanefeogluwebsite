# ADR-015 — Writing Modeli: tek akış + `kind` enum + çoklu yayın ilişkisi

## Status
Accepted — 2026-04-18 (Faz 2 başlangıç)

## Context
Faz 1'in ton kalibrasyonu (ADR-014) "Reports + Column" iki ayrı model yerine **tek "Writing" akışı** sonucuna vardı. İçerik tipi `kind` etiketiyle ayrışacak; URL tek `/yazilar/{slug}` altında.

Faz 2'de bu veri modelini kuruyoruz. Kararlar:
- Translatable alanlar (ADR-011 Spatie Translatable pattern)
- Slug stratejisi (locale başına unique)
- Status machine (draft / scheduled / published)
- Taxonomy: `kind` + `region` (Spatie Tags) + `publications` (ayrı tablo — byline karşılığı)
- Cover görseli: Faz 2B'de Spatie Media Library; geçiş süresince decorative placeholder (hueA/hueB)
- Read time: otomatik hesaplama (body text word count / 200 wpm, round ceil dakika)
- Revizyon geçmişi: Faz 2 scope dışı (3. oturumda eklenir)

## Decision

### `writings` tablosu
| Kolon | Tip | Notlar |
|---|---|---|
| `id` | BIGINT PK | |
| `author_id` | FK users | `onDelete: set null` (kullanıcı silinirse yazı kalır) |
| `kind` | ENUM('saha_yazisi', 'roportaj', 'deneme', 'not') | Tag yerine enum — sabit küme, DB-level integrity |
| `status` | ENUM('draft', 'scheduled', 'published') | Scheduled için cron `writings:publish-scheduled` |
| `published_at` | TIMESTAMP NULLABLE | status=scheduled için gelecek tarih, published için geçmiş |
| `location` | VARCHAR(120) NULLABLE | "Gazze", "İstanbul" — dateline için |
| `title` | JSON | Translatable: `{tr: "...", en: "..."}` |
| `slug` | JSON | Per-locale unique (ADR-011) |
| `excerpt` | JSON | Kısa özet (~2-3 cümle) |
| `body` | JSON | HTML (TipTap output; HTMLPurifier ile sanitize) |
| `read_minutes` | UNSIGNED SMALLINT | Otomatik hesap (save observer) |
| `cover_hue_a` | UNSIGNED TINYINT | 0-255 (HSL hue); decorative placeholder |
| `cover_hue_b` | UNSIGNED TINYINT | 0-255 |
| `is_featured` | BOOLEAN | Hero card için |
| `sort_order` | INT DEFAULT 0 | Admin'den drag-reorder |
| `meta_title` | JSON NULLABLE | SEO override |
| `meta_description` | JSON NULLABLE | |
| `canonical_url` | VARCHAR(255) NULLABLE | Cross-post durumu |
| `created_at`, `updated_at` | TIMESTAMP | |
| `deleted_at` | TIMESTAMP NULLABLE | Soft delete |

### Translatable alanlar (JSON)
`title`, `slug`, `excerpt`, `body`, `meta_title`, `meta_description` — Spatie Translatable `HasTranslations`.
Fallback locale: `tr` (ADR-011).

### Slug
- Per-locale unique check: `where("slug->{$locale}", $value)`
- Otomatik üretim: title'dan Spatie Sluggable, locale bazlı
- Slug değişimi → `redirects` tablosuna otomatik insert (Faz 4'te aktif; şimdilik placeholder)

### Read time
- `Writing::saving` observer: body HTML strip → word count / 200 → ceil → `read_minutes`

### `publications` tablosu
| Kolon | Tip |
|---|---|
| `id` | BIGINT PK |
| `name` | VARCHAR(120) UNIQUE |
| `slug` | VARCHAR(140) UNIQUE |
| `url` | VARCHAR(255) NULLABLE |
| `sort_order` | INT |
| `created_at/updated_at` | |

### `publication_writing` pivot
| Kolon | Tip |
|---|---|
| `writing_id` | FK |
| `publication_id` | FK |
| `link` | VARCHAR(255) NULLABLE — yayın adresindeki URL |

### Status machine
```
draft ──(publish now)──► published
   │                         ▲
   │                         │
   └─(schedule at future)──► scheduled ──(cron)──► published
```
- `draft`: Sadece `auth+can:update` görür (admin preview).
- `scheduled`: `published_at > now()`. Cron (`php artisan writings:publish-scheduled`) her dakika çalışır; `published_at <= now()` olanları `published`'e günceller.
- `published`: Public görür; `published_at` ≤ now + status='published'.

### Public routing
- `/yazilar` — index, sayfalanmış, filtre: `?kind=...&region=...&year=...`
- `/yazilar/{slug}` — detail; slug locale-aware `Writing::whereTranslation('slug', $slug, app()->getLocale())->firstOrFail()`
- `/en/yazilar/...` — i18n EN (Faz 2C)

### Admin routing
- `/admin/writings` — list (filter + search)
- `/admin/writings/create`, `/admin/writings/{id}/edit` — form (TipTap, Faz 2B)
- `/admin/writings/{id}` — destroy (soft delete)

## Consequences

### Pozitif
- Tek model → routing tek, UI tek, arama tek. Faz 3 search entegrasyonunda kolay index.
- `kind` enum DB-level guarantee — admin UI'da dropdown; yanlış kind tagleme imkansız.
- Translatable JSON kolon → FULLTEXT sınırı Meilisearch ile aşılır (ADR-008).
- `publications` ayrı tablo (Spatie Tags içinde değil) — her yayın için logo, URL, sort_order alanı ihtiyacı var.

### Negatif / Trade-off
- Enum migration değişimi `ALTER TABLE MODIFY COLUMN`. Yeni kind eklemek için migration yaz. Mitigation: kind listesi stabil (4 kind, Faz 2'de sabitleniyor).
- Read time auto-calc observer yaparken body HTML her save'de parse ediliyor — yük makul (kısa metin, senkron).

### Risk
- **Slug uniqueness gotcha:** Bir locale'de boş slug bırakılırsa JSON path `where slug->en = null` birden fazla eşleşebilir. Mitigation: validasyonda her locale'de slug zorunlu değil — ama varsa unique olmalı (custom rule).
- **Status consistency:** `published` ama `published_at` geleceğe ayarlı olursa UI karışır. Observer: status değişirken `published_at` eşzamanlı kontrol.

## Alternatives Considered

### İki ayrı model: `Dispatch` + `Column`
- Brief ADR-013 yapısına daha yakın.
- Reddedildi: ADR-014 ton kalibrasyonu sonrası tek akış doğru. `kind` ile filtrelenir; ayrı tablo boşuna karmaşa.

### `kind` Spatie Tags ile
- Esnek (new kind eklemek migration'sız).
- Reddedildi: 4 sabit kind yeter; DB-level enum + UI dropdown daha sağlam.

### Publications Spatie Tags ile
- Minimal setup.
- Reddedildi: yayınların URL/logo/sort_order meta alanları var; tag modeli bunları tutamaz.

## References
- ADR-011 (i18n, translatable)
- ADR-013 (domain pivot)
- ADR-014 (ton kalibrasyonu, tek akış)

## İlgili ADR'lar
- ADR-007: Medya yönetimi (Faz 2B'de cover upload)
- ADR-006: TipTap editor (Faz 2B'de body editor)
