# Editorial Silence — Logo & Marka Felsefesi

**Versiyon:** 2.0 — *Yalın İmza* (04) · 2026-05-01 · **LOCKED**
**Sahip:** Ozan Efeoğlu — gazeteci · araştırmacı · yazar · foto-muhabir
**Site:** ozanefeoglu.com
**Tipografi:** Source Serif 4 Variable (display, opsz axis aktif) · IBM Plex Sans (sans) · IBM Plex Mono (eyebrow)
**Renk paleti:** Ink `#141310` · Paper `#faf9f5` · Censor's Red `#b91c1c`

---

## Felsefe

**Editorial Silence** — yazılı sözün ağırlığını taşıyan, bağırmayan, kendine güvenen bir marka kimliği. Bu logo bir gazetecinin imzasıdır; bir kolofonun son satırı, bir editörün cümlenin sonuna koyduğu noktadır.

Tasarım iki harf ve bir noktadan ibarettir: **`oe.`**

Lowercase "oe" — büyük harf bir başlık talep eder, küçük harf bir cümlenin içinde yaşar. Sahibi gazeteci — manşet değil; cümle. İki harf arasındaki bağ Source Serif 4'ün geçiş serifinde, optical-size axis'i 48'e set edilmiş hâliyle kazınır; ne çağdaş bir geometric sans'ın steriliği, ne de süslü bir didone'un dramı; sadece klasik baskı. Sağdaki tek kırmızı nokta bir editörün kalemiyle koyulan onay noktasıdır — "okudum, gönderdim".

**Form ve uzay**: Mark çerçevesizdir. Önceki versiyonda kare çerçeve vardı; o çerçeve bir disiplin sözü veriyordu ama markayı içine kapatıyordu. Yalın İmza versiyonu çerçeveyi atar — harfler kendi disiplinini taşır. Negatif alan harflerin nefes almasına izin verir; kırmızı nokta harflerin sağında, baseline'a yakın, neredeyse bir period gibi durur. 64 unit grid'de "o" ve "e" 6-30 ve 30-46 arasını kullanır; nokta cx=49 cy=46. Bu sayısal disiplin tesadüf değildir; baskı mizanpajıdır.

**Renk ve materyal**: Üç renk, daha fazla değil. **Ink** mürekkebin kendisidir — saf siyah değil, çünkü saf siyah dijital bir yalandır; gerçek baskı mürekkebi sıcaklık taşır. **Paper** ofset baskı kâğıdının ışığa karşı verdiği o hafif sarımsı tondur. **Censor's Red** ise sadece bir noktada vardır: editörün kalemi, mührün kırmızı yakası, "imprimatur" damgası geleneğinden gelir. Bu kırmızıyı asla genişletmeyiz; o sadece bir noktadır.

**Ölçek ve ritim**: Mark dört ölçekte yaşar:
- **16–32 px** (favicon, browser tab) — `oe.` tek hâl, opsz=24
- **48–96 px** (navbar, app icon) — aynı kompozisyon, opsz=48
- **128 px+** (mark print, social avatar) — opsz=60
- **Wordmark** (480px+) — "Ozan Efeoğlu" tam isim + sağında küçük kırmızı nokta

Optical size axis bilinçli olarak ölçeğe göre ayarlanır — büyük render'da serifler ince ve uzun, küçük render'da kalın ve kısa. Bu Source Serif 4'ün gerçek display rendering'idir; sıradan bir font'ta sadece ölçek değişir, opsz axis varlığında tasarım değişir.

**Kompozisyon ve denge**: Yatay lockup'ta wordmark "Ozan Efeoğlu" + sağında kırmızı nokta; mark ayrı kullanılmaz — wordmark kendi başına bir mark gibi davranır. Navbar'da mark `oe.` kompakt formunda solda, wordmark stack'i sağda. Mobile drawer'da wordmark kaybolur, sadece `oe.` kalır.

**Hiyerarşi**: Mark → Wordmark → Tagline (opsiyonel italic). Tagline asla logoyla aynı görsel ağırlıkta olmaz; eyebrow olarak (caps tracked, mono font) sessizce konumlanır. Bu hiyerarşi okuyucuya "önce kim, sonra ne" der — gazeteciliğin temel prensibi.

---

## Versiyon Notu — v1.0'dan v2.0'a

**v1.0 (Stamp)** — UPPERCASE OE, kare çerçeve, sağ alt köşede red dot. "Editor's stamp" felsefesi. Path-based, tüm cihazlarda identik render.

**v2.0 (Yalın İmza, LOCKED)** — lowercase `oe.`, çerçevesiz. "Editor's signature" felsefesi. Source Serif 4 + opsz axis ile metrik-keskin. Daha humble, daha literary, daha az süs. Anthropic Design System süreciyle locked, "04 · Yalın İmza" yön damgası.

