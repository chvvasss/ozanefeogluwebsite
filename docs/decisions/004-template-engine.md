# ADR-004 — Template Engine: Blade

## Status
Accepted — 2026-04-18

## Context
Laravel kullanıyoruz (ADR-001). Template engine seçenekleri:
- **Blade** (Laravel native)
- **Twig** (Symfony native, Laravel'a `rcrowe/twigbridge` ile)
- **Saf PHP** (zero-dependency, fakat manual escape)

## Decision
**Blade** — Laravel native, component sistemi olgun, Alpine.js + HTMX ile uyumu mükemmel.

## Consequences

### Pozitif
- **Component sistemi:** `<x-button />`, slot, attribute-merge, props validation. Vue/React'a yakın ergonomi.
- **Default escape:** `{{ $var }}` HTML-escape (htmlspecialchars + double-encode). XSS korumasının default davranışı.
- **`{{!! !!}}` raw output** sadece açıkça istendiğinde (ve audit edilebilir grep'le).
- **Laravel ile sıkı entegrasyon:** Auth, Route, Validation, Form Request — hep ergonomik directive'ler.
- **Performans:** Cached compile çıktısı; ilk render'dan sonra direct PHP include performansında.
- **HTMX ile uyum:** Partial Blade view'ları HTMX swap target için ideal.

### Negatif / Trade-off
- **Laravel-bağımlı:** Framework değişirse template engine de değişir. Ama framework değişmiyor.
- **Logic-in-template eğilimi:** `@if`, `@foreach` ile karmaşık iş mantığı template'a sızabilir. Kuralla yönetilir: template'da sadece display logic; iş mantığı view model / Blade component class'ında.

### Risk
- **`{!! !!}` yanlış kullanımı:** XSS açığı. Kontrol: lint rule + grep CI'da, `{!! !!}` sadece sanitize edilmiş HTMLPurifier çıktısı için.

## Alternatives Considered

### Twig
- **Pro:** Tasarımcı dostu syntax, daha sıkı separation of concerns, Symfony ekosistemi.
- **Con:** Laravel'a third-party, Blade kadar iyi entegre değil; component sistemi Twig 3'te güzelleşti ama Blade kadar Laravel feature'larıyla eşleşmiyor.
- **Karar:** Reddedildi — Laravel'da Blade en doğru seçim.

### Saf PHP
- **Pro:** Sıfır bağımlılık, native syntax.
- **Con:** Manual escape, no component, no template inheritance — boilerplate fazla.
- **Karar:** Reddedildi.

### Plates / Mustache / Smarty
- **Pro:** Bağımsız.
- **Con:** Ekosistem ufak; entegrasyon iş yükü.
- **Karar:** Reddedildi.

## References
- Blade docs: https://laravel.com/docs/blade
- Anonymous components: https://laravel.com/docs/blade#anonymous-components

## İlgili ADR'lar
- ADR-001: Laravel framework
- ADR-003: Frontend stack (Tailwind + Alpine + HTMX, Blade ile en iyi uyum)
