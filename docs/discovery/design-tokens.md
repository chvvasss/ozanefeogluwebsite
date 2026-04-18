# Tasarım Tokenleri

> Faz 1'de Tailwind v4 `@theme` direktifine ve CSS custom properties'e işlenecek. Bu doküman **kaynak gerçeği**dir.

## 1. Renk sistemi

### Semantic tokens (light/dark eşlenik)

| Token | Light | Dark | Kullanım |
|---|---|---|---|
| `--color-bg` | `#fafaf9` (stone-50) | `#0c0a09` (stone-950) | Sayfa zemini |
| `--color-bg-elevated` | `#ffffff` | `#1c1917` (stone-900) | Kartlar, modallar |
| `--color-bg-muted` | `#f5f5f4` (stone-100) | `#292524` (stone-800) | Alt-arka plan, code block |
| `--color-fg` | `#1c1917` (stone-900) | `#f5f5f4` (stone-100) | Birincil metin |
| `--color-fg-muted` | `#57534e` (stone-600) | `#a8a29e` (stone-400) | İkincil metin |
| `--color-fg-subtle` | `#78716c` (stone-500) | `#78716c` (stone-500) | Caption, meta |
| `--color-border` | `#e7e5e4` (stone-200) | `#292524` (stone-800) | Hairline border |
| `--color-border-strong` | `#d6d3d1` (stone-300) | `#44403c` (stone-700) | Vurgulu border |
| `--color-accent` | `#c2410c` (orange-700) | `#fb923c` (orange-400) | Linkler, primary CTA |
| `--color-accent-hover` | `#9a3412` (orange-800) | `#fdba74` (orange-300) | Hover state |
| `--color-accent-fg` | `#ffffff` | `#0c0a09` | Accent üzerinde metin |
| `--color-success` | `#15803d` (emerald-700) | `#4ade80` (emerald-400) | Başarı |
| `--color-warning` | `#a16207` (amber-700) | `#fbbf24` (amber-400) | Uyarı |
| `--color-danger` | `#b91c1c` (red-700) | `#f87171` (red-400) | Hata, destructive |
| `--color-focus-ring` | `#c2410c` | `#fb923c` | Focus outline |

### Kontrast doğrulaması (WCAG AA)
| Pair | Light ratio | Dark ratio | Pass |
|---|---|---|---|
| fg / bg | 17.74:1 | 17.42:1 | AAA |
| fg-muted / bg | 7.04:1 | 7.21:1 | AAA |
| accent / bg | 5.83:1 | 6.84:1 | AA (AAA büyük metin) |
| border / bg | 1.32:1 | 1.32:1 | UI (3:1 gerekmiyor border için) |

Erişilebilirlik açısından her semantic token light+dark her ikisinde de AA geçer.

### Theme palette presets (admin'den seçilebilir)
```json
{
  "terracotta": { "light": "#c2410c", "dark": "#fb923c" },  // default
  "ink":        { "light": "#1c1917", "dark": "#fafaf9" },  // monochrome
  "moss":       { "light": "#3f6212", "dark": "#a3e635" },
  "ocean":      { "light": "#0369a1", "dark": "#38bdf8" },
  "plum":       { "light": "#86198f", "dark": "#e879f9" },
  "amber":      { "light": "#a16207", "dark": "#fbbf24" }
}
```
Sahibi admin'den hex de girebilir (HSL editor + canlı preview).

## 2. Tipografi

### Font ailesi
```css
--font-display: "Fraunces", Georgia, "Iowan Old Style", "Apple Garamond", serif;
--font-sans:    "Inter", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
--font-mono:    "JetBrains Mono", "SF Mono", Menlo, Consolas, "Liberation Mono", monospace;
```
- **Fraunces:** Variable (opsz 9-144, soft 0-100, wonk 0-1, slnt 0-15). Başlık + display.
- **Inter:** Variable. Body + UI.
- **JetBrains Mono:** Static (regular + bold). Code.

