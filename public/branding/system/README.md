# Ozan Efeoğlu — Editorial Silence Design System

**Codename:** "The Field Dossier" (ADR‑016)
**Brand:** Editorial Silence v1.0
**Owner / surface:** Ozan Efeoğlu — kişisel yayın platformu (gazeteci · araştırmacı · yazar · foto‑muhabir)
**Voice:** Türkçe, ciddi, sade, kazınmış. Bağırmaz; sessizliğinde güvenir.

---

## What this is

This folder is a working design system for `ozanefeoglu.com` — a personal long‑form publishing platform built around four surfaces:

- **/yazılar** — saha yazısı, röportaj, deneme, not (longform editorial)
- **/goruntu** — Anadolu görsel arşivi (foto‑seri + drone + kontrol baskısı)
- **/hakkımda** — biyografi + workareas (saha · görsel · araştırma · yayıncılık)
- **/iletişim** — gerçek kanallar yalnız (e‑posta · Signal · PGP)
- **Hukuksal:** /kvkk, /gizlilik, /kunye
- **CMS Admin (yazı masası):** writings · publications · pages · photos · contact · settings · users · audit

The system is built to ASVS Level 2, WCAG AA, and Core Web Vitals green; the visual language is a deliberate rebuild of the codebase's earlier "editorial blog" register into a tighter, kolofon‑aesthetic editorial dossier.

---

## Sources

| Source | Path / link | Access |
|---|---|---|
| Production codebase (Laravel + Blade + Alpine) | `ozanefeoglu.com/` (mounted) | local file‑access; user must re‑attach if disconnected |
| Brand repo | `chvvasss/ozanefeogluwebsite` | GitHub (read on demand) |
| ADR‑016 — Field Dossier | `reference/ADR-016-field-dossier.md` | in‑project |
| Logo philosophy | `reference/LOGO_PHILOSOPHY.md` | in‑project |
| Token & component CSS (verbatim copies) | `reference/_tokens.css`, `_base.css`, `_components.css`, `_editorial.css`, `_grid.css` | in‑project |

The reader does **not** need access to the source codebase to use this system; the rules and tokens are reproduced here in full.

---

## Onboarding — 30 dakikada içeriden

This system is large. To avoid the trap of "skim the README and miss everything" — read in this order:

### 0. Açılış (3 dakika)

Open `index.html` (root). It is the **doorway** — every artefact in the system is one click away from there. If you read nothing else, read that page.

### 1. Felsefe (8 dakika)

`reference/ADR-016-field-dossier.md` — the *Field Dossier* doctrine. What it is, what it isn't, why corners are square, why there are exactly three colours.

Then `reference/LOGO_PHILOSOPHY.md` — what the OE mark means, where it is allowed to appear, what minimum size keeps the dot legible.

These two files are why everything else looks the way it does. Skipping them turns the rest of the system into arbitrary rules.

### 2. Kurallar (10 dakika)

This README, in order:
- **CONTENT FUNDAMENTALS** — voice, casing, microcopy. *The voice is the loudest element.*
- **VISUAL FOUNDATIONS** — three colours, three families, modular 1.2, square corners.
- **ICONOGRAPHY** — typewriter glyph rules and the small list of allowed marks.

### 3. Belirteçler & bileşenler (5 dakika)

Open these in order:
- `reference/index.html` — referans hub.
- `reference/tokens.html` — every CSS variable, every value.
- `reference/components.html` — the 10 primitives with prop tables.
- `reference/states.html` — interaction sextet + body sextet.

Then look at `colors_and_type.css` once — it is the canonical export.

### 4. Sahneler (5 dakika)

- `ui_kits/public/index.html` — 13 public sahne (yazılar, görüntü, hakkımda, iletişim, yazı detay, 404, 500, KVKK, gizlilik, künye, bakım, boş hâl).
- `ui_kits/admin/index.html` — 15 yönetim sahnesi (masa, yazılar, oluşturma, gelen kutusu, ayarlar, denetim, fotoğraf, sayfalar, yayınlar, kullanıcılar, profil, oturumlar, 2FA kurulum, 2FA kapatma, yedek).

Click around. Both kits use the kit‑bar at the top to switch sahne.

---

## Index

