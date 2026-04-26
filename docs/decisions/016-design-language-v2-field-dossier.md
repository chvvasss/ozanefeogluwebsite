# ADR-016 — Tasarım Dili v2: "The Field Dossier"

## Status
Accepted — 2026-04-19. **Supersedes** ADR-014 (Editorial Journal kalibrasyonu).
ADR-013 (domain pivot) aynen geçerli — ton/estetiği değil **kimliği** tanımlar; bu ADR sunum katmanını yeniden düzenler.

## Context
Sahibi, özgeçmişi ve portresi geldikten sonra mevcut tasarımı **acımasızca** değerlendirmemizi istedi:

> *"Bu site savaş temalı değil. Bu site savaşın içinden geçmiş bir gazetecinin sitesi. … Bu tasarım gerçekten bu kişiye mi ait, yoksa başkasına da giydirilebilir mi?"*

Özgeçmiş beklenenden çok **katmanlı** bir profil çıkardı: (1) saha muhabirliği (Zeytin Dalı, Hatay, Adana bölgesi, İstanbul AA Uluslararası Haber Merkezi), (2) **görsel** omurga (Aydın Üni. Fotoğraf+Kameramanlık, freelance ürün/reklam, şu an "Foto Muhabiri ve Yayıncı"), (3) akademik derinlik (yüksek lisans: drone haberciliği + görsel göstergebilim; saha pratikleri kitabı), (4) yazılım kökeni (Aksa Teknik Lisesi + Uludağ bilgisayar programcılığı — gizli omurga).

Mevcut site (ADR-014 ile kalibre edilmiş) bu dört katmandan **sadece birine** hitap ediyor ("editorial blog"). Başlıklarda 9+ italik ember em vurgu, Fraunces SOFT=20 magazine dokunuşu, HSL cover placeholder, "Gittim, baktım, yazdım" slogan-hero, sahte footer sosyal linkler ve portrenin hiçbir yerde olmaması — brief'in tam ihlali.

Ayrıntılı audit ve alternatifler `docs/REDESIGN_2026.md` içinde.

## Decision

### Yeni tasarım dili adı
**The Field Dossier** — *sessiz dosya / arşiv dosyası*. Editör masasında açık duran dosya idiomu. NY Times Magazine, Granta, Aperture, Topic, Magnum Foundation register'ı.

### Tipografi
| Rol | Eski | Yeni |
|---|---|---|
| Display | Fraunces Variable (SOFT=20) | **Source Serif 4 Variable** (SemiBold 600, opsz 32/60) |
| Body | IBM Plex Sans Variable | IBM Plex Sans Variable *(korunur)* |
| Mono / dateline | IBM Plex Mono | IBM Plex Mono *(korunur)* |
| Italic em başlık | 9+ yerde accent renk | **0 yer — sadece body içinde italic** |
| Drop cap | `.prose-article > p:first-of-type::first-letter` | **Kaldırıldı**; yerine "lede paragraph" (ilk 2-3 kelime mono caps eyebrow) |
| Modular scale | 1.25 | **1.2** (daha sıkı, editoryal ritim) |

Source Serif 4, Adobe tarafından açık kaynak olarak yayımlanan haber+akademi-için-tasarlanmış bir serif. Fraunces'e göre daha **clinical**, daha az "literary magazine chic". Yüksek lisans tezi ya da uzun saha raporu aynı fontta eşit ağırlıkta yaşar.

### Renk
| Token | Eski | Yeni |
|---|---|---|
| Paper bg (light) | `#fafaf7` | `#f6f4ee` (hafif sıcak shift) |
| Ink (light) | `#16140f` | `#1a1816` (mat siyah, hafif sıcak) |
| Accent (light) | `#9a3412` (ember-800 — turuncu-kahverengi) | **`#7c5a3a` (brass-600 — oksitlenmiş bakır/pirinç)** |
| Accent (dark) | `#fb923c` (ember-400 — sıcak turuncu) | **`#b8915f` (brass-400 — mat brass)** |
| Success | `#15803d` | `#4a6b3a` (moss) |
| Warning | `#a16207` | `#8a6a2c` (aged amber) |
| Danger | `#b91c1c` | `#883028` (oxidized rust) |

Brass patine (bakır oksiti) doğrudan "savaş alarmı" çağrışımından uzak; **zaman geçmiş, metal oksit almış** hissi veriyor. Brief'teki "mineral bakır, yanık amber, yıpranmış zeytin" ailesinin doğru üyesi.

