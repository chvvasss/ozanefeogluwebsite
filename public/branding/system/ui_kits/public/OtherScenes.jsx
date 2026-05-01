/* global React */

window.GoruntuScene = function GoruntuScene({ onNavigate }) {
  const series = [
    { title: "Sınır hattı, Hatay", year: "2023–2026", count: 24, frames: PHOTO_FRAMES.slice(0, 4) },
    { title: "Kabin · gece treni", year: "2025", count: 12, frames: PHOTO_FRAMES.slice(2, 6) },
    { title: "Drone · saha pratikleri", year: "2024–2026", count: 18, frames: PHOTO_FRAMES.slice(0, 6) },
  ];

  return (
    <main>
      <section className="scene scene--tight">
        <div className="page-wrap">
          <Eyebrow className="mb-3">Görüntü arşivi</Eyebrow>
          <h1 className="display-statuesque" style={{ fontSize: "clamp(2.4rem, 6vw, 5rem)" }}>Görüntü.</h1>
          <Standfirst className="mt-5" style={{ maxWidth: "55ch" }}>
            Üç seri, bir not defteri. Her kare için tarih, konum ve kısa bir bağlam.
          </Standfirst>
        </div>
      </section>

      {series.map((s, i) => (
        <section key={s.title} className={`scene scene--tight ${i % 2 ? "scene--muted" : ""}`}>
          <div className="page-wrap">
            <header style={{ display: "flex", alignItems: "baseline", justifyContent: "space-between", gap: 24, marginBottom: 28 }}>
              <div>
                <Kicker accent>Seri {String(i + 1).padStart(2, "0")} <DateSep /> <span style={{ color: "var(--color-ink-muted)" }}>{s.year}</span></Kicker>
                <h2 className="display-editorial mt-3">{s.title}</h2>
              </div>
              <Dateline>{s.count} kare</Dateline>
            </header>
            <div className="contact-sheet">
              {s.frames.map((f) => (
                <a key={f.idx} href="#" className="contact-sheet-frame" onClick={(e) => e.preventDefault()}>
                  <PhotoPlaceholder ratio="3/2" tone={i % 2 ? "default" : "darker"} label={`${String(f.idx).padStart(2, "0")}`} subtitle={f.loc} />
                  <div className="contact-sheet-caption">
                    <span className="contact-sheet-index tabular-nums">{String(f.idx).padStart(2, "0")}</span>
                    <span>{f.loc.toUpperCase()}</span>
                  </div>
                </a>
              ))}
            </div>
          </div>
        </section>
      ))}
    </main>
  );
};

window.HakkindaScene = function HakkindaScene({ onNavigate }) {
  return (
    <main>
      <section className="scene scene--tight">
        <div className="page-wrap">
          <div className="dossier-grid" style={{ alignItems: "start" }}>
            <div className="dg-7">
              <Eyebrow className="mb-3">Künye</Eyebrow>
              <h1 className="display-statuesque" style={{ fontSize: "clamp(2.4rem, 6vw, 5rem)" }}>Hakkında.</h1>
              <Standfirst className="mt-6" style={{ maxWidth: "55ch" }}>
                Foto‑muhabir, araştırmacı, yayıncı. İstanbul'da yaşıyor, çoğunlukla saha defterinde.
              </Standfirst>
              <div className="prose mt-8">
                <p>
                  <span className="lede-open">İSTANBUL —</span>
                  1996, Bursa doğumluyum. Uludağ Üniversitesi Bilgisayar Programcılığı'nda başladığım yol, Anadolu Ajansı'nın haber merkezinde uzun bir vardiyaya, oradan da görsel haberciliğe açıldı.
                </p>
                <p>
                  Şu an İstanbul Aydın Üniversitesi'nde drone haberciliğinin etiği üzerine yüksek lisans tezi yazıyorum. Saha pratikleri, görsel göstergebilim ve mahalle ölçeğindeki gazetecilik üzerine düşünüyorum.
                </p>
                <h2>Çalışma alanları</h2>
                <p>
                  Saha yazıları, uzun röportajlar, drone tabanlı görsel araştırma, arşiv çalışmaları. Yıllık iki dosya, üç röportaj, sürekli not defteri.
                </p>
              </div>
            </div>

            <aside className="dg-4" style={{ gridColumnStart: 9 }}>
              <PhotoPlaceholder ratio="3/4" tone="darker" label="PORTRE — OE" subtitle="2026" />
              <p className="photo-caption mt-3">
                <span className="photo-caption-loc">İstanbul</span>{" "}
                2026 <span className="photo-caption-credit">© Arşiv</span>
              </p>
              <dl className="marginalia mt-6">
                <div><dt>Doğum</dt><dd>1996, Bursa</dd></div>
                <div><dt>Eğitim</dt><dd>Uludağ Ü. — bilgisayar prog.<br />İst. Aydın Ü. — yüksek lisans</dd></div>
                <div><dt>Yayınevi</dt><dd>ozanefeoglu.com</dd></div>
                <div><dt>İletişim</dt><dd>ozan@ozanefeoglu.com</dd></div>
              </dl>
            </aside>
          </div>
        </div>
      </section>

      <section className="scene scene--muted">
        <div className="page-wrap">
          <Eyebrow className="mb-5">Çalışma alanları</Eyebrow>
          <ul className="atolye-mini">
            {WORKAREAS.map((a) => (
              <li key={a.label}>
                <p className="atolye-mini-label">{a.label}</p>
                <div>
                  <h3 className="atolye-mini-title">{a.title}</h3>
                  <p className="atolye-mini-lines">{a.lines.join(" · ")}</p>
                </div>
              </li>
            ))}
          </ul>
        </div>
      </section>
    </main>
  );
};

