# MASTER PLAN 2026 — Launch & Dinamik Rebuild

> **Tarih:** 2026-04-23 · **Sentezci:** Patron (Claude) · **Kaynak:** Owner Agent + Architect Agent (+ 8 öncü audit raporu)
> **Statü:** Plan — implementation pending owner's human-only items (AA dilekçe + KVKK avukat)
> **GO/NO-GO:** 🟡 KOŞULLU GO — 2-kademeli launch

---

## I. YÖNETİCİ ÖZETİ (200 kelime)

Site mimari olarak sağlam (Scene system + cardinal red + Source Serif 4 + Spatie stack) ama **yayına hazır değil**. 12 kırmızı madde var; hepsi blocker batch (~1 oturum) ile çözülür. Sonrasında mimari rebuild: **her şey admin-dinamik** — 13 settings grubu, 7 yeni tablo, hero modu (portre→iş fotoğrafı), foto arşiv pipeline. **AA fotoğraf arşivi (72 JPG) V1'de YOK**; AA İK + Editöryal Koordinasyon'dan yazılı izin alınana kadar arşiv private'ta kalır. V1 sahibinin AA-dışı drone + kişisel + stock kareleri ile açılır. Çocuk kundak karesi **hiçbir zaman** site'de kullanılmaz (UN CRC + KKK 5395 + KVKK m.6). IP/UA saklama **tamamen drop** — kaynak koruma absolutism. KVKK Veri Sorumlusu = Ozan kişisel, avukat onayı şart. Launch 2-kademeli: soft noindex (beta 5 kişi, 2 hafta) → public (AA onayı gelirse banner/fotoğraf açılır; gelmezse AA-suz tam launch). Toplam ~10 oturum dev + paralel ~2 hafta Owner insan işi (dilekçe + avukat + foto seçimi).

---

## II. HUKUKİ VE ETİK ÇERÇEVE (Owner Agent'ten ayrıntılı)

| Konu | Yasal çıpa | Karar |
|---|---|---|
| AA watermark + kişisel site | FSEK m.18/2 (işveren eseri), Basın İş K. m.11 (yan-iş onayı), AA iç yönetmelik | 🟡 HOLD — yazılı izin şart; V1'de AA foto yok |
| AA banner metni | FSK m.7 (marka kullanımı), Basın Konseyi m.12 | Güçlü metin tutulur ama `affiliation_approved` flag default false → render yok |
| Slash-kredi "/ AA" | FSEK m.15 (adın belirtilmesi) + m.71 (gasp suç) | Default tüm yazılarda `Foto: Ozan Efeoğlu / AA`; observer AA-pattern cover için zorlar |
| Demo dispatches | FSEK m.15, TTK m.55 (haksız rekabet), Basın Konseyi m.1 | Gazze/Harkiv/Artsakh/Kahire ENTRY'LERİ + fake publications **TAMAMEN** sil |
| Contact IP/UA | KVKK m.4 (ölçülülük), Kurul 2020/649 (IP kişisel veri), Basın Konseyi m.10 (kaynak gizliliği), CMK m.167 | Kolon **DROP**; rate-limit Redis hash+TTL 1 saat |
| KVKK metni | KVKK m.10 (aydınlatma), m.12 (güvenlik), m.13 (başvuru) | Veri Sorumlusu = Ozan kişisel; 1-sayfa metin; avukat onay zorunlu |
| Çocuk fotoğrafı | UN CRC m.16, KKK 5395 m.4-5, KVKK m.6 özel nitelikli, Basın Konseyi m.5 | `20230215_…_870256` **HİÇ** public kullanım yok; archive/private |
| AI crawlers | Basın Konseyi + EBU 2024 pozisyonu (AI training opt-out) | robots.txt Disallow: GPTBot, Google-Extended, CCBot, anthropic-ai, Claude-Web, PerplexityBot |
| Writings retention | Basın Konseyi m.4 (düzeltme hakkı) | Asla silinmez; soft-delete only; hard delete 30 gün gecikmeli super-admin |
| Photo storage | KVKK m.9 (yurtdışı aktarım) | Cloudflare R2 Frankfurt (AB region) |

