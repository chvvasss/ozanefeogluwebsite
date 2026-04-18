# Tasarım Araştırması

> Faz 0 keşif çıktısı. Awwwards / SOTD kalibresinde portfolyo + bağımsız blog deneyimleri tarandı, çalan örüntüler çıkarıldı, kendi yaklaşımımıza nasıl uyarlanacağı yazıldı.

## 1. Pazar taraması — portfolyo (incelenen 20+ örnek)

Awwwards, SiteInspire, Httpster, Godly, Lapa Ninja arşivlerinden son 18 ayda öne çıkan örnekler. Çalışan örüntüler:

### 1.1 Tipografi
- **Editorial-display kombinasyonu yaygın:** Display font (genellikle modern serif veya humanist sans) hero'da, body için ayrı bir okuma fontu.
- **Variable font kullanımı standart hale geldi:** Tek dosya, çoklu weight/optical size. Inter Variable, Geist, Söhne Variable.
- **Optical sizing önemli:** Büyük başlıklarda farklı optical size, body'de farklı. Otomatik kullanan örnekler daha "premium" hissediyor.
- **Tracking (letter-spacing) negatif büyük başlıklarda:** Display fontlarda -0.02em ile -0.05em arası tipik.
- **Modular scale 1.25 veya 1.333:** Catlin Smith sayfalarında 1.25 (more conservative); editorial sitelerde 1.333.

### 1.2 Layout
- **Asimetrik grid:** 12-col değil, 8-col + offset / fluid grid yaygın. Hero metni çoğu zaman 60-70% genişlikte ve sol/center'a yakın hizalı.
- **Confident whitespace:** Hero altında 200-400px padding-top yaygın.
- **Side notes / marginalia:** Editorial sitelerde paragrafın yanında küçük not blokları.
- **Sticky/sticky'siz hibrit nav:** Üstte minimal nav, scroll'da daralıyor (height transition).

### 1.3 Renk ve doku
- **Monochrome + 1 accent:** Genelde siyah/beyaz/sıcak gri + 1 vurgu rengi (genelde unexpected: olive, terracotta, cobalt).
- **Light mode default popüler:** Dark mode toggle'ı var ama default light tercih ediliyor (okuma için).
- **Texture / paper noise:** Çok ince grain overlay (SVG noise) "kâğıt hissi" verir, generic flat'ten kurtarır.
- **Renkli accent yerine renk pasajı:** Tek bir image veya gradient panel, geri kalanı sade.

### 1.4 Mikro-etkileşimler
- **Link hover'da underline animasyonu:** Sol→sağ veya çift-katman (üst kalkar, alt iner).
- **Image hover:** Çok hafif scale (1.02), aspect maintained.
- **Page transition:** View Transitions API kullanan örnekler artıyor (Astro, modern static sites).
- **Scroll-triggered, scroll-jacking değil:** Fade-in, parallax minimal. Kullanıcının scroll'unu çalan örnekler düşüyor.
- **Cursor effect:** Çoğu bıraktı (a11y kötü). En iyisi kullanmamak.

