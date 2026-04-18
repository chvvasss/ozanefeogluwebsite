# ADR-012 — Build Pipeline: Vite 6+ + Tailwind v4 + npm

## Status
Accepted — 2026-04-18

## Context
Build pipeline sorumlulukları:
- Tailwind v4 CSS compile (Lightning CSS engine).
- JS bundle (Alpine.js + HTMX + custom modules; admin & public ayrı bundle).
- Asset hashing + manifest (cache busting).
- HMR (dev mode) — < 100ms feedback.
- Production: minify, source-map ayrı, gzip+brotli pre-compress.

## Decision
**Vite 6+** — Laravel resmi `laravel-vite-plugin` ile.

**Package manager:** npm (Global rule: package manager npm).

**Bundle stratejisi:**
- `resources/js/app.js` — public site bundle (Alpine.js + theme toggle + lightbox + minimal helpers).
- `resources/js/admin.js` — admin bundle (Alpine + HTMX + TipTap + admin helpers).
- `resources/css/app.css` — global Tailwind entry (public + admin shared base).
- `resources/css/admin.css` — admin-specific styles (sidebar, dense forms).

Vite config code-split: dynamic import for TipTap (lazy load editor sayfasında).

## Consequences

### Pozitif
- **Vite hızlı:** ESM dev server, sub-second cold start.
- **Tailwind v4 plugin official:** Sıfır config plumbing.
- **Manifest:** Laravel `@vite(['resources/css/app.css', 'resources/js/app.js'])` directive otomatik resolve.
- **Production:** Asset hashing immutable cache (`Cache-Control: max-age=31536000, immutable`).
- **HMR:** Tailwind class değişikliği instant; JS HMR module-level.

### Negatif / Trade-off
- **Node.js gereksinim:** Build için Node 20+ (LTS). Production'da deploy script'inde npm ci + npm run build (artifact sonra). Shared hosting'de Node yoksa lokal/CI build → upload `public/build/`.
- **Lock file:** `package-lock.json` commit (deterministik build); CI `npm ci` kullanır.

### Risk
- **Major upgrade:** Vite 6 → 7 breaking; semver-major upgrade testlerle ele alınır.
- **Dependency sayısı:** node_modules büyük. Mitigation: minimal direct deps; transitive temiz tutmak için `npm-check-updates`+ audit düzenli.

## Production build akışı

```bash
npm ci
npm run build
# Çıktı: public/build/manifest.json + public/build/assets/*.{css,js}
```

Asset path Laravel'den otomatik:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

## Vite config (taslak — Faz 1'de yazılacak)

```js
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/css/admin.css',
        'resources/js/app.js',
        'resources/js/admin.js',
      ],
      refresh: true,
    }),
    tailwindcss(),
  ],
  build: {
    sourcemap: false,    // production: source map separate file or disabled
    cssCodeSplit: true,
    rollupOptions: {
      output: {
        manualChunks: {
          tiptap: ['@tiptap/core', '@tiptap/starter-kit'],
        },
      },
    },
  },
});
```

## Tailwind v4 entry

```css
/* resources/css/app.css */
@import "tailwindcss";

@theme {
  --font-display: "Fraunces", Georgia, serif;
  --font-sans: "Inter", system-ui, sans-serif;
  --font-mono: "JetBrains Mono", monospace;
  /* ... design-tokens.md'den geri kalan */
}

@layer base {
  /* base reset, prose styles */
}
```

## Bundle hedefleri

| Bundle | Hedef gzipped |
|---|---|
| Public CSS (`app.css`) | < 25KB |
| Public JS (`app.js`) | < 15KB |
| Admin CSS (`admin.css`) | < 35KB (extended) |
| Admin JS (`admin.js`) (Alpine + HTMX) | < 35KB |
| TipTap chunk (lazy) | < 100KB (admin editor route) |

CI'da `bundle-size-report.js` action:
- `dist/` taranır, gzip boyutları rapor edilir.
- PR comment'a yazılır.
- Hard limit: bundle hedef + 20% (örn. public JS > 18KB → CI fail).

## Pre-compression
- Production deploy script: `gzip -k -9 public/build/assets/*.{css,js}` + `brotli -k -q 11`.
- Web server (Caddy/Nginx) static gzip/brotli on (no runtime compression).

## Source maps
- Production: `sourcemap: 'hidden'` — map dosyası generate edilir ama HTML'e link verilmez. Sentry/GlitchTip upload için (Faz 7).

## CI build job (taslak)

```yaml
# .github/workflows/ci.yml (Faz 1'de)
- name: Setup Node
  uses: actions/setup-node@v4
  with:
    node-version: 20
    cache: 'npm'
- run: npm ci
- run: npm run build
- name: Bundle size report
  run: npm run analyze:bundle
```

## Alternatives Considered

### Webpack 5
- **Pro:** Olgun, çok ekosistem.
- **Con:** Yavaş dev server, karmaşık config. Laravel Mix da deprecated.
- **Karar:** Reddedildi.

### esbuild standalone
- **Pro:** Çok hızlı.
- **Con:** Plugin ekosistemi Vite'in altında; HMR yok.
- **Karar:** Reddedildi (Vite zaten esbuild'i altta kullanıyor + Rollup).

### Bun build / Bun runtime
- **Pro:** Çok hızlı, JS+TS native.
- **Con:** Production build deterministiği için npm + Node ekosistemi daha güvenli (2026'da Bun stabilize ama bizim için risk yok-zaman geliyor).
- **Karar:** Şimdilik reddedildi; gelecekte değerlendirilebilir.

### Yarn / pnpm
- **Pro:** pnpm symlink store ile disk verimli.
- **Con:** Global tercih npm (kullanıcı kuralı). Ekstra tooling karmaşıklığı yok.
- **Karar:** Reddedildi (npm tutarlılık).

## References
- Vite: https://vitejs.dev/
- Laravel Vite plugin: https://laravel.com/docs/vite
- Tailwind v4 + Vite: https://tailwindcss.com/blog/tailwindcss-v4

## İlgili ADR'lar
- ADR-003: Frontend stack
- ADR-006: Editor (TipTap lazy chunk)