---

## III. 12 KIRMIZI MADDE — YAYIN ÖNCESİ BLOCKER BATCH

**1 oturum, zorunlu. Bu madde çözülmeden hiçbir canlı yok. Sıra:**

| # | Aksiyon | Dosya | Agent kim flagledi |
|---|---|---|---|
| 1 | `WritingSeeder.php` → Gazze/Harkiv/Artsakh/Kahire 4 entry sil | `database/seeders/WritingSeeder.php` | Editorial + Content + Owner |
| 2 | Tüm `publications` sync → Reuters/Guardian/Le Monde/BBC Türkçe/Foreign Policy sil, sadece "Anadolu" kalır | Aynı dosya | Editorial + Owner |
| 3 | 6 İstanbul-bazlı yeni entry (Content önerisi): "Haber masasında bir kare", "Drone hattında mesafe", "Protokol fotoğrafında görünmeyen", "Sabah dosyası", "Adana alan notu", "Kaynak koruma" — Owner body review sonrası ekle | Aynı dosya | Content + Owner |
| 4 | Kind enum migration: `saha_yazisi` → `foto_notu` + `editoryal` + `analiz` ekle (Owner: DB-driven OK) | `app/Models/Writing.php` + migration | Architect + Content |
| 5 | `writings.photo_credit` (nullable string 120) migration; `writings.cover_caption_json` (json nullable); `writings.is_demo` (bool default false); `writings.hero_eligible` (bool); `writings.cover_hue_a/b` **drop** (dead field) | Migration | Architect |
| 6 | `config/site.php` ekle: `default_photo_credit: 'Foto: Ozan Efeoğlu / AA'`, `affiliation_approved: env('SITE_AA_APPROVED', false)`, `contact_retention_days: 0`, `legal.kvkk_page_slug: 'kvkk'` | `config/site.php` | Owner |
| 7 | `show.blade.php:91` hardcoded `Foto: Ozan Efeoğlu` → dinamik `$writing->photo_credit ?? config('site.default_photo_credit')` | `writing/show.blade.php` | Editorial + Content |
| 8 | `contact_messages.ip_address` + `user_agent` kolonlarını **DROP** (yeni migration); Redis sliding window rate-limit `hash(ip)` TTL 1 saat | Migration + `ContactController.php` | Security + Owner |
| 9 | Contact form KVKK disclaimer + "Bu kanal kişisel; Anadolu Ajansı kurumsal talepleri için ajansa yazın" uyarısı | `contact.blade.php` | Editorial + Security |
| 10 | `/admin/login` 404 fix — Fortify redirect düzelt | `routes/web.php` | Test |
| 11 | `public/robots.txt` — admin + AI crawlers disallow; Sitemap satırı | `public/robots.txt` | SEO + Owner |
| 12 | AA banner `affiliation_approved === true` koşullu — landing + about'ta flag ile sarılı, default false → render yok | View'lar | Owner + Editorial |

---

## IV. OWNER İNSAN İŞLERİ (paralel, 2 hafta)

Dev batch'i beklerken Owner yapmalı:

1. **AA Dilekçe** → AA İK + Editöryal Koordinasyon'a yazılı. İçerik (Owner Agent §5.Pre-approval checklist):
   - Site amacı + kapsamı
   - Hangi AA kareleri kullanılacak (5-8 thumbnail)
   - Kredi formatı ("/AA" slash)
   - AA marka/logo kullanılmayacak
   - Banner metni
   - KVKK politikası (bilgi amaçlı)
   - Contact formun AA kurumsal iletişimden ayrıştığının teyidi
2. **KVKK Avukat** → 2 saat danışmanlık (~5000 TL). Aydınlatma metni review + VERBIS kayıt muafiyeti teyidi + Veri Sorumlusu sıfatı
3. **6 Dispatch Body Review** → Content önerisi taslak; Owner sonuç metni yazar
4. **Portre Final** → mevcut kare tutulur, anasayfa'dan çıkar (hero artık iş-foto)
5. **Drone Pilot Kareler** → `/görüntü` için 6-8 AA-dışı drone kare seç (kişisel/freelance)
6. **Deprem çocuk karesi** → `20230215_…_870256` dosyasını public'ten private/archive'a taşı (hiç kullanılmayacak)