```
Editorial Silence/
├── README.md                  ← you are here
├── SKILL.md                   ← skill manifest (Claude Code‑compatible)
├── index.html                 ← design system doorway (start here)
├── colors_and_type.css        ← single‑file token export (canonical)
│
├── assets/                    ← brand marks
│   ├── logo-mark.svg          OE çerçeve + nokta (ve inverse)
│   ├── logo-horizontal.svg    yatay kilit (ve inverse)
│   ├── logo-wordmark.svg      saf wordmark
│   ├── favicon.svg            16/32 px
│   ├── apple-touch-icon.svg   180 px
│   └── og-mark.svg            social card mark
│
├── fonts/                     ← .woff2 substitution layer (README inside)
│
├── reference/                 ← raw doctrine + el kitabı
│   ├── ADR-016-field-dossier.md     felsefe
│   ├── LOGO_PHILOSOPHY.md           marka anlamı
│   ├── _tokens.css                  prod token kaynağı (verbatim)
│   ├── _base.css | _components.css | _editorial.css | _grid.css
│   ├── _ref.css                     referans sayfaları stili
│   ├── index.html                   referans hub
│   ├── states.html                  durum matrisi (etkileşim + gövde)
│   ├── components.html              10 primitif, prop tabloları
│   └── tokens.html                  her CSS değişkeni
│
├── preview/                   ← 18 tasarım sistemi kartı (700×h)
│   ├── _card.css
│   ├── palette-3color.html | palette-paper-scale.html | palette-semantic.html
│   ├── type-display.html | type-scale.html | type-families.html
│   ├── type-atoms.html | type-pullquote.html
│   ├── spacing-radius.html | spacing-rhythm.html | spacing-rules.html
│   ├── grid-12col.html
│   └── components-buttons.html | components-forms.html
│       components-flash.html | components-channel.html
│       components-marginalia.html | components-writing-row.html
│
└── ui_kits/
    ├── public/                ← 13 sahne — kişisel site
    │   ├── README.md          kit notları
    │   ├── index.html         interaktif kit‑bar + sahne yükleyici
    │   ├── kit.css            sahne‑seviyesi destek CSS
    │   ├── primitives.jsx     Btn, Field, Eyebrow, Tag, Chip, …
    │   ├── data.jsx           sahte içerik (yazılar, fotoğraflar)
    │   ├── Header.jsx | Footer.jsx
    │   ├── HomeScene.jsx      anasayfa (6 sahne segmenti)
    │   ├── OtherScenes.jsx    yazılar listesi, görüntü, hakkımda, iletişim
    │   ├── Yazi.jsx           yazı detay sahnesi
    │   └── SystemScenes.jsx   404, 500, KVKK, gizlilik, künye, bakım, boş
    │
    └── admin/                 ← 15 sahne — Yazı Masası CMS
        ├── README.md
        ├── index.html         interaktif kit‑bar + sahne yükleyici
        ├── admin.css          panel chrome + tablo + form
        ├── data.jsx           navigasyon + sahte koleksiyonlar
        ├── Shell.jsx          AdminSidebar + AdminTopbar
        ├── Scenes.jsx         masa, yazılar, oluştur, gelen, ayarlar, denetim
        └── MoreScenes.jsx     fotoğraf, sayfalar, yayınlar, kullanıcılar,
                               profil, oturumlar, 2FA kurulum/kapatma, yedek
```

---

## CONTENT FUNDAMENTALS

The voice is the loudest design element in this system. **Most products are decorated; this one is written.** Treat copy as a primary surface.

### Tone

- **Ciddi, sade, kazınmış.** Like an editor's signature, not a marketer's pitch.
- **Self‑confident through silence.** The page does not announce itself — no "welcome", no "hello there", no "I'm a writer who…". It begins with the work.
- The narrator is a journalist who has been to where they write about. Authority comes from having been there, not from declaring expertise.

### Person & address

- **Türkçe. Doğrudan. Tezgâh‑altı kelime yok.** No slang, no English code‑switching unless the term is technically required.
- Avoid "ben/biz" hero copy. Headings refer to the **work** ("Saha yazısı · Hatay"), not the author.
- When the author appears, it is in third‑person colophon position: "Ozan Efeoğlu · Yayıncı / muhabir · İstanbul, TR".
- "Siz" / formal address never used in casual UI; the site assumes a literate adult reader.

### Casing