**Migration nedenleri:**
- Lowercase = humble. Sahibi manşet değil, cümle.
- Çerçevesiz = sessizlik. Editorial Silence felsefesinin saf hâli.
- Source Serif 4 + opsz = metric-aligned, ölçeğe göre optimize.
- Wordmark'ta period = "burada bir cümle bitti" damgası.

---

## Ustalık Standardı

Bu logo bir saatte değil, dört yüz yıl içinde tasarlanmıştır. Gutenberg'in tipo geleneğinden, William Caslon'un kâğıt kontrastından, Jan Tschichold'un asimetrik tipografisinden, Massimo Vignelli'nin Helvetica disiplininden, Le Monde'un dikey kolonlarından miras alır. Source Serif 4'ün opsz axis'i klasik baskının "optical scaling" geleneğinin dijital karşılığıdır — yüzyıllar önce typecutter'lar farklı punto için farklı punch keserdi; biz aynısını CSS `font-variation-settings`'le yapıyoruz.

Hiçbir öğe gradient taşımaz, hiçbir öğe gölgeli değildir, hiçbir öğe bevel/emboss/glow gibi 90'lar grafik kümülasyonu içermez. Bu logo tek mürekkep baskıyla, ofset baskıyla, gravürle, lazer kesimle, embosé ile, riso baskıyla aynı şekilde çalışır.

---

## Çıktı Sistemi

| Dosya | Boyut | Format | opsz | Kullanım |
|---|---|---|---|---|
| `public/favicon.svg` | 32×32 | SVG (text) | 24 | Browser tab |
| `public/branding/logo-mark.svg` | 64×64 | SVG (text) | 48 | Genel mark, sosyal medya avatar |
| `public/branding/logo-mark-inverse.svg` | 64×64 | SVG (text) | 48 | Koyu zemin |
| `public/branding/logo-wordmark.svg` | 360×96 | SVG (text) | 32 | Sadece isim + period |
| `public/branding/logo-horizontal.svg` | 360×96 | SVG (text) | 32 | Navbar / header |
| `public/branding/logo-horizontal-inverse.svg` | 360×96 | SVG (text) | 32 | Koyu navbar |
| `public/branding/apple-touch-icon.svg` | 180×180 | SVG (text) | 60 | iOS / Android home screen |
| `public/branding/og-mark.svg` | 1200×630 | SVG (text) | 60 | Open Graph / Twitter card |

**Renk değerleri (v2.0 — DEĞİŞMEDİ):**
- `--brand-ink: #141310` (mürekkep)
- `--brand-paper: #faf9f5` (kâğıt)
- `--brand-stamp: #b91c1c` (censor's red)

**Tipografi referansı:**
- Mark: Source Serif 4 Variable, weight 600 SemiBold, lowercase, opsz scale ile ölçeğe ayarlı
- Wordmark: Source Serif 4 Variable, weight 600, letter-spacing -0.025em
- Eyebrow: IBM Plex Mono, weight 500, all-caps, tracking 0.18em

---

## Kullanım Kuralları

**Yapın:**
- Mark'ı her zaman safe area (8 unit her yan) ile koruyun
- Yatay wordmark'ı navbar/header gibi sınırlı yüksek alanlarda kullanın
- Sadece-mark'ı (`oe.`) favicon, sosyal avatar, app icon olarak kullanın
- Inverse versiyonu sadece `--color-ink` veya daha koyu zeminlerde kullanın
- Source Serif 4 yüklü olduğundan emin olun (site Fontsource ile self-host eder)

**Yapmayın:**
- Lowercase'i UPPERCASE yapmayın (v1.0 dönüşü değil; bu locked karar)
- Mark'ı eğmeyin, döndürmeyin, gradient eklemeyin, gölge vermeyin
- Wordmark'ın font'unu değiştirmeyin
- Kırmızı noktayı genişletmeyin, çıkarmayın veya başka renge çevirmeyin
- Mark'a çerçeve veya outline eklemeyin (v1.0 paterni — bilinçli olarak terkedildi)
- 16 pixel altında full mark kullanmayın

---

## Versiyon Tarihi

- **2.0 — 2026-05-01** — *Yalın İmza* (04). LOCKED. Lowercase oe + period, çerçeve atıldı, Source Serif 4 + opsz axis. Anthropic Design System çıktısıyla locked.
- **1.0 — 2026-04-26** — Stamp. UPPERCASE OE + kare çerçeve + red dot, path-based. v2'ye terkedildi (`docs/branding/archive/v1-stamp/` altında saklı).
