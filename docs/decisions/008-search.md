# ADR-008 — Search: Meilisearch (MySQL FULLTEXT fallback)

## Status
Accepted — 2026-04-18

## Context
Site-geneli arama: yazılar + projeler + sayfalar. Hedefler:
- **Relevance:** Yazım hatası tolere etmeli (typo-tolerance), prefix match (search-as-you-type).
- **Hız:** < 50ms response.
- **TR + EN:** Türkçe karakter doğru tokenize, stop word, Türkçe sıralama.
- **Self-host:** Vendor lock-in yok.
- **Faceting:** Tag + kategori + tip filtresi.
- **Index update:** Yazı yayınlandığında otomatik.

Adaylar:
- **Meilisearch** (Rust, modern, typo-tolerant, faceting, geo opsiyonel)
- **Typesense** (C++, benzer feature, MIT)
- **Algolia** (managed, mükemmel, ücretli + privacy concerns)
- **Elasticsearch / OpenSearch** (overkill, ağır, JVM)
- **MySQL FULLTEXT** (varsayılan-mevcut, ngram parser ile TR makul)
- **SQLite FTS5** (basit, MySQL alternatifi)

## Decision
**Birincil: Meilisearch** (production VPS hosting'de Docker container).

**Fallback: MySQL FULLTEXT** (shared hosting deploy mode için, `.env` `SEARCH_DRIVER=mysql` toggle).

Abstraction: `App\Search\SearchEngine` interface, `MeilisearchEngine` ve `MysqlEngine` implementations. Controller ve UI driver-agnostic.

## Consequences

### Pozitif
- **Meili ergonomi:** Çok kolay setup — Docker run, master key, SDK çağrısı. Web UI dashboard.
- **Typo tolerance:** Default `2 typos`, kullanıcı "lavarel" yazsa "laravel" gelir.
- **Locale-aware:** Index başına dil ayarı, stop word ve segmenter Turkish dahil.
- **Faceting:** Tag/kategori filtre native, count ile.
- **Hız:** RAM-resident, 100k document'a kadar < 10ms p99.
- **Free, MIT.**

### Negatif / Trade-off
- **Yeni süreç:** PHP-FPM + MySQL + Redis'e ek olarak Meili process (Docker container). Disk + 200MB-500MB RAM.
  - Mitigation: Tek instance Meili genelde 100MB altında bizim ölçek için.
- **Veri replication:** Source of truth DB; Meili index'lemek için observer + queue. Eventual consistency (5-10s delay).
- **Backup:** Meili dump utility var; cron ile yedekleme planlanmalı.

### Risk
- **Index drift:** Meili çökerse veya silinse, re-index. Komut: `php artisan search:reindex`. Çok büyük olmadığı için (yüzlerce post) <30s.
- **Sürüm upgrade:** Meili 2.x stable; 3.x geçiş breaking olabilir, dump/load veya re-index gerekir. Karşı önlem: deploy script'te version pinned (`meilisearch:v1.x.y`).
- **Shared hosting'de yok:** Fallback MySQL FULLTEXT zaten plan; abstraction garanti.

## Alternatives Considered

### Typesense
- **Pro:** Aynı paradigma, çok benzer. C++ → marjinal hız.
- **Con:** Topluluk biraz daha küçük; Meili'nin yöneticilik UI'ı (admin dashboard) daha olgun.
- **Karar:** Tie-break'te Meili (PHP SDK kalitesi, Laravel Scout driver).

### Algolia
- **Pro:** En iyi DX, en hızlı.
- **Con:** Ücretli (free tier 10k op/ay), data third-party'de, lock-in.
- **Karar:** Reddedildi.

### Elasticsearch / OpenSearch
- **Pro:** En zengin feature, kurumsal.
- **Con:** JVM, 2GB RAM'den başlar, kurulum karmaşık. Bizim ölçeğimiz için topa füze atmak.
- **Karar:** Reddedildi.

### Sadece MySQL FULLTEXT
- **Pro:** Sıfır yeni süreç.
- **Con:** Typo-tolerance yok, relevance zayıf, ngram TR için orta. UX itibarıyla "modern arama" hissi vermez.
- **Karar:** Sadece fallback.

### Sadece SQLite FTS5
- **Pro:** Tek dosya, çok hızlı.
- **Con:** Multi-write contention; production MySQL kullanıyoruz (ADR-002), iki DB'ye birden yazmak abes.
- **Karar:** Reddedildi.

## Index şeması (Meili)

```json
// Index "posts_tr"
{
  "id": 1,
  "title": "Sade yazılım üzerine",
  "excerpt": "Kısa özet...",
  "body_text": "Plain text body (HTML-stripped)...",
  "tags": ["software", "essays"],
  "category": "essays",
  "author": "Ozan Efeoğlu",
  "published_at": 1734567890,  // unix timestamp
  "url": "/writing/sade-yazilim-uzerine"
}
```
Settings:
- `searchableAttributes: ["title", "excerpt", "body_text", "tags"]`
- `filterableAttributes: ["tags", "category", "published_at"]`
- `sortableAttributes: ["published_at"]`
- `stopWords` Turkish + English mixed list
- `synonyms`: opsiyonel (örn. "javascript" ↔ "js")
- `typoTolerance.minWordSizeForTypos`: `{ oneTypo: 4, twoTypos: 8 }`

## Re-index iş akışı
- **Observer:** `Post::saved` → `if ($post->isPublishedAndAvailable()) dispatch(new IndexPostJob($post)); else dispatch(new RemoveFromIndexJob($post));`
- **Bulk re-index command:** `php artisan search:reindex {model?}`
- **Locale per index:** `posts_tr`, `posts_en`. Yayınlanmış translation bazında.

## UI: search-as-you-type
- HTMX + Meili → 200-300ms debounce, top 5 result preview dropdown.
- `/search` full page: faceting (tag, category, type, year).
- 0 result: "Did you mean ..." (Meili built-in).

## References
- Meilisearch: https://www.meilisearch.com/
- Laravel Scout (compatible): https://laravel.com/docs/scout
- MySQL FULLTEXT ngram: https://dev.mysql.com/doc/refman/8.0/en/fulltext-search-ngram.html

## İlgili ADR'lar
- ADR-002: MySQL (FULLTEXT fallback)
- ADR-009: Cache & queue (re-index job queued)