---

## V. ADMIN-DİNAMİK REBUILD (Phases B-E, 9 oturum)

Architect blueprint'inin sentezi:

### PHASE B — Core Dynamicization (3 oturum)
**B.1 — Foundation**
- `app/Support/SettingsRepository.php` (cached, locale-aware, group-based eager load)
- `app/Support/helpers.php` → `site_setting($key, $default)`
- Blade directive `@setting('key')` + view composer `layouts.public` inject
- `SiteSettingsSeeder` — `config/site.php` değerlerini idempotent seed

**B.2 — Identity + Contact + Nav Admin UI**
- 13 settings key × 3 grup × admin formları (Identity, Contact, Nav)
- `site_links` tablosu (social)
- `legal_pages` tablosu + /kvkk /cerez-politikasi /telif routes
- KVKK metni draft seed

**B.3 — Hero Redesign (user yön değişikliği)**
- `hero.mode` enum: `featured_photo` (YENİ DEFAULT) / `rotation` / `typographic` / `portrait`
- Admin photo picker modal (3 sekme: Arşiv / Dispatch kapakları / Yükle)
- Portre anasayfadan çıkar, `/hakkimda` masthead + footer head-tight'ta kalır
- OG image auto = hero_photo conversion

### PHASE C — Photo Archive + Görüntü (2 oturum)
**C.1 — Pipeline**
- `photo_archive_meta` + `IngestPhotoJob` (queued)
- EXIF parse + AA filename regex + `is_aa_wire=true` auto
- Spatie Media conversions: 640/1280/1920 × (WebP + AVIF + JPG fallback) + `og_1200`
- **Watermark crop yasağı** — pipeline sadece resize, hiç crop
- Storage: R2 (prod), public (dev)

**C.2 — `/görüntü` Pilot + Admin**
- `photo_series` + `photo_series_items` model/migration
- `/görüntü` route + index view
- Pilot: "Kuşbakışı / Drone" tek seri, 6-8 kare (Owner seçimi, AA-dışı öncelik)
- Admin: bulk upload + series curator + caption editor
- `writings.cover_media_id` picker admin'den arşive bağlı

### PHASE D — Theme + Legal + Footer (2 oturum)
- Theme settings (paper/ink/accent color picker APCA contrast warning)
- Font scale slider, display/body font dropdown (Fontsource catalog)
- `<x-color-input>` + `<x-tiptap-editor>` blade components
- Legal pages CRUD + TipTap
- Footer drag-drop columns + copyright editable
- Analytics toggle (Plausible self-hosted default)
- Feature flags UI (feed / newsletter / comments / visuals / search)

### PHASE E — Polish (1 oturum)
- Drag-drop ordering (nav items + footer columns + photo series items)
- SEO ileri: JSON-LD Person/WebSite/NewsArticle, sitemap.xml generator, BreadcrumbList
- `<time datetime>` tag, article description layout'a geç, homepage H1 semantik
- Custom 429 view
- Portre WebP + `<link rel=preload>` + `width/height` attribute
- Font subset (latin + latin-ext only, 48 → 12 dosya)
- HIBP fail-closed, daily login lockout, X-Powered-By strip, CSP `unsafe-hashes` kaldır

---

## VI. İMPLEMENTASYON TAKIMI (6 paralel agent — bir sonraki turda)

Hiyerarşi Phase A blocker'dan sonra devreye girer:

```
PATRON (Claude — sentezci)
├── BACKEND AGENT
│   Models + migrations + SettingsRepository + admin controllers
├── FRONTEND AGENT
│   View refactor (setting-driven), admin panel new views,
│   Blade components, TipTap + color picker + drag-drop
├── CONTENT AGENT (reuse)
│   6 dispatch final body, KVKK metin taslak, kicker labels
├── DESIGN AGENT (reuse)
│   Hero mode photo picker UX, admin IA wireframe, foto archive browser
├── SECURITY AGENT (reuse)
│   IP drop migration, Turnstile wire, CSP hardening, rate-limit Redis
└── TEST AGENT (reuse)
    Regression after each phase, new E2E flows (hero mode swap, KVKK disclosure, photo upload), a11y check
```

