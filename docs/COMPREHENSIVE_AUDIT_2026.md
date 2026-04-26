# Kapsamlı Denetim — Ozan Efeoğlu Portfolyosu

> **Tarih:** 2026-04-23 · **Kaynak:** 8 paralel sub-agent raporu (Design · Responsive · Editorial/Political · SEO · Performance · Security · Test · Content)
> **Sahip:** Ozan Efeoğlu — **Foto Muhabir + Yayıncı/Editör**, Anadolu Ajansı Uluslararası Haber Merkezi
> **Patron (sentez):** Claude
> **Statü:** audit tamamlandı — aksiyon için roadmap önerildi; **go-live BLOCKER'lar çözülmeden yayın yapılamaz**

---

## A. KAPSAM KESİTİ

| Agent | Odak | Kritik bulgu sayısı | En yüksek risk |
|---|---|---|---|
| Editorial/Political | İçerik etiği, kurumsal sınır | 4 critical · 6 yüksek | Uydurma publications + hardcoded `Foto: Ozan Efeoğlu` telif ihlali |
| Content | Pozisyon yeniden yazım | 20 spesifik copy fix | Savaş muhabiri framing → foto muhabir + editör rewrite |
| Design | Foto arşiv entegrasyonu (72 AA JPG) | 1 critical · 2 yüksek | AA watermark kırpılamaz; 72 foto toplu kapak değil — 3-5 seçmeli |
| Responsive | Viewport testleri (375/768/1280/1440) | 2 SEV1 · 3 SEV2 | Tap targets &lt;44px; display-statuesque line-height 0.95 |
| SEO | Head, meta, structured data | 2 yüksek · 8 orta | Canonical / OG / Twitter / JSON-LD / sitemap **hepsi yok** |
| Performance | CWV + bundle | 1 yüksek · 5 orta | Portre preload+WebP eksik; 48 font dosyası subset gerek |
| Security | ASVS-lite + Laravel | 1 critical · 4 yüksek | **IP+UA retention = kaynak ifşa riski** (gazeteci için özellikle) |
| Test | E2E, a11y, flows | 7 issue | `/admin/login` 404; H1 isim değil konu olmalı; autocomplete eksik |

---

## B. ÇAPRAZ-KESEN 7 TEMA (birden fazla ajanı birleştiren kök neden)

### B.1 — Pozisyon krizi → "savaş muhabiri" yanlışı, düzeltme zorunlu
**Çapraz onay:** Editorial + Content + Design
- Site tonu "war correspondent / field dispatch / Gazze-Harkiv" kurgusunda
- Gerçek rol: **AA İstanbul haber masasında foto muhabir + yayıncı/editör** + drone haberciliği YL araştırması
- Hatay / Zeytin Dalı geçmişi biyografide kalır, başlık olarak **satılmaz**
- 20 spesifik copy fix (Content rapora bakılacak)

### B.2 — Copyright / kredit ihlali riski
**Çapraz onay:** Editorial + Design + Content
- `show.blade.php:91` hardcoded `Foto: Ozan Efeoğlu` — 72 AA logolu görsel için **telif ihlali**
- Doğrusu: `Foto: Ozan Efeoğlu / AA` (wire-standart slash kredi)
- Writing modeline `photo_credit` alanı; default config-driven `'Foto: Ozan Efeoğlu / AA'`
- AA watermark **kırpılamaz, filtrelenemez, retuşlanamaz**

### B.3 — Demo content pozisyonu hijackluyor
**Çapraz onay:** Editorial + Content + Test
- `WritingSeeder.php`'deki 4 savaş-bölgesi entry (Gazze hastane, Artsakh koridoru, Harkiv sığınağı, Kahire basın kartı) **demo bayraksız** canlıya çıkarsa overclaim
- `publications` array'leri Reuters / Guardian / Le Monde / BBC Türkçe — **uydurma byline**
- Fix: ya sil + 6 yeni entry (Content önerisi: İstanbul editoryel/foto_notu/analiz), ya `is_demo` flag + site-wide banner

