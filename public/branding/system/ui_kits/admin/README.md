# Yazı Masası — Admin UI kit

CMS panel for the editorial publishing platform. Open `index.html`, switch scenes from the floating bottom toolbar.

## Scenes

| Toolbar route | Description |
|---|---|
| Masa | Dashboard — greeting + 4 stat cells + recent published table + drafts/messages aside |
| Yazılar | Archive table — chips for status filter, hover row actions, status pips |
| Yeni yazı | Two‑column compose — canvas (eyebrow / title / standfirst / body with toolbar) + meta (publishing / künye / cover / disclosure) |
| Mesaj kutusu | Two‑pane inbox — list (left) + detail (right) with reply/resolve/archive/delete |
| Ayarlar | Two‑column settings (nav left, sectioned cards right) — site identity / publishing defaults / backup |
| Denetim | Mono‑typed single‑line audit log with timestamp + actor + action |

## Stack

- `colors_and_type.css` (root) + `../public/kit.css` (editorial patterns) + `admin.css` (sidebar/topbar/table/inbox/settings)
- `../public/primitives.jsx` — reused atoms
- `data.jsx` — fixture: `ADMIN_NAV`, `ADMIN_WRITINGS`, `ADMIN_INBOX`, `ADMIN_AUDIT`
- `Shell.jsx` — `AdminSidebar`, `AdminTopbar`
- `Scenes.jsx` — `AdminDashboard`, `AdminWritings`, `AdminCompose`, `AdminInbox`, `AdminSettings`, `AdminAudit`

## Sidebar nav (taken from production `admin-sidebar.blade.php`)

Four groups, mono caps Turkish labels — Yayın (Masa, Yazılar, Yayınlar, Sayfalar, Fotoğraflar) · Gelen kutusu (Mesajlar) · Site (Ayarlar, Denetim kaydı, Yedekleme) · Hesap (Kullanıcılar, Profil, Oturumlar, İki faktör). Active state = ink fill on paper text. Glyphs are bare Unicode (▤ ✎ ⎈ ☰ ◨ ✉ ⚙ ⌇ ⬒ ☺ ◉ ⎙ ⚿) — no icon library.

## What's not modeled

- Two‑factor enrollment flow (would re‑use Field + Btn primitives)
- Photo library / media browser (placeholder cell on compose form)
- Backup restore wizard
- Audit log filters beyond date range
- Mobile drawer interaction details (responsive shell included; full mobile behavior is partial)
- Login / forgot password (same patterns as `İletişim` form)
