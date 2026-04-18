# ADR-005 — Admin Yaklaşımı: Sıfırdan Custom (Filament/Nova/Voyager reddedildi)

## Status
Accepted — 2026-04-18

## Context
Brief Bölüm 3.2 admin paketi açısından dikkat çekiyor: *"Karar kalite çubuğunu korumalı — generic admin teması kabul edilemez."*

Adaylar:
- **Filament 3** — Laravel admin paketi, TALL stack (Tailwind + Alpine + Livewire), TypeScript-vari ergonomi.
- **Laravel Nova** — Resmi, ücretli (~$199), kurumsal, sınırlı görsel özelleştirme.
- **Voyager / Backpack** — Klasik, daha eski, generic admin teması.
- **Sıfırdan custom** — Blade + Alpine + HTMX + Tailwind ile kendi tasarımımız.

### Çatışma
Filament gerçekten güzel, hızlı, yetenekli. Ama:
- **Görsel kimlik standart:** Hardcoded layout, "panel admin" hissi. Custom theme yapmak için Filament'in Vite/Tailwind config'ine yamalanmak gerekiyor; "panel page" override etmek mümkün ama pahalı.
- **Brief'in tonu:** "Bu site sahibi 3 yıl sonra hâlâ modern hissetsin" — admin tarafı da bunun kapsamında.
- **Owner non-tech:** Admin paneli **operasyon arayüzü**. Filament gibi "developer-grade" admin (her şey görünür, çok seçenek) gayri-teknik kullanıcı için **fazla**. Biz **görev odaklı** UI istiyoruz: "Yeni yazı ekle" → distraction-free editor + minimum form alanı.

## Decision
**Sıfırdan custom admin.** Aynı stack: Blade + Alpine.js + HTMX + Tailwind v4. Public site'la aynı design system'i paylaşır, ama admin'e özgü dense layout patterns (form-first, list-first, dashboard).

Spatie ekosisteminden **utility paketleri** (Permissions, Activity Log, Translatable, Media Library) admin'in **arkasında** kullanılıyor — bunlar UI vermez, sadece domain logic.

## Consequences

### Pozitif
- **Görsel tutarlılık:** Public ve admin aynı design system, aynı "his". Sahibi giriş yapınca yabancı bir araç yerine kendi sitesinin idare ettiği bir kokpit görür.
- **Görev odaklı UI:** Sadece sahibinin gerçekten ihtiyaç duyduğu alanlar. "Field type registry" değil, kasıtlı seçilmiş form'lar.
- **Esneklik:** İçerik tipleri evrildikçe admin'i de evrilteceğiz; Filament'ın Resource abstraction'ına yamalanmak yok.
- **Bundle minimal:** Filament 200+KB JS gerektiriyor; bizimki <50KB hedef.
- **Bağımsızlık:** Filament major upgrade ödevleri yok.

### Negatif / Trade-off
- **İş yükü daha yüksek:** Resource scaffolding yok. Her CRUD form, list, detail view'ı kendimiz yazıyoruz.
  - **Karşı önlem:** Blade component library + Action pattern + form macro generator (Artisan command) → 80% boilerplate'i azalt.
- **Edge case'leri (file upload progress, bulk action, complex filters) kendimiz çözmek:** Plus side: çözümlerimiz tam ihtiyaca göre.

### Risk
- **Skope kayması:** "Bu alanı da admin'den editable yapalım" sürekli; her birini tasarlamak zaman. Karşı önlem: Faz 4 admin gelişmiş features'ı sıkı kapsam tutar; nice-to-have'lar Faz 9 sonrası.
- **A11y:** Custom widget'larda (drag-reorder, autocomplete) ARIA pattern'leri doğru uygulanmazsa sorun. Karşı önlem: WAI-ARIA Authoring Practices guide'ı her widget için referans; testler axe-core ile.

## Alternatives Considered

### Filament 3
- **Pro:** Hızlı geliştirme, zengin field types (file upload, repeater, tag input, rich editor), built-in plugins (Spatie ile entegre), TipTap editor.
- **Con:** Görsel "Filament" kokuyor (header, sidebar, panel chrome — kolay tanınır). Custom theme via package mümkün ama maliyetli; "look like ours" yapmak için %60 yeniden CSS.
- **Karar:** Reddedildi — generic admin teması kabul edilemez kuralı net.

### Laravel Nova
- **Pro:** Resmi, sade, ücretli destek.
- **Con:** Vue 3, Inertia, ek bundle. Görsel daha sade ama yine de "Nova".
- **Karar:** Reddedildi (aynı sebep + para).

### Voyager / Backpack
- **Pro:** "Hızlı admin".
- **Con:** Eski, görsel tarihçe ağır, Bootstrap-vari estetik.
- **Karar:** Reddedildi.

### Statamic / Kirby
- **Pro:** İçerik-odaklı CMS, güzel admin.
- **Con:** Kendi framework'ü; Laravel'in altında değil. Kurum lock-in.
- **Karar:** Reddedildi — kapsam dışı.

## Yardımcı paketler (admin'in arkasında)

| Paket | Amaç | Neden bu paket |
|---|---|---|
| `spatie/laravel-permission` | Rol + yetki | Olgun, polymorphic, Eloquent-friendly |
| `spatie/laravel-activitylog` | Audit log | Auto change tracking, polymorphic |
| `spatie/laravel-translatable` | i18n alanlar | JSON-kolon, locale-aware accessor |
| `spatie/laravel-medialibrary` | Medya yönetimi | Conversion, responsive, S3-ready |
| `spatie/laravel-sluggable` | Slug oluşturma | Source-target, locale-aware kolaylık |
| `spatie/laravel-tags` | Tag/Kategori (poly) | Polymorphic, type-scoped |
| `pragmarx/google2fa-laravel` | TOTP 2FA | Aktif bakım |
| `bacon/bacon-qr-code` | QR (2FA setup) | Standart |
| `spatie/laravel-backup` | Yedekleme | Dosya + DB + storage; cron-friendly |
| `intervention/image` | Image işleme | Variant generation, metadata strip |
| `meilisearch/meilisearch-php` | Search client | Meili (ADR-008) |

Hepsi aktif bakımlı, geniş topluluk. ADR-001'deki "kalite + bakım" kıstaslarını geçer.

## TipTap editor neden seçildi (özet — detay ADR-006'da)
TipTap headless rich-text editor, Filament içinde de zaten kullanılıyor. Bizim tarafta direkt entegre edilecek. Vanilla JS bundle ~80KB, makul.

## References
- Filament: https://filamentphp.com/
- Spatie packages: https://spatie.be/open-source
- WAI-ARIA APG: https://www.w3.org/WAI/ARIA/apg/

## İlgili ADR'lar
- ADR-001: Laravel
- ADR-003: Frontend stack
- ADR-006: Editor seçimi (TipTap)
- ADR-010: Auth & 2FA