- **Türkçe sentence case** for headlines and titles. *"Hatay'da bir kahvenin tezgâhı"*. Never Title Case, never ALL‑CAPS for content.
- **MONO ALL‑CAPS, tracking 0.18em** is reserved for editorial machinery: eyebrows, kickers, datelines, location stamps, künye fields, section labels. This is the typewriter strip, not the body.
- Numbers are tabular. Dates default to `dd.mm.yyyy` in dense rows, `26 Nisan 2026` (long form, Turkish month name) in datelines.
- Turkish characters (`ğ ı İ ş ç ö ü`) must always render correctly — never substitute `i` for `İ` etc.

### Naming pattern (the four nav items)

`Yazılar · Görüntü · Hakkında · İletişim` — bare nouns, no verbs, no qualifiers. The site does not say "Read my writing"; it says "Yazılar" and trusts you.

### Forbidden phrases

- "Welcome to my world", "Hello there", "Hi, I'm Ozan"
- "Gittim, baktım, yazdım" (the old slogan‑hero, explicitly killed in ADR‑016)
- "Coming soon" / "Yakında" placeholders that fake content
- "Sign up for my newsletter" pop‑ups, "Follow me on…" social hero blocks
- Marketing superlatives ("award‑winning", "ödüllü", "leading", "öncü")

### Examples (from the codebase, verbatim)

| Surface | Copy |
|---|---|
| Eyebrow | `Saha · 016 dosya` |
| Kicker (accent) | `Son dosya · İSTANBUL` |
| Title | `Kasanın altındaki şehir` |
| Standfirst (italic display) | *"Hatay'da bir kahvenin tezgâhında, kasa makinesinin ses çıkarmadığı saatlerde, bir başka şehir kuruluyordu."* |
| Dateline | `26 NİS 2026 · Saha yazısı · 14 dk` |
| Disclosure box | `Bu yazı için seyahat ve konaklama kendi imkânlarımla karşılandı; herhangi bir kuruluş tarafından sponsorluk verilmedi.` |
| Empty‑state cover | `FOTOĞRAF EKLENMEDİ` (mono caps, no decoration) |
| Admin nav group | `Yayın · Gelen kutusu · Site · Hesap` |
| Admin brand | `Ozan Efeoğlu yazı masası` |
| Footer kolofon row | `© 2026 Ozan Efeoğlu` · `OZANEFEOGLU.COM` (right) |

### Emoji & symbols

- **No emoji.** Anywhere. Ever. (Brand rule, ADR‑016.)
- Limited Unicode glyphs are allowed as editorial machinery: `·` (middle dot — primary separator), `→ ↗` (link affordance), `◐ ◑` (theme toggle), `≡ ×` (drawer), `▤ ✎ ⎈ ☰ ◨ ✉ ⚙` (admin nav stand‑ins where the codebase uses them).
- Numerals are spelled in body prose ("on yıl") for round counts, but **always digits** in datelines, read‑times, archive counts.

---

## VISUAL FOUNDATIONS

### Palette (three colors, not more)

| Token | Hex | Role |
|---|---|---|
| `--brand-ink` | `#141310` | Mürekkep — never pure black; warm‑shifted |
| `--brand-paper` | `#faf9f5` | Kâğıt — never pure white; offset warm |
| `--brand-stamp` | `#b91c1c` | Censor's red — single‑point only |

A neutral **paper scale** (`--color-paper-50` … `-950`) gives the rest of the surface vocabulary; semantic tokens (`--color-bg`, `--color-bg-elevated`, `--color-bg-muted`, `--color-bg-sunken`, `--color-rule`, `--color-rule-strong`, `--color-ink-muted`, `--color-ink-subtle`) are aliased onto it. Status semantics (`--color-success` `#4a6b3a` moss, `--color-warning` `#8a6a2c` aged amber, `--color-danger` `#883028` oxidized rust) are calibrated to **not** read as the censor's red.

**Stamp red usage rules — non‑negotiable:**
1. Focus ring (outline)
2. `<a>` body link `text-decoration-color`
3. **One** `.btn` (primary) per page max — and even that is usually ink on paper
4. The single dot in the logo mark
5. Eyebrow / kicker `--accent` variant, **once** per scene
6. Never on headings, badges, large fills, gradients, illustrations.

### Typography

Three families, no more:

| Role | Family | Notes |
|---|---|---|
| Display / serif | **Source Serif 4 Variable** (Adobe, OFL) | `opsz 8–60`, weights 400/500/600/700; the only italic used |
| Sans / body | **IBM Plex Sans Variable** | UI, controls, body where we don't want serif |
| Mono | **IBM Plex Mono** | eyebrows, datelines, kolofon, ref marks |