**Accent kullanım kuralı (sert):**
1. Focus ring
2. `<a>` body link altçizgi rengi (text-decoration-color)
3. Tek `.btn--accent` (sayfa başına en fazla 1)
4. Hiçbir başlık, eyebrow, badge, timeline-dot, section-break mark'ta accent **yok**.

### Atmosfer
- Paper grain overlay **0.022 opacity korunur**. Tek atmosferik tutarlılık unsuru.
- Reveal stagger animasyonu (`.reveal-1..6`) **kaldırılır**. Sayfa anlık açılır.
- Hover'larda `transform` yasak. Sadece `color`, `text-decoration-thickness`, `border-color` geçişi.
- Motion süresi: normal 250ms → 200ms (daha keskin ama hâlâ nazik).

### Cover sistemi
- `.cover-placeholder` (HSL gradient) **kaldırılır** → `.cover-skeleton`
- Honest skeleton: `paper-200` blok + mono caps **"FOTOĞRAF EKLENMEDİ"** + dateline meta altta
- Admin'deki `cover_hue_a/cover_hue_b` slider'ları kullanılmaz olur (Writing modelde kolon kalır, DB migration yok — dead field; gelecekte drop migration yazılabilir)
- Gerçek fotoğraf yüklendiğinde Spatie Media webp variant pipeline'ı *(Faz 2B)* aynen çalışır

### Bilgi mimarisi
Nav 3 → **4** item:
1. **Saha** — saha yazıları, röportajlar, denemeler (eski "Yazılar"ın büyük çoğunluğu)
2. **Görüntü** — foto seri + drone arşivi *(IA'da yerini açar; Faz 5'e kadar "yakında" mesajıyla boş)*
3. **Hakkında**
4. **İletişim**

Writing `kind` enum: `saha_yazisi / roportaj / deneme / not` **korunur**; foto seri Faz 5'te ayrı model (`PhotoStory`) olarak eklenir. Bu ADR IA değiştirmez, yerini açar.

### Anasayfa — Editor's Desk pattern
- Slogan-hero **kaldırılır**
- Lead dispatch (son yayımlanan, 8-col) + portre rail (4-col, 3:2 crop küçük)
- Altında kronolojik dispatch listesi (card grid değil, text-row)
- "Daha fazlası" CTA bloğu **kaldırılır**; yerine "bir sonraki dispatch" ya da son taslak linki
- Bylines strip korunur, tipografi güncelle

### Diğer sayfa kararları
- **Writing index:** card grid → kronolojik liste (yıl başlıkları + dateline + headline + 1-line lede). Sticky filter rail (tür × bölge × yıl).
- **Writing show:** drop cap kaldırıldı, lede paragraph stili; true marginalia (sticky meta + footnotes); cover caption + credit zorunlu.
- **About:** 3:4 portre sticky sol + 4 work-area sütun (Saha · Görsel · Araştırma · Yayıncılık) + kronoloji (accent-free dot) + metodoloji bölümü; özgeçmişten nesir biyografi.
- **Contact:** italik em kaldırıldı, primary channel border accent kaldırıldı (sadece `PRIMARY` mono eyebrow).
- **Header:** logo dot kaldırıldı; wordmark IBM Plex Mono Bold ALL CAPS tracking 0.1em + alt dateline microcopy "İSTANBUL · MUHABİR".
- **Footer:** 4-col → 2-col; sahte sosyal linkler **kaldırıldı** (gerçek hesap varsa admin editable); colophon satırı (build date + version) **eklendi** — teknik şeffaflık katmanı.

### Portrait policy
- Source: `storage/app/public/portraits/source.jpg` (sahibi yükler)
- Spatie Media otomatik variant: **1:1** (600×600) · **3:4** (600×800) · **3:2** (900×600)
- Tedavi: filtre yok, duotone yok; hafif contrast +5% + warm +2 (paper ile uyum)
- Kullanım: **About primary** (3:4 sticky) · **Landing rail** (3:2 küçük) · opsiyonel footer damga (1:1 head-tight)
- Full-bleed portre hero kullanımı **yasak** (LinkedIn influencer pattern).

## Consequences

### Pozitif
- **Profile uyum:** 4 katmanın (saha/görsel/akademi/teknik) hepsi tek tasarım dilinde yaşar.
- **Portre güçlenir:** Sessiz paper bg üstünde doğal ışıklı portre kaybolmaz — karakterli olur.
- **Accent renk bağımlılığı azalır:** Renk-körü kullanıcı için bilgi taşıyıcı renge bağımlılık yok.
- **Zaman testi:** Mono + serif + kâğıt estetiği NYT Magazine/Aperture 50+ yıldır aynı omurga üstünde.
- **Backend dokunulmaz:** Writing modeli, policy'ler, Tipta P, Spatie Media, auth, 2FA, admin omurga — hiç dokunulmuyor. Sadece view+CSS+2 controller bağlamı+1 seeder.
- **Bundle küçülür:** Fraunces Variable (~35KB gz) çıkar, Source Serif 4 Variable (~28KB gz) girer → ~7KB kazanç.

