# ADR-001 — PHP Framework: Laravel (en güncel stabil sürüm)

## Status
Accepted — 2026-04-18

## Context
Proje brief'i PHP tabanlı bir uygulama gerektiriyor. Adaylar:
- **Laravel** (en yaygın kullanım, devasa ekosistem, "convention over configuration")
- **Symfony** (enterprise, modüler bileşenler, sıkı tip sistemi)
- **Slim + bileşenler** (mikro framework, kontrol yüksek, iş yükü yüksek)
- **"Modern vanilla"** (kendi MVC + Composer parçaları, sıfırdan)

Brief'in kalite çubuğu (ASVS L2 güvenlik, Core Web Vitals, WCAG AA, kapsamlı admin, i18n, queue, cache) ve iki farklı deploy senaryosu (VPS + shared hosting fallback) kararı yönlendiriyor.

Sahibinin gayri-teknik olduğu da göz önünde: framework'ün kendi başına evrim güveni (LTS, güvenlik update'leri) önemli.

## Decision
**Laravel'in en güncel stabil sürümünü kullan** (proje kurulumunda `composer create-project laravel/laravel` ile çekilen sürüm). PHP 8.4 minimum.

## Consequences

### Pozitif
- En zengin first-party ekosistem: Eloquent, Blade, queue, cache, broadcasting, mail, scheduler, policies, gates, Sanctum/Fortify hep kutudan çıkıyor.
- Güvenlik defaults iyi: CSRF middleware, prepared statements, password hash Argon2id, mass-assignment koruması.
- Spatie/Filament/Livewire gibi tartışmasız kaliteli third-party'lerin standart hedefi.
- Dokümantasyon mükemmel; topluluk büyük.
- Long-term: Laravel'in 8 sürümdür süren tutarlı release ritmi (her yıl Mart/Şubat'ta major), 2 yıllık aktif + 1 yıllık güvenlik desteği planlanabilir bakım yükü demek.
- Shared hosting uyumu kabul edilebilir (PHP-FPM + MySQL yeterli; queue `sync`, cache `file`, search MySQL FULLTEXT fallback).

### Negatif / Trade-off
- "Magic" denilen bazı ergonomik şeyler (Facade, Eloquent global scopes, model events) test edilebilirliği zorlayabilir; biz bu noktalarda **Action pattern** (Spatie tarzı) ile yan etkileri açık konteynere taşıyacağız.
- Symfony'nin saf tip sistemi ve component-by-component opt-in yapısına göre Laravel daha "all-in" — kullanmadığımız bileşenleri composer'da disable edemiyoruz (bundle size servis tarafında problem değil; AOT compile gerektirmiyor).
- Eloquent N+1 hata kaynağı; statik analizle (`larastan` strict-rules) ve `Model::preventLazyLoading()` ile ele alınacak.

### Risk
- **Major upgrade ödevleri:** Yıllık LTS upgrade. Karşı önlem: composer.json'da `"laravel/framework": "^XX.0"` (caret minor + patch için), CI'da `composer outdated` raporu, 6 ayda bir upgrade audit.
- **Filament/Livewire bağımlılığı:** Eğer Filament seçersek (ki **etmiyoruz** — bkz. ADR-005), kendisi Laravel'e sıkı bağlı; o yüzden bağımlılık yokmuş gibi davranabiliriz.

## Alternatives Considered

### Symfony (5+ kıdemli component-based framework)
- **Pro:** Enterprise-grade tip sistemi, daha az "magic", DI container daha açık, Doctrine ORM saf data-mapper pattern (Active Record'tan daha test edilebilir).
- **Con:** Boilerplate fazla; küçük takım/tek geliştirici hızı için Laravel daha verimli. EasyAdmin/Sonata gibi admin paketleri Laravel ekosistemine göre daha sınırlı.
- **Karar:** Reddedildi — overkill, hız maliyeti yüksek.

### Slim + bileşenler (mikro framework)
- **Pro:** Maksimum kontrol, küçük footprint, "biz sadece istediğimizi koyarız".
- **Con:** Yetkilendirme, queue, mail, validation, ORM — hepsini biz seçip lehimleyeceğiz. 6 hafta + bug yüzeyi. Ekosistem fragmentasyonu.
- **Karar:** Reddedildi — iş yükü gereksiz; brief'in faz timeline'ı (9 faz) kısa.

### Modern vanilla (sıfırdan MVC + Composer parçaları)
- **Pro:** "Hiçbir framework kararına bağlı değiliz" hissi.
- **Con:** Reinventing wheel; security defaults bizden bekleniyor (büyük risk). 6 ay'lık iş.
- **Karar:** Reddedildi — kalite çubuğunu yakalamak için maliyet çok yüksek.

### CodeIgniter / CakePHP / Yii
- **Pro:** Hafif, eski PHP versiyonlarıyla uyumlu.
- **Con:** Modern PHP (typed properties, enums, readonly) idiomatic kullanımları zayıf; topluluk küçülüyor.
- **Karar:** Reddedildi — uzun vadeli bakım kötü.

## References
- Laravel sürüm desteği takvimi: https://laravel.com/docs/releases
- PHP supported versions: https://www.php.net/supported-versions.php
- "Action pattern" Spatie'den: https://spatie.be/docs/laravel-actions/

## İlgili ADR'lar
- ADR-002: Veritabanı seçimi (Eloquent ile uyumlu)
- ADR-004: Template engine (Blade)
- ADR-005: Admin yaklaşımı (custom — Filament reddediliyor)
- ADR-009: Cache & queue (Laravel cache/queue abstractions)
