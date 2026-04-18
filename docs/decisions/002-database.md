# ADR-002 — Veritabanı: MySQL 8 (PostgreSQL fallback hazır)

## Status
Accepted — 2026-04-18

## Context
Adaylar:
- **MySQL 8+** — paylaşımlı hosting'lerde de var, JSON kolon, generated column, FULLTEXT search, CTE.
- **MariaDB 11+** — MySQL fork, çoğu özellik uyumlu, bazı yerlerde daha hızlı, RDS dışı open governance.
- **PostgreSQL 16+** — en güçlü tip sistemi, GIN/GiST index, JSONB, FTS güçlü.
- **SQLite** — file-based, deploy kolay, küçük site için yeter, prod read-heavy bile sorun değil.

### Kıstaslar
- **Hosting uyumu:** Paylaşımlı hosting fallback senaryosunda (brief Mimari §4.2) MySQL beklentisi en yüksek.
- **JSON kolon ihtiyacı:** Spatie Translatable JSON kolon kullanır (locale-keyed). MySQL 8 JSON desteği yeterli.
- **FULLTEXT:** Meilisearch'siz fallback için. MySQL 8 FULLTEXT (with ngram parser TR için sınırlı ama çalışır), PG çok daha iyi (ts_vector + ispell) ama hosting yokluğu sorun.
- **Geliştirici ergonomisi:** Eloquent her ikisinde de iyi; bazı PG-only feature'lar (CITEXT, array column, JSONB operatörleri) framework'ten geçmez.
- **Backup ekosistemi:** mysqldump + Spatie/laravel-backup uyumlu.

## Decision
**MySQL 8.0+ (veya InnoDB destekli ≥10.6 MariaDB)** birincil veritabanı.

PostgreSQL desteği abstract edilecek (Eloquent zaten yapıyor); migration'larda DB-spesifik raw SQL kullanmaktan kaçın. PG geçişi gelecekte gerekirse ekstra iş 1 günü geçmez.

Geliştirme ortamı **SQLite** (test + lokal hızlı kurulum). CI testleri MySQL'de de çalışır.

## Consequences

### Pozitif
- Her hosting'de bulunur. Sahibi VPS yerine cPanel hosting'e geçirmek isterse engel yok.
- Backup, restore, monitoring araçları olgun (mysqldump, Percona Toolkit, pt-online-schema-change).
- JSON path operatörleri (`->`, `->>`) Eloquent'ten translatable accessor için yeterli.
- FULLTEXT search Meili olmadığı senaryoda kabul edilebilir (kalite TR/EN için ngram + UTF8MB4 ayarıyla).
- Ücretsiz, GPL/permissive, vendor-lock yok.

### Negatif / Trade-off
- PG'nin JSONB operatör zenginliği yok. Eloquent abstraksyonu bunu çoğu zaman gizliyor; raw query gereken yerlerde dikkat.
- FTS PG'ye göre daha kısıtlı; bu yüzden default search arka ucu **Meilisearch** (bkz. ADR-008).
- MySQL 8 charset default `utf8mb4_0900_ai_ci` Türkçe sıralama için "her zaman doğru" değil; `utf8mb4_tr_0900_ai_ci` veya `utf8mb4_unicode_520_ci` tercih.

### Risk
- **Charset gotcha:** Türkçe `i/İ`, `ı/I` casing ve sıralama sorunları. Karşı önlem: tüm yeni tablolar `utf8mb4_unicode_520_ci`, slug uniqueness check `LOWER(slug)` veya generated column.
- **Migration drift:** Lokal SQLite ↔ Prod MySQL fark. Karşı önlem: CI'da MySQL service container ile test job.
- **Read replica gelecekte:** Laravel `read`/`write` connection split'i kullanmaya hazır config; şimdilik tek connection.

## Alternatives Considered

### PostgreSQL 16+
- **Pro:** En zengin tip sistemi, JSONB, gerçek arrays, partial index, GiST/GIN, ts_vector FTS, listen/notify.
- **Con:** Paylaşımlı hosting'de yok; sahibinin 3 yıl sonra "VPS gereği yok" karar vermesi durumunda blokaj.
- **Karar:** İkincil hedef olarak destekleniyor (Eloquent abstraction yüzünden). Birincil değil.

### MariaDB 11+
- **Pro:** Open governance, bazı feature'larda daha güzel (sequences, RETURNING).
- **Con:** Bazı paylaşımlı hosting'lerde MySQL versiyonu var; uyumluluk için MySQL'e ayar yapılmalı.
- **Karar:** **Kabul edilen alternatif** — MySQL ile değiştirilebilir; doc'larda eşit gösterilecek.

### SQLite
- **Pro:** Zero-config; tek dosya backup; deploy çok hızlı; site küçük olduğu sürece performans yeterli.
- **Con:** Concurrent write zayıf (single writer); admin panel + cron + queue worker bir arada olunca lock contention. Migration eşitlemesi.
- **Karar:** **Sadece geliştirme + test ortamı için**. Prod'da değil.

## References
- MySQL 8 character sets: https://dev.mysql.com/doc/refman/8.0/en/charset-collations.html
- Eloquent JSON columns: https://laravel.com/docs/eloquent#json-where-clauses
- MySQL FULLTEXT ngram parser (CJK + opsiyonel TR): https://dev.mysql.com/doc/refman/8.0/en/fulltext-search-ngram.html

## İlgili ADR'lar
- ADR-001: Laravel framework (Eloquent ORM)
- ADR-008: Search (MySQL FULLTEXT fallback)
- ADR-011: i18n (translatable JSON column)
