# Public site UI kit — Editorial Silence

Editorial public‑facing surfaces for `ozanefeoglu.com`. Open `index.html`, switch scenes from the floating top toolbar.

## Scenes

| Toolbar route | Description |
|---|---|
| Anasayfa | Hero (typographic) → Featured dossier → Constellations 2×2 → Contact sheet → Profile band → Recent dispatches → Bylines |
| Yazılar (arşiv) | Filter chips (Hepsi / Saha yazısı / Röportaj / Deneme / Not) + writing‑row list |
| Yazı detay | Article masthead → hero photo → 8/3 prose + margin‑note column → inverse pull‑quote band → related dossiers |
| Görüntü | Three contact‑sheet seri, alternating muted scenes |
| Hakkında | Bio masthead 7/4 + portrait + marginalia + workareas list |
| İletişim | Contact channels (e‑posta primary / telefon / posta) + message form |

## Stack

- `colors_and_type.css` (project root) — single token source
- `kit.css` — editorial component patterns (writing‑row, typo‑cover, contact‑sheet, mason 2×2, constellation, channel‑card, prose, künye, marginalia, atölye‑mini)
- `primitives.jsx` — atomic React components (`Eyebrow`, `Kicker`, `Standfirst`, `Dateline`, `PullQuote`, `LinkQuiet`, `Btn`, `IconBtn`, `Field`, `BrandMark`, `BrandLockup`, `PhotoPlaceholder`, `PhotoFigure`, `DateSep`, `TypoCover`, `WritingRow`)
- Scenes split into `HomeScene.jsx`, `Yazi.jsx`, `OtherScenes.jsx`
- `Header.jsx` / `Footer.jsx` — sticky public chrome with theme toggle

## Theme

Light is canonical. Dark mode (`<html data-theme="dark">`) is provided as a substitution layer in the kit shell — the production site honors `prefers-color-scheme` if enabled.

## What's not modeled

- /kvkk, /gizlilik, /künye legal pages (single column prose; same patterns as Hakkında)
- Real photography (placeholders use `PhotoPlaceholder` with mono caps `FOTOĞRAF EKLENMEDİ` empty state)
- Pagination on Yazılar archive (current list shows all 7 fixture entries)
- RSS feed