### B.4 — Kaynak koruma açığı (gazeteci-spesifik kritik)
**Çapraz onay:** Security
- `contact_messages.ip_address` + `user_agent` **tam** + **süresiz** saklanıyor
- Savcılık celbi senaryosunda ifşa eder — KVKK Md. 7 / GDPR Md. 5(1)(e) ihlali
- Fix: IP hash veya /24 mask; 90-180 gün TTL auto-delete; KVKK aydınlatma metni

### B.5 — SEO head minimum bile eksik
**Çapraz onay:** SEO + Test
- Layout head'de sadece `<title>` + `<meta name="description">`
- Canonical / OG / Twitter / JSON-LD / `<time datetime>` **sıfır**
- Article description `layout` tarafına geçmiyor → tüm dispatch'lerde meta **duplicate**
- Fix: layout head 10-15 satırlık refactor + per-page `@stack('meta')` + JSON-LD partial

### B.6 — Mobil a11y & WCAG ihlalleri
**Çapraz onay:** Responsive + Test
- Hamburger 32×32 (44 hedef), footer links 20px, filter chips 20-21px — **tap target SEV1**
- Form fields autocomplete yok (WCAG 1.3.5)
- Honeypot `website` label'lı + DOM'da — screen reader tuzağı (WCAG 1.3.5 + 4.1.2)
- `aria-current="false"` spam (sadece aktif'te set)
- `/yazilar` H3 hopping (H2 eksik)
- Homepage H1 "Ozan Efeoğlu" — kişi adı, sayfa konusu değil

### B.7 — Foto pipeline (72 JPG × 1.9 GB)
**Çapraz onay:** Design + Performance
- Spatie Media conversions yapılandırılmış (640/1280/1920 webp) ama `nonQueued()` prod-unsafe; 72 foto yüklendiğinde 216 senkron resize
- Portre bile Spatie collection dışı ham JPG
- AVIF yok
- AA watermark crop/retuş yasağı → pipeline sadece boyut + format
- Foto archive storage: S3/R2 zorunlu (1.9 GB public disk şişmez)

---

## C. ÖNCELİKLİ AKSİYON PLANI

### 🚨 ÖNCELİK 1 — GO-LIVE BLOCKERS (7 madde)
Bunlar çözülmeden yayın **yapılamaz**. Etik / hukuki / kaynak güvenliği.

| # | Dosya | Fix | Kim flagledi |
|---|---|---|---|
| 1 | `database/seeders/WritingSeeder.php` | 4 savaş-bölgesi entry sil; Content'in önerdiği 6 yeni İstanbul-based entry koy (foto_notu / editoryal / analiz / deneme / not) | Editorial + Content |
| 2 | `database/seeders/WritingSeeder.php` | Tüm `publications` sync'lerini sil — uydurma Reuters/Guardian/Le Monde byline kalmasın | Editorial |
| 3 | `app/Models/Writing.php` + migration | `photo_credit` nullable string alanı ekle; `config/site.php` `default_photo_credit: 'Foto: Ozan Efeoğlu / AA'` | Editorial + Content |
| 4 | `resources/views/public/writing/show.blade.php:91` | Hardcoded `Foto: Ozan Efeoğlu` → dinamik `$writing->photo_credit ?? config('site.default_photo_credit')` | Editorial + Content |
| 5 | `app/Http/Controllers/ContactController.php:39-40` + migration | `ip_address` → IP hash (HMAC-SHA256 + günlük salt) veya `/24` mask; `user_agent` aynı; 90-180 gün TTL scheduled delete | Security |
| 6 | `resources/views/public/pages/contact.blade.php` | Form üstüne KVKK aydınlatma linki + retention/amac disclaimer; "bu sayfa kişisel kanalım, AA kurumsal talepleri için ajansa yazın" güçlü uyarı | Editorial + Security |
| 7 | `routes/web.php` / Fortify config | `/admin/login` 404 fix — Fortify default `/login`'e redirect, admin prefix mismatch | Test |

### 🔴 ÖNCELİK 2 — V1 PUBLISH için HIGH (15 madde)

| # | Fix | Agent |
|---|---|---|
| 8 | Pozisyon rewrite — 20 copy fix (Content tablosu) — landing eyebrow, overture, dispatch→çalışma, etc. | Content |
| 9 | Kind enum: `saha_yazisi → foto_notu` + `editoryal` + `analiz` ekle; migration + WritingSeeder entries yeni enum ile | Content |
| 10 | `config/site.php:31` description rewrite — "saha yazıları" → "haber fotoğrafı, editöryel notlar, drone haberciliği" | Content |
| 11 | `PageSeeder.php` about body — "kritik gelişmeleri yerinden takip" → "sınır hattı haberleri"; "kitaplaştırdım" → "derliyorum"; identities `'editör · yayıncı'` eklenti | Editorial + Content |
| 12 | `resources/views/layouts/public.blade.php` head refactor — canonical + OG (title/desc/url/image/locale) + Twitter card + `@stack('meta')` hook | SEO + Test |
| 13 | `writing/show.blade.php:1` — `'description' => Str::limit(strip_tags($writing->excerpt), 155)` layout'a geç | SEO |
| 14 | Homepage H1 — "Ozan Efeoğlu" kişi adı değil, sayfa konusu olmalı (H1: "Foto muhabir ve editör — arşiv", "Ozan Efeoğlu" H2 veya plate içinde) | Test + SEO |
| 15 | `public/robots.txt` — `Disallow: /admin, /login, /two-factor-challenge, /forgot-password, /reset-password` + `Sitemap: https://ozanefeoglu.com/sitemap.xml` | SEO |
| 16 | `sitemap.xml` — Spatie/Laravel-Sitemap ya da custom controller; tüm published writings + 3 static page | SEO |
| 17 | Tap targets — hamburger `icon-btn` 44×44; footer/nav links padding-block 0.5rem; filter chips min-height 44px | Responsive |
| 18 | Form a11y — `autocomplete="name|email|off|off"` + honeypot `aria-hidden="true"` + `tabindex="-1"` + label sr-only'a gömülü | Test |
| 19 | `aria-current="false"` spam temizle — sadece aktif linkte `page` | Test |
| 20 | SecurityHeaders — `X-Powered-By` strip; CSP'den `'unsafe-hashes'` kaldır | Security |
| 21 | Spatie Honeypot wire — `@honeypotFields` + `ProtectAgainstSpam` middleware (manuel `website` field yerine) | Security |
| 22 | Portre `<link rel="preload" as="image" fetchpriority="high">` + WebP dönüşüm (`cwebp -q 80 ozan.jpg`) + `width/height` attribute | Performance |
| 23 | `_tokens.css` `.display-statuesque` `line-height: clamp(1, 1.05, 1.1)` (0.95 riskli 3+ satırda) | Responsive |

### 🟡 ÖNCELİK 3 — MEDIUM (V1.1 / Faz 2C+ / 3) (12 madde)

| # | Fix | Agent |
|---|---|---|
| 24 | `/görüntü` rotası aç — 3 foto-öyküleme kümesi (Şubat 2023 deprem; Drone; Mevsim/Rutin); kronolojik yıl-bazlı galeri | Design |
| 25 | Writing modeline `is_demo` flag — demo entries banner | Editorial |
| 26 | Foto seçimi — Design önerdiği 5 dispatch cover (deprem portresi → hastane yazısına; müze → deneme; drone → research) | Design |
| 27 | Spatie Media `nonQueued()` → `queued()`, AVIF variant ekle, `<picture>` wire | Performance |
| 28 | Font subset Fontsource config `['latin', 'latin-ext']` — 48 → 12 dosya (~800 KB kazanç) | Performance |
| 29 | JSON-LD — home WebSite+SearchAction, about Person (worksFor AA), article NewsArticle | SEO |
| 30 | `<time datetime="...">` tag'i dispatch published_at + updated_at için | SEO |
| 31 | BreadcrumbList (visual + JSON-LD) /yazilar/{slug}'ta | SEO |
| 32 | `/yazilar` H3 → H2; `/hakkımda` Essay + Research için H2 ekle | Test |
| 33 | Custom 429 view + `/confirm-password` + `/reset-password/{token}` route düzeltme | Test |
| 34 | Marginalia sticky `position: sticky; top: 6rem; align-self: start` @lg+ | Responsive |
| 35 | HIBP fail-closed (network error → throw, register flow reject) | Security |

### 🟢 ÖNCELİK 4 — LOW (V1.2+ / Faz 4-7)
- `SetAppLocale` env() → config — security hardening
- Daily login lockout (perDay limit)
- Analytics DNT + IP anonymize
- Session cookie `__Host-` prefix prod
- Hero mobile composition (portre → avatar scale)
- Plate affiliation "Gündüz işi: AA" soft downgrade
- SEO hreflang (EN locale gelince)
- Responsive zigzag timeline about
- 2 yedek seed dispatch (Yakınlığın etiği, Görsel göstergebilim giriş)

---

## D. POZİSYONLAMA REWRİTE — ÖZET (tam liste: Content agent raporu)

### Doğru kimlik cümlesi
> **"Anadolu Ajansı Uluslararası Haber Merkezi'nde foto muhabir ve yayıncı. Drone haberciliği ve görsel göstergebilim üzerine yüksek lisans çalışıyor."**

### Önemli string değişiklikler
| Eski | Yeni |
|---|---|
| "Saha yazıları, röportajlar, denemeler ve kısa notlar" | "Haber fotoğrafı notları, röportajlar, editöryel denemeler ve kısa yazılar" |
| "Portfolyo · arşiv · saha" | "Portfolyo · arşiv · haber masası" |
| "Dosyalar →" (CTA) | "Çalışmalar →" |
| "Son dispatch / Son dosya / dispatches" | "Öne çıkan / Son çalışmalar / Son yayımlananlar" |
| "Foto muhabir | yayıncı | araştırmacı" | "Foto muhabir | editör · yayıncı | araştırmacı" |
| Kicker: "SAHA YAZISI · GAZZE" | "FOTO NOTU · İSTANBUL" / "EDİTORYAL · HABER MASASI" / "ANALİZ · DRONE" |

### Yeni kind enum
```
foto_notu · editoryal · roportaj · deneme · analiz · not
```
(Migration gerekir; eski `saha_yazisi → foto_notu` map.)

### Yeni 6 seed dispatch (Content önerisi)
1. **Haber masasında bir kare — seçimin gramerleri** (editoryal / İstanbul)
2. **Drone hattında mesafe, insanda ölçek** (analiz / İstanbul)
3. **Protokol fotoğrafında görünmeyen** (foto_notu / İstanbul)
4. **Sabah dosyası: bir ajans gününün açılışı** (editoryal / İstanbul)
5. **Alan notu — Adana bölgesi ev dönüşü** (foto_notu / Adana)
6. **Kaynak koruma — dijital temiz masa** (not / İstanbul)

---

## E. FOTO ARŞIV ENTEGRASYON STRATEJİSİ — ÖZET (tam liste: Design agent raporu)

### 72 AA JPG üç kümeye ayrılır
1. **Öne çıkan 5 dispatch kapağı** (Design seçimi):
   - `20230215_…_870256` (deprem, sağlık görevlisi + kundak) → yeni foto_notu veya related dispatch hero
   - `20220908_…_589438` (müze, siyah zemin) → "Haber masasında bir kare" deneme kapağı
   - `20220905_…_498612` (dalgıç ikilisi) → about masthead ara-görsel (metafor değil, göz dinlendirme)
   - `20210727_…_374089` (Be-200 yangın uçağı) → `/görüntü` landing
   - `20220712_…_965166` (drone — aslan balığı jetski) → drone araştırma kartı
2. **`/görüntü` kronolojik arşiv** — kalan ~65 foto; yıl başlıkları (2021/2022/2023); sıkı 3:2 grid; her kare: LOC + tarih + konu + "Foto: Ozan Efeoğlu / AA"
3. **3 foto-öyküleme kümesi** (manuel sekanslı):
   - "Şubat 2023 — Enkaz ve Kundak" (deprem serisi, 8-10 kare)
   - "Kuşbakışı / Drone" (4-6 kare)
   - "Mevsim / Rutin" (yangın uçağı, müze, dalış)

### AA watermark kuralı (zorunlu)
- **Crop yok** (sağ-alt logo her zaman görünür)
- **Retuş/filtre yok** (pipeline sadece resize + format)
- **Kredi her yerde** `Foto: Ozan Efeoğlu / AA`

### Pipeline
- Spatie Media Library kuruldu — conversions 640/1280/1920 webp var
- **Eklenecek:** AVIF variant, `<picture>` template, queued conversion, S3/R2 disk (1.9 GB public disk'te olmasın)

---

## F. UYGULAMA SIRASI — ADIM ADIM ROADMAP

### Adım 1 — BLOCKER BATCH (1 oturum, zorunlu)
1.1. WritingSeeder yeniden yaz (6 yeni İstanbul-based entry, publications temiz)
1.2. Writing modeline `photo_credit` (nullable) migration
1.3. `config/site.php` `default_photo_credit: 'Foto: Ozan Efeoğlu / AA'`
1.4. `writing/show.blade.php:91` dinamik credit
1.5. ContactController IP hash + migration `contact_messages.ip_address` hash column
1.6. Contact view KVKK aydınlatma + "kişisel kanal" disclaimer
1.7. `/admin/login` 404 route fix (Fortify)
1.8. Build + test + review

### Adım 2 — POZİSYON REWRITE + SEO HEAD (1 oturum)
2.1. Kind enum migration (saha_yazisi → foto_notu) + Writing model enum + kind_label map
2.2. 20 copy fix (Content tablosu)
2.3. `config/site.php` description rewrite
2.4. PageSeeder about body rewrite
2.5. Layout head refactor: canonical + OG + Twitter + @stack('meta')
2.6. writing/show.blade.php description layout'a geç
2.7. Homepage H1 semantik düzeltme
2.8. Build + test

### Adım 3 — A11Y + SECURITY POLISH (1 oturum)
3.1. Tap targets 44px global (icon-btn, footer, filter)
3.2. Form autocomplete + honeypot proper hide
3.3. `aria-current="false"` spam temizlik
3.4. `display-statuesque` line-height fix
3.5. SecurityHeaders `X-Powered-By` strip + CSP unsafe-hashes kaldır
3.6. Spatie Honeypot proper wire
3.7. `robots.txt` + `sitemap.xml` (Spatie)

### Adım 4 — FOTO ENTEGRASYON (2 oturum)
4.1. Spatie Media config refine (AVIF + queued)
4.2. 5 seçilmiş foto Spatie upload (Admin panelden veya seeder-driven)
4.3. Portre Spatie collection'a taşı + WebP + preload
4.4. Writing covers 5 dispatch'e bağla + figcaption
4.5. `/görüntü` route + controller + view (yıl-gruplu galeri)
4.6. 3 foto-öyküleme kümesi manuel sekanslama (admin tool veya seeder)

### Adım 5 — PERFORMANCE + SEO ileri (1 oturum)
5.1. Font subset (latin + latin-ext)
5.2. JSON-LD partial (Person + WebSite + NewsArticle)
5.3. BreadcrumbList
5.4. `<time datetime>` tag
5.5. Custom 429 + legacy route guards

### Adım 6 — KVKK + SECURITY HARDENING (Faz 7)
6.1. Contact message retention TTL job
6.2. HIBP fail-closed
6.3. Daily login lockout
6.4. SetAppLocale config taşıma
6.5. Analytics DNT + IP anonymize

---

## G. OWNER ONAY BEKLEYEN KARAR NOKTALARI

Ben (patron-claude) karar veremem, sahibinden net "evet/hayır" beklenen:

1. **AA watermark + kişisel site yayın hakkı** — AA kurumsal onayı var mı? About'a "AA ile görsel gösterim izni çerçevesinde" disclaimer eklenecek mi?
2. **Photo_credit per-item** — bağımsız iş için "Foto: Ozan Efeoğlu" tek imza, AA iş için "/AA" — hangi kareler hangi kategoriye girer? (admin tool lazım)
3. **Demo dispatches** — 6 yeni İstanbul-based entry (Content önerisi) ile değiştirme onayı?
4. **AA kurumsal banner tonu** — "Anadolu Ajansı Uluslararası Haber Merkezi · İstanbul" mevcut yerinde mi kalsın, yoksa "Gündüz işi: AA" gibi soft downgrade mi?
5. **Contact form — IP tutma?** Tamamen silmek mi, hash mi, /24 mask mı? (Security önerisi: ya hiç ya hash+7gün)
6. **KVKK aydınlatma metni** — sahibi mi yazacak, şablon mu üretilsin? Veri Sorumlusu: Ozan Efeoğlu kişisel mi, başka bir kuruluş mu?
7. **Deprem fotoğrafı** (çocuk yüzü içeren) — site'de kapak olarak kullanım onayı? AA wire versiyonuyla sınırlı mı?
8. **`/görüntü` açılışı** — tek bir pilot dispatch ile mi yoksa tam 65 foto kronolojik galeri ile mi live olsun?

---

## H. BAŞARI ÖLÇÜTÜ

Yayın öncesi bu 8 check pass etmeli:

- [ ] **Editorial**: Hiçbir yerde "Foto: Ozan Efeoğlu" tek imzalı AA logolu kare; hiçbir yerde uydurma publications; hiçbir yerde Gazze/Harkiv/Artsakh demo
- [ ] **Content**: 20 copy fix uygulandı; kind enum güncel; description overclaim yok
- [ ] **Security**: IP/UA hashlı veya silinmiş; KVKK metni bağlantılı; X-Powered-By gitmiş; CSP temiz
- [ ] **SEO**: Canonical + OG + Twitter + sitemap + robots admin disallow; her sayfa unique description
- [ ] **Responsive**: Tüm tap targets ≥44px; display-statuesque 3 satır başlıkta çakışmıyor
- [ ] **Test**: `/admin/login` 200; H1 sayfa konusu; autocomplete set; honeypot reader-safe
- [ ] **Performance**: Portre webp + preload; Core Web Vitals yeşil (LCP<2.5s / CLS<0.1 / INP<200ms)
- [ ] **Design**: En az 2 dispatch real photo cover; `/görüntü` rotası hazır

---

**Sonuç:** Site mevcut haliyle **yayına hazır değil** — 7 blocker + 15 yüksek öncelik madde var. Ama mimari sağlam (Scene system + Pattern B + token sistemi doğru yerde). Pozisyon düzeltmesi + copyright fix + kaynak koruma + SEO head + foto pipeline = ~5 oturum iş. Sonrasında editoryal olarak tutarlı, hukuki olarak temiz, erişilebilir ve performant bir portfolyo.

**Patron kararı:** Blocker batch (Adım 1) başlasın onay verilirse. Öncesinde sahibinin §G'deki 8 karar noktası için net tercihleri gerekli.