Modular scale **1.2** (tighter editorial rhythm than the old 1.25): `--text-xs 0.78rem` → `--text-8xl 5.83rem`. Tracking `--tracking-caps 0.18em` is the eyebrow standard.

**Display registers** (three voices, all weight 600+):
- `.display-statuesque` — opsz 144, clamp(2.2 – 9rem). Hero title only.
- `.display-editorial` — opsz 72, clamp(3xl – 5xl). Section titles.
- `.display-quiet` — opsz 48, clamp(xl – 2xl). Card / row titles.

Italic is **never** used as accent emphasis (the old "9 italic em vurgu" pattern is killed in ADR‑016). Italic is reserved for: standfirst, pull‑quote, photo‑caption credit. Drop caps are removed; the lede is set via `.lede-open` (first 2–3 mono‑caps eyebrow words).

### Grid — Field Dossier (12 columns)

- `--container-base 1100px` (default), `--container-wide 1320px`
- Gutter: `clamp(1rem, 1.6vw, 1.5rem)` — fluid
- Page pad: `clamp(1rem, 4vw, 3rem)`
- Span helpers: `.dg-1 … .dg-12`. Editorial‑favorite splits are `.dg-7 / .dg-5` (lead photo + standfirst) and `.dg-4 / .dg-8` (margin + prose).

### Spacing & rhythm

`--space-row clamp(1rem,1.6vw,1.5rem)`, `--space-subsection clamp(2rem,4vw,3.5rem)`, `--space-section clamp(4rem,9vw,8rem)`. Scenes are separated by these macros, not by hand‑tuned magic numbers.

### Backgrounds

- Default `--color-bg` is `--color-paper-100` (warm offset white).
- Scene shading is layered, **never gradient** — `.scene--muted` (paper‑200), `.scene--darker` (paper‑300), `.scene--inverse` (ink, with `--layer-ink-inverse` text).
- A single atmospheric layer is permitted: a 0.022‑opacity paper‑grain texture overlay (the only non‑editorial visual). Nothing else.
- **No** stock photography, no AI imagery, no illustrative SVGs, no decorative shapes. Photos must come from the owner's archive.

### Borders, rules, dividers

- The full decoration vocabulary: `1px var(--color-rule)` hairline, `1px var(--color-rule-strong)`, `1px var(--color-ink)` ink hairline, `2px var(--color-ink)` section break, and the `3px var(--color-accent)` **brass indent** (left border on pull‑quotes — max one per page).
- That is everything. There is no `box-shadow` system because **there are no shadows**. There is no glow, bevel, or emboss.
- Borders are always single‑hairline, never double, never dashed for decoration (dashed reserved for "this is empty / placeholder").

### Corners