### Negatif / Trade-off
- **ADR-014 kararlarının yarısı süperseded.** Ember accent, Fraunces SOFT=20, HSL cover, italik em vurgu kuralı, hero slogan — hepsi çıkıyor. Geriye kalan: paper grain, 3-col layout, stone neutrals omurgası.
- **Font değişimi:** ADR-014 bilerek reddetmişti ("marjinal kazanç için aşırı iş"). Artık ana karar — çünkü **kimlik** değişiyor, sadece ton değil.
- **Test stringleri:** "Hakkında · kısa biyografi" gibi statik text assert eden ~5 test güncellenir.
- **Cover slider'lar dead field:** Migration şimdilik drop edilmiyor (safe); admin form'dan kaldırılıyor.
- **Görüntü nav boş:** Faz 5'e kadar "yakında" placeholder. IA'yı açık tutmak için kabul edilir.

### Risk
- **Çok kuru izlenimi:** Sahibi "biraz daha sıcaklık" derse → primary CTA brass-600 kalır, ek vurgu brass-400'e kaydırılır. Extra renk istenmez.
- **Source Serif 4 TR karakter desteği:** Variable font latin + latin-ext subset full desteklidir (Adobe resmi). Preload kritik weight 600 + 700.
- **Disk dolu durumu:** Mevcut npm cache'i yetecek; Source Serif ~5MB install. Fraunces kaldırıldığında fark minimal.

## Alternatives Considered

### Direction A — Wire Service (Reuters/AP clinical)
- **Neden seçilmedi:** Çok cold. "Kişisel ama gösterişsiz" brief maddesi anonim wire service ile uyumsuz. Bir wire desk editoryal masasında "ben" yoktur.

### Direction B — Field Notebook (deri defter, cream paper, ruled lines)
- **Neden seçilmedi:** Skeuomorphism tehlikesi ("cute"). Foto muhabir sitesi defter rolünde görüntüleri yutabilir — fotoğraf alanı kısıtlanır. Brief: "görsel olarak güçlü ama habere hizmet eden" — defter metin istiyor, foto sıkışıyor.

### Direction C+B hybrid (paper kâğıt + mono başlık)
- **Neden seçilmedi:** Iki kimliğe birden yaslanır, Fraunces değişikliğinin sağladığı netliği tekrar bulanıklaştırır.

### Source Serif 4 yerine Newsreader / Literata / GT Sectra
- **Newsreader:** Google Fonts variable, iyi ama biraz daha "reading-first", daha az "editoryal otorite".
- **Literata:** Kindle reader için optimize — çok reading-focused, display olarak zayıf.
- **GT Sectra:** Lisanslı (ücretli), bu projede kabul edilemez.
- Source Serif 4 Adobe açık lisansla + variable + opsz destekli + haber+akademi register'ında test edilmiş.

## References
- Brief: 2026-04-19 kullanıcı mesajı ("DÜŞÜN ARAŞTIR DÜŞÜN ÖĞREN ANALİZ ET...")
- Özgeçmiş: mesaj içeriğinde paylaşıldı
- Portre: mesaj içeriğinde görsel olarak paylaşıldı (beyaz tişört, doğal ışık, sakin ifade)
- Detaylı audit + alternatifler: `docs/REDESIGN_2026.md`
- Source Serif 4: https://github.com/adobe-fonts/source-serif
- Adobe lisans (SIL Open Font License 1.1): her kullanım serbest

## İlgili ADR'lar
- ADR-003 — Frontend stack (Tailwind v4 + Alpine + HTMX + Vite) korunur
- ADR-013 — Domain pivot (savaş muhabiri) korunur; bu ADR onu **doğru pozisyonlandırır**: "foto muhabir + yayıncı + araştırmacı" olarak.
- **ADR-014 — süperseded**: ton kalibrasyonu bu ADR ile yeniden yapılmış; eski kararlar (ember, Fraunces SOFT, HSL cover, italik em) terk edilmiş.
- ADR-015 — Writing model korunur; `cover_hue_a/b` alanları artık kullanılmıyor ama kalıyor (dead field tolere edilir, future drop migration opsiyonel).