window.IletisimScene = function IletisimScene({ onNavigate }) {
  const [sent, setSent] = React.useState(false);

  return (
    <main>
      <section className="scene scene--tight">
        <div className="page-wrap">
          <div className="dossier-grid" style={{ alignItems: "start" }}>
            <div className="dg-6">
              <Eyebrow className="mb-3">Saha telefonu</Eyebrow>
              <h1 className="display-statuesque" style={{ fontSize: "clamp(2.4rem, 6vw, 5rem)" }}>İletişim.</h1>
              <Standfirst className="mt-6" style={{ maxWidth: "50ch" }}>
                Saha çağrıları, röportaj talepleri ve atölye davetleri için. Yanıtlama süresi 48 saat, hafta sonları kapalı.
              </Standfirst>

              <div className="mt-8" style={{ display: "grid", gap: 12 }}>
                <div className="channel-card channel-card--primary">
                  <span className="channel-pmark">Birincil</span>
                  <span className="channel-type">E‑posta</span>
                  <a href="mailto:ozan@ozanefeoglu.com" className="channel-handle">ozan@ozanefeoglu.com</a>
                  <span className="channel-note">Tüm dosyalar ve röportaj talepleri için.</span>
                </div>
                <div className="channel-card">
                  <span className="channel-type">Telefon · saha</span>
                  <span className="channel-handle">+90 (212) 555‑0102</span>
                  <span className="channel-note">Yalnızca acil saha durumları.</span>
                </div>
                <div className="channel-card">
                  <span className="channel-type">Posta</span>
                  <span className="channel-handle" style={{ fontSize: ".9rem", lineHeight: 1.5 }}>
                    PK 016, Beyoğlu PTT<br />34421 Beyoğlu / İstanbul
                  </span>
                </div>
              </div>
            </div>

            <div className="dg-5" style={{ gridColumnStart: 8 }}>
              <Eyebrow className="mb-4">Mesaj defteri</Eyebrow>
              {sent ? (
                <div className="flash flash--success">
                  <span aria-hidden="true">✓</span>
                  <span>Mesaj defterine kaydedildi. 48 saat içinde dönüş yapılacak.</span>
                </div>
              ) : null}
              <form onSubmit={(e) => { e.preventDefault(); setSent(true); }}>
                <Field label="Adınız">
                  <input className="input" type="text" defaultValue="" placeholder="Ad Soyad" />
                </Field>
                <Field label="E‑posta">
                  <input className="input" type="email" required placeholder="ad@alanadi.com" />
                </Field>
                <Field label="Konu">
                  <select className="input">
                    <option>Saha — yeni dosya</option>
                    <option>Röportaj talebi</option>
                    <option>Atölye / konuşma</option>
                    <option>Arşiv kullanımı</option>
                    <option>Diğer</option>
                  </select>
                </Field>
                <Field label="Mesajınız" hint="Özet bir paragraf yeterli; gerekirse dönüş yapılacaktır.">
                  <textarea className="input" rows="5" placeholder="Birkaç satır yazın..."></textarea>
                </Field>
                <Btn type="submit">Mesaj defterine yaz</Btn>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>
  );
};
