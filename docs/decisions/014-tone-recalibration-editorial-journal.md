# ADR-014 — Ton Kalibrasyonu: Editorial Journal > War Dispatch

## Status
Accepted — 2026-04-18 (Faz 1 içinde, ADR-013'ün devamı)

## Context
ADR-013 ile domain savaş muhabiri olarak netleştirildi; accent **dispatch-red** (#991b1b), display font **Fraunces SOFT=0** (keskin, gazete başlığı) yapıldı. Status bar "ŞU AN · SAHADA · AÇIKLANMADI · DOĞU CEPHESİ" gibi dramatik bir dil aldı.

Kullanıcı ilk browser doğrulamasından sonra notunu netleştirdi:

> *"o ciddi ortamdan biraz da çıkmamız lazım. rapor olarak değil, yazı köşe yazısı gibi bir şekilde teslim edilmeli. blog yazılarının grid tasarımları. mesela her birinin görsel kapağı olmalı."*

Site bir savaş muhabirinin sitesi, ama **blog hissi** de taşımalı — yalnız dispatch değil. Tonu dramatik-acil çizgisinden **editorial journal** (köşe yazısı + saha parçası + deneme) hattına geri çekmek gerekiyor.

## Decision

### Ton
- İçerik tipi jenerik: **"yazı"** (saha yazısı, köşe yazısı, deneme, not). "Rapor / dispatch" yok.
- Nav kısaltıldı: **Yazılar · Hakkında · İletişim** (eski Reports + Column tek "Yazılar" akışında birleşti; tür `kind` etiketiyle).
- Hero: kişisel, minimal. "Gittim, baktım, _yazdım_."
- Status bar dramatic emergency'den gündelik bilgilere: son yazı tarihi, RSS linki, yayın temposu.

### Palet
- Accent renk: dispatch-red → **ember**.
  - Light: `#9a3412` (orange-800) — sıcak turuncu-kahverengi; AA kontrast.
  - Dark: `#fb923c` (orange-400) — ember glow.
- "Ember" (kor) metaforu: hâlâ warm, lived-in, ama "alarm siren" değil.

### Tipografi
- Fraunces Variable kalır (değişiklik risk + bundle ek iş).
- Default `SOFT=20, WONK=0` (önceki `SOFT=0, WONK=0`'tan bir nebze yumuşatma). Italic `SOFT=40`.
- Hero `opsz=144`, içerik başlıkları `opsz=96`, gövde başlıkları `opsz=72`.
- Wonky aksiyonlar hâlâ kapalı — editorial stability korunuyor.

### Grid / kart tasarımı
Liste kartları **fotoğraf kapaklı**:
1. Cover: `aspect-ratio: 4/3`, CSS-generated placeholder (radial gradients + noise overlay + grain), her karta unik renk çifti (HSL hue pair).
2. Cover üstünde ya da altında dateline label: `İSTANBUL · 2025-09`.
3. Kartın altında: başlık (Fraunces), kısa excerpt (Geist), alt satır: `tür · okuma süresi`.
4. Grid 12-col asymmetric: 1 large hero card (8 col) + 4-6 eşit kartlar (4 col), responsive.

**Cover placeholder sistemi (Faz 2 geçişine kadar):**
```css
.cover-placeholder {
  --hue-a: 20;  --hue-b: 180;
  background:
    radial-gradient(at 30% 20%, hsl(var(--hue-a) 40% 40% / .9), transparent 60%),
    radial-gradient(at 80% 70%, hsl(var(--hue-b) 30% 25% / .8), transparent 70%),
    linear-gradient(135deg, hsl(var(--hue-a) 15% 15%), hsl(var(--hue-b) 10% 8%));
}
```
Noise grain (mevcut film grain sistemi yeniden kullanılır). Faz 2'de admin media library kapak görsel upload ettiğinde placeholder yerine gerçek foto geçer.

### İçerik şeması (HomeController placeholder)
Writing entry alanları:
- `title`, `excerpt`, `date`, `kind` (deneme/röportaj/not/saha-parçası), `read`, `location`, `slug`
- `cover_hue_a`, `cover_hue_b` (decorative placeholder)
- `size` (hero | regular) — grid span

### Faz 2'ye aktarım
Writing modeli (Faz 2) `kind` enum ile tüm yazı türleri tek tabloda. Cover media collection via Spatie Media Library. Admin editörü TipTap. URL: `/yazilar/{slug}`.

## Consequences

### Pozitif
- **Ton esnek:** Sahibi blog yazısı, uzun röportaj, deneme — aynı grid'te hepsini eşdeğer sergileyebilir. İçerik takımı büyüdüğünde kategorilerle alt-ayrışma ekleyeceğiz (Faz 3).
- **Accent warm:** Ember, dispatch-red'e göre daha uzun süreli yaşar; "her yeni yazı alarm" hissi yok.
- **Grid görselci:** Bir muhabir için fotoğraf kanıt — boş metin-kartları yerine kapaklı hâl profesyonel.
- **Fraunces yumuşatması:** Keskin = haber başlığı; SOFT=20 = editorial + okunaklı + sıcak. "Karakter" var ama dramatik değil.

### Negatif / Trade-off
- **Cover placeholder geçici:** Faz 2'ye kadar gerçek fotoğraf yok. "Abstract" placeholder'lar tasarım kalitesi hissini riske atar; mitigation: noise + hue variety + küçük mono label ile editorial-magazine hissi verildi.
- **ADR-013 süperseded değil, revize:** Domain pivot kararı geçerli; sadece **ton ve detaylar** kalibre edildi. ADR-013 + ADR-014 birlikte okunmalı.
- **URL şeması güncellendi:** `/reports` → `/yazilar` (Türkçe slug). i18n EN kopyasında `/writing` ya da `/notes` olacak (Faz 2'de).

### Risk
- **Ton yeterince "muhabir" hissi vermez:** Düz blog'a kaydırırsak mesleki ağırlık kaybolur. Mitigation: dateline formatı (`KONUM · YAYIN`) her kartta korunur; bylines stripi hâlâ NYT/Reuters/Guardian gösteriyor; biography credentials satırları değişmiyor.

## Alternatives Considered

### Font değiştirme (Newsreader / Source Serif)
- Newsreader: gerçek blog/reading font, optical size var.
- Reddedildi: Fraunces zaten bundle'da; SOFT kalibrasyonu yeterli. Font değişimi +12KB woff2 + cache invalidation; marjinal kazanç için aşırı iş.

### Accent olarak "rust" (daha koyu)
- `#7c2d12` (orange-900) — daha "ciddi"
- Reddedildi: sahibinin istediği "biraz çıkmak" hissiyle uyumsuz; ember-800 (9a3412) doğru denge.

### Cover'sız grid (sadece metin)
- Minimal, text-heavy olabilirdi.
- Reddedildi: kullanıcının netleştirdiği "görsel kapak olmalı" gereksinimiyle çelişir.

## References
- Muhabir/yazar blog örnekleri: Anand Giridharadas, Rukmini Callimachi, Peter Pomerantsev
- Fraunces variable axes: https://fonts.google.com/specimen/Fraunces

## İlgili ADR'lar
- ADR-003: Frontend stack
- ADR-013: Domain pivot (war correspondent) — bu ADR onu **ton seviyesinde** kalibre ediyor; teknik mimari aynı.