### 1.5 Dark mode
- En iyi örnekler dark için ayrı palet kuruyor (sadece invert değil).
- Pure black (#000) yerine kömür (#0a0a0a, #111) tercih.
- Accent dark mode'da daha doygun olmalı (light mode #c2410c → dark #fb923c).

## 2. Bağımsız blog incelemesi (5 örnek)

### Paul Stamatiou (paulstamatiou.com)
- **Çalan:** Photoblog + writing hibridi. Görselin kahraman olduğu büyük, edge-to-edge image kullanımı. Yazılar uzun-form, dikkatli typography.
- **Bizim için:** Image-heavy yazılar için layout pattern. Görselleri full-bleed yapma cesareti.

### Josh Comeau (joshwcomeau.com)
- **Çalan:** Interaktif blog post (canlı playground'lar). React-heavy ama prensip evrensel.
- **Bizim için:** Custom MDX-vari "callout", "code group", "interactive demo" component'leri. Bizim Blade component karşılığı: `<x-callout type="warning">`, `<x-code-group>`.

### Maggie Appleton (maggieappleton.com)
- **Çalan:** "Garden" / "Notes" / "Essays" ayrımı (epistemik durumla). Sketchnote estetik. Sidebar marginalia.
- **Bizim için:** Yazı statüsü kavramı (taslak/seedling/evergreen) güzel ama kapsamı genişletir. Şimdilik basit "draft/published" tut, ileride.

### Robin Rendle (robinrendle.com)
- **Çalan:** Newsletter-vari uzun yazılar, tipografi-öncelikli, illustration spot kullanımı.
- **Bizim için:** RSS-first mentalite. Newsletter ile entegre düşünmek.

### Rauno Freiberg (rauno.me)
- **Çalan:** Aşırı minimal, mono ve sistem fontu, yüksek özgüvenli "az ile çok" anlatımı.
- **Bizim için:** Risk: "kuru" hissedebilir. Bizimki daha sıcak olmalı (kişisel marka).

## 3. Çalan örüntülerin sentezi — bizim yaklaşımımız

### 3.1 Tipografi sistemi
- **Display:** [Fraunces](https://fonts.google.com/specimen/Fraunces) Variable (Optical size + Soft + Wonky) — özgün karakter, "stock Inter" değil. **Self-hostable** (OFL).
- **Body:** [Inter](https://rsms.me/inter/) Variable (display=inter-display kombo) — okunabilir, evrensel, ücretsiz.
- **Mono:** [JetBrains Mono](https://www.jetbrains.com/lp/mono/) — kod blokları için.
- **Modular scale:** 1.25 (major third). Base 16px → 20px → 25px → 31px → 39px → 49px → 61px. Daha büyükler hero için manuel.
- **Optical size:** Variable font feature kullan, 14-72 arası otomatik.

### 3.2 Renk sistemi
- **Light mode neutral:** Off-white (#fafaf9 stone-50) zemin, koyu ink (#1c1917 stone-900) metin.
- **Dark mode neutral:** Kömür (#0c0a09 stone-950) zemin, sıcak beyaz (#f5f5f4 stone-100) metin.
- **Accent kararı:** Default **terracotta** (#c2410c orange-700, dark mode #fb923c orange-400). Admin'den değiştirilebilir (palette preset + custom hex).
- **Functional renkler:** success (emerald), warning (amber), danger (red) — sadece UI feedback'inde, dekoratif değil.

### 3.3 Spacing
- **Base 4px,** ölçek: 4, 8, 12, 16, 24, 32, 48, 64, 96, 128, 192, 256.
- Tailwind default spacing zaten bu doğrultuda — extend etmeden kullan.

### 3.4 Radius / shadow / border
- **Radius:** 0 (keskin), 2px (input), 6px (kart), 12px (modal), 9999 (badge/avatar).
- **Shadow:** Az ve incelikli. `0 1px 2px rgba(0,0,0,0.05)` (subtle), `0 4px 12px rgba(0,0,0,0.08)` (raised). Material Design ağır gölgelerinden kaçın.
- **Border:** `0.5px` veya `1px` ince. Renk: stone-200 light, stone-800 dark.

### 3.5 Motion
- **Süre skalası:** 75ms (instant feedback), 150ms (hover), 250ms (toggle), 400ms (page-section), 600ms (page transition).
- **Easing:** Custom cubic-bezier: `cubic-bezier(0.25, 0.1, 0.25, 1)` (standart), `cubic-bezier(0.5, 0, 0.5, 1)` (snappy).
- **`prefers-reduced-motion`:** `transition-duration` 0ms zorla, scroll-triggered animasyonlar disable, parallax disable.

### 3.6 Layout sistemi
- **Container:** Maksimum okuma genişliği 65ch (~720px) body için. Görseller daha geniş olabilir (full-bleed dahil).
- **Grid:** CSS Grid `grid-template-columns: [full-start] minmax(1rem, 1fr) [main-start] minmax(0, 65ch) [main-end] minmax(1rem, 1fr) [full-end]` pattern. Yazı içinde `class="full-bleed"` ile görseller geniş olur.

### 3.7 Detail orientation
- **Karşılayan animasyon:** Sayfa yüklendiğinde hero hafif fade+rise (800ms, ease-out).
- **Linkler:** Underline default (text-decoration-thickness: 1px, underline-offset: 4px). Hover'da thickness 2px (smooth).
- **Form state:** Focus ring offset, error border + ikon + mesaj triple feedback.
- **Kod blokları:** Şeritli dil etiketi (sağ üst), copy button (klavye erişilebilir), satır vurgusu desteği.

## 4. Bizim için kaçınma listesi (brief'in tekrarı, somutlaştırılmış)

| Anti-pattern | Onun yerine |
|---|---|
| Mor→pembe gradient hero | Solid renk veya tek dramatik fotoğraf |
| Merkez hizalı "Welcome..." | Sol hizalı editorial başlık (büyük, asimetrik) |
| Heroicons her button'da | Birkaç custom-drawn / Phosphor Bold icon, sparingly |
| Glassmorphism panel | Solid card, ince border, hafif shadow |
| Stock unsplash | Kendi çekimleri / illüstrasyonlar / abstract noise |
| Generic gri kart grid | Asymmetric "bento" grid + farklı içerik tipleri |
| "Hi I'm X, passionate dev" | Spesifik, anlatı dokulu kişisel cümle |
| Emoji-ağırlıklı copy | Tipografik vurgu (italic, weight) |
| Cursor effect | Yok |
| Scroll-jacking parallax | Hafif scroll-fade max |

## 5. İlk wireframe yönü (Faz 1'de detay)

```
Anasayfa:
┌──────────────────────────────────────────────────┐
│ [logo]              works  blog  about  contact  │
├──────────────────────────────────────────────────┤
│                                                  │
│  Ozan Efeoğlu                                    │
│  Builds quietly                                  │
│  ambitious software.                             │  ← left, big, asymmetric
│                                                  │
│  Currently building [link] · Based in [link]     │  ← small caption
│                                                  │
├──────────────────────────────────────────────────┤
│                                                  │
│  Selected work                              ↗    │
│                                                  │
│  ┌──────────────┐  ┌──────────────┐              │
│  │ project 1    │  │ project 2    │              │  ← bento, irregular
│  │ image+title  │  │ image+title  │              │
│  └──────────────┘  └──────────────┘              │
│  ┌────────────────────────────────┐              │
│  │ project 3 (full bleed wide)    │              │
│  └────────────────────────────────┘              │
│                                                  │
├──────────────────────────────────────────────────┤
│                                                  │
│  Recently writing                           ↗    │
│                                                  │
│  · Apr 12 — On rebuilding my site without ...    │  ← simple list, dates
│  · Mar 30 — A small case for boring software     │
│  · Mar 15 — Notes from three months of ...       │
│                                                  │
├──────────────────────────────────────────────────┤
│  About snippet + photo                           │
├──────────────────────────────────────────────────┤
│  Contact CTA                                     │
└──────────────────────────────────────────────────┘
```

**Yazı detay sayfa:**
```
┌──────────────────────────────────────────────────┐
│  ← back to writing                               │
│                                                  │
│  ESSAY · 8 MIN READ · APR 12, 2026               │  ← meta tipografik
│                                                  │
│  On rebuilding my site                           │
│  without compromise                              │  ← display font, large
│                                                  │
│  ──────────────────────────────────              │  ← subtle divider
│                                                  │
│  Lorem ipsum body text in serif/sans, 65ch       │
│  width, generous leading (1.7), comfortable...   │
│                                                  │
│  > Pull quote, larger, italic, set off           │
│                                                  │
│  Body continues ...                              │
│  ┌──────────────────────────────────────────┐    │
│  │ code block, dark background              │    │
│  │ syntax highlighted                       │    │
│  └──────────────────────────────────────────┘    │
│                                                  │
│  More body, footnotes¹ inline like this...       │
│                                                  │
│  ──────────────────────────────────              │
│                                                  │
│  Related: [post] [post] [post]                   │
│  ¹ Footnotes here                                │
└──────────────────────────────────────────────────┘
```

## 6. Aksiyona dönüştürme

Bu araştırmadan çıkarılacak somut tasarım tokenleri `design-tokens.md`'de. Tailwind v4 `@theme` ve CSS custom properties olarak Faz 1'de işlenecek.
