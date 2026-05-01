# Editorial Silence — Font Handoff Paketi

**Hedef:** Anthropic Design System çıktısındaki `fonts/` klasörüne drop edilecek.
Sekiz `.woff2` dosyası — sistem font fallback'lerini değiştirir, pixel-perfect render sağlar.

## İçerik

| Dosya | Boyut | Aile / weight |
|---|---|---|
| `source-serif-4-latin-wght-normal.woff2` | 50 KB | Source Serif 4 Variable, wght axis, upright |
| `source-serif-4-latin-wght-italic.woff2` | 51 KB | Source Serif 4 Variable, wght axis, italic |
| `source-serif-4-latin-opsz-normal.woff2` | 122 KB | Source Serif 4 Variable, opsz axis, upright |
| `source-serif-4-latin-opsz-italic.woff2` | 130 KB | Source Serif 4 Variable, opsz axis, italic |
| `ibm-plex-sans-latin-wght-normal.woff2` | 46 KB | IBM Plex Sans Variable, upright |
| `ibm-plex-sans-latin-wght-italic.woff2` | 50 KB | IBM Plex Sans Variable, italic |
| `ibm-plex-mono-latin-400.woff2` | 15 KB | IBM Plex Mono Regular |
| `ibm-plex-mono-latin-500.woff2` | 15 KB | IBM Plex Mono Medium |
| **TOPLAM** | **484 KB** | |

## Kaynak

Tüm dosyalar projenin `node_modules/` ağacından kopyalandı:
- `@fontsource-variable/source-serif-4` (Adobe, SIL Open Font License)
- `@fontsource-variable/ibm-plex-sans` (IBM, SIL Open Font License)
- `@fontsource/ibm-plex-mono` (IBM, SIL Open Font License)

Lisanslar Open Font License — ticari + ürün kullanımı serbest.

## Drop talimatı

1. Anthropic'ten gelen tasarım sistemi zip'ini açın.
2. Bu klasördeki 8 `.woff2` dosyasını **doğrudan** zip'in `fonts/` klasörüne kopyalayın.
3. `colors_and_type.css` dosyasındaki `@font-face` blokları zaten bu adlandırmaya göre yazılmış olmalı — başka değişiklik gerekmez.
4. Tarayıcıda bir kit sahnesini açın; sistem font fallback'i yerine artık gerçek fontlar yüklenmeli.

## Doğrulama checklist

- [ ] Source Serif 4 — Türkçe karakterler düzgün (ğ, ı, ş, ç, ö, ü, İ)
- [ ] IBM Plex Sans — caps tracking 0.18em düzgün ölçeklenmiş
- [ ] IBM Plex Mono — eyebrow/colophon yatay düzgün
- [ ] CLS = 0 (font swap sıçramasız — `size-adjust` + `ascent-override` token'ları aktif olmalı)
- [ ] `font-display: swap` — fontlar yüklenirken fallback görülür, atlama yok
- [ ] Optical-size axis — büyük başlıklarda ince serif, küçük metinde kalın çizgi (Source Serif opsz çalışıyor)

## Versiyonlar

Paketleme tarihi: 2026-05-01
Source Serif 4: Fontsource v5.x
IBM Plex Sans: Fontsource v5.x
IBM Plex Mono: Fontsource v5.x

Bu paket projenin canlı bağımlılıklarından türetilmiştir; site `npm install` ile aynı dosyaları self-host eder.
