# Emergency Design Diagnosis + Visual Rebuild Plan

> **Tarih:** 2026-04-19 · **Durum:** ACİL — Patch modu durduruldu.
> **Yöntem:** Render edilmiş site üzerinden 10 screenshot + 4 background research agent (NYT Magazine + Aperture + Granta · Magnum + Reuters Wider Image + Magnum Foundation · The Marshall Project + Longreads + Frontline + ProPublica · 7 gerçek gazeteci sitesi).
> **Bu doküman:** Acımasız teşhis → kök neden → benchmark sentezi → revize yön → sahne sahne yeniden kurulum planı. **Uygulama bu turdan sonra, onayla.**

---

## A. BRUTAL MACRO DIAGNOSIS — sayfa sayfa, ne gördüğüm

### A.1 — Anasayfa (`/`) desktop @1440px

**Ne gördüm:**
- Header: wordmark + microcopy `İSTANBUL` + 3 nav + theme toggle ◐ + Masa button + ≡ hamburger (1440px'de hamburger görünmemeli — `md:hidden` çalışıyor ama screenshot'ta yan yana mevcut; muhtemelen layout viewport hesabı yanlış)
- Top date strip: `19 NİSAN 2026 ··· PAZAR` (bilgi var ama context'i yok — neden bu sayfada bugünün tarihi var?)
- Lead bloğu: eyebrow + display headline `Yakınlığın etiği üstüne` + standfirst italic + dateline meta + "Devamını oku →"
- Lead bloğu **fold genişliğinin yaklaşık %40'ını kaplıyor** (sol). Sağ %60 BOMBOŞ.
- Section break (hairline rule)
- "MASADA / Daha fazla dispatch" başlık + "Tüm arşiv →"
- 6 dispatch chronological list (writing-row pattern)
- "YAYIMLANDIĞI YERLER" bylines tek satır mono caps
- Footer 3-col + alt hairline

**Brutal okuma:**
- ❌ **Foto muhabir sitesi anasayfasında SIFIR FOTOĞRAF.** Ölümcül. NYT Magazine bench: hero image 60:40 image-dominant olur. Magnum bench: photographer = headline; image carries emotional load. Bizimki hiç fotoğraf yok — kişinin **birincil mesleği görsellik** olduğu halde.
- ❌ **Lead bloğu fold %40, sağ %60 boş.** NYT Magazine bench: lead 9/12 col, sonra demotion. Bizim "currentContext rail" boş olduğunda fall-back full-width yok — sayfa "bir şey eksik" hissi veriyor. Asimetri editoryal değil, kullanılmamış alan.
- ❌ **Top date strip context'siz.** "19 NİSAN 2026 / PAZAR" — neden orada? "Bugün" eyebrow yok, "yayın günlüğü" konseptine bağlı değil. Tarih dekor olarak duruyor.
- ❌ **Hero "Yakınlığın etiği üstüne" headline çok kasıyor.** Source Serif 4 SemiBold 7xl — ama içeriğin ağırlığını **arkasında bir image olmadan tek başına taşıyor**. Bu Granta-style olabilir (headline = lead) ama Granta'da author-name aynı ağırlıkta + issue number context'i var. Bizimkinde sadece display headline + dateline — ağırlık merkezi tek bir typografik öğe, image desteği yok.
- ❌ **"Devamını oku →" link ondan ayrı asılı.** Headline zaten klikli (link), bu link tekrar — artıklılık + button-vibe ekliyor (onun için yapılmadı, ama görünüşte öyle).
- ✓ **Chronological dispatch list temiz.** Aperture bench "uniform card cadence — peers look like peers" pattern ile uyumlu. Bu kısım iyi.
- ✓ **Bylines strip tek satır mono caps** — Marshall/ProPublica bench ile uyumlu, yayın güvenirliği taşıyor.
- ❌ **Footer "Sayfalar" sütunu jenerik** — Yazılar/Hakkında/İletişim/RSS. Wesley Lowery bench: footer'da newsletter / "On Journalism" gibi format-specific bölümler olur. Bizimki SaaS-jenerik.

### A.2 — Anasayfa mobile @375px

- ✓ **Mobile fold belki desktop'tan daha iyi.** Single column = lead headline tüm width'i kullanıyor; standfirst italic okunaklı; dateline + Devamını oku akıyor. "Editor's desk" mantığı mobile'da daha doğal hissettiriyor çünkü asimetri sorunu yok.
- ⚠️ Header'da Masa button auth olmadan görünür (`@auth` ile sınırlı yapmıştım, cookie persist olmuş olabilir; tek-kullanıcı site için marjinal).

### A.3 — `/yazilar` (writing index) desktop

**Ne gördüm:**
- Header + ARŞİV eyebrow + "Tüm yazılar" display headline + intro + sayım `6 yazı · tür: hepsi`
- Hairline rule
- 2-col grid: sol filter rail (`TÜR / hepsi / saha yazısı / röportaj / deneme / not` — link list) + sağ year-grouped chronological list
- `2026` year-label + writing-row'lar

**Brutal okuma:**
- ✓ **Year groupings + filter rail iyi.** Aperture bench pattern: filter taxonomy = institutional weight.
- ✓ **Writing-row'lar tutarlı; hairline ayraç temiz.** Magnum/Marshall bench ile uyumlu.
- ❌ **Hâlâ sıfır fotoğraf.** Foto muhabir arşivi olması gereken sayfada **bir tane bile thumbnail yok**. Magnum bench: "fixed width, free height" (340×var) — fotoğraf SOL kolonda 80×60 thumbnail bile yok. Liste fakir görünüyor.
- ⚠️ **Filter "saha yazısı / röportaj / deneme / not"** — 4 tip yazı türü ama **format-bazlı navigation** (Anna Badkhen bench: Books / Short reads / Dispatches / Appearances) yapılmamış. Foto serisi (Görüntü), video, akademik makale, röportaj transkripti — hepsi tek "yazı" havuzunda.
- ❌ **"6 yazı · tür: hepsi"** — sayım anasayfa lead'e göre disipline edilmemiş; "ARŞİV: 6 dosya / 4 tür / 2022-2026" gibi NYT-kicker tonu yok.

### A.4 — `/yazilar/{slug}` (writing show) desktop

**Ne gördüm:**
- Header + ← Yazılar + dateline bar (`HARKİV · 04 ARALIK 2022 · RÖPORTAJ · 1 DK OKUMA`)
- Display headline `Boşalmayı reddeden bir şehir` (Source Serif 700, 2 satır)
- Standfirst italic gray
- **DEVASA** cover-skeleton 16:9 hero blok ortasında küçük "FOTOĞRAF EKLENMEDİ" mono caps + sol alt meta
- Aşağıda 3-col: marginalia rail (sol) + body prose + 1fr boş kolon

**Brutal okuma:**
- ✓ **Headline + standfirst + dateline bar bütün halinde güçlü.** Marshall/Frontline bench: kicker → headline → dek → byline → date → cover → caption pattern bizimkine yakın (kicker olarak dateline kullanıyor).
- ❌ **Cover skeleton 16:9 hero bloğu SUÇLU.** Honest empty state fikir doğru ama uygulama yanlış: foto yokken devasa gri bir "fotoğraf yokmuş" göstergesi koymak kullanıcıyı yanıltır + sayfanın yarısını boş alana yatırır + sayfa "ben bir foto yapısına aitim ama foto yok" der. **Doğru çözüm: foto yoksa cover bloğu HİÇ olmamalı, headline'dan body'ye direkt akış.**
- ❌ **"FOTOĞRAF EKLENMEDİ" text görünmez/silik** — paper-200 üstüne paper-500, kontrast düşük; meta sol alt aynı problem.
- ⚠️ **Standfirst italic gray** çok hafif — Marshall bench: standfirst `tek cümle, iddia içersin` — bizimki var ama color paper-600 muted, ağırlık taşımıyor.
- ⚠️ **3-col grid: marginalia + body + 1fr boş kolon.** Sağ kolon **boşa harcanmış**. NYT Magazine + Marshall bench: body genişliği viewport'un %60-70'i, ortalı, side rail YA marginalia (sol) YA related/footnotes (sağ) — ama ikisi birden değilse o kolon boş kalmamalı. Bizim layout 1fr-180-62ch-1fr sıralaması viewport'u boşa harcıyor.
- ❌ **Body bittikten sonra prev/next + "Aynı türden" liste** — Marshall/ProPublica bench: end-of-article üç bant (yazar mini-bio + ekip credit + ilgili dosyalar + newsletter). Bizim tek bant: "Aynı türden" ile karışık — yazar credit yok, ekip credit yok, methodology box yok, republish offer yok.

### A.5 — `/hakkimda` desktop

**Ne gördüm:**
- Header + "HAKKINDA · KISA BİYOGRAFİ" eyebrow
- Display H1: `Ozan` (yeni satırda) + `Efeoğlu` ITALİK BRASS ACCENT RENK
- Sağda kısa intro paragraph
- Sticky sol künye rail (`başlangıç 2015 / üs İstanbul, Türkiye / saha MENA + Kafkasya + Doğu Avrupa / dil TR + EN + UA / tempo / basın / güvenlik`)
- Sağda body prose biyografi başlangıcı

**Brutal okuma:**
- ❌❌❌ **`Ozan / Efeoğlu` italik em ACCENT RENK BAŞLIK.** ADR-016 net biçimde yasakladı. Bu kalan eski Session 0 dilinden — **hâlâ orada**. Sayfanın en görünür yerinde ADR ihlali.
- ❌ **PORTRE YOK.** User'ın 1 numaralı isteği. Sağ üst boş, hiçbir görsel yok.
- ❌ **"Saha muhabiri ve yazar"** — eski 1-rol framing. Janine di Giovanni bench: "war correspondent | author | academic | human rights advocate" — 4 rol pipe ile. Bizim hâlâ tek satır slogan, **"foto muhabir + yayıncı + akademisyen + yazılım kökeni"** 4 katmanı yok.
- ❌ **"saha muhabiri, yazar. On yıldır haberin kör noktalarından yazıyor."** — kendini-pazarlama. Wesley Lowery bench: üst-fold üçüncü-şahıs alıntı (NYT övgüsü). Bizim self-promo.
- ❌ **Drone haberciliği + görsel göstergebilim** (akademik kimliği) — sıfır vurgu. Hasan Kalyoncu Üni. yüksek lisans tezi, saha pratikleri kitabı görünmüyor.
- ❌ **Anadolu Ajansı Uluslararası Haber Merkezi** — kurumsal afiliyasyon banner (di Giovanni bench: 5 kurumsal logo) yok. AA İstanbul'daki şu anki rolü görünmüyor.
- ✓ **Künye rail sol kolon** — başlangıç/üs/saha/dil/tempo/basın/güvenlik — yapı OLDUKÇA iyi. Anna Badkhen bench: kısa bullet'lar yayın isimleriyle güven inşa eder; bizim künye o tonda.
- ❌ **CV bullet → nesir geçişi yapılmamış.** Brief 4 hedefiydi.

### A.6 — `/iletisim` desktop

**Ne gördüm:**
- "İLETİŞİM · GÜVENLİ KANALLAR" eyebrow
- Display H1: `Üç` + ITALİK BRASS ACCENT `güvenli` + `kanal.`
- Intro paragraph
- 3 kart: EDİTÖRYAL E-POSTA (`press@ozanefeoglu.com`) / SİGNAL (`@ozanefeoglu.42`) / PGP ŞİFRELİ E-POSTA (`secure@ozanefeoglu.com`)
- Sağda body: "Kurumsal değilse, doğrudan _buradan_." (yine italik em accent)

**Brutal okuma — KRİTİK İHLALLER:**
- ❌❌❌ **2 italik em accent başlık** (ADR ihlali × 2).
- ❌❌❌ **SİGNAL `@ozanefeoglu.42` FAKE.** User dedi: "Signal için signal.me link kullan." + "PGP yoksa tamamen gizle. Yakında yazma." + "Halen olmayan hiçbir şeyi göstermesin." Mevcut 3 kart kullanıcıyı **YANLIŞ BİLGİYLE** karşılıyor — "@ozanefeoglu.42" ne signal.me link, ne gerçek bir handle.
- ❌❌❌ **PGP `secure@ozanefeoglu.com` FAKE** — hangi anahtar? Hangi parmak izi? Sayfanın altında PGP fingerprint bloğu da fake.
- ❌ **3 channel iddiası** — slogan ("Üç güvenli kanal") sayıyı vurguluyor ama sadece 1'i gerçek (e-posta). "Üç" iddiası **3 kanaldan 2'si fake** üzerine kurulu.
- ❌ **"Kurumsal değilse, doğrudan buradan"** — slogan-y copy. Editoryal/profesyonel iletişim sayfası olmalı, blog yazısı tonu değil.

### A.7 — `/yazilar/{slug-yok}` (404 sayfası)

**Ne gördüm:**
- "HATA · 404" eyebrow
- Display H1: `Aradığın sayfa` + ITALİK BRASS ACCENT `kaybolmuş.`
- Body intro + 2 button (Anasayfaya dön + Yazılara göz at)

**Brutal okuma:**
- ❌ **İtalik em accent başlık × 1.** ADR-016 ihlali, eski Session 0 dilinden kalmış.
- ✓ Buton aileleri Session 3.5 sonrası temiz: primary + secondary çifti doğru.

### A.8 — Dark mode (kısmi gözlem)

- ✓ Renk shift düzgün — paper-950 bg + paper-100 ink + brass-400 accent.
- ⚠️ Dark mode'da italik em accent **brass-400** olarak görünür (test edilmedi tam ama CSS gereği) — ADR ihlalini darkmode dahil her yerde taşıyor.

### A.9 — Genel desktop layout patolojisi

**Container kullanımı:** `--container-wide: 1320px` üstünde 1440 viewport. Yan margin clamp(1rem, 4vw, 3rem) — yan kenarlar OK ama **iç kompozisyon zayıf**. Lead 5/12 col, marginalia + body + boş 1fr — tüm sayfalar viewport'un çok büyük kısmını ya boşa harcıyor ya tek bir merkezi öğeye yığıyor.

**12-col grid hiç kullanılmıyor.** Tasarım sistemi `--container-wide` + clamp gutter dışında **grid mantığı taşımıyor**. Sayfa "wide container içinde tek block" + "wide container içinde 3 block flex" + "wide container içinde 2-col grid 200/1fr" karışımı — her sayfa farklı ad-hoc grid mantığıyla yazılmış. NYT Magazine + Marshall bench: **12-col strict grid + grid breaks editorial signaling**.

---

## B. MACRO ANALİZ — 10 EKSEN

User'ın istediği eksenlerde teşhis:

| # | Eksen | Durum | Kanıt |
|---|---|---|---|
| 1 | **Composition** | ❌ ZAYIF | Lead 5/12 + 7/12 boş; show'da 180+62ch+1fr boş kolon; about'ta sticky künye + body iyi ama H1 pozisyonu "stack" değil "akış" mantığında karışık |
| 2 | **Pacing** | ⚠️ ORTA | Anasayfa: lead → liste → bylines → footer ritmi var, ama lead'den listeye geçiş ani. Show: headline → cover (devasa empty) → body geçişi cover'la kırık |
| 3 | **Hierarchy** | ⚠️ ORTA | Lead headline mevcut ama **secondary tier yok**: NYT bench "1 hero + 4 secondary + 6 tertiary" — bizim "1 lead + 6 list" sadece 2 tier |
| 4 | **Visual gravity** | ❌ ZAYIF | Anasayfada headline tek başına ağırlığı taşıyor — image desteği yok. About'ta italik soyad accent ağırlık merkezi (hatalı yer). Show'da cover skeleton ağırlık merkezi (boş ağırlık) |
| 5 | **Editorial tension** | ❌ ZAYIF | "Hero hayır lead var" tek bir karar + section break + uniform liste — gerilim hiç yok. Marshall bench: methodology kutusu + pull quote + ekip credit gerilim üreten ritimler — bizde sıfır |
| 6 | **Image-to-text balance** | ❌❌ FELAKET | Anasayfada 0% image. Show'da ya devasa boş cover ya hiç. Index'te 0% image. **Foto muhabir sitesi için kabul edilemez.** Magnum bench: 60:40 image-text |
| 7 | **Density vs silence** | ⚠️ ORTA | Silence çok fazla (anasayfa fold %60 boş). Density çok az (yalnız 6 dispatch list). NYT bench: silence rezerve, density list'te — bizimki tersine işliyor |
| 8 | **Credibility vs stylization** | ⚠️ ORTA-ZAYIF | Bylines strip + AA çağrışımı yok + akademik affiliasyon yok + üçüncü şahıs quote yok = credibility eksik. Stylization (italik em accent, brass) ihlal halinde devam ediyor — credibility'yi yiyor |
| 9 | **Personality vs vanity** | ⚠️ ORTA | Wordmark sade (iyi) + dateline microcopy fısıltı (iyi). Ama hero "Gittim baktım yazdım" kalıntısı yok artık (Session 3'te düzeldi); about'ta ego "Ozan _Efeoğlu_" italik kullanılıyor (vanity) |
| 10 | **Realism vs design-performance** | ❌ ZAYIF | "Tasarım yapıldı" hissi her sayfada — fake Signal/PGP, italik em accent ısrarcı kalıntı, devasa boş cover skeleton, içerikten önce form. Patrick Radden Keefe bench: silinmemiş redesign izleri = lived-in. Bizim her şey fazla yeni-yeni tasarlanmış görünüyor |

---

## C. TOP 10 KÖK NEDEN — gerçek teşhis

Bu site neden **hâlâ güzel değil / gerçek değil / güçlü değil / bir dünya kurmuyor**:

### KN-1: Foto muhabir sitesi — sıfır fotoğraf
| | |
|---|---|
| **Semptom** | Anasayfa, /yazilar, /yazilar/{slug} hero — hepsinde fotoğraf yok |
| **Kök neden** | Spatie Media + cover variant pipeline kuruldu (Faz 2B) ama (a) seeder'da gerçek foto bağlanmadı, (b) sayfa kompozisyonları "foto opsiyonel" varsayımıyla tasarlandı, (c) honest skeleton fikri "foto yoksa devasa boş kutu" olarak yanlış yorumlandı |
| **Ekrandaki etki** | Foto muhabir sitesi metin-tabanlı blog gibi görünüyor; kişinin **birincil mesleği görünmez** |
| **Kullanıcı hissi** | "Bu site bir foto muhabirine ait değil, bir yazara ait" — kimlik kayması |
| **Çözüm yönü** | (a) Seeder'da Unsplash/sahibi'nin gerçek fotoları + caption + credit ile bağla, (b) homepage hero "lead dispatch + dispatch fotoğrafı" şeklinde yeniden kur, (c) cover-skeleton-as-block fikri **iptal** — foto yoksa cover yok |

### KN-2: ADR-016 italik em accent kuralı 3 sayfada ihlal halinde
| | |
|---|---|
| **Semptom** | About: "Ozan _Efeoğlu_" / Contact: "Üç _güvenli_ kanal" + "doğrudan _buradan_" / 404: "Aradığın sayfa _kaybolmuş_" — hepsi italik em + brass-600 |
| **Kök neden** | Session 1'de CSS'ten `h1 em` kuralı kaldırıldı ama **inline `<em class="italic text-[var(--color-accent)]">` Blade'lerde kaldı**. Session 2-3.5'te CSS temizlendi, view rename yapıldı, ama italik em accent inline pattern'i view-by-view temizlenmedi |
| **Ekrandaki etki** | Sayfaların en görünür typografik öğesi (display H1) ihlal taşıyor — "tasarım dili kararlı" yalanı |
| **Kullanıcı hissi** | "Bu site session-session katmanlı" — KULLANICININ TAM KENDİ TEŞHİSİ |
| **Çözüm yönü** | View-wide grep + brutally hepsini at: hiçbir display H1'de italik em accent kalmasın. Standfirst veya body içinde italik OK ama vurgu rengi YOK |

### KN-3: Contact sayfası fake data ile güveni öldürüyor
| | |
|---|---|
| **Semptom** | Signal `@ozanefeoglu.42` fake, PGP `secure@ozanefeoglu.com` fake, "Üç güvenli kanal" iddiası 2/3 yalan |
| **Kök neden** | PageSeeder Session 0'dan beri uydurulmuş seed data kullanıyor; user'ın "PGP yoksa tamamen gizle" + "signal.me link kullan" kararı **seeder'a uygulanmadı** |
| **Ekrandaki etki** | Sahibi gerçek ziyaretçi alırsa fake handle'a yazar — kimse cevap vermez |
| **Kullanıcı hissi** | Sahibinin "boş vaat istemiyorum" istediği şey ana iletişim sayfasında ihlal |
| **Çözüm yönü** | PageSeeder'ı yeniden yaz: Signal/PGP `null` default, sadece e-posta gerçek; view config-driven (Session 2'de yapıldı `config/site.php` ama PageSeeder hâlâ extras JSON kullanıyor) |

### KN-4: Anasayfa lead 5/12 col, sağ 7/12 boş
| | |
|---|---|
| **Semptom** | Desktop @1440 fold, lead block sol yarı; sağ rail boş (currentContext null) |
| **Kök neden** | Lead grid `grid-cols-[minmax(0,1fr)_minmax(240px,320px)]` ama rail boş olduğunda grid fallback yok — lead `1fr` aslında container'ın yarısı kadar |
| **Ekrandaki etki** | Fold'un %60'ı kullanılmamış; lead headline tek başına asılı; sayfa "yarım yapılmış" görünüyor |
| **Kullanıcı hissi** | "Bir şey eksik buradan" — KULLANICININ DEĞİL HENÜZ AMA YAKINDA HİSSEDECEĞİ |
| **Çözüm yönü** | (a) Lead'in YANINDA gerçek bir image (cover photo of lead dispatch) — bu KN-1'i de çözer; (b) rail gerçekten dolu olduğunda göster, boşsa lead full-width 8/12 + 4/12 secondary list |

### KN-5: Cover-skeleton 16:9 hero "fotoğraf yokmuş" devasa empty
| | |
|---|---|
| **Semptom** | /yazilar/{slug} writing show'da fotoğraf olmayan dispatch için 16:9 paper-200 blok + silik "FOTOĞRAF EKLENMEDİ" mono text |
| **Kök neden** | "Honest empty state" prensibi yanlış uygulandı: empty state UI öğesi olmamalı, **boş alan boş bırakılmalı**. Empty state ancak bir **işlem** beklerken kullanılır (örn: "yazı yok, ekle" admin'de) |
| **Ekrandaki etki** | Body'den önce viewport'un %50'si gri "fotoğraf yokmuş" göstergesi — tasarım performansı, bilgi değil |
| **Kullanıcı hissi** | "Bu site fotoğraf bekliyor ama yok — eksik bir şeye bakıyorum" — sürekli eksiklik bildirimi |
| **Çözüm yönü** | Cover-skeleton-as-hero KALDIRILSIN. Foto varsa: 16:9 hero + figcaption + credit. Foto yoksa: hiç cover yok, dateline + headline + standfirst → body direkt akar |

### KN-6: 12-col grid yok — her sayfa ad-hoc grid
| | |
|---|---|
| **Semptom** | Anasayfa lead `1fr_320px`; show `180_62ch_1fr`; about `20rem_1fr`; index `200px_1fr` — hiçbir sayfa aynı grid'e oturmuyor |
| **Kök neden** | `--container-wide` + clamp gutter dışında **CSS grid sistemi yok**; her view kendi flex/grid ad-hoc'u yazıyor |
| **Ekrandaki etki** | Sayfalar arası geçişte içerik genişlikleri kayıyor; "aynı sitede miyim" hissi zayıflıyor |
| **Kullanıcı hissi** | "Sayfalar ayrı tasarımlar" — kullanıcının TAM kendi teşhisi |
| **Çözüm yönü** | **Strict 12-col grid** kur (`--gutter`, `--col`, `--page-grid` utilities); her sayfa aynı grid'e oturur; grid breaks (full-bleed) editoryal sinyal |

### KN-7: About sayfası 4-katman kimlik taşımıyor
| | |
|---|---|
| **Semptom** | "saha muhabiri, yazar" 1-rol slogan; portre yok; AA Uluslararası vurgusu yok; akademik kimlik (drone hab + visual semiotics + saha pratikleri kitabı) görünmüyor |
| **Kök neden** | PageSeeder Session 0'dan beri eski "savaş muhabiri" framing kullanıyor; ADR-016'da netleşen 4-katman kimlik (foto+yayıncı+akademisyen+teknolog) seeder'a yazılmadı |
| **Ekrandaki etki** | Kişinin **gerçek mesleki çoğulluğu** kayıp; site onu "blog yazarı" konumuna sıkıştırıyor |
| **Kullanıcı hissi** | "Bu adamın gerçekten ne yaptığını anlamıyorum" |
| **Çözüm yönü** | PageSeeder yeniden: Janine di Giovanni pattern'inde "foto muhabir | yayıncı | akademisyen | teknolog" çoklu kimlik + AA banner + Hasan Kalyoncu Üni. tezi + saha pratikleri kitabı |

### KN-8: Hero pattern kişiyi sahnelemiyor — image desteksiz tek typografik öğe
| | |
|---|---|
| **Semptom** | Anasayfa lead = sadece headline + standfirst; about = sadece display H1 italik soyad; show = headline + standfirst |
| **Kök neden** | "Slogan yok" kararı doğru ama yerine "image-supported lead" konmadı — sadece headline kaldı. Tek typografik öğe ağırlığı taşıyamıyor |
| **Ekrandaki etki** | Her hero "büyük başlık + küçük metin" formülü — hiçbiri sayfayı tutmuyor |
| **Kullanıcı hissi** | "Tasarım yapıldı ama içerik tutmuyor" |
| **Çözüm yönü** | NYT/Magnum bench: hero = image (60-85%) + headline (üstte veya altta) + dateline + caption. Bizim hero'lar bu yapıya geçmeli — anasayfa lead, about masthead, show cover hepsi |

### KN-9: End-of-article + footer çok jenerik
| | |
|---|---|
| **Semptom** | /yazilar/{slug} sonu: prev/next + "Aynı türden 3 dispatch". Hiç yazar mini-bio, ekip credit, methodology, republish offer yok. Footer: Sayfalar/İletişim/RSS jenerik |
| **Kök neden** | ProPublica/Marshall/Frontline bench'in "end-of-article 3 bant" pattern'i bilinmiyordu Session 3'te |
| **Ekrandaki etki** | Yazı bitince hiçbir authority signal yok — blog post bittiği gibi |
| **Kullanıcı hissi** | "Burada bir gazeteci ekibi var" hissi yok; tek-yazar-blog hissi var |
| **Çözüm yönü** | End-of-article pattern: yazar mini-bio (foto + AA credit + e-posta) + co-published rozeti (varsa) + republish notu + ilgili dosya 2-3 kart + footer'da newsletter (varsa) ya da gerçek dağıtım kanalı |

### KN-10: Direction C "refined editorial minimalism" doğru ama uygulamada SADELİK değil ÇIPLAKLIK
| | |
|---|---|
| **Semptom** | Sayfalar minimal ama sayfaları "minimal" yapan unsurlar (fotoğraf, caption, kicker, byline, methodology box, pull quote, kurum afiliyasyon banner) eksik. Refinement'a layered detail gerekir; bizde layer eksik |
| **Kök neden** | "Az şey, doğru şey" prensibi yanlış uygulandı: gereken şeyleri de koymadık. Aperture bench: az yazı + güçlü foto + caption disiplini — bizde "az yazı + hiç foto + caption yok" |
| **Ekrandaki etki** | Steril, ruhsuz, "tasarım kasıyor ama söyleyecek bir şey yok" |
| **Kullanıcı hissi** | TAM USER'IN TEŞHİSİ: "Bu güzel mi? Hayır. Sadece düzenli. Bu güçlü mü? Hayır. Sadece sessiz." |
| **Çözüm yönü** | Direction C'yi koruma ama **refinement = doğru detay tabakası** olarak revize et. Foto + caption + credit + kicker + byline + kurum banner + pull quote — her sayfada doğru editoryal detay zorunlu |

---

## D. BENCHMARK SENTEZİ — 4 agent rapor → 12 en kritik patern

4 agent toplam **36 transferable principle** verdi. Şu projenin acil ihtiyacı olanlar (öncelik sırasına göre):

### Birinci öncelik (KN-1, KN-5, KN-8 ile doğrudan ilgili — image-led editorial)

1. **Lead = image-dominant 60:40 (foto için) veya text-dominant 30:70 (yazılı feature için).** Per-piece karar, site default'ı YOK. — *NYT Magazine, Aperture*
2. **Fixed-width column, free-height image** — fotoğrafçının seçtiği aspect ratio'a saygı. Constant width + variable 4:5 / 4:3 / 16:9 height = documentary, not template. — *Magnum*
3. **Caption block tek typografik öğe**: `LOCATION (caps tracked) — descriptive sentence — Photographer / Agency`. ~75-80% of body size, neutral gray. — *Reuters Wider Image*
4. **Credit lives with the image, never inside the headline**. Photographer = metadata typography, ayrı satır. — *Magnum Foundation, Reuters*
5. **Foto yokken cover BLOK YOK** — hiç skeleton göstergesi yok. Headline → body direkt. — *Anti-pattern: Adam Ferguson templated grid*

### İkinci öncelik (KN-2, KN-6, KN-9 ile ilgili — editorial discipline + grid)

6. **Demote by REMOVING, not shrinking** — secondary önce image, sonra dek, sonra shrink. 3 discrete tier. — *NYT Magazine*
7. **Kicker/series label HEADLINE'IN ÜSTÜNDE** small-caps tek satır — yazıyı bir dosya/seriye bağlar. Headline gerçek cümle olur. — *NYT Magazine, ProPublica*
8. **Standfirst = tek cümle, iddia içersin** — özet değil, tezin tek satır iddiası. Byline'dan önce. — *Marshall Project*
9. **Body kolonu 65-72ch, viewport %55-65, ortalı.** Hiçbir koşulda full-width. Side rail YA marginalia (sol) YA related (sağ); ikisi birden değilse o kolon body'ye verilir. — *Marshall, Longreads, Frontline*
10. **End-of-article 3 bant**: (1) yazar mini-bio + iletişim, (2) ilgili dosya 2-3 kart, (3) newsletter (varsa). Tag pill listesi en alta. — *Marshall, ProPublica*

### Üçüncü öncelik (KN-7, KN-10 ile ilgili — kişisel site authority)

11. **Üst-fold üçüncü-şahıs alıntı / kurumsal afiliyasyon banner** — kendini-pazarlama yerine üçüncü şahıs otoritesi (NYT övgüsü, Yale/CFR/Guggenheim/AA logoları). — *Wesley Lowery, Janine di Giovanni*
12. **Çoklu kimlik pipe-ayrımı** ("foto muhabir | yayıncı | akademisyen | teknoloji okuryazarı") — tek-rol slogan'ı yalanı yok. — *Janine di Giovanni*

### Bench'ten ÇIKARDIĞIM ANTI-PATTERNS (yapmayacaklarımız)

- ❌ Vanity domain (anand.ly istisnası kişisel marka kararı; biz `ozanefeoglu.com` zaten formal)
- ❌ Ayrı "Books / Speaking / Press" gibi 5+ menu item — 4 fazla, 3 yeter (Sebastião Salgado tour-like feel)
- ❌ Squarespace cart icon / "200+ photographs" social proof bar (Sebastião Salgado anti-pattern)
- ❌ Hero portre full-bleed (LinkedIn influencer)
- ❌ "Show thumbnails" toggle, "Ctrl+K" custom shortcut (lived-in olabilir ama bizim için over-engineering)

---

## E. REVIZE EDİLMİŞ ART DIRECTION

### Direction C — *The Field Dossier* — DOĞRU MUYDU?

**Cevap: Yön doğru. Uygulama yanlış.**

Direction C'nin DNA'sı (Source Serif 4 + IBM Plex Sans + brass patine + warm paper + mat ink + no italic em accent + honest empty state + chronological list + true marginalia) **prensiplerin kendisi sağlam**. NYT Magazine + Aperture + Marshall + Granta hepsi bu prensiplere yakın çalışıyor. Yön doğru.

**Sorun:** Prensipleri sayfa kompozisyonuna geçirirken **layer'lar atlandı**. Sayfalar "az" yapıldı ama "az ama doğru"nun "doğru" kısmı eksik.

### Revize: **Direction C+ — *The Photographic Field Dossier***

Aynı tipografi + renk + grain — **PLUS**:

| Önceki | Revize |
|---|---|
| Image opsiyonel | **Image zorunlu birinci sınıf öğe** (anasayfa lead, about masthead, show hero — hepsinde) |
| Cover-skeleton-as-hero (foto yoksa devasa blok) | Cover yoksa **hiç gösterme** (block yok); foto varsa **caption + credit zorunlu** |
| 1 hero + N uniform secondary | 3-tier hierarchy: lead (image-dominant) + secondary (image-text balance) + tertiary (text-only mono dateline) |
| Standfirst süs | Standfirst = **tek cümle iddia** (Marshall pattern) |
| Display H1 italik em accent (eski kalıntı) | **Sıfır italik em accent başlıkta** — view-by-view sökme |
| Ad-hoc grid her sayfada | **Strict 12-col grid** (`--page-grid` utility), grid breaks editoryal sinyal |
| About: italik soyad + tek-rol | About: **portre 4 col + 4-katman kimlik + AA banner + akademik thesis kart** |
| Contact: fake Signal/PGP, slogan başlık | Contact: **sadece gerçek kanallar** (e-posta + isteğe bağlı signal.me + isteğe bağlı PGP), slogan H1 yok |
| End-of-article: prev/next + 3 related | **End-of-article 3 bant**: yazar mini-bio + ilgili dosya + newsletter/kanal |
| Footer 3-col jenerik | Footer: identity + format-bazlı nav (Dispatches / About / Contact) + colophon (build date) |

### En önemli karakter cümlesi (revize)

> "Burada bir foto muhabirin masası var. Önce gördüğü, sonra yazdığı. Görmediğini iddia etmiyor. Tasarım sahneye çıkmıyor."

---

## F. SAHNE SAHNE YENİDEN KURULUM PLANI

Komponent değil **sahne**. Her sayfa = scene sequence + dominant element + secondary tension + visual anchor + negative space strategy.

### F.1 — Anasayfa = "Editor's Desk, Photographic"

```
SCENE 1 — DATELINE STRIP (height 40px, full-bleed hairline border)
  Bugün · 19 Nisan 2026 · İSTANBUL                         AA Uluslararası
  (factual marker: today + base + affiliation; not decoration)

SCENE 2 — LEAD BLOCK (above fold, ~85% of viewport height)
  Grid: 12-col strict
  Lead = 8/12 cols (left)
    KICKER:    "Son dispatch — Kahire" (mono caps, brass accent ALLOWED here as it's editorial label)
    HEADLINE:  Display 700 6xl-7xl (clamp), 2 lines max, text-wrap balance
    STANDFIRST: italic body lead, 1 sentence, claim-bearing, max 38ch
    DATELINE:  KONUM · TARIH · TÜR · OKUMA, mono caps body color
    LINK:      headline itself is link; no separate "Devamını oku"
  Image rail = 4/12 cols (right)
    Hero photo of lead dispatch, 4:5 aspect (Magnum-style portrait orientation)
    Caption block below: LOCATION — sentence — Photographer/Agency

SCENE 3 — SECTION RULE + KICKER STRIP (height ~8rem)
  Hairline border-top + 2-col: "MASADA" eyebrow LEFT + "Tüm arşiv →" link RIGHT

SCENE 4 — SECONDARY DISPATCH STRIP (3-up grid, image-top)
  3 dispatches as cards: thumbnail 16:9 (real photo, contained, ~280px wide)
  Card content: kicker (kind) + headline (display 600 lg) + dateline mono
  No standfirst, no excerpt — image carries it

SCENE 5 — TERTIARY LIST (text-only, hairline divided)
  Remaining 3-4 dispatches as writing-row pattern (Session 3 already correct)
  No image, dateline left, kind+headline right, lede 1 line

SCENE 6 — BYLINES STRIP (full-bleed, single line)
  YAYIMLANDIĞI YERLER · 8 publication mono caps (Session 3 correct, keep)

SCENE 7 — FOOTER (compact, 3-col, real-only)
```

**Dominant element:** SCENE 2 lead photo — the photograph carries authority.
**Secondary tension:** SCENE 4 secondary strip vs SCENE 5 tertiary list — image vs text-only contrast.
**Visual anchor:** lead photo, 4:5 portrait, sticky in mind.
**Negative space:** generous around SCENE 2 (above + below), tight inside SCENE 5 list.
**Mobile collapse:** SCENE 2 stacks (image first 16:9, then text); SCENE 4 horizontal scroll OR 1-up; SCENE 5 unchanged.
**First screen:** SCENE 1 + SCENE 2 (image + headline + standfirst visible).
**Scrolling cadence:** silence → density → silence → density.

### F.2 — `/yazilar` (writing index) = "The Archive"

```
SCENE 1 — INDEX HEADER
  ARŞİV kicker + display H1 "Tüm dispatches" + intro
  Real metric: "47 dispatch · 4 tür · 2014–2026" (NYT-style)

SCENE 2 — FILTER + LIST 2-col
  Filter rail (200px, sticky lg+): TÜR / BÖLGE / YIL (year-based archive will come Faz 3)
  List (rest):
    Per year heading (display 700 3xl, 2px ink border-bottom)
    Per dispatch row:
      80px thumbnail (real photo, contained 1:1) | dateline mono | kicker+headline+lede

SCENE 3 — FOOTER
```

**Dominant element:** uniform writing-row cadence (Aperture pattern: peers look like peers).
**Secondary tension:** thumbnail (image) vs text — every row balanced.
**Visual anchor:** year heading.
**Negative space:** between year groups (4rem); inside rows tight (1.25rem).
**Mobile:** filter collapses to top horizontal pills above list; thumbnail becomes 60×60 left of text stack.

### F.3 — `/yazilar/{slug}` = "The Dispatch"

```
SCENE 1 — TOP DATELINE BAR (sticky? no, in-flow above headline)
  ← Yazılar (link-quiet) | KONUM · TARIH · TÜR · OKUMA mono caps

SCENE 2 — HEADLINE BLOCK
  KICKER: series/dossier label (small caps, brass-allowed editorial label)
  HEADLINE: display 700 6xl-7xl, max 22ch
  STANDFIRST: italic, 1 sentence claim, max 48ch, color ink (not muted)
  BYLINE BAR: "Foto + Yazı: Ozan Efeoğlu" (Patrick Radden Keefe pattern: byline = part of editorial weight)

SCENE 3 — COVER (only if photo exists)
  16:9 contained, full container width
  Figcaption ZORUNLU: LOCATION — descriptive sentence — Photographer (italic small caps)
  If no photo: SCENE 3 is OMITTED entirely (no skeleton, no block)

SCENE 4 — BODY + MARGINALIA
  3-col grid: marginalia rail 180px (sticky) | body 62ch | EMPTY 1fr
  Marginalia: yayım / konum / tür / okuma / yayın / methodology link (if exists)
  Body: prose-article pattern, lede paragraph (no drop cap)
  Inline images: contained column-width, figcaption + credit zorunlu

SCENE 5 — PULL QUOTE (if exists in body, mid-article)
  Italic Source Serif Display, indented from body, 2px ink left bar (not full-width break)

SCENE 6 — END-OF-ARTICLE (3 bands)
  Band 1: AUTHOR MINI-BIO (1:1 portrait 80px + name + 2-line role + email link)
  Band 2: RELATED DISPATCHES (2-up text-row pattern, same series or region)
  Band 3: CONTACT INVITATION (1 line: "Bu dispatch hakkında bilgi için: [email link]")

SCENE 7 — PREV / NEXT (compact, hairline, no display H2)

SCENE 8 — FOOTER
```

**Dominant element:** SCENE 2 headline block + SCENE 3 cover (if exists).
**Secondary tension:** marginalia rail vs body — discipline vs prose.
**Visual anchor:** SCENE 3 photo (when present); when absent, SCENE 2 headline carries.
**Negative space:** between SCENE 2 and SCENE 4 (after standfirst); inside body tight.
**Mobile:** marginalia collapses to inline strip below standfirst; body full width.
**First screen:** SCENE 1 + SCENE 2 (dateline + kicker + headline + standfirst + byline).

### F.4 — `/hakkimda` = "The Person File"

```
SCENE 1 — MASTHEAD (3-col asymmetric)
  Left 4 cols: PORTRAIT (3:4 portrait crop, real photo, contained, photographer credit caption below)
  Right 8 cols:
    KICKER: "Hakkında — kısa biyografi"
    HEADLINE: "Ozan Efeoğlu" (display 700, no italic, no accent — name as fact)
    SUBHEAD: "foto muhabir | yayıncı | akademisyen" (di Giovanni pipe pattern, mono small caps)
    AFFILIATION BANNER: "Anadolu Ajansı, Uluslararası Haber Merkezi · İstanbul"
    THIRD-PARTY QUOTE (if available): pull quote from a publication or editor

SCENE 2 — KÜNYE + BIO (2-col)
  Left 4 cols: KÜNYE (sticky) — başlangıç / üs / kurum / saha / dil / tempo / basın / güvenlik
  Right 8 cols: BIO PROSE (3-4 paragraph nesir from CV — NOT bullets)

SCENE 3 — WORK AREAS (4-col strip, hairline-top)
  SAHA | GÖRSEL | ARAŞTIRMA | YAYINCILIK
  Each: workarea-label + workarea-title + 3-line list

SCENE 4 — KRONOLOJİ (timeline-rail, ink dot, 8 entries 2010-2026)

SCENE 5 — METHODOLOGY (single text block, indented)
  "Nasıl çalışıyor": akreditasyon, kaynak güvenliği, etik, verifikasyon, foto editing minimum

SCENE 6 — RESEARCH / ACADEMIC (1-col strip, distinct background)
  Hasan Kalyoncu Üni. yüksek lisans tezi (drone haberciliği + visual semiotics)
  Saha pratikleri kitabı (cover thumbnail + 1-line description)

SCENE 7 — RECENT DISPATCHES (3 text-rows)

SCENE 8 — FOOTER
```

**Dominant element:** SCENE 1 portrait (real, 3:4 contained — Magnum portrait orientation).
**Secondary tension:** portrait (image) vs subhead pipe (typography).
**Visual anchor:** portrait + name.
**Negative space:** generous around SCENE 1 + SCENE 5 methodology.
**Mobile:** portrait stacks first (full-width), then headline, subhead, affiliation; künye stacks below.
**First screen:** portrait + name + 4-katman pipe + AA affiliation.

### F.5 — `/iletisim` = "Channels (real only)"

```
SCENE 1 — HEADER
  KICKER: "İletişim · doğrudan kanallar"
  HEADLINE: "Yazışma" (display 700 3xl-4xl, NO italic, NO accent, NO slogan)
  STANDFIRST: 1 sentence: "Editör, basın, kaynak temasları için aşağıdaki kanallardan biriyle yazın."

SCENE 2 — CHANNEL CARDS (1-3 cards depending on what's REAL)
  Only render channels that are config-set (not null)
  Always: editorial e-mail card (primary mark)
  Conditional: signal.me link card (if config.signal_url set)
  Conditional: PGP card (if config.pgp_fingerprint set)
  Each card: type kicker + handle (mono code) + copy icon-btn + 1-line note
  If only e-mail exists: 1 card centered, not 3-col grid pretending

SCENE 3 — FORM (1-col, contained, max 580px)
  Eyebrow "Mesaj bırak"
  Form fields: name + e-mail + subject + body + honeypot
  Submit primary button
  Note: response time (config-set: "~3 iş günü" or omit)

SCENE 4 — DISCLOSURE (sidebar or below form)
  Source-protection note: "Hassas kaynaklar Signal/PGP ile yazsın" (only if those channels exist)

SCENE 5 — FOOTER
```

**Dominant element:** SCENE 2 channel cards — but **only as many as are real**.
**Secondary tension:** none needed; this page is utility.
**Visual anchor:** primary e-mail card.
**Negative space:** generous around form (form is a serious commitment, not a quick contact).
**Mobile:** cards stack 1-up; form full-width.
**First screen:** kicker + headline + 1 channel card.

### F.6 — Header / Nav

```
DESKTOP (md+):
  [WORDMARK + İSTANBUL micro]   [Yazılar  Hakkında  İletişim]   [theme]  [Masa@auth]
  No hamburger md+, single row, hairline border-bottom on scroll.

MOBILE:
  [WORDMARK + micro]                                               [theme]  [≡]
  Drawer = full overlay self-contained (Session 3.5 already correct).
```

**Dominant element:** wordmark.
**Visual anchor:** wordmark + İSTANBUL.
**Negative space:** between wordmark and nav.

### F.7 — Footer

```
[IDENTITY 5 cols]               [DISPATCHES nav 3 cols]   [REAL CONTACT 4 cols]
Wordmark + İstanbul             Dispatches               press@... (mono link)
1-line description              Hakkında                 Tüm güvenli kanallar →
                                İletişim                 (Signal/PGP only if real)
                                RSS                      (Sosyal only if real)

──── hairline ──────────────────────────────────────────────────────────────
© 2026 Ozan Efeoğlu                                              İSTANBUL
```

---

## G. NE ÇÖPe ATILIYOR

| Karar / kalıntı | Statü |
|---|---|
| Cover-skeleton 16:9 hero blok | **ÇÖP** — foto yoksa hiç gösterme |
| Tüm display H1'lerde italik em accent renk (about, contact, 404) | **ÇÖP** — view-by-view sökme |
| Contact fake Signal handle (`@ozanefeoglu.42`) | **ÇÖP** — config null default, render gizle |
| Contact fake PGP (`secure@ozanefeoglu.com` + fake fingerprint) | **ÇÖP** — config null default, render gizle |
| Anasayfa lead `1fr_320px` grid (rail boş olduğunda boş yarı) | **ÇÖP** — image rail 4/12 zorunlu |
| `--container-wide` + clamp gutter dışında ad-hoc grid | **ÇÖP** — strict 12-col `--page-grid` utility |
| Standfirst color paper-600 muted | **REVİZE** — color ink full, weight 400 italic |
| About display H1 "Ozan / Efeoğlu" italik soyad | **ÇÖP** — düz tek satır "Ozan Efeoğlu" |
| About "saha muhabiri ve yazar" framing | **ÇÖP** — "foto muhabir | yayıncı | akademisyen" pipe |
| About awards bölümü (özgeçmişte yok) | **ÇÖP** — kalkıyor (zaten Session 4'e bekliyordu) |
| Writing index pagination kavramı | **DOĞRULANDI ÇÖP** — Session 3'te zaten kalktı |
| Hero "Gittim baktım yazdım" slogan kalıntısı | **DOĞRULANDI ÇÖP** — Session 3'te zaten kalktı |
| Footer "Sosyal" sütunu sahte linkler | **DOĞRULANDI ÇÖP** — Session 2'de zaten kalktı |
| Top date strip context'siz | **REVİZE** — eyebrow "Bugün" + tarih + yer + AA |
| Anasayfa "Devamını oku →" ayrı link | **ÇÖP** — headline kendisi link yeter |
| `cover_hue_a / cover_hue_b` Writing model alanları | **DEAD FIELD** — drop migration Session 5'te opsiyonel |
| `lede-open` utility CSS'te | **KORUNUR** — sahibi içerikte kullanırsa stilize eder |
| Drawer link ".drawer-link" pattern | **KORUNUR** — Session 3.5 doğru |
| Buton ailesi (.btn / .btn--secondary / .icon-btn / .link-quiet) | **KORUNUR** — Session 3.5 doğru |
| Source Serif 4 + IBM Plex Sans + Mono + Fontsource | **KORUNUR** |
| Brass-600/400 accent | **KORUNUR** ama kullanım disipline edilir (kicker editoriyal label OK; başlık asla) |

---

## H. CSS KALİTESİ — user'ın "kötü" eleştirisi

User dedi: "CSS KALİTESİDE ÇOK KÖTÜ DÜZELTİLMELİ". Mevcut `app.css`:
- 1183 satır
- Token / @utility / komponent karışık layered
- 12-col grid system **YOK** — sadece `.page-grid` 4-col template
- `--gutter` token yok (her yerde clamp inline)
- Komponent file'ları ayrılmamış (hepsi tek dosyada)
- Backward-compat `.btn--ghost` + `.btn--accent` admin alias bloğu yer kaplıyor
- `.writing-card` legacy tutuluyor ama public'te kullanılmıyor

**CSS rebuild önerisi:**
1. **Strict 12-col grid system** (`--col`, `--gutter`, `.grid-12`, `.col-span-N`, `.col-start-N`)
2. **Token / base / utilities / components / pages** — 5 ayrı `@layer` ile organize
3. `.writing-card` **kaldırılır** — hiç kullanılmıyor (Session 3'te `.writing-row` ile değişti)
4. Backward-compat alias **admin'i de geçirerek kaldır** (Session 5+ planı; ama bu emergency rebuild içinde admin'i de geçir)
5. **Komponentleri partial CSS dosyalarına ayır** — Vite import-chain ile: `@import "./components/_buttons.css"` vb. CSS modüler okunur
6. `--space-*` editorial rhythm token'ları zenginleştir (`--space-row`, `--space-section`, `--space-page`)
7. Cover-skeleton-as-hero kuralları **silinir** (yapı çıkıyor)
8. `.timeline-rail`, `.workarea`, `.portrait-frame` `.marginalia-rail` `.year-label` — about/show patternleri kalır

---

## I. DEĞİŞEN ÖNCELİKLER

| Eski roadmap önceliği (STATE_AND_ROADMAP v0.3) | Yeni öncelik |
|---|---|
| Faz 2C+: RSS / sitemap / JSON-LD | **ERTELEDİ** — once page composition rebuild |
| Faz 3: Search UI / tags / breadcrumbs / static pages | **ERTELEDİ** |
| Faz 4: Admin gelişmiş | **ERTELEDİ** |
| Session 4: about + contact + portrait + page seeder + CV PDF | **YENİDEN PLANLANDI** — bu acil rebuild içine entegre edildi |
| Yeni #1: **Page composition rebuild** (homepage + show + about + contact + index + grid system + CSS rebuild) | **EMERGENCY — ŞIMDI** |
| Yeni #2: **Photo content** (Unsplash placeholder + sahibinin gerçek fotoları geldiğinde swap) | EMERGENCY içinde |
| Yeni #3: PageSeeder yeniden (about 4-katman, contact gerçek-only, methodology) | EMERGENCY içinde |
| Yeni #4: 12-col grid + CSS modular rebuild | EMERGENCY içinde |
| Sonra: Faz 2C+, 3, 4, 5, 6, 7, 8, 9 (orijinal roadmap, ama Field Dossier v3 üstüne) | sonra |

---

## J. UYGULAMA SIRASI (onaydan sonra)

5 paralel iş grubuna ayrıldı (10 agent ile değil ama 5 paralel atomic batch ile, çünkü dosyalar birbirine bağımlı):

### Batch 1 — Foundation (CSS rebuild + grid system)
- `resources/css/app.css` modular split: tokens / base / utilities / components / pages
- 12-col grid utility (`.grid-12`, `.col-span-N`, `--gutter`, `--col`)
- Cover-skeleton-as-hero kuralları sil
- Backward-compat admin alias geçici tutulur (admin Session 5'te geçirir)

### Batch 2 — Image foundation (placeholder content + caption discipline)
- `database/seeders/WritingSeeder` — gerçek Unsplash placeholder photos (war/peace journalism stock; sahibi sonra swap eder) + photographer credit (placeholder "Foto: Ozan Efeoğlu" or actual Unsplash credit)
- `Writing` model `coverCaption()` accessor (location · date · credit format)
- `_writing-row` partial — 80x60 thumbnail eklenir (foto var ise)
- `_writing-card` legacy SİLİNİR

### Batch 3 — Page rebuild (5 view komple yeniden)
- `landing.blade.php` — F.1 scene sequence
- `writing/index.blade.php` — F.2 scene sequence (thumbnail eklenmiş)
- `writing/show.blade.php` — F.3 scene sequence (cover-as-block kalktı, end-of-article 3-bant)
- `pages/about.blade.php` — F.4 scene sequence (portrait + 4-katman + AA banner + akademik kart)
- `pages/contact.blade.php` — F.5 scene sequence (fake çıkar, sadece gerçek render)

### Batch 4 — Data layer (PageSeeder + portrait wiring)
- `PageSeeder` rewrite: about 4-katman + workareas + methodology + akademik; contact `null` Signal/PGP default
- `config/site.php` — Signal/PGP/portrait keys (Session 2'de var, kontrol)
- Portrait asset placeholder: `public/img/portrait-placeholder.svg` honest "Portre yüklenmedi" — about masthead'de fallback
- Spatie Media `Setting` model'e portrait collection (ya da basit `config('site.portrait_url')`)

### Batch 5 — Misc cleanup
- 404 + 500 italik em accent kaldır
- Header `Masa` `@auth` doğrula; mobile drawer Masa link aynı
- Footer Sosyal column gerçekten config-driven test
- Build + test + smoke + screenshots ile karşılaştırma

**Toplam:** 5 oturum içinde tamamlanabilir; ben paralelden hangisi mantıklıysa onları paralel yapabilirim (örneğin Batch 1 + Batch 4 paralel; Batch 2 + Batch 3 paralel).

---

## K. ONAY GEREKLİ NOKTALAR

Sahibinden net "evet" beklediğim 5 karar:

1. **Cover-skeleton-as-hero pattern'i çöpe atıyorum.** Foto yoksa hiç gösterilmez. Onay?
2. **Anasayfa lead'in YANINDA gerçek fotoğraf zorunlu.** Şimdilik Unsplash placeholder, sahibi gerçek fotoyu Spatie Media'dan değiştirir. Onay?
3. **About masthead 3:4 portrait + 4-katman pipe + AA banner.** Portre placeholder olarak honest skeleton, sahibi yükleyince gerçek. Onay?
4. **Contact: sadece e-posta render edilir; Signal/PGP config null default.** Gerçek değer geldiğinde otomatik açılır. "Üç güvenli kanal" sloganı kalkar; başlık "Yazışma" olur. Onay?
5. **CSS rebuild — strict 12-col grid + modular split.** Mevcut app.css 5 partial'a ayrılır; admin geçici alias kalır (Session 5'te modernize). Onay?

---

## L. BAŞARI ÖLÇÜTÜ

Rebuild bittiğinde site şunu hissettirsin:

- "Bu tasarım moda değil, karakter."
- "Bu düzen şık değil, ikna edici."
- "Bu site yapılmış değil, oluşmuş."
- "Bu adamın işi ve dünyası burada gerçekten var."

**Görsel test (uygulama sonrası):**
- Anasayfa fold = headline + standfirst + foto + dateline + 1 secondary. Hiç boş alan yok.
- Show sayfa = headline + standfirst + (foto varsa caption) + body + author + ilgili. Cover yoksa cover yok.
- About = portre + 4-katman pipe + AA banner + 4 work area + chronology + methodology + akademik kart.
- Contact = sadece gerçek olan render. Fake yok. Slogan yok.
- Index = year-grouped chronological + 80px thumbnail (foto var ise).

---

**Doküman sonu. Onayını bekliyorum: 5 onay sorusuna "evet" gelirse 5 batch'e başlıyorum.**