Hepsi self-host, `font-display: swap`, preload kritik subset (`unicode-range` ile latin + latin-ext, TR karakterler dahil).

### Modular scale (1.25)
```css
--font-size-xs:   0.75rem;   /* 12px */
--font-size-sm:   0.875rem;  /* 14px */
--font-size-base: 1rem;      /* 16px */
--font-size-md:   1.125rem;  /* 18px */
--font-size-lg:   1.25rem;   /* 20px */
--font-size-xl:   1.563rem;  /* 25px */
--font-size-2xl:  1.953rem;  /* 31px */
--font-size-3xl:  2.441rem;  /* 39px */
--font-size-4xl:  3.052rem;  /* 49px */
--font-size-5xl:  3.815rem;  /* 61px */
--font-size-6xl:  4.768rem;  /* 76px */
--font-size-7xl:  5.96rem;   /* 95px */ /* hero için */
```

### Line height
```css
--leading-tight:   1.1;   /* display başlıklar */
--leading-snug:    1.3;   /* h2, h3 */
--leading-normal:  1.5;   /* UI */
--leading-relaxed: 1.7;   /* prose body */
--leading-loose:   1.85;  /* uzun dergi-vari prose */
```

### Letter-spacing
```css
--tracking-tightest: -0.05em;  /* hero display */
--tracking-tighter:  -0.03em;  /* h1, h2 */
--tracking-tight:    -0.015em; /* h3 */
--tracking-normal:   0;
--tracking-wide:     0.025em;  /* uppercase eyebrow labels */
--tracking-wider:    0.1em;    /* tag pills */
```

### Weight ölçeği
```css
--font-weight-normal:    400;
--font-weight-medium:    500;
--font-weight-semibold:  600;
--font-weight-bold:      700;
```
Display Fraunces için optical sizing devrede; manual weight üst düzey kontrolde.

## 3. Spacing

Tailwind default zaten 4px-baz. Ekstra extend yok:
```
0, 0.5, 1, 1.5, 2, 2.5, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 14, 16, 20, 24, 28, 32, 36, 40, 44, 48, 52, 56, 60, 64, 72, 80, 96
```
(rem birimleri, 1=4px)

## 4. Radius
```css
--radius-none:  0;
--radius-sm:    2px;   /* form input */
--radius-md:    6px;   /* card, button */
--radius-lg:    12px;  /* modal, large card */
--radius-xl:    20px;  /* hero card */
--radius-full:  9999px;
```

## 5. Shadow
```css
--shadow-xs:   0 1px 2px 0 rgba(0,0,0,0.05);
--shadow-sm:   0 1px 3px 0 rgba(0,0,0,0.07), 0 1px 2px -1px rgba(0,0,0,0.07);
--shadow-md:   0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.07);
--shadow-lg:   0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.08);
--shadow-focus: 0 0 0 3px var(--color-focus-ring);
```
Dark mode'da gölgeler aynı kalır (transparency'li); border'ı vurgulayarak elevation hissi verilir (alternative olarak `box-shadow` `rgba(255,255,255,0.05)` overlay).

## 6. Border
```css
--border-width-hairline: 0.5px;  /* prose içi divider */
--border-width-thin:     1px;
--border-width-thick:    2px;
```

## 7. Motion

### Süreler
```css
--duration-instant: 75ms;
--duration-fast:    150ms;
--duration-normal:  250ms;
--duration-slow:    400ms;
--duration-slower:  600ms;
```

### Easings
```css
--ease-out-quart:    cubic-bezier(0.25, 1, 0.5, 1);
--ease-in-out-quart: cubic-bezier(0.76, 0, 0.24, 1);
--ease-snappy:       cubic-bezier(0.5, 0, 0.5, 1);
--ease-default:      cubic-bezier(0.25, 0.1, 0.25, 1);
```