- Square corner paradigm. Radius scale: `1px / 2px / 4px / 8px`.
- **4px** is the maximum, used only on form inputs. Buttons sit at 2px. Cards, modals, drawers, panels are square (0 radius).
- 8px exists for emergency use only (e.g. system‑mandated radii that can't be argued with) and is flagged in code review.

### Shadows / elevation

There is no shadow system. Elevation is conveyed by **paper‑scale layering** (`.scene--muted` over `.scene--darker`) and by **rules** (the hairline above a sticky header darkens to `--color-rule-strong` on scroll). Cards do not float; they sit on the page.

### Hover states

- Links: `text-decoration-thickness` 1px → 2px, `text-decoration-color` 60%‑mix accent → solid accent.
- Rows / cards: background goes from transparent to `rgba(232,230,220,.5)` (paper‑200 at 50%); the title color may darken from `--color-ink-muted` to `--color-ink`.
- Buttons: ink fill stays, opacity 1 → 0.92, border unchanged.
- **No transform, no scale, no translate on hover.** ADR‑016 forbids motion on hover. The page is paper, not jelly.

### Press states

- Buttons: very brief darken (ink → paper‑900), `60ms` ease‑in. No shrink.
- Form controls: focus shows the 2px stamp‑red outline at `outline-offset: 2px`, with the input border simultaneously promoting from `--color-rule-strong` to `--color-ink`.

### Animation & motion

- Motion is rare, short, sharp.
- Allowed: fade (`opacity`), `text-decoration-thickness`, `border-color`, `color`. Modal drawers slide.
- Forbidden: bounce, parallax, reveal staggers (`.reveal-1..6` is killed in ADR‑016), Lottie, scroll‑linked scale.
- Easings live in `--ease-default` (`cubic-bezier(0.25,0.1,0.25,1)`), `--ease-out-quart`, `--ease-in-out-quart`. Durations: `60ms / 120ms / 200ms / 320ms`. The default is **200ms**.
- `prefers-reduced-motion: reduce` collapses all durations to `0.01ms`.

### Transparency & blur

- The sticky public header is the only intentional translucency in the entire system: `backdrop-blur-md` over `color-mix(in oklch, var(--color-bg) 88%, transparent)`. Nowhere else.
- Modal scrims are solid `var(--color-bg)` at full opacity (the drawer is opaque, not a frosted veil).

### Imagery — color vibe

- Photographs are the owner's archive only. No stock, no generative.
- Color treatment: **warm, slightly desaturated, grain present**. Not graded for Instagram contrast. The reference is documentary photo books (Magnum, Aperture, Topic), not social media.
- Aspect ratios: 3:2 (35mm portrait/landscape), 4:5 (medium format crop), 16:9 reserved for cinema/drone stills.
- A 5‑variant conversion pipeline (`thumb / 640 / 1280 / 1920 / 2560`) is generated by Spatie Media; UI must use `srcset` + lazy loading.
- The empty cover state is **typographic**, not a placeholder image: a paper‑200 block with `FOTOĞRAF EKLENMEDİ` mono caps + dateline meta below. No HSL gradients, no skeleton shimmer.

### Cards

A "card" in this system is rarely a styled object. The dominant card is a **writing‑row**: a hairline‑separated text block with `dateline | thumb? | body` columns. When a card needs a visual edge it gets:
- `1px solid var(--color-rule)` border
- `2px` radius (the `--cc--primary` channel card upgrades to `1px solid var(--color-ink)`)
- 18–20px internal padding
- Background `--color-bg-elevated` (`#fffdf6`)

A `PRIMARY` capsule (mono caps, hairline ink box) is the only badge form used. There are no pill badges, no colored chip backgrounds.

### Layout rules — fixed elements

- Public header: sticky, translucent‑blur, transitions from 20px → 12px vertical pad after `scrollY > 24`.
- Admin sidebar: fixed left, `260px` wide, paper background, hairline right border. Mobile: drawer overlay opened from `≡`.
- No floating action buttons, no chat bubbles, no toast stacks (flash messages live inline at top of page).

### Forms

Square inputs, mono labels, hairline borders. Focus is `2px outline-offset: 2px var(--color-accent)` with input border simultaneously becoming `--color-ink`. Error state borders are `--color-danger` `#883028`, never the stamp red. Field hints are `--text-sm` `--color-ink-subtle`; field errors are the same size, danger color.

---

## ICONOGRAPHY

The brand's posture toward icons is **subtractive**: the question is "can we remove it?", not "which one fits?". Most UI affordances are carried by typography (mono caps eyebrow + position + hairline), not by glyphs.

### What is in the codebase

The production site uses **no icon library**. Every glyph in `admin-sidebar.blade.php` is a **single Unicode character** chosen for legibility at body‑text size:

| Glyph | Use |
|---|---|
| `▤` | Masa (dashboard) |
| `✎` | Yazılar (writings) |
| `⎈` | Yayınlar (publications) |
| `☰` | Sayfalar (pages) |
| `◨` | Fotoğraflar |
| `✉` | Mesajlar (contact inbox) |
| `⚙` | Ayarlar (settings) |
| `⬒` | Yedekleme (backup) |
| `☺` | Kullanıcılar |
| `◉` | Profil |
| `⎙` | Oturumlar |
| `⚿` | İki faktör |
| `⌇` | Denetim kaydı |
| `≡ ×` | Drawer toggle / close |
| `◐ ◑` | Theme toggle (light / dark) |
| `→ ↗ ←` | Link affordance, external, back |
| `·` | Primary separator (middle dot) |

Glyphs are rendered in the inherited font (so they pick up `IBM Plex Sans Variable`'s glyph repertoire where present, falling back to system). Color is `currentColor` — never decorative red, never gradient.

### Logos & brand SVGs

Path‑based, single‑mürekkep, no gradients, no shadows. The single red dot in `logo-mark.svg` is the only colored fill in the entire mark family. All logo files live in `assets/`:

- `logo-mark.svg` (64×64) — letters + frame + dot
- `logo-mark-inverse.svg` — same on ink ground
- `logo-horizontal.svg` (560×96) — mark + hairline rule + wordmark
- `logo-horizontal-inverse.svg`
- `logo-wordmark.svg` (480×96)
- `favicon.svg` (32×32) — letters only, no frame, no dot
- `apple-touch-icon.svg` (180×180)
- `og-mark.svg` (1200×630)

### When an icon is unavoidable

If a UI moment genuinely requires an icon outside the Unicode set above (rare), use **Lucide** (1.5px stroke, square caps, 24px) at `currentColor`. This is a **substitution** — the brand has no committed icon library, so any Lucide use is flagged. Prefer typography first, then hairline geometry, then Lucide.

### Emoji

Forbidden. Anywhere.

---

## Font notes (substitution flag)

The production site self‑hosts:
- `SourceSerif4Variable-Roman.woff2` (Roman)
- `SourceSerif4Variable-Italic.woff2`
- `IBMPlexSansVar-Roman.woff2`
- `IBMPlexMono-Regular.woff2` / `-Medium.woff2` / `-SemiBold.woff2` / `-Bold.woff2`

**Substitution in this design system:** the `fonts/` directory is empty. Previews fall back to system serifs / sans / mono via the metric‑aligned fallback faces declared in `colors_and_type.css` (Charter / Iowan Old Style / Georgia for serif; Helvetica Neue for sans; SF Mono / Menlo for mono). For pixel‑accurate output, please drop the eight `.woff2` files into `fonts/` (paths already wired in `colors_and_type.css`).

---

## Caveats — what's covered & what's deliberately out

**Covered fully:**
- 13 public sahne — yazılar listesi + detay (TOC, marginalia, dipnot), görüntü arşivi, hakkımda, iletişim, anasayfa (6 segment), 404, 500, KVKK, gizlilik, künye, bakım modu, boş hâl.
- 15 admin sahnesi — masa, yazılar tablosu (toplu işlem), yazı oluştur/düzenle, gelen kutusu, ayarlar (4 sekme), denetim kaydı, fotoğraf kütüphanesi (galeri + yükleyici), sayfalar, yayınlar (basın), kullanıcılar, profil, oturumlar, 2FA kurulum (4 adım) ve kapatma (3 adım), yedekleme.
- Bütün belirteçler — 11‑basamak kâğıt, 7‑basamak damga, 3 yazı ailesi, modular 1.2 punto, harf aralığı, köşe yarıçapı, boşluk ritmi, kontrol yükseklikleri, motion süreleri.
- 10 primitif — Btn (4 varyant), Field, Eyebrow, Tag, Chip, StatusPip, LinkQuiet, DisclosureBox, DossierGrid, StatGrid — prop tabloları + örneklerle.
- Durum matrisi — etkileşim altılısı (idle/hover/active/focus/disabled/loading) + gövde altılısı (boş/yükleniyor/hata/başarılı/çevrimdışı/silinmiş).

**Substitution flagged (font notes above):**
- `.woff2` dosyaları paketlenmedi. Previews use Charter / Iowan Old Style / Georgia for serif; Helvetica Neue for sans; SF Mono / Menlo for mono — metric‑aligned, no CLS.

**Deliberately out:**
- Real photo archive — `/goruntu` previews use placeholder gri bloklar; gerçek fotoğraflar prod sitede ayrı bir CDN'den gelir.
- Tam interaktif zengin metin editörü — kompoze sahnesinde toolbar var, ama editör kabuğunun kendisi durum (focus/selection) ve örnek metin gösterimi seviyesinde tutuldu.
- E‑posta şablonları — public site mailing yapmaz; admin'in iletişim cevap kompozisyonunda inline form var, ama `mail/` dizini yok.
- API endpoint dökümanı — bu bir **front‑end** sistemidir. Laravel route/controller imzaları prod kodbasını referans alır; burada değil.

**Superseded:**
- Eski "brass" / sarı aksent referansları (önceki sürüm). ADR‑016'dan itibaren tek aksent **red‑700 sansür kırmızısı**.

---

> **Editorial Silence — yazılı sözün ağırlığı.**
> *Gazetecilik parlamaz, kazınır.*