Her phase sonunda takım rapor verir, patron sentezler, sonraki phase'e geçer.

---

## VII. LAUNCH SEKANSI

| Hafta | Olay | Statü |
|---|---|---|
| 0 | Blocker batch merge (Phase A) + `noindex` flag `true` | ✅ tüm 12 kırmızı çözüldü |
| 0-2 | AA dilekçe + KVKK avukat + 5 kişi beta davet | Owner paralel |
| 2-4 | Phase B dev + feedback iterasyon | Patron + takım |
| 4-6 | Phase C + D dev · AA onay bekleniyor | Patron + takım |
| 6 | **AA onay?** | → Evet: banner flag true + `/görüntü` pilot publish · → Hayır: AA-suz V1 launch (drone pilot + kişisel kareler) |
| 6 | `noindex` kaldır + sitemap submit — **sessiz canlı** | Public launch |
| 6-12 | Phase E polish + organik discovery | Patron |
| 12+ | Phase C full `/görüntü` seriler (AA onayı geldiyse) | Faz 6 |

**Press announcement YOK** — sadece sahibi kendi hesaplarından minimum duyuru; AA onayı gelmişse AA iç iletişimine tek satır mail.

---

## VIII. KIRMIZI IŞIKLAR (launch iptal tetikleyicileri)

Owner'ın kesin duruşu:

1. Uydurma publications herhangi bir yerde kalmış
2. Çocuk kundak karesi site'de görünür
3. IP/UA saklama aktif
4. AA watermarklı kare tek-imza `Foto: Ozan Efeoğlu` ile yayımlanmış
5. Fake Signal/PGP handle görünür
6. İtalik em accent display H1'de kalmış (ADR-016 ihlali)
7. AA yazılı ret var ama banner/foto hâlâ site'de

Herhangi biri canlıya kaçarsa → **derhal takedown + sahibinden özür postu + AA'ya bilgi**.

---

## IX. BAŞARI ÖLÇÜTLERİ

Public launch ancak bu 9 check pass ise:

- [ ] 12 blocker madde kapalı (bu dosya §III)
- [ ] AA yazılı onay **veya** AA-suz tam fotoğraf uyumluluğu
- [ ] KVKK avukat yazılı onay imzalı
- [ ] Lighthouse CWV yeşil (LCP<2.5s / CLS<0.1 / INP<200ms) her public sayfa
- [ ] axe-core 0 critical/serious public sayfalar
- [ ] 112+ Pest test yeşil
- [ ] SEO: canonical + OG + Twitter + JSON-LD + sitemap + robots admin disallow
- [ ] Settings %90+ DB'den okuyor (hardcoded `config('site.*')` sadece fallback)
- [ ] Backup otomatik (R2 snapshot) + first restore test OK

---

## X. ADR SABİTLEMESİ

Bu plan 3 ADR'ya dökülür:

- `docs/decisions/017-owner-decision-dossier.md` — Owner Agent full report
- `docs/decisions/018-admin-first-dynamic-architecture.md` — Architect blueprint
- `docs/decisions/019-launch-strategy-and-compliance.md` — bu master plan özeti + GO/NO-GO kriterleri

---

**Sonuç:** Plan hazır. Onay gelirse sıra:
1. 3 ADR'ı dosyala (bu turdan sonra veya paralel)
2. **Phase A Blocker Batch** başla (1 oturum — dev işi, insan işi paralel yürür)
3. Phase A merge sonrası implementation team launch
4. Phase B-E sıralı uygulama
5. Launch 2-kademeli

**Bekleyen:** Owner'dan **insan işleri için tarih taahhüdü** (AA dilekçe + avukat randevusu) ve Phase A için **dev-tarafı go-ahead**.
