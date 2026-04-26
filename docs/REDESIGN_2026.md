# Yeniden Tasarım — Field Dossier v2

> **Tarih:** 2026-04-19 · **Sürüm:** v0.1 (yön kararı)
> **Yazar:** Otonom inşa — sahip onayı sonrası uygulamaya geçer.
> **Tetikleyici:** Sahibinin yeni brief'i + portre + ayrıntılı özgeçmiş.
> **Statü:** ADR-016 ile birlikte okunmalı. v0.3 STATE_AND_ROADMAP.md'yi süpersedes ETMEZ; tasarım katmanını yeniden tarif eder.

---

## Önsöz — bu dokümanın amacı

Mevcut site teknik olarak çalışıyor (Faz 0–2C kapalı, 112 test geçer). Sorun **tasarım dilinin profile uymaması**: özgeçmiş ve portre geldikten sonra ortaya çıkan kişilik, sitedeki mevcut "literary blog" estetiğine sığmıyor. Bu doküman:

1. Mevcut tasarımı acımasız parçalar (Aşama 1).
2. Özgeçmiş + portreden çıkan **dört katmanlı kimliği** tanımlar (Aşama 2).
3. Üç yaratıcı yön önerir (Aşama 3).
4. Bir yön seçer ve gerekçelendirir (Aşama 4).
5. Seçilen yön için **design system v2** kurar (Aşama 5).
6. Sayfa-sayfa restructure planı verir (Aşama 6).
7. Uygulama sırası ve dosya listesi (Aşama 7).
8. Self-review checklist (Aşama 9; uygulama sonrası dönüp soracağım sorular).

Aşama 8 (kod uygulaması) bu doküman dışında — bir sonraki turda dosya dosya yapılır.

---

## AŞAMA 1 — Brutal creative audit

Aşağıdaki 21 madde mevcut sitenin **gerçek hâlinin** eleştirisidir; "olması gereken" değil "olan" anlatılır. Hiçbir şey "yapılmış olduğu için" korunmuyor.

### 1.1 — Tipografi sorunları

**M-1. Italic em accent enflasyonu.**
ADR-014 "italik em vurgu **3 yerle sınırlı**" diyor. Pratikte landing'de "yazdım", "seçme", "kanallar"; about'ta "Efeoğlu", "kilometre taşları", "notları", "ne yazıyor"; contact'ta "güvenli", "buradan"; show'da "damarda" — **toplam 9+ italic ember vurgu**. Bu artık vurgu değil, dekorasyon. Bir saha gazetecisinin sesi değil, marka direktörünün sesi. Brief'in "Bu site kendisini sahnelemez" cümlesinin doğrudan ihlali.

**M-2. Fraunces SOFT=20 + opsz=144 her başlıkta.**
Fraunces zarif bir display font ama bu ayarda ("yumuşak optik 144 punto") sürekli yumuşak/şefkatli/dergisel hissettiriyor. Saha gazetecisinin başlığı "ben size güzel bir şey okutuyorum" değil "burada ne olduğuna bak" demeli. SOFT=0 + opsz=72 daha sert ve doğru olurdu — ama Fraunces zaten yanlış aile. Daha doğru: **GT Sectra/Source Serif 4 gibi haber kaynakları için tasarlanmış serifler.** Fraunces literary/floral tonunu hep taşıyor.

**M-3. Drop cap 4.5rem ember — magazine pattern.**
`prose-article > p:first-of-type::first-letter` kuralı her yazıyı bir Granta/N+1 makalesi gibi açıyor. Saha raporu için yanlış register: rapor "lede paragrafı" ile açılır (büyük punto, küçük caps açılış, tarihsel ağırlık), drop cap ile değil.

**M-4. Section break italik mark + hairline.**
`section-break > .mark` kuralı ortaya italik ember "§" benzeri bir işaret yerleştiriyor. Bu literary journal idiomu. Bir editör masasında section break = boşluk + en fazla "—" mono. İtalik renk vurgusu zorlama.

### 1.2 — Renk ve atmosfer sorunları

