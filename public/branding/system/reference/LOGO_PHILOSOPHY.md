# Logo Philosophy — Editorial Silence

> **Editorial Silence — Yazılı sözün ağırlığını taşır.**
> Bağırmaz; sessizliğinde güvenir.

---

## ADR Reference

- **ADR‑016**: The Field Dossier · v1.0
- **Brand**: Editorial Silence
- **Owner**: Ozan Efeoğlu — gazeteci · araştırmacı · yazar · foto‑muhabir
- **Logo direction**: 04 · Yalın imza
- **Locked**: 2026.05.01

---

## Felsefe

**Editorial Silence** — yazılı sözün ağırlığını taşıyan, bağırmayan, kendine güvenen bir marka kimliği. Bu logo bir gazetecinin, araştırmacının ve yazarın kişisel imzasıdır; bir kurumsal kimlik değil, bir yazı işleri imzasıdır.

Tasarım tek bir karar üzerine kuruludur: **isim, ve sonunda bir nokta**. Daha azı isimsiz olurdu; daha fazlası gürültüye dönüşürdü. Nokta hem cümleyi bitirir hem de bir mühür gibi durur — *yayımlandı* işareti.

**Form ve uzay**: İsim Source Serif 4'ün 600 ağırlığında, sıkı bir tracking ile yazılır (-0.025em). Nokta ismin baseline'ında, soyadın "u" harfinin sağ ucundan **0.4em** mesafede oturur. Bu boşluk pazarlık konusu değil — yakın olursa virgül gibi okunur, uzak olursa ilişki kopar.

**Renk ve materyal**: Üç renk, daha fazla değil. **Ink** mürekkebin kendisidir — saf siyah değil, çünkü saf siyah dijital bir yalandır; gerçek baskı mürekkebi sıcaklık taşır (`#141310`). **Paper** ham kâğıttır (`#faf9f5`), beyazlatılmamış, hafif sıcak. **Kırmızı** (sansürün kırmızısı, `#b91c1c`) yalnızca noktaya ayrılmıştır. Hiçbir yerde başka bir renk kullanılmaz, hiçbir noktada gradient yoktur.

**Kare işaret**: Yatay alana sığmayan bağlamlarda (favicon, app icon, sosyal avatar) yalın imzanın ilk iki harfi — küçük "oe" — kare zemin içinde, sağ alt köşede aynı kırmızı nokta ile yazılır. Bu çoklu çıktının kaynağı tek bir tipografik karardır: aynı font, aynı ağırlık, aynı nokta.

**Ölçek ve ritim**: Logo dört ölçekte yaşar:
- **16–24 px** (browser tab) — `favicon.svg`, sadece "oe."
- **28–40 px** (header, sidebar) — `logo-mark.svg` veya horizontal lockup
- **44–96 px** (footer, künye, e‑mail imza) — `logo-horizontal.svg`
- **180–1200 px** (app icon, OG card) — `apple-touch-icon.svg`, `og-mark.svg`

**Kompozisyon**: Yatay lockup tek satırlıktır — hairline rule, alt başlık, başka geometri yok. Yalnız isim, yalnız nokta. Eğer ek bilgi gerekiyorsa (alt başlık, tagline) o ayrı bir tipografik bloktur, logonun parçası değildir.

---

## Dosya envanteri

| Dosya | Boyut | Format | Kullanım |
|---|---|---|---|
| `favicon.svg` | 32×32 viewBox | SVG | Browser tab, modern tarayıcılar |
| `logo-mark.svg` | 64×64 viewBox | SVG | Genel mark, sosyal avatar (kare zemin) |
| `logo-mark-inverse.svg` | 64×64 viewBox | SVG | Koyu zemin / dark backgrounds |
| `logo-wordmark.svg` | 360×96 viewBox | SVG | Sadece isim (kuralsız) |
| `logo-horizontal.svg` | 360×96 viewBox | SVG | Navbar / header — primary lockup |
| `logo-horizontal-inverse.svg` | 360×96 viewBox | SVG | Koyu navbar varyantı |
| `apple-touch-icon.svg` | 180×180 viewBox | SVG | iOS home screen |
| `og-mark.svg` | 1200×630 viewBox | SVG | Open Graph, Twitter card |

**Renk değerleri (kanonik):**
- Mürekkep: `#141310` — `--color-ink`
- Kâğıt: `#faf9f5` — `--color-bg`
- Kırmızı (sansür): `#b91c1c` — `--color-accent` / `--color-paper-red-700`

**Tipografi referansı:**
- Wordmark "Ozan Efeoğlu": Source Serif 4 Variable, weight 600, opsz 32, tracking -0.025em
- Kare işaret "oe": Source Serif 4 Variable, weight 600, tracking -0.04em
- Tagline (opsiyonel, ayrı blok): IBM Plex Sans Variable, weight 500, all‑caps, tracking 0.18em
- Nokta: dolgu daire, çap = `0.10 × cap‑height`, baseline'a hizalı

---

## Kullanım kuralları

**Yapılacaklar**:
- Logo'yu her zaman safe area (her yönden 0.5× cap‑height) ile koruyun
- Yatay lockup'ı navbar gibi sınırlı yüksek alanlarda kullanın
- Kare işareti (oe.) favicon, sosyal avatar, app icon olarak kullanın
- Inverse versiyonu sadece `--color-ink` veya daha koyu zeminlerde kullanın
- Nokta her zaman ismin sağında, baseline hizasında, **küçük** olmalıdır

**Yapılmayacaklar**:
- Logoyu döndürmeyin, eğmeyin veya stretch etmeyin
- Renkleri değiştirmeyin (özellikle nokta — hep aynı kırmızı)
- Logoya gradient, gölge, glow uygulamayın
- İsmi farklı bir font'la yeniden çizmeyin
- Noktayı genişletmeyin, küçültmeyin veya kaldırmayın
- Noktayı isimden 0.4em'den fazla uzaklaştırmayın
- Yatay lockup'a çerçeve eklemeyin (kare işaret dışında çerçeve yoktur)
- 80 px altında full lockup kullanmayın (favicon kullanın)

---

## Versiyon Tarihi

- **2.0** — 2026.05.01 — **04 Yalın imza** yönü kilitlendi. OE monogram + çerçeve bırakıldı; isim + son‑nokta tek karar.
- **1.0** — 2026.04.26 — İlk yayım. OE monogram + çerçeve. *(superseded)*
