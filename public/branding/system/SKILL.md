# Editorial Silence — Skill manifest

**For:** Ozan Efeoğlu's personal publishing platform (`ozanefeoglu.com`)
**Codename:** Field Dossier (ADR‑016)
**When to use:** any UI work that lives on Ozan Efeoğlu's site or admin — public surfaces (yazılar, görüntü, hakkında, iletişim, hukuksal sayfalar) and the Yazı Masası CMS. Do **not** use this for unrelated brands.

---

## Rules of engagement

1. **Read `README.md` first.** Voice rules, palette, type, grid and motion are codified there. Do not reinvent.
2. **Import `colors_and_type.css`** as the only token source. Do not redefine `--color-*`, `--text-*`, `--space-*`, `--font-*`. If a value is missing, raise an issue, do not paper over it.
3. **Layer `ui_kits/public/kit.css`** for editorial component patterns (writing‑row, contact‑sheet, typo‑cover, channel‑card, prose, künye, marginalia, mason‑2x2). Use `ui_kits/admin/admin.css` for admin chrome.
4. **Reuse primitives from `ui_kits/public/primitives.jsx`** (`Eyebrow`, `Kicker`, `Standfirst`, `Dateline`, `PullQuote`, `LinkQuiet`, `Btn`, `IconBtn`, `Field`, `BrandMark`, `BrandLockup`, `PhotoPlaceholder`, `PhotoFigure`, `DateSep`). Do not duplicate.

## The hard "no" list (ADR‑016)

- ❌ Emoji of any kind.
- ❌ Stock photos, AI imagery, illustrative SVGs, decorative shapes, gradients.
- ❌ Drop caps, italic accent emphasis, "9 italic em vurgu" pattern.
- ❌ "Coming soon" / "Yakında" placeholders. Empty state is **typographic** (`FOTOĞRAF EKLENMEDİ`).
- ❌ "Welcome to my world", "Hello there", "I'm Ozan…", marketing superlatives.
- ❌ "Gittim, baktım, yazdım" slogan.
- ❌ Hover transforms (`scale`, `translate`), reveal staggers (`.reveal-1..6`), bounce, parallax, Lottie.
- ❌ Drop shadows. There is no shadow system.
- ❌ Pill badges with colored chip backgrounds. Use mono caps + hairline ink box (`.channel-pmark`, `.status-pip`).
- ❌ Title Case for content. Use Türkçe sentence case.
- ❌ Pure black (`#000`) or pure white (`#fff`). Use `--brand-ink` and `--brand-paper`.
- ❌ Stamp red (`#b91c1c`) on headings, badges, large fills, gradients.

## Censor's red — the only colored ink

`--brand-stamp` / `--color-accent` is permitted in **six places** and nowhere else:

1. Focus ring (`outline`, 2px, offset 2px).
2. `<a>` body link `text-decoration-color`.
3. **One** primary `Btn` per page (and even that is usually ink).
4. The single dot in the logo mark.
5. `Kicker` `accent` variant — once per scene.
6. `.constellation-card-kicker`, `.contact-sheet-index` accent (used sparingly).

## Voice & copy

- **Türkçe sentence case** for titles. Never Title Case, never ALL‑CAPS body.
- **MONO ALL‑CAPS, tracking `0.18em`** for editorial machinery only: eyebrows, kickers, datelines, location stamps, künye fields. The "typewriter strip".
- Numerals are tabular. Default date format `dd.mm.yyyy` (dense), `26 Nisan 2026` (long, Turkish month name).
- Proper Turkish chars (`ğ ı İ ş ç ö ü`) — never `i` for `İ` etc.
- Body Turkish first; English may appear in quoted source material only.
- Forbidden phrases: see hard‑no list above.

## Editorial stack pattern (every public scene)

```
[ Eyebrow — mono caps + dot separators ]
[ Kicker — accent variant, once per scene ]
[ Title — display, sentence case, opsz‑tuned ]
[ Standfirst — display italic, 1‑2 sentences, max 60ch ]
[ Dateline — mono caps, dot‑separated meta ]
[ Body / scene content ]
```

## Layout rhythm

- 12‑column Field Dossier grid (`.dossier-grid` + `.dg-1..12`).
- Editorial split favorites: **dg‑7 / dg‑5** (lead photo + standfirst), **dg‑8 / dg‑3** (prose + margin‑note column).
- Scene shading: alternate `.scene` ↔ `.scene--muted` ↔ `.scene--darker` ↔ `.scene--inverse`. Never gradient.
- Hairlines and 2px section breaks are the only dividers.
- Square corners — radius scale stops at 4px (form inputs); buttons at 2px; cards at 0.

## Build checklist (before shipping any new screen)

- [ ] Imported `colors_and_type.css` and `kit.css` (and `admin.css` if admin).
- [ ] Used existing primitives — no shadow components.
- [ ] Title is sentence case Turkish; eyebrow/kicker is mono caps.
- [ ] Standfirst is italic display, ≤ 60ch.
- [ ] One accent color use per scene at most (kicker accent, primary CTA, focus ring count once each).
- [ ] No emoji, no stock photo, no decorative SVG.
- [ ] Dates use Turkish month names long form OR `dd.mm.yyyy` dense.
- [ ] Photographs are owner‑archive only (placeholder = typographic empty state, not skeleton).
- [ ] Disclosure box present on saha yazısı surfaces if travel/sponsorship is relevant.
- [ ] WCAG AA contrast verified on text + interactive elements.
- [ ] `prefers-reduced-motion` respected (already handled in token CSS).
- [ ] No box‑shadow, no transform hover, no gradient.

## Quick reference — key class hooks

| Pattern | Use |
|---|---|
| `.writing-row` | Hairline‑separated archive row (date · thumb? · title · lede) |
| `.typo-cover.typo-cover--{kind}` | Typographic post cover, no photo. Variants: `saha_yazisi`, `roportaj`, `deneme`, `not`. |
| `.contact-sheet` | 6‑col image‑archive grid with index + location captions |
| `.constellation-card` | 4‑up "selected works" card with thumb + lede |
| `.atolye-mini li` | Workareas list — 2‑col label / detail rows |
| `.channel-card` | Contact channel with mono caps type, hairline border |
| `.kunye dl` | Article colophon — mono caps key/value pairs |
| `.disclosure-box` | Travel / sponsorship disclosure — left ink rule |
| `.prose` | Long‑form body — sans‑first, italic standfirst opener |
| `.lede-open` | First 2‑3 mono caps location words at start of body |
| `.photo-caption` | Loc / date / credit row beneath any image |

## Admin (Yazı Masası) specifics

- Sidebar = paper background, hairline right border, 260px fixed (mobile drawer at <880px).
- Active nav state = ink fill on paper text. Hover = paper‑muted bg only. No accent on nav.
- `.status-pip` carries status: `published`, `draft`, `scheduled`, `archived`, `new`, `resolved`.
- Compose form is **two‑column**: canvas (left) for editorial fields with display/italic/sans inputs; meta (right) for künye + publishing settings.
- Audit list is mono, single line per event, no decoration.

---

> *Editorial silence is not the absence of speech. It is the deliberate placement of the page break.*
