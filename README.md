# ozanefeoglu.com

Kişisel portfolyo + blog + CMS. Ultra-profesyonel, editorial estetikli, güvenliği sertleştirilmiş bir PHP uygulaması.

> **Durum:** Faz 0 — Discovery (devam ediyor). Henüz kod yok; tasarım, mimari ve karar dokümanları üretiliyor.

## Ne yapıyor

- **Portfolyo:** Case-study formatında proje detayları, filtreli liste, hero + öne çıkanlar.
- **Blog:** Long-form içerik, ToC, syntax highlight, RSS, arama, etiket/kategori arşivi.
- **Admin paneli:** Tüm içerik (projeler, yazılar, sayfalar, medya, menüler, tema, SEO, redirect, kullanıcılar, ayarlar) gayri-teknik bir kullanıcının yönetebileceği tek bir kokpit.
- **Çok dilli:** TR + EN (her içerik kaydı için ayrı çeviri, doğru `hreflang`).

## Tasarım yönü

- Editorial-first tipografi, asimetrik grid, anlamlı whitespace.
- Tek güçlü accent + monokrom palet. Generic gradient/glassmorphism yok.
- Dark mode native (invert değil, ayrı düşünülmüş tonlar).
- Awwwards/SOTD kalibresi hedef.

## Kalite çubuğu

| Eksen | Hedef |
|---|---|
| Güvenlik | OWASP ASVS Level 2 |
| Performans | Core Web Vitals yeşil (LCP < 2.5s, INP < 200ms, CLS < 0.1) |
| Erişilebilirlik | WCAG 2.2 AA (axe + pa11y temiz) |
| SEO | Lighthouse 100 |
| Kod | PHPStan level 8, strict types, PSR-12 |
| Test | Kritik paths %90+ coverage |

## Klasör haritası (öngörülen)

```
/
├── README.md                  # Buradasın
├── docs/
│   ├── architecture.md        # Sistem mimarisi
│   ├── discovery/             # Faz 0 keşif çıktıları
│   ├── decisions/             # ADR (Architecture Decision Records)
│   ├── deployment.md          # Production rehberi (Faz 9)
│   ├── security.md            # Tehdit modeli + kontroller
│   └── admin-guide.md         # Sahibi için kullanım kılavuzu (Faz 9)
├── app/                       # Laravel uygulama kodu (Faz 1+)
├── public/                    # Web root
├── resources/                 # Views (Blade), CSS, JS
├── database/migrations/
├── database/seeders/          # Demo içerik
├── tests/
├── storage/                   # Logs, cache, uploads (gitignored)
├── .env.example
├── docker-compose.yml         # Local dev
└── .github/workflows/         # CI
```

## Faz takibi

- [x] **Faz 0 — Discovery** (devam): teknoloji seçimleri, tehdit modeli, IA, tasarım tokenleri.
- [ ] **Faz 1 — Foundation:** iskelet, auth + 2FA, admin shell.
- [ ] **Faz 2 — Content core:** projeler, yazılar, kategoriler, medya.
- [ ] **Faz 3 — Public polish:** ana sayfa, SEO, RSS, arama.
- [ ] **Faz 4 — Admin gelişmiş:** menü, tema, redirect, kullanıcı, audit log, yedek.
- [ ] **Faz 5 — Blog derinliği:** ToC, syntax, footnote, ilgili yazılar, yorumlar/webmentions.
- [ ] **Faz 6 — Performans + a11y geçişi.**
- [ ] **Faz 7 — Güvenlik sertleştirme.**
- [ ] **Faz 8 — i18n + final polish.**
- [ ] **Faz 9 — Teslimat.**

## Geliştirici notları (kurulduğunda)

```bash
# Faz 1'den itibaren geçerli olacak komutlar
make setup     # composer + npm install + .env + key + migrate + seed
make dev       # vite + php server
make test      # pest + phpstan + lint
make build     # production assets
```

Detaylar Faz 1 sonunda buraya yazılacak.

## Kararlar nerede

Her teknoloji ve mimari seçimi `docs/decisions/` altında ADR (Architecture Decision Record) olarak belgeli. Önce neden o kararı verdiğimizi okuyun, kodu sonra incelersiniz.