**M-5. Ember orange (#9a3412 / #fb923c) accent yanlış sıcaklıkta.**
Brief açık: "Yanık amber, mineral bakır, yıpranmış zeytin, mat lacivert, sisli petrol, küllü toprak." Ember turuncusu **alarm-adjacent**: dikkatçek, alıntı, hover... Hep göze giriyor. Hâlbuki istenen: iz bırakan, sessiz, oksitlenmiş. Bakır turuncusu değil, bakır oksiti (yeşilimsi-kahverengi) ya da brass patine.

**M-6. Paper grain 0.022 — tek atmosferik unsur, doğru.**
Tek tutarlı kalsın. Dark mode'da 0.045 yanlış değil; korunabilir.

**M-7. Cover-placeholder HSL gradient (atmospheric "fotoğraf" yerine soyutlama).**
Saha gazetecisi için **felaket karar**. Onun kimliği görüntüde. Placeholder olarak HSL bulanıklığı koymak "ben görüntü yokluğunu güzelleştiriyorum" demek. Daha dürüst skeleton: charcoal/paper-200 düz blok + mono caps "FOTOĞRAF EKLENMEDİ · {LOCATION} · {DATE}". Eksik bir muhabir karta verir, yumuşatmaz.

### 1.3 — Bilgi mimarisi sorunları

**M-8. "Yazılar" tek kategori — foto muhabir kimliği yok.**
Özgeçmiş: "Foto Muhabiri ve Yayıncı". Site: "Yazılar". Görüntü, dosya, fotoğraf serisi, drone serisi diye **tek bir kategori yok**. Bu kişinin işinin %50'si site'de görünmüyor.

**M-9. Akademik katman görünmez.**
Yüksek lisans tezi (drone haberciliği), saha pratikleri kitabı, görsel göstergebilim ilgisi — hiçbiri sitenin haritasında yok. "Hakkımda" sayfasında kısa cümlelerle bahsedilebilir ama **"Araştırma" / "Notlar" / "Yayınlar" gibi ayrı bir alan yok**. Editoryal/akademik kimlik tek bir blog post sınıfına sıkışmış.

**M-10. "Saha yazısı / röportaj / deneme / not" — tek eksenli sınıflandırma.**
Bu sınıflandırma türü ayırıyor ama **bağlam ayırmıyor**. Editör masasında dosyalar şu eksenlerde durur: tür × konu (bölge/tema) × yıl × yayın × format (text/photo/data). Şu an sadece tür var.

**M-11. Footer 4-kolon — sahte sosyal linkler.**
"Sosyal: X / Instagram / LinkedIn" — `href="https://x.com/"` (boş). PGP "#". Footer "burada bir şey var" gibi davranıyor ama yok. Bu **editoryal saygısızlık**: ya gerçek link, ya hiç.

**M-12. Header logo dot.**
2-pixel ember dot + serif wordmark — generic indie blog/SaaS logosu. Bir saha gazetecisi için anlamsız. Ya kalksın (sadece adın mono caps versiyonu), ya gerçekten anlam taşıyan bir nişan olsun (örn. küçük "İstanbul" konum işareti, ya da hiç).

### 1.4 — Anlatı / kopya sorunları

**M-13. Hero "Gittim, baktım, yazdım." — slogan ego.**
Caesar parodisi (veni vidi vici). Şair/copywriter sloganı. Bir editör/muhabir bunu söylemez; bir reklamcı söyler. Brief: "Kendisini sahnelemez, ikna etmeye çalışmaz, poz vermez." Hero burada **poz veriyor**.

**M-14. Hero altı tagline "...akreditasyonlu."**
"akreditasyonlu" doğru bilgi ama dilin sonuna sıkıştırılmış. Bu kelime başlığa, sayfa başına ait — sahibinin kim olduğunu söyler.

**M-15. CTA copy: "Daha fazlası / Uzun özgeçmiş, güvenli kanallar."**
Standard SaaS landing kapanış pattern'i. Editorial site için **kapanış genelde kapanmaz**: bir sonraki dispatch'e, en son dosyaya, ya da pazartesi notuna açılır. Buton değil, link.

**M-16. About masthead "Ozan / Efeoğlu" — italik ember soyad.**
Soyadına vurgu ego pattern. Aynı dosyada özgeçmiş "Merhaba, ben Ozan Efeoğlu" diye başlıyor — bu doğal ses. Site bunu büyük italik dramatize ederek ses tonunu bozuyor.

### 1.5 — Görsel hayata geçiş sorunları

**M-17. Portre yok.**
Brief diyor: "Fotoğrafı en çok About sayfasında ve kontrollü biçimde ana sayfada kullan." Şu an **sıfır kullanım**. About'ta bile yok. Bu en büyük eksik: kişinin yüzü olmayan bir muhabir sitesi.

**M-18. Görsel arşiv yok.**
Bu kişinin işi görüntü. Saha fotoğrafı, drone görüntüsü, vesika fotoğrafı, basın kartı, akreditasyon belgesi — hiçbiri için bir alan yok. Tüm "cover" alanları soyut HSL gradientle dolduruluyor.

**M-19. Caption kültürü yok.**
Brief: "Caption sistemi güçlü olsun. Görsel ile metin aynı aileye ait hissettirsin." Şu an cover'larda sadece "{LOCATION} · {YEAR-MM}" mono pill var. Caption (fotoğraf altyazısı: ne, ne zaman, kim, neden) yok. Foto muhabir sitesi için bu **profesyonel ihmal**.

### 1.6 — Etkileşim ve detay sorunları

**M-20. writing-card hover translate-Y -3px + img scale 1.03.**
Tipik product UI hover. Kart kendini "satıyor". Editorial bir liste için çok rahatsız. Hover'da **sadece** underline-thickness değişimi + dateline rengi değişimi yeterli.

**M-21. Reveal animation (.reveal-1..6) + scroll-reveal animation.**
Sayfa açılışında staggered fade-up. Modern, evet — ama **fazla kibar**. Editorial bir front page **anında** açılır, "şimdi sana okuyacağım" demez, "buradayım, oku" der. Reveal'lar 99% sahnelenmiş, 1% bilgisel.

### 1.7 — Audit özeti

| Eksen | Durum | Öncelik |
|---|---|---|
| Tipografi (italik em + Fraunces SOFT + drop cap + section mark) | Yanlış aile + zorlama dekor | YÜKSEK |
| Renk (ember orange) | Yanlış sıcaklık | YÜKSEK |
| Cover placeholder (HSL) | Profile karşıt | YÜKSEK |
| IA (foto/akademi/yazı tek eksen) | Eksik | YÜKSEK |
| Hero copy + masthead italik | Ego/sahnelenme | YÜKSEK |
| Portre yok | Brief ihlali | KRİTİK |
| Sahte footer linkleri | Editoryal saygısızlık | ORTA |
| Hover/reveal animasyonları | Fazla kibar | ORTA |
| Logo dot | Generic | DÜŞÜK |
| Caption kültürü | Yok | ORTA |

---

## AŞAMA 2 — Yeni kimlik (özgeçmiş + portre okuması)

### 2.1 — Özgeçmiş kim olduğunu söylüyor?

Özgeçmişten çıkan dört katman (sitedeki konumlandırmayı bunlar belirler):

| Katman | İçerik | Site karşılığı |
|---|---|---|
| **1. Saha (birincil)** | Anadolu Ajansı Savaş Muhabirliği eğitimi → Hatay → Zeytin Dalı sınır hattı → Adana Bölge (Adana/Hatay/Mersin/Osmaniye) → İstanbul AA Uluslararası Haber Merkezi | Sahanın sesi: dispatch'ler, fotoğraf serileri, alan notları |
| **2. Görsel (ikincil)** | İstanbul Aydın Üni. Fotoğraf+Kameramanlık · freelance ürün/reklam fotoğrafçılığı · şu an "Foto Muhabiri ve Yayıncı" | Görüntü öncelikli sayfa: foto seri, drone arşivi |
| **3. Akademik (üçüncül)** | Kocaeli Üni. Gazetecilik · Hasan Kalyoncu Üni. yüksek lisans (drone haberciliği + görsel göstergebilim) · saha pratikleri kitabı | Araştırma alanı: tez özetleri, makaleler, kitap |
| **4. Teknik (gizli omurga)** | Yalova Aksa Anadolu Teknik Lisesi (yazılım) · Uludağ Üni. bilgisayar programcılığı | Görünmez ama ses tonunu belirler: clean info architecture, transparency (colophon), keyboard shortcut'lar, performance hassasiyeti |

**Sonuç:** "Savaş muhabiri" çerçevesi yetmez — yanlış pozisyonlandırır. Doğru çerçeve: **Saha tecrübesi olan görsel muhabir + yayıncı + araştırmacı**. Site bu sırayı taşımalı.

### 2.2 — Portre okuması

Sahibinin portresine bakarsak (gözlemler, dramatize değil betimleme):

- Sade, beyaz tişört, doğal arka plan (nötr açık duvar/perde)
- Yüz hafif sağa dönük, doğrudan göz teması, sakin ifade
- Doğal ışık (sol-üst kaynak, hafif gölge sağ yanakta)
- Sakal kontrollü, saç kısa
- Soğuk dramatize, askeri ya da "savaş muhabiri kıyafeti" vurgusu **yok** — bu önemli, brief de bunu istiyor

**Crop stratejisi (en doğru kullanım):**

| Crop | Boyut | Kullanım |
|---|---|---|
| Square (1:1) | 600×600 | Wordmark yanı / about masthead küçük (avatar değil, vesika seviyesi) |
| Vertical (3:4) | 600×800 | About sayfası primary — sticky sol kolon |
| Horizontal (3:2) | 900×600 | Landing'de tek kullanım (kontrollü, küçük) — "editor's desk" pattern içinde |
| Tight head (1:1, head only) | 400×400 | Footer kolofon küçük damga (opsiyonel) |

**Tedavi (foto post-prod):**
- Doğal ışık ve renk **korunur** (filtre/duotone yok)
- Hafif kontrast +5%, hafif warm shift +2 (paper bg ile uyum için)
- Beyaz tişört fazlalığı **3:4 crop** ile azaltılır (göz hizası yukarı çekilir)
- Dark mode'da: aynı portre kullanılır, mat charcoal arkaplan ile karşıtlık doğal kalır (siyah-beyaz konversiyona gerek yok)

**Asla:**
- Filtre, duotone, dramatic vignette
- Kırmızı/siyah karışım, askeri tutum
- "Hero portre full-bleed" pattern (LinkedIn influencer)
- Aynı portrenin 4+ yerde tekrarı

### 2.3 — Karakter cümlesi (ses tonu kuzey yıldızı)

> Saha tecrübesi olan, görüntüyü yazıya katmayı bilen, kaynaklarını koruyan, akademik soruları olan İstanbul tabanlı bir foto muhabiri. Neye baktığını anlatır, görmediğini iddia etmez.

Bu cümleyi her tasarım kararında okuyacağım. Kelimeler "anlatır", "korur", "anlatır", "iddia etmez" — sahiplenmez, sahnelenmez, çevreler.

---

## AŞAMA 3 — Üç yaratıcı yön

Üç yön de aynı 4-katmanlı kimliği taşır. Tek fark: **görsel-typografik tavır.**

### Direction A — *Wire Service*

> "Bir haber ajansının ön sayfası gibi."

| Boyut | İçerik |
|---|---|
| **Duygu** | Clinical, anonim ama profesyonel. Reuters, AP, AFP wire desk vibinde. |
| **Tipografi** | Display: Charter / Tiempos Headline (ya da open-source Source Serif 4 BoldSubhead). Body: IBM Plex Sans (kalır). Mono dateline (kalır). Daha küçük display ölçek (max 4xl). |
| **Renk** | Mat seal-grey (#26282b) ink, off-white #f6f5f1 paper, accent: muted slate-blue #3b4a5b (tek). Hiç turuncu yok. |
| **Grid** | 12-col katı grid. Asimetri yok. Her dispatch wire-style: HEADLINE / DATELINE / LEAD / continuation indicator. |
| **Komponent tavrı** | Hairline rule her şeyin etrafında. Hiç border-radius (kare köşeler). Hover'da sadece underline. |
| **Art direction** | Foto siyah-beyaz veya doğal ama "filed photo" caption disiplini ile (ALL CAPS LOCATION · DATE · AGENCY). |
| **Riskler** | Çok cold. "Bu kişinin kişisel sitesi" hissini öldürür. Anonim. |
| **Neden seçilebilir** | Foto muhabir mantığına en yakın, "burada belge var" hissi en güçlü, brief'in "documentary restraint" maddesi tam. |
| **Neden seçilmemeli** | Brief: "kişisel ama gösterişsiz." Wire service "kişisel" değil. |

### Direction B — *Field Notebook*

> "Saha defteri — yıpranmış deri kapak, çizgili kağıt, kenarda not."

| Boyut | İçerik |
|---|---|
| **Duygu** | Samimi, dokunsal, kişisel. El yazısı + yazıcı karması. |
| **Tipografi** | Display: IBM Plex Mono Bold (büyük punto, monospaced başlık → "yazıcı çıktısı"). Body: serif (Source Serif 4 var) — okunabilirlik için. Mono dateline kalır. |
| **Renk** | Cream paper #efe9d8 (sararmış kağıt) bg, sepia ink #2c2418, accent: gun-metal slate #3a3f44 (tek). |
| **Grid** | Notebook ruled-line grid (faint horizontal lines bg-attachment fixed gibi); hafif yıpranmış sağ kenar (subtle SVG mask). |
| **Komponent tavrı** | Margin-note sayısı (1, 2, 3 kenarda elle yazılmış gibi mono italik). Section-break = handwritten "—" gibi mono em-dash. |
| **Art direction** | Foto: polaroid frame benzeri kalın alt margin + mono caption tek satır. Cover skeleton: kraft paper rengi blok + "TBA" damgası gibi. |
| **Riskler** | Skeumorphism tehlikesi ("cute" durumu). Dijital değil, kopya kâğıt görünümü. Erişilebilirlikte cream bg kontrast riski. |
| **Neden seçilebilir** | Brief'teki "notebook margins, dateline discipline" kelime-kelime karşılığı. Kişisel duygu en güçlü. |
| **Neden seçilmemeli** | Bir foto muhabiri sitesi defter rolüne girince **görüntü öncelikli olamaz**. Defter metin ister, foto sıkışır. |

### Direction C — *The Field Dossier* (sessiz dosya)

> "Sessiz arşiv — bir editörün masasında açık duran dosya."

| Boyut | İçerik |
|---|---|
| **Duygu** | Sessiz, ağır, tartılmış. Bir gazete eki + arşiv klasörü karışımı. NYT Magazine / Granta / Aperture / Topic / Magnum Foundation tonu. |
| **Tipografi** | Display: **Source Serif 4 Variable** (open-source, akademik+gazete-için-tasarlanmış). SemiBold ağırlık, opsz=72/96, normal italik. Body: IBM Plex Sans (kalır). Dateline: IBM Plex Mono (kalır). |
| **Renk** | Mat ink #1a1816 (paper-950'den biraz daha sıcak), off-white paper #f6f4ee, accent: oksitlenmiş bakır/brass patine **#7c5a3a** (kahverengi-bakır, turuncu DEĞİL). Dark mode: ink off-white, bg #15140f, accent mat brass #b8915f. Accent **çok kısıtlı** kullanılır: focus ring + bağlantı altçizgisi + tek bir aktif işaret. Hiçbir başlıkta accent renk yok. |
| **Grid** | Editorial front page: asimetrik ama RİTMİK. 12-col base; hero alanında "lead-and-rail" pattern (ana lead + sağ rail küçük dispatch'ler). |
| **Komponent tavrı** | Hairline rule + cömert whitespace + tek hat italik. Section-break = sadece boşluk + opsiyonel mono "—" hat. Hover sessiz: underline thickness 1→2px, ink rengi muted→full. |
| **Art direction** | Foto öncelikli ama "kelimeler-önce-görüntü-sonra" hiyerarşi. Cover skeleton: paper-200/charcoal blok + mono caps "FOTOĞRAF EKLENMEDİ" + dateline. Caption disiplini: 2 satır mono caps üst caption (NE/NEREDE) + 1 satır italik body altcaption (NEDEN/BAĞLAM). |
| **Riskler** | Çok ciddi → "burada renk yok" izlenimi. Sahibinin "fazla mı kuru?" hissi. Dengeyi tipografi varyasyonu (italik açılış paragrafları, küçük caps eyebrow, drop-cap-yerine-lede-paragraph) kurtarır. |
| **Neden seçilebilir** | Brief: "sessiz ama ağır, rafine ama steril değil, kişisel ama gösterişsiz, görsel olarak güçlü ama habere hizmet eden." Maddelerin **tamamı** Direction C ile karşılanır. Saha + görsel + akademi + teknik dört katmanın hepsi C'de yaşar. |
| **Neden seçilmemeli** | Sahibi "biraz daha sıcaklık ister" derse C+B karması yapılabilir (sıcak kâğıt + mono dateline + serif body) — ama o zaman kimliği bulanıklaştırır. |

### Karşılaştırma (özet)

| Eksen | A · Wire | B · Notebook | **C · Dossier** |
|---|---|---|---|
| Saha (birincil) | ★★★★★ | ★★★★☆ | ★★★★★ |
| Görsel (ikincil) | ★★★★☆ | ★★★☆☆ | ★★★★★ |
| Akademi (üçüncül) | ★★★☆☆ | ★★★★☆ | ★★★★★ |
| Teknik (gizli omurga) | ★★★★☆ | ★★☆☆☆ | ★★★★★ |
| Kişisel sıcaklık | ★★☆☆☆ | ★★★★★ | ★★★★☆ |
| Editoryal güven | ★★★★★ | ★★★☆☆ | ★★★★★ |
| 3 yıl sonra hâlâ güçlü | ★★★★☆ | ★★★☆☆ | ★★★★★ |

---

## AŞAMA 4 — Seçim ve gerekçe

### Karar: **Direction C — The Field Dossier**

Saf C; B'nin bir damlasıyla (mono dateline kültürünün gücü) ve A'nın bir damlasıyla (caption disiplini, hairline rule). Hibrit ismi **"The Field Dossier"** — "saha dosyası" / "arşiv dosyası". Kararın gerekçesi:

1. **Profile uyum**: Dört katman (saha + görsel + akademi + teknik) tek bir tasarım dilinde yaşar.
2. **Brief kelime-kelime karşılanır**: "front page of a serious international magazine", "longform field report", "archival document elegance", "documentary restraint" — hepsi Direction C'nin tanımı.
3. **Portre dengelenir**: Sıcak doğal ışıklı portre, sessiz monokrom paper üzerinde **gücünü kaybetmez** — tersine güçlenir. Renk çok olsa portre kaybolurdu.
4. **Görsel arşiv için zemin**: Gerçek fotoğraflar (saha + drone) yüklendiğinde sayfa sayfa **fotoğraf konuşur**, tasarım çekilir. Bu Direction A'da da olur ama A çok cold; B'de olmaz çünkü kraft paper estetiği fotoğrafla rekabet eder.
5. **3 yıl test**: Renk modası geçer, mono+serif+kâğıt geçmez. NYT Magazine 50 yıldır aynı omurga üstünde duruyor; Aperture 70 yıldır.
6. **Mevcut altyapı korunur**: Spatie Translatable, Tipta P 3, Spatie Media (webp variants), HTMLPurifier, ember accent KALMAZ ama font değişikliği dışında her şey yerinde — refactor cerrahi.
7. **Erişilebilirlik avantajı**: Yüksek kontrast (mat ink + off-white paper), accent renk az = renk-körü kullanıcı için bilgi taşıyıcı renge bağımlılık yok.
8. **Akademik kimlik için doğru çerçeve**: Tez/araştırma sayfaları ek tasarım gerektirmeden bu sistemde **sıfır gürültü** ile yaşar.

### Direction C'nin DNA'sı (özet)

| Karar | Eski | Yeni |
|---|---|---|
| Display font | Fraunces Variable | **Source Serif 4 Variable** (SemiBold) |
| Body font | IBM Plex Sans Variable | IBM Plex Sans Variable (korunur) |
| Mono | IBM Plex Mono | IBM Plex Mono (korunur) |
| Italic em vurgu | Başlıklarda 9+ yer | **0 yer başlıkta** — sadece body italik (alıntı, başlık) |
| Accent renk | Ember orange (#9a3412 / #fb923c) | **Brass patina (#7c5a3a / #b8915f)** |
| Drop cap | Var | **Yok** — yerine "lede paragraph" stili (büyük punto, küçük caps açılış) |
| Cover placeholder | HSL gradient | **Honest skeleton** (mono caps "FOTOĞRAF EKLENMEDİ") |
| Hero | "Gittim, baktım, yazdım." slogan | **Editor's desk**: dateline + son dispatch lead + portre küçük + 3 dispatch list |
| Logo | Ember dot + Fraunces wordmark | **Mono caps wordmark** (no dot), altında dateline microcopy "İstanbul · Foto muhabiri" |
| Section break | Italik ember mark | **Boşluk** + opsiyonel mono em-dash |
| Reveal animation | Stagger 1..6 | **Yok** — anlık açılır |
| Hover (writing-card) | translateY -3px + img scale | **Sadece** underline thickness + dateline ink değişimi |
| Footer | 4-col + sahte sosyal linkler | **2-col**: gerçek iletişim + colophon (build/version/last-updated) |
| Footer-CTA | "Daha fazlası" SaaS pattern | **Bir sonraki dispatch** linki (en son yayımlanan veya draft) |
| IA | "Yazılar" (tek kategori) | **Saha · Görüntü · İnceleme · Not** (4 kategori, görsel ayrı sınıf) |

---

## AŞAMA 5 — Design system v2 (token şeması)

### 5.1 — Tipografi tokenları

```css
/* Tipografi aileleri */
--font-display: "Source Serif 4 Variable", "Charter", "Iowan Old Style", Georgia, serif;
--font-sans:    "IBM Plex Sans Variable", "Helvetica Neue", -apple-system, "Segoe UI", system-ui, sans-serif;
--font-mono:    "IBM Plex Mono", "SF Mono", Menlo, Consolas, monospace;

/* Modular scale 1.2 (1.25'ten 1.2'ye - daha sıkı, editoryal) */
--text-xs:   0.78rem;
--text-sm:   0.875rem;
--text-base: 1rem;
--text-md:   1.125rem;
--text-lg:   1.35rem;
--text-xl:   1.62rem;
--text-2xl:  1.95rem;
--text-3xl:  2.34rem;
--text-4xl:  2.81rem;
--text-5xl:  3.37rem;
--text-6xl:  4.05rem;
--text-7xl:  4.86rem;
```

**Display davranışı (Source Serif 4):**
- Variation: `wght 600, opsz 32` (subhead) ve `wght 700, opsz 60` (display headline)
- Italik: yalnızca body içinde (alıntı, başlık), başlıklarda **kullanılmaz**
- Letter spacing: tighter (-0.02em) sadece display 4xl+

### 5.2 — Renk tokenları

```css
/* Paper neutrals (warm shift) */
--color-paper-50:  #faf8f1;  /* eski #fafaf7'den daha sarı */
--color-paper-100: #f6f4ee;  /* primary bg light */
--color-paper-200: #e9e5d9;
--color-paper-300: #d4cebf;
--color-paper-400: #a39c89;
--color-paper-500: #6b6555;
--color-paper-600: #4a4538;
--color-paper-700: #322e25;
--color-paper-800: #1f1c16;
--color-paper-900: #15140f;  /* primary ink dark */
--color-paper-950: #0a0907;

/* Brass patina accent — oksitlenmiş bakır/pirinç */
--color-brass-300: #d4ad75;
--color-brass-400: #b8915f;
--color-brass-500: #9c7549;
--color-brass-600: #7c5a3a;  /* light mode accent */
--color-brass-700: #5e4429;
--color-brass-800: #463320;

/* Semantic */
--color-bg:           var(--color-paper-100);
--color-bg-elevated:  #fffdf6;
--color-bg-muted:     var(--color-paper-200);
--color-bg-sunken:    var(--color-paper-300);
--color-ink:          #1a1816;  /* mat siyah, hafif sıcak */
--color-ink-muted:    var(--color-paper-600);
--color-ink-subtle:   var(--color-paper-500);
--color-rule:         var(--color-paper-300);
--color-rule-strong:  var(--color-paper-400);
--color-accent:       var(--color-brass-600);  /* light */
--color-accent-hover: var(--color-brass-700);
--color-accent-fg:    #faf8f1;
--color-focus:        var(--color-brass-600);

--color-success: #4a6b3a;  /* moss */
--color-warning: #8a6a2c;  /* aged amber */
--color-danger:  #883028;  /* oxidized rust */
```

Dark mode: ink → paper-100, bg → #15140f, accent → brass-400 (#b8915f).

**Accent kullanım kuralı:**
- Focus ring (zorunlu)
- `<a>` body içinde altçizgi rengi (text-decoration-color)
- Tek "primary CTA" buton background (sayfada en fazla 1)
- **Hiçbir başlık, hiçbir vurgu, hiçbir dot/badge'da accent yok.**

### 5.3 — Spacing & motion

```css
/* Editorial rhythm — nefes al, kasma */
--space-section:    clamp(4rem, 9vw, 8rem);   /* section arası */
--space-subsection: clamp(2rem, 4vw, 3.5rem); /* subsection */

/* Motion — neredeyse görünmez */
--duration-instant: 60ms;
--duration-fast:    120ms;
--duration-normal:  200ms;  /* eski 250ms */
--duration-slow:    320ms;
```

**Animasyon politikası:**
- Reveal stagger animasyonu: **YOK**.
- Hover: `transition: text-decoration-thickness, color, border-color` — `transform` ASLA.
- View transitions: korunur (Chromium default), `duration-slow`.
- `prefers-reduced-motion: reduce` kuralı zaten var, korunur.

### 5.4 — Containers & grid

```css
--container-prose:  62ch;   /* eski 65ch'ten daha sıkı */
--container-narrow: 720px;
--container-base:   1100px; /* eski 1200 */
--container-wide:   1320px; /* eski 1440 */
```

**Editorial grid (page-grid):** 12-col base. Lead-and-rail variant: `[lead 8] [rail 4]`. Rail sticky.

### 5.5 — Komponent tavrı

| Komponent | Yeni davranış |
|---|---|
| `.btn` | Border 1px ink, kare köşe (radius 2px), hover sadece bg→ink + fg→paper, **transform yok** |
| `.btn--accent` | Tek primary CTA (sayfa başına 1) — brass-600 bg + paper fg |
| `.btn--ghost` | Hairline border, hover bg-muted |
| `.input` | Border 1px rule-strong, focus border accent (1px değil 1px kalır), kare köşe |
| `.nav-link` | Underline scaleX animation **kaldırılır**; sadece color transition + aria-current'da hairline border-bottom |
| `.writing-card` | Hover: cover img scale **YOK**; sadece title color full-ink; dateline color full-ink |
| `.cover-placeholder` (rename → `.cover-skeleton`) | Düz `var(--color-paper-300)` blok; merkezde mono caps "FOTOĞRAF EKLENMEDİ" + altında dateline; HSL gradient YOK |
| `.section-break` | Sadece `margin-block: var(--space-section)`; mark yok; opsiyonel hairline `<hr>` |
| `.dropcap` (kaldırılır) | Yerine: `.lede-paragraph` — first paragraph: `font-size: var(--text-md); font-weight: 500; color: var(--color-ink); first 2 words letter-spacing wider uppercase font-mono text-xs` |
| `.timeline-rail` | Korunur ama dot accent → ink rengi; bg shadow rule rengine değişir |
| `.channel-card` | Korunur ama border accent KALDIRILIR primary için; sadece "primary" eyebrow ile işaretlenir |

---

## AŞAMA 6 — Sayfa-sayfa restructure planı

### 6.1 — `/` (Anasayfa) — *Editor's Desk*

**Eski**: Hero slogan + bylines strip + featured grid (1+5) + footer-CTA.

**Yeni**: Bir editör masasının ön sayfası. Slogan yok.

```
┌──────────────────────────────────────────────────────────────┐
│ HEADER: wordmark mono caps · İSTANBUL · MUHABİR              │
├──────────────────────────────────────────────────────────────┤
│ EYEBROW: Bugün · 2026-04-19                                  │
│                                                              │
│ ┌─ LEAD (8 col) ─────────────────┐ ┌─ RAIL (4 col) ──────┐  │
│ │ Son dispatch'in headline'ı     │ │ PORTRE (3:2 küçük)  │  │
│ │ (Source Serif Display 5xl)     │ │                      │  │
│ │ Lede paragraph (2-3 cümle)     │ │ "Şu sıralar:"        │  │
│ │ → Devamını oku                 │ │ tek satır current    │  │
│ │                                │ │ context (örn.        │  │
│ │ DATELINE: KAHİRE · 2026-04-12  │ │ "İstanbul, drone     │  │
│ │                                │ │ haberciliği üzerine  │  │
│ └────────────────────────────────┘ │ kitap çalışması")    │  │
│                                    └──────────────────────┘  │
├──────────────────────────────────────────────────────────────┤
│ EYEBROW: Son altı dispatch                                   │
│                                                              │
│ Chronological list — yıl başlığı + dateline + headline + lead│
│                                                              │
│  2026 ──────────────────────────────────────────────────────│
│  04-12  KAHİRE  Saha   "Headline buraya"                    │
│  03-30  GAZZE   Foto   "Headline"                           │
│  02-18  ANKARA  İncele "Headline"                           │
│  2025 ──────────────────────────────────────────────────────│
│  12-04  HARKİV  Saha   "Headline"                           │
│  ...                                                         │
├──────────────────────────────────────────────────────────────┤
│ EYEBROW: Yayımlandığı yerler                                 │
│ Hairline rule — yayın isimleri düz mono, etrafında nefes    │
├──────────────────────────────────────────────────────────────┤
│ FOOTER (yeni 2-col)                                          │
└──────────────────────────────────────────────────────────────┘
```

**Önemli:** Featured cover image GÖSTERİLMEZ ana sayfada. Ana sayfa metin önceliklidir; her dispatch'in fotoğrafı **detayda** çıkar. Bu kural, fotoğrafların gücünü korur.

İstisna: Eğer admin "öne çıkar fotoğraf" toggle açarsa lead bölümü 16:9 fotoğraf alanı içerir. Default kapalı.

### 6.2 — `/yazilar` — *The Index*

**Eski**: Filter pill + 3-col grid + pagination.

**Yeni**: Kronolojik liste (NY Times index, Aperture archive idiomu).

```
┌──────────────────────────────────────────────────────────────┐
│ EYEBROW: Arşiv · 47 dispatch · 12 yıl                        │
│ Display headline: "Tüm dispatch'ler" (italik vurgu YOK)     │
│ Filter rail (sticky sol kolon lg+):                          │
│   Tür: tümü / saha / görüntü / inceleme / not                │
│   Bölge: tümü / Ortadoğu / Kafkasya / Avrupa / yurt içi      │
│   Yıl: 2026 (4) · 2025 (8) · 2024 (5) ...                   │
│ ── ── ── ── ── ── ── ── ── ── ── ── ── ── ── ── ── ── ── ──│
│ 2026                                                         │
│   04-12  KAHİRE   SAHA   Headline buraya                     │
│                          1-line lede preview                  │
│   03-30  GAZZE    FOTO   Headline                            │
│                          1-line lede preview                  │
│ ── ── ── ── ── ── ── ── ── ── ── ── ── ── ── ── ── ── ── ──│
│ 2025                                                         │
│   ...                                                        │
└──────────────────────────────────────────────────────────────┘
```

3-col card grid YOK. Bunun yerine: kronolojik liste, her satır `[date · location · kind] [title] [lede]`. Cover image satırın yanında **küçük 80×60 thumbnail olabilir** (sadece foto-kategorisinde) — ama default text-only.

### 6.3 — `/yazilar/{slug}` — *The Dispatch*

**Eski**: Cover hero + dateline + h1 + excerpt + cover image + 3-col (margin / prose / margin) + prev/next + related.

**Yeni**: Cleaner, lede-paragraph based, true marginalia.

```
┌──────────────────────────────────────────────────────────────┐
│ EYEBROW: ← Tüm dispatch'ler · Saha · 2026                    │
│                                                              │
│ DATELINE bar: KAHİRE · 2026-04-12 · 12 dk · Saha             │
│                                                              │
│ HEADLINE (Source Serif Display 6xl) — 2 satır max            │
│                                                              │
│ STANDFIRST (italic, 1.5 line, max 38ch)                      │
│ Bağlam veren tek paragraf.                                   │
├──────────────────────────────────────────────────────────────┤
│ COVER IMAGE (16:9 full-bleed, gerçek foto VEYA honest skel.) │
│ CAPTION (2 satır mono caps + 1 satır italik):                │
│   KAHİRE · TAHRİR · 2026-03                                  │
│   Tahrir Meydanı'nda akşam vakti — Mısır parlamentosu        │
│   önündeki nöbet dağıldıktan sonra. (Foto: OE)               │
├──────────────────────────────────────────────────────────────┤
│ ┌─ MARGIN (2 col) ────┐ ┌─ PROSE (8 col, 62ch) ────────┐    │
│ │ Sticky:             │ │                              │    │
│ │ - yayım: 2026-04-12 │ │ Lede paragraph (text-md,     │    │
│ │ - konum: Kahire     │ │   first 2-3 words mono caps  │    │
│ │ - tür: Saha         │ │   eyebrow style).            │    │
│ │ - okuma: 12 dk      │ │                              │    │
│ │ - yayın: AA Wire    │ │ Body paragraphs...           │    │
│ │                     │ │                              │    │
│ │ Footnotes:          │ │ Inline footnote ref [1]      │    │
│ │ [1] Kaynak: ...     │ │                              │    │
│ │ [2] Bağlam: ...     │ │                              │    │
│ └─────────────────────┘ └──────────────────────────────┘    │
├──────────────────────────────────────────────────────────────┤
│ Ek görüntüler galerisi (varsa) — 2-col, foto + caption       │
├──────────────────────────────────────────────────────────────┤
│ İletişim hattı: "Bu dispatch hakkında bilgi vermek           │
│   isteyenler için: [Signal] [PGP] [E-posta]"                 │
├──────────────────────────────────────────────────────────────┤
│ Prev / Next (mevcut korunur, sadece styling güncelle)        │
├──────────────────────────────────────────────────────────────┤
│ İlgili dispatch'ler (text-only liste, 3 satır)               │
└──────────────────────────────────────────────────────────────┘
```

**Drop cap** kalkar. **Lede paragraph** stili: ilk paragrafın **ilk 2-3 kelimesi** mono caps eyebrow gibi (örn. "KAHİRE — ", "GAZE'DE, AKŞAM — "), sonra normal akış. Bu klasik wire-service lede pattern'i.

### 6.4 — `/hakkimda` — *The Person File*

**Eski**: Masthead "Ozan / Efeoğlu" + sticky künye + bio prose + timeline + awards + recent + CTA.

**Yeni**: Portre-merkezli, dört katmanlı kimlik.

```
┌──────────────────────────────────────────────────────────────┐
│ EYEBROW: Hakkında · Foto muhabiri ve yayıncı                 │
│                                                              │
│ ┌─ PORTRE (4 col, 3:4) ──┐ ┌─ INTRO (8 col) ─────────────┐  │
│ │                        │ │ Display H1:                 │  │
│ │   [Portre fotoğrafı]   │ │ "Ozan Efeoğlu"              │  │
│ │   alt + caption mono   │ │ (italik vurgu YOK)          │  │
│ │   (Foto: Ad Soyad)     │ │                             │  │
│ │                        │ │ Standfirst (italik, max     │  │
│ │   Sticky lg+           │ │   38ch):                    │  │
│ │                        │ │ "Saha tecrübesi olan,       │  │
│ │                        │ │   görüntüyü yazıya katmayı  │  │
│ │                        │ │   bilen, kaynaklarını       │  │
│ │                        │ │   koruyan, akademik         │  │
│ │                        │ │   soruları olan İstanbul    │  │
│ │                        │ │   tabanlı bir foto          │  │
│ │                        │ │   muhabiri."                │  │
│ └────────────────────────┘ └─────────────────────────────┘  │
├──────────────────────────────────────────────────────────────┤
│ İki kolon: BİYO + KÜNYE                                      │
│                                                              │
│ ┌─ BİYO (8 col) ──────────────┐ ┌─ KÜNYE (4 col) ────────┐  │
│ │ Eyebrow: KISA HİKAYE         │ │ Eyebrow: KÜNYE         │  │
│ │ 3-4 paragraf — özgeçmişin    │ │ üs:    İstanbul        │  │
│ │ doğal nesir formu (CV bullet │ │ kurum: AA Uluslararası │  │
│ │ değil).                      │ │ saha:  ME · CC · SE-EU │  │
│ │                              │ │ dil:   TR · EN · AR    │  │
│ │ Source bağlantı:             │ │ ─────                  │  │
│ │ "CV (PDF)"                   │ │ CV (PDF) ↗             │  │
│ └──────────────────────────────┘ │ İletişim →             │  │
│                                   └────────────────────────┘ │
├──────────────────────────────────────────────────────────────┤
│ ÇALIŞMA ALANLARI (4 sütun, hairline arası)                  │
│                                                              │
│  SAHA           GÖRSEL          ARAŞTIRMA       YAYINCILIK   │
│  Çatışma       Foto muhabir    Drone hab.       AA Uluslar.  │
│  Sınır hattı   Drone           Görsel gösterg.  Bağımsız     │
│  ME · Kafkas   Reklam (geçmiş) Saha pratikleri  yayınlar     │
├──────────────────────────────────────────────────────────────┤
│ KRONOLOJİ (timeline-rail; mevcut korunur, dot ink rengi)    │
│ 2026  ◆ İstanbul, AA Uluslararası Haber Merkezi              │
│ 2024  ◆ Yüksek lisans tezi: Drone haberciliği                │
│ 2018  ◆ Hatay, Zeytin Dalı sınır hattı                       │
│ 2017  ◆ AA Savaş Muhabirliği eğitimi                         │
│ 2016  ◆ Kocaeli Üni. Gazetecilik                             │
│ 2014  ◆ İst. Aydın Üni. Fotoğrafçılık+Kameramanlık           │
│ 2012  ◆ Uludağ Üni. Bilgisayar Programcılığı                 │
│ 2010  ◆ Yalova Aksa Anadolu Teknik Lisesi                    │
├──────────────────────────────────────────────────────────────┤
│ METODOLOJİ — kısa madde (3-5 paragraf veya bullet)          │
│ "Nasıl çalışıyor": akreditasyon, kaynak güvenliği, etik,    │
│ verifikasyon, foto editing minimum, bağlam verme zorunlu.   │
├──────────────────────────────────────────────────────────────┤
│ SON DİSPATCH'LER (3 text-row, no card)                       │
└──────────────────────────────────────────────────────────────┘
```

**Awards bölümü** kalkar (özgeçmişte ödül listesi yok; sahibi sonradan ekleyebilir, o zaman geri gelir).

### 6.5 — `/iletisim` — *Channels*

Mevcut yapı **mantıklı**, sadece tipografi güncelleme:
- "Üç **güvenli** kanal." italik em → düz "Üç güvenli kanal" (italik kalkar, accent renk kalkar)
- Channel-card primary border accent → ink border + small "PRIMARY" mono eyebrow
- "Kurumsal değilse, doğrudan **buradan**." → düz "Kurumsal değilse, doğrudan buradan." (italik kalkar)
- PGP box, disclosure box korunur ama border-radius minimum (2px)

### 6.6 — Header

**Eski:** Ember dot + Fraunces wordmark + 3 nav + theme + Masa.

**Yeni:**
```
[OZAN EFEOĞLU]                  Saha   Görüntü   Hakkında   İletişim   [☾]  [Masa↗]
[İSTANBUL · MUHABİR]
```
- Wordmark: IBM Plex Mono Bold, all caps, tracking 0.1em, 14px
- Sub-line: dateline mono microcopy 10px, color ink-subtle
- Logo dot **kaldırılır**
- Nav: 4 link (Saha · Görüntü · Hakkında · İletişim) + theme + Masa
- "Görüntü" yeni nav (foto seri arşivi — Faz 5'e kadar pasif olabilir; "Yakında" tooltip)

### 6.7 — Footer

**Eski:** 4-col + sahte sosyal linkler.

**Yeni:** 2-col + colophon.

```
[OZAN EFEOĞLU]                  İLETİŞİM
İstanbul tabanlı foto muhabir.  E-posta: press@ozanefeoglu.com
Saha · görsel · araştırma.       Signal: signal.org/+90... (gerçek)
                                 PGP: parmak izi (gerçek) ↗
                                 RSS ↗

──────────────────────────────────────────────────────────────
© 2026 Ozan Efeoğlu · İstanbul · Bu site açık kaynaktır →
Build: 2026-04-19 · v0.4 · son güncelleme: 2 saat önce
```

Sahte sosyal linkler **kaldırılır**. Eklenecekse gerçek hesap olmalı (admin'den editable, "ekleme" yoksa görünmez).

### 6.7+ — Cover skeleton refactor

Eski `.cover-placeholder` (HSL gradient) → yeni `.cover-skeleton`:

```html
<div class="cover-skeleton">
  <span class="cover-skeleton-mark">FOTOĞRAF EKLENMEDİ</span>
  <span class="cover-skeleton-meta">KAHİRE · 2026-04</span>
</div>
```

```css
.cover-skeleton {
  aspect-ratio: 4 / 3;
  background: var(--color-paper-200);
  border: 1px solid var(--color-paper-300);
  display: grid;
  place-items: center;
  position: relative;
}
[data-theme="dark"] .cover-skeleton {
  background: var(--color-paper-800);
  border-color: var(--color-paper-700);
}
.cover-skeleton-mark {
  font-family: var(--font-mono);
  font-size: 0.7rem;
  letter-spacing: 0.25em;
  text-transform: uppercase;
  color: var(--color-ink-subtle);
}
.cover-skeleton-meta {
  position: absolute;
  bottom: 0.75rem;
  left: 1rem;
  font-family: var(--font-mono);
  font-size: 0.65rem;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: var(--color-ink-subtle);
}
```

Hue sliders admin'de **kaldırılır** (anlamsız oldular). Yerine: cover photo upload + caption + credit alanı.

---

## AŞAMA 7 — Uygulama planı (faz ve dosya listesi)

Uygulama 5 küçük "session" parçaya ayrılır. Her session bağımsız, atomic commit yapılabilir.

### Session 1 — Token & font swap (1 oturum)
- `package.json`: Fraunces kaldır, `@fontsource-variable/source-serif-4` ekle
- `resources/css/app.css`: tüm tipografi + renk + spacing tokenları yeniden çiz
- Tailwind theme bloğu, dark overrides, base h1-h6, drop cap kaldırma, section break, hover utilities, btn variants, cover-skeleton CSS
- Build doğrulama: `npm run build` temiz
- **Dosyalar:** 2 dosya. **Süre:** ~30 dk.

### Session 2 — Header + Footer + Layout shell (1 oturum)
- `resources/views/partials/public-header.blade.php`: wordmark + dateline + 4 nav + Masa
- `resources/views/partials/public-footer.blade.php`: 2-col + colophon + gerçek-or-gizli iletişim
- `resources/views/layouts/public.blade.php`: ortak head, font preload (Source Serif), CSS preload
- **Dosyalar:** 3 dosya. **Süre:** ~30 dk.

### Session 3 — Landing + Writing index + show (2 oturum)
- `resources/views/public/landing.blade.php`: editor's desk pattern
- `app/Http/Controllers/HomeController.php`: lead writing + 6 chronological + currentContext setting
- `resources/views/public/writing/index.blade.php`: chronological list + filter sidebar
- `resources/views/public/writing/show.blade.php`: lede paragraph + true marginalia + caption + standfirst
- `resources/views/partials/_writing-card.blade.php` → `_writing-row.blade.php` (text-row variant) + `_writing-card.blade.php` korunur (foto-kategorisi için sınırlı)
- **Dosyalar:** 5-6 dosya. **Süre:** ~1.5 sa.

### Session 4 — About + Contact + Portrait wiring (1 oturum)
- `resources/views/public/pages/about.blade.php`: 4-col portre + bio + künye + work areas + timeline + methodology
- `resources/views/public/pages/contact.blade.php`: italik kaldır, primary border kaldır, copy temizle
- `database/seeders/PageSeeder.php`: about + contact yeniden yazılır (CV'den nesir, 4 work area, gerçek timeline, methodology bölümü)
- `app/Http/Controllers/AboutController.php`: portre yolu, currentContext, recent dispatches
- Portre asset: `storage/app/public/portraits/ozan-3x4.jpg` + `ozan-1x1.jpg` + `ozan-3x2.jpg` (admin manuel upload edecek; placeholder olarak `public/img/portrait-skeleton.svg` honest skeleton döşenir)
- `app/Models/Setting.php`: `person.portrait_path`, `person.current_context`, `person.handles.email/signal/pgp` settings
- `app/Services/SettingsRepository.php`: `person()` helper
- **Dosyalar:** 6-7 dosya. **Süre:** ~1.5 sa.

### Session 5 — Tests + ADR + self-review (1 oturum)
- `docs/decisions/016-design-language-v2-field-dossier.md`: bu dokümanın özeti + supersedes ADR-014
- Test güncellemesi: cover-placeholder → cover-skeleton; assertion stringleri güncelle
- `php artisan test` 112+ tüm yeşil
- `npm run build` + `composer run pint` yeşil
- Self-review checklist (Aşama 9) madde madde geçilir
- `docs/STATE_AND_ROADMAP.md` v0.4 — design language v2 notu eklenir
- **Dosyalar:** 4-5 dosya. **Süre:** ~1 sa.

**Toplam tahmin:** 5-6 oturum, ~6 saat etkin çalışma. Mevcut roadmap'in **Faz 2C+ öncesine** sıkıştırılır; Faz 3+ orijinal yol haritasıyla devam eder.

### Risk & azaltma

| Risk | Azaltma |
|---|---|
| Source Serif 4 Variable subset boyutu (latin+tr) | Fontsource latin-ext subset kullan; preload kritik weight 700 |
| Cover skeleton çıplak görünür | Honest tone önemli; yine de paper-300 + dotted border ile "kart" hissi korunur |
| Sahibi "fazla kuru" der | A/B fırsatı: bir oturumda primary CTA brass-600 vs kalsın seçeneği gösterilebilir |
| Test stringleri kırılır | Pest test'ler "Hakkında" gibi static text'leri assert ediyor; çoğu korunur. Kırılan 3-5 test güncellenir. |
| Linter revert davranışı (STATE_AND_ROADMAP §C.2) | Her batch sonunda dosya re-read; commit hızlı |
| Disk dolu (228MB) | Vendor + node_modules ihtiyacı yok bu refactor için; sadece npm install (Source Serif) ~5MB |

---

## AŞAMA 9 — Self-review checklist (uygulama sonrası)

Uygulama bittiğinde her sayfa için bu sorular:

### Genel
- [ ] Hiçbir başlıkta italik em accent renk vurgusu yok mu?
- [ ] Hover'da `transform` kullanılan tek bir komponent var mı? (Olmamalı)
- [ ] Tek bir sayfada birden fazla `.btn--accent` var mı? (Olmamalı)
- [ ] Cover skeleton "FOTOĞRAF EKLENMEDİ" kullanıcıya **dürüst** mü yoksa süs mü?
- [ ] Sahte link var mı? (`href="#"`, `https://x.com/`, `https://instagram.com/`) — varsa kaldır

### Anasayfa
- [ ] Slogan yok, lead headline var mı?
- [ ] Portre küçük + kontrollü kullanılmış mı (full-bleed değil)?
- [ ] Yazılar grid değil chronological liste mi?
- [ ] "Daha fazlası" CTA blok yerine "bir sonraki dispatch" linki var mı?

### Hakkımda
- [ ] Portre var mı, sticky lg+ mı, alt+caption (Foto: ...) var mı?
- [ ] CV bullet değil, nesir biyografi mi?
- [ ] 4 work area sütunu var mı (Saha · Görsel · Araştırma · Yayıncılık)?
- [ ] Timeline accent renk kullanmıyor mu (ink renk dot)?
- [ ] Metodoloji bölümü var mı? (Brief: "akreditasyon, kaynak güvenliği, etik")

### Yazı detay
- [ ] Drop cap **yok** mu?
- [ ] Lede paragraph (ilk 2-3 kelime mono caps) var mı?
- [ ] Cover image varsa caption + credit zorunlu mu?
- [ ] Margin sticky tabs gerçek bilgi mi (yayım, konum, tür, okuma) yoksa dekor mu?
- [ ] Footnote referansı çalışıyor mu? (Faz 5'e bırakılabilir; bu fazda sadece HTML output destekli)

### Yazı index
- [ ] Card grid değil chronological liste mi?
- [ ] Yıl başlıkları + dateline her satır + 1-line lede var mı?
- [ ] Filter rail sticky mı (lg+)?

### İletişim
- [ ] Italic em accent kalktı mı (3 yerde de — başlık, copy, kart)?
- [ ] Channel cards primary border accent kalktı mı?
- [ ] Form field-label uppercase tracking-wide korundu mu?

### Header / Footer
- [ ] Logo dot **yok** mu?
- [ ] Wordmark IBM Plex Mono Bold all caps mı?
- [ ] Footer'da sahte sosyal link **yok** mu?
- [ ] Footer colophon (build/version) var mı (teknik şeffaflık katmanı)?

### Erişilebilirlik
- [ ] Brass-600 (#7c5a3a) on paper-100 (#f6f4ee) WCAG AA pass mı? Hedef 7.1:1+ → kontrast checker ile doğrula
- [ ] Focus-visible 2px brass outline 3px offset hâlâ görünür mü?
- [ ] Skip link, aria-current, semantic HTML korunmuş mu?

### Performans
- [ ] Source Serif 4 latin+tr subset preloaded mı?
- [ ] CSS bundle gz altında 25KB hâlâ mı? (Fraunces çıkmasıyla küçülmesi beklenir)
- [ ] No JS regressions — Alpine sadece header scroll + theme + copy

### Karakter (en önemli)
- [ ] Bir başkasına giydirilebilir mi bu site? (Hayır demek istiyoruz)
- [ ] 3 yıl sonra hâlâ güçlü mü? (Evet demek istiyoruz)
- [ ] Sahibinin "ben buyum" dediği yer mi yoksa "ben böyle olmak istiyorum" sahnelemesi mi?
- [ ] Foto muhabir + akademik + teknik üç katman da görülüyor mu?

---

## EK — Karar bekleyen küçük noktalar (sahibi onaylasa iyi olur)

1. **"Görüntü" nav item Faz 5'e mi bırakılsın, yoksa şimdiden boş arşiv mi?** Önerim: şimdiden, "yakında dolacak" mesajıyla — IA'da yerini açmak önemli.
2. **CV PDF dosyası sahibi tarafından sağlanacak mı, yoksa ben özgeçmişten generate mi edeyim?** Önerim: özgeçmişten basit bir tek-sayfalık otomatik üretim (admin'den indirilebilir).
3. **Portre dosyası sahibinin yüklediği orijinal mi (~15MB), yoksa ben 3 crop'ı önceden mi hazırlamalıyım?** Önerim: orijinali sahibi `storage/app/public/portraits/source.jpg` olarak yüklesin; ben Spatie Media ile 3 variant (1:1, 3:4, 3:2) otomatik üretim sağlarım. Bu iş Faz 2B'de yapılan webp variant pattern'i kullanır.
4. **`person.handles.signal` gerçek Signal numarası mı, signal.me link mi?** Önerim: signal.me link (numara gizli kalır).
5. **`person.handles.pgp` parmak izi gerçek anahtar mı?** Sahibinin gerçek public key'i lazım. Yoksa şimdilik "PGP yakında" placeholder.

Bu beş cevabı bekliyorum **ama yokluğunda** her birine sensible default ile devam edebilirim — proje akışını durdurmaz.

---

## Kapanış

Bu doküman bir karar dokümanıdır, bir teklif değil. Sahibi yön değiştirmek isterse bu sefer önce duraklarım — aksi halde **Direction C ("The Field Dossier")** ile uygulamaya geçerim. Mevcut Faz 2C kapanışını parçalamadan, sadece sunum (presentation) katmanını yeniden inşa eder. Backend modeller, policy'ler, testler, admin omurga **dokunulmaz** — yalnızca view'lar + CSS + iki controller bağlamı + bir seeder güncellenir.

> **Sonraki tur:** Session 1 — token & font swap.

---

*Belge sonu.*