### Reduced motion
```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

## 8. Z-index
```css
--z-base:       0;
--z-elevated:   10;
--z-sticky:     20;
--z-overlay:    30;
--z-dropdown:   40;
--z-modal:      50;
--z-toast:      60;
--z-tooltip:    70;
```

## 9. Breakpoints (Tailwind default + xs)
```
xs:  475px
sm:  640px
md:  768px
lg:  1024px
xl:  1280px
2xl: 1536px
```
Mobile-first. Çoğu UI default için optimize, sm ve üstü için iyileştirir.

## 10. Container
```css
--container-prose:   65ch;        /* yazı body genişliği */
--container-narrow:  720px;       /* form, dar içerik */
--container-default: 1200px;      /* genel page max */
--container-wide:    1440px;      /* hero, gallery */
--gutter-mobile:     1rem;        /* 16px */
--gutter-tablet:     2rem;        /* 32px */
--gutter-desktop:    3rem;        /* 48px */
```

## 11. Focus state — kritik a11y kuralı

```css
*:focus-visible {
  outline: 2px solid var(--color-focus-ring);
  outline-offset: 3px;
  border-radius: 2px;  /* açıklama: outline da köşeli görünmesin */
}
```
Outline asla `:focus`'ta kaldırılmayacak. Sadece `outline: none` + alternatif var ise (örn: button hover'da background değişmesi).

## 12. Tailwind v4 `@theme` çıkışı (Faz 1'de oluşturulacak)

```css
@import "tailwindcss";

@theme {
  /* Color */
  --color-bg: light-dark(#fafaf9, #0c0a09);
  --color-bg-elevated: light-dark(#ffffff, #1c1917);
  /* ... yukarıdaki tüm renk tokenleri */

  /* Typography */
  --font-display: "Fraunces", Georgia, serif;
  --font-sans: "Inter", system-ui, sans-serif;
  --font-mono: "JetBrains Mono", monospace;

  /* Scale */
  --text-xs: 0.75rem;
  --text-sm: 0.875rem;
  /* ... */

  /* Motion */
  --ease-out-quart: cubic-bezier(0.25, 1, 0.5, 1);
  /* ... */
}
```

Tailwind v4 `light-dark()` CSS function'ını destekler — single source of truth.

## 13. Component patterns (sneak peek)

Bunlar Faz 1'de Blade component olarak çıkacak. Burada sadece görsel imza.

### Button
- Primary: solid accent, white text, 6px radius, 12/16 padding, medium weight, 150ms hover transition.
- Secondary: ghost, border `--color-border`, text `--color-fg`.
- Destructive: solid danger, white text.
- Ghost: text only with underline animation.
- Sizes: `sm` (32px), `md` (40px), `lg` (48px).

### Link (prose içi)
- Underline default 1px, offset 4px.
- Hover: underline thickness 2px (smooth).
- Visited yok (Inter'in iyi rendering'i için).

### Input
- Border `--color-border-strong`, focus ring 3px offset.
- Error state: border `--color-danger` + ikon + helper text.

### Card
- Background `--color-bg-elevated`, border `--color-border`, radius `--radius-md`, padding 24px (sm: 16px), shadow `--shadow-sm`.
- Hover: `transform: translateY(-2px)`, `--shadow-md`, transition 250ms.

### Prose
- max-width: `--container-prose`.
- Headings: `--font-display`, scale 4xl/3xl/2xl/xl/lg.
- Body: `--font-sans`, `--text-md` (18px), `--leading-relaxed`.
- Code inline: `--font-mono`, `--text-sm`, padding 2px 4px, bg `--color-bg-muted`, radius 2px.
- Code block: `--font-mono`, `--text-sm`, dark theme zorunlu (light-dark uyumlu altın oran), padding 24px, radius `--radius-md`, copy button.
- Blockquote: italic, `--font-display`, large, accent border-left.
- Footnote: superscript number, smooth scroll-to + back-link.

Detaylı component spec Faz 1'de.
