# ADR-003 — Frontend Stack: Tailwind v4 + Alpine.js + HTMX (server-rendered, ada yaklaşımı)

## Status
Accepted — 2026-04-18

## Context
Brief net: bu bir içerik sitesi, SPA değil. Server-rendered MPA + progressive enhancement doğru varsayılan. Kararlar:
1. CSS framework
2. JS interaktivite stratejisi
3. Bundle boyutu ve performans hedefleri

### Hedefler
- LCP < 2.5s mobile 3G ile.
- Public sayfalarda **mümkünse 0 JS**, gerekirse < 40KB gzipped.
- Admin sayfalarında zengin interaktivite (drag-drop, live search, autosave) ama yine de minimal bundle.
- Build pipeline modern (Vite), HMR hızlı.

## Decision

### CSS: **Tailwind CSS v4**
- Lightning CSS engine, oxide compiler — eski JIT'ten daha hızlı.
- `@theme` direktifi ile design token tanımı CSS'in içinde; framework config'ine bağımlı değil.
- `@source` ile dosya keşfi otomatik.
- Native CSS variable çıktısı (`light-dark()` desteği).

### JS interaktivite (public): **Alpine.js 3**
- Public sayfalarda yalnız mikro-etkileşimler için: theme toggle, mobile nav toggle, modal/dropdown, image lightbox.
- 15KB gzipped; sıfır build adımı zorunlu değil.
- `x-data`, `x-show`, `x-transition` gibi declarative directive'ler Blade ile çok iyi karışıyor.

### JS interaktivite (admin): **Alpine.js + HTMX 2.x**
- HTMX server-side state ile partial swap'lar (form submit, list filter, drag-reorder, autosave).
- 14KB gzipped (Alpine ile birlikte ~30KB toplam) — admin'de SPA hissini sağlar; public bundle'a sızmaz.
- Admin route group'unda yüklenir, public'te değil.

### Build: **Vite 6+**
- Tailwind plugin (resmi).
- Asset hashing, manifest, code splitting (admin/public bundle ayrımı).
- HMR < 100ms target.

### Web platformu: **View Transitions API**
- Sayfa geçişlerinde continuity (progressive enhancement, fallback hardcut).
- Browser-side, sıfır JS bundle artışı.

## Consequences

### Pozitif
- **Bundle minimal:** Public sayfa 0-15KB JS, < 30KB CSS gzipped. LCP hedefi yakın.
- **Build hızlı:** Vite + Tailwind v4 modern stack — `npm run dev` < 1s cold start.
- **Server-side state:** SPA'da olan "client/server data drift" sorunu yok; tek doğru kaynak DB.
- **A11y by default:** Server-rendered HTML doğru semantic, tarayıcı default davranışları korunur.
- **Progressive enhancement:** JS kapatılsa bile site çalışır (HTMX fallback olarak normal form submit, Alpine.js noJS = static).
- **No framework lock-in:** Tailwind, Alpine, HTMX, Blade — her biri ayrı ayrı değiştirilebilir.

### Negatif / Trade-off
- **HTMX'in mental modeli farklı:** SPA backend deneyimi olan biri için "server-driven UI" alışkanlık gerekiyor. Doc gerekli.
- **Alpine memory:** 50+ Alpine component aynı sayfada → nadiren memory leak; component lifecycle dikkatli kullan.
- **Tailwind class verbosity:** HTML "shouty"; mitigation: `@apply` orta düzey reuse + Blade component composition.

### Risk
- **HTMX uzun-vade desteği:** Aktif geliştirici (Carson Gross), 2.x çıktı 2024 sonu. Stabil görünüyor; ama "tek geliştirici" risk. Karşı önlem: HTMX olmadan da admin çalışır (form submit'ler standart POST). Geri çekme maliyeti orta.
- **Alpine.js v4 (varsa) breaking changes:** Alpine 3 stabil, 4'e geçiş zaman alır; risk düşük.

## Alternatives Considered

### React/Vue + Inertia.js
- **Pro:** SPA-vari deneyim, server-side route, Laravel ile sıkı entegrasyon.
- **Con:** Bundle 100KB+ (React 40 + Inertia 20 + app code). Public site için overkill. Hidrat maliyeti LCP/INP'ye zarar.
- **Karar:** Reddedildi — public bundle hedefine uymuyor.

### Livewire 3
- **Pro:** Laravel-native, full-stack PHP, güzel DX.
- **Con:** Her interaksiyon için round-trip (network bağımlı), public sayfada 30-50KB Livewire JS. HTMX ile aynı paradigm ama "Laravel-only".
- **Karar:** Reddedildi — HTMX daha hafif + framework-agnostic, public sayfada 0 JS hedefi için daha temiz.

### Astro
- **Pro:** Component island modeli, mükemmel performans, MDX yerleşik.
- **Con:** PHP değil — brief PHP istiyor.
- **Karar:** Reddedildi — kapsam dışı.

### Saf Tailwind without Alpine/HTMX (vanilla JS)
- **Pro:** En küçük bundle.
- **Con:** Modal/dropdown/toggle gibi her tekerleği kendimiz yazarız; kod tekrar.
- **Karar:** Reddedildi — Alpine.js'in 15KB'lik kazancı maliyetinden büyük.

### Bootstrap 5 / Bulma / vanilla CSS
- **Pro:** Hazır componentler.
- **Con:** Brief'in "generic" anti-pattern listesinde Bootstrap-vari kart grid'leri var. Tasarım özgürlüğü kısıtlı.
- **Karar:** Reddedildi.

## References
- Tailwind v4 release: https://tailwindcss.com/blog/tailwindcss-v4
- HTMX docs: https://htmx.org/
- Alpine.js docs: https://alpinejs.dev/
- View Transitions API: https://developer.mozilla.org/en-US/docs/Web/API/View_Transitions_API

## İlgili ADR'lar
- ADR-004: Template engine (Blade — Tailwind/Alpine/HTMX ile en iyi uyum)
- ADR-005: Admin yaklaşımı (HTMX'in admin partial swap'ları kritik)
- ADR-012: Build pipeline (Vite)
