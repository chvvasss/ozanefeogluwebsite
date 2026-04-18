# ADR-013 — Domain Pivot: Savaş Muhabiri Portfolyo + Köşe Yazıları

## Status
Accepted — 2026-04-18 (Faz 1 içinde, kullanıcı yeni bilgi verince)

## Context
Faz 0'da "kişisel portfolyo + blog CMS" briefi üstüne teknik ve tasarım kararları verildi; varsayılan içerik tonu **sessiz yazılım atölyesi** idi ("Builds quietly ambitious software", İstanbul koordinatları, "projeye konuş" CTA).

Faz 1 görsel teslimden sonra kullanıcı domain'i netleştirdi: **site sahibi bir savaş muhabiri**. Portfolyo = saha raporları (dispatches). Blog = köşe yazıları / saha notları. İçerik öncelikli olarak **belgesel fotoğraf + uzun-form röportaj**.

Bu, ton ve bilgi mimarisinde önemli bir kalibrasyondur. Teknik stack etkilenmez (Laravel, MySQL, TipTap, Meilisearch kararları aynı kalır); etkilenenler:
- Tipografi varyantları (display font aksları)
- Renk accent
- Copy / mikro-yazım
- Menü başlıkları
- İçerik tipi isimleri (projeler → raporlar; yazılar → köşe/saha notu)
- Dokümantasyon odağı
- Medya pipeline — AVIF/WebP yanında **grayscale fotoğraf** ve EXIF metadata (lokasyon gizliliği için opt-in silme) özel önem kazanır

## Decision

### Ton ve terminoloji
| Eski | Yeni |
|---|---|
| portfolyo · işler | **reports · dispatches** (saha raporları) |
| blog · yazılar | **column · field notes** (köşe · saha notları) |
| hakkımda | **biography** (özgeçmiş; credential-first) |
| projeye konuş | **commission · assignment** |
| İstanbul koordinatları | dinamik "currently — [location]" satırı (admin-editable) |

### Tasarım dili kalibrasyonu
- **Display font (Fraunces):** `SOFT=0, WONK=0, opsz=144` varyasyonu öne çıkar. Yumuşak / wonky aksiyonlar kaldırıldı — editorial ama *gazete başlığı* karakterinde.
- **Accent:** `--color-terracotta-*` → `--color-dispatch-*`: light `#991b1b` (red-800), dark `#ef4444` (red-500). Nonradikal, acil ama militan değil. Ekran kontrastı AAA.
- **Noise overlay:** kağıt grain → **film grain** (baseFrequency `0.75`, daha kaba). Fotojürnalistik his.
- **Dateline kalıbı:** her item için `KONUM · TARİH · YAYIN` — monospace, uppercase, letter-spacing `0.15em`.

### Bilgi mimarisi kalibrasyonu (`docs/discovery/information-architecture.md` revize edilecek)
| Sayfa | URL | Açıklama |
|---|---|---|
| Anasayfa | `/` | Currently-on-assignment bandı, son 4 rapor, son 3 köşe yazısı, özgeçmiş kırıntısı, iletişim |
| Reports | `/reports` | Tüm saha raporları; filtre: bölge, yıl, yayın |
| Report detail | `/reports/{slug}` | Uzun-form belgesel hikaye; fotoğraf-galeri ön planda |
| Column | `/writing` (yoldaş) veya `/column` | Kısa saha notları, ethics denemeleri, köşe parçaları |
| Column entry | `/column/{slug}` | Tek yazı |
| Biography | `/biography` | Özgeçmiş, credentials, yayın listesi, CV |
| Contact | `/contact` | Assignment / press / licensing formu |

### Admin label kalibrasyonu (Faz 2'de uygulanır)
- Model: `Post` → kalır (domain-neutral)
- Model: `Project` → kalır (yapı aynı; UI'da "Report" olarak sunulur)
- Taxonomies eklenir (Faz 2): `regions` (bölge), `publications` (yayın). `technologies` yerine.

### Özel teknik hassasiyetler
- **EXIF metadata strip:** Spatie Media Library + Intervention Image 3. Zaten ADR-007'de planlı; şimdi **GPS coordinate strip default** olarak zorunlu (savaş bölgesi lokasyon gizliliği). Admin override isteğe bağlı.
- **Image proxy SSRF:** ADR-007'de planlı. Saha fotoğraflarının bir kısmı URL-fetch olabilir. Whitelist sıkı.
- **Publication credentials:** bir `publications` taxonomy + media collection (logo). Homepage'de logo strip'i görünür.

## Consequences

### Pozitif
- Site sahibinin profesyonel kimliğine uygun ton. "Generic AI" kokusundan iki kat uzak.
- Medya pipeline'ı (fotoğraf-öncelikli) brief'in zaten planladığı şey — şimdi daha net hedefli.
- Accent renk değişikliği düşük risk (token-bazlı, tek yerde).

### Negatif / Trade-off
- Landing ve auth copy'leri Faz 1 sonunda bir kez yazıldı, şimdi revize. ~6 Blade view + design tokens CSS + 1 ADR.
- Faz 2 modelleri artık domain-aware isimlerle gelir (Report, Column, Dispatch). İkinci bir refactor'dan kaçınmak için şimdi belirleyelim.

### Risk
- **Ton çok karanlık olabilir:** savaş muhabiri = dramatik, ama site bir *sahibinin özel mülkü*; aşırı karanlık hava sahibini rahatsız edebilir. Mitigation: accent red *muted* varyantı seçildi (red-800 değil red-900 değil — ikisinin ortasında); film grain opaklığı düşük (3.5%); dark mode default *değil* (sistem tercihi + manuel).

## Alternatives Considered

### Tasarım: daha radikal brutalizm
- Raw HTML, zero CSS, bant gibi başlıklar.
- Reddedildi: profesyonel muhabir kimliğine uymaz; editorialitasyon seviyesinin korunması şart.

### Accent: olive / field green
- Field journalism vibes.
- Reddedildi: dispatch-red daha acil ve okunabilir; olive biraz "askeri tatbikat" hissi veriyor.

### Font: Redaction (Titling Type)
- Newspaper titling, tam war reporter feel.
- Reddedildi: Google Fonts / Fontsource'da yok, self-host manuel (build pipeline ek iş). Fraunces'ın `SOFT=0` varyasyonu yeterince keskin.

## References
- Foreign Policy Typography case studies
- War reporter portfolyo örnekleri (Paul Watson, Lynsey Addario, Alexandra Rojkov)
- UN OCHA Reporting style guide (dateline kalıbı)

## İlgili ADR'lar
- ADR-003: Frontend stack (design tokens layer)
- ADR-007: Media management (EXIF GPS strip zorunlu)
- ADR-011: i18n (TR + EN; muhabir çift dilli yayın)
