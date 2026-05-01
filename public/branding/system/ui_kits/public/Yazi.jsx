/* global React */

window.YazilarScene = function YazilarScene({ onNavigate }) {
  const [filter, setFilter] = React.useState("hepsi");
  const filtered = filter === "hepsi" ? WRITINGS : WRITINGS.filter(w => w.kind === filter);

  return (
    <main>
      <section className="scene scene--tight">
        <div className="page-wrap">
          <Eyebrow className="mb-3">Arşiv</Eyebrow>
          <h1 className="display-statuesque" style={{ fontSize: "clamp(2.4rem, 6vw, 5rem)" }}>Yazılar.</h1>
          <Standfirst className="mt-5" style={{ maxWidth: "55ch" }}>
            Saha yazıları, röportajlar, denemeler ve notlar — tarihe göre. Toplam {WRITINGS.length} dosya.
          </Standfirst>

          <div className="mt-8" style={{ display: "flex", gap: 8, flexWrap: "wrap", borderBottom: "1px solid var(--color-rule)", paddingBottom: 16 }}>
            {[
              ["hepsi", "Hepsi"],
              ["saha_yazisi", "Saha yazısı"],
              ["roportaj", "Röportaj"],
              ["deneme", "Deneme"],
              ["not", "Not"],
            ].map(([k, label]) => (
              <button
                key={k}
                onClick={() => setFilter(k)}
                className={filter === k ? "btn btn--sm" : "btn btn--secondary btn--sm"}
              >
                {label}
              </button>
            ))}
          </div>
        </div>
      </section>

      <section className="scene scene--tight" style={{ paddingTop: 0 }}>
        <div className="page-wrap">
          <div style={{ borderTop: "2px solid var(--color-ink)" }}>
            {filtered.map((w) => <WritingRow key={w.id} w={w} onNavigate={onNavigate} />)}
          </div>
        </div>
      </section>
    </main>
  );
};

window.YaziScene = function YaziScene({ id, onNavigate }) {
  const w = WRITINGS.find((x) => x.id === id) || WRITINGS[0];

  return (
    <main>
      {/* Article masthead */}
      <article className="scene scene--tight">
        <div className="page-wrap">
          <p className="mb-6">
            <LinkQuiet onClick={() => onNavigate("yazilar")} className="text-sm">
              ← Tüm yazılar
            </LinkQuiet>
          </p>
          <Kicker accent>
            {w.kindLabel} <DateSep />
            <span style={{ color: "var(--color-ink-muted)" }}>{w.location?.toUpperCase()}</span>
          </Kicker>
          <h1 className="display-statuesque mt-5" style={{ fontSize: "clamp(2.4rem, 6vw, 5.5rem)", maxWidth: "16ch" }}>
            {w.title}
          </h1>
          <Standfirst className="mt-6" style={{ maxWidth: "60ch" }}>
            {w.excerpt}
          </Standfirst>
          <Dateline className="mt-8" style={{ display: "flex", flexWrap: "wrap", gap: "0 8px" }}>
            <span>{w.dateLong}</span>
            <DateSep />
            <span>Ozan Efeoğlu</span>
            <DateSep />
            <span className="tabular-nums">{w.readMins} dk okuma</span>
          </Dateline>
        </div>
      </article>

      {/* Hero photo (placeholder) */}
      <figure className="scene scene--tight" style={{ paddingTop: 0 }}>
        <div className="page-wrap">
          <PhotoPlaceholder ratio="3/2" tone="darker" label={`SAHA — ${w.location?.toUpperCase() || "ARŞIV"}`} subtitle={w.dateShort} />
          <figcaption className="photo-caption mt-3">
            <span className="photo-caption-loc">{w.location || "Arşiv"}</span>{" "}
            {w.dateLong} —{" "}
            <span className="photo-caption-credit">© Ozan Efeoğlu</span>
          </figcaption>
        </div>
      </figure>

      {/* Body */}
      <section className="scene scene--tight" style={{ paddingTop: 0 }}>
        <div className="page-wrap">
          <div className="dossier-grid">
            <div className="dg-8">
              <div className="prose">
                <p>
                  <span className="lede-open">{(w.location || "Saha").toUpperCase()} —</span>
                  Sabah altıyı henüz geçmişti. Tezgâhın altındaki kasanın metal tıkırtısı, yan masada uyuyan kediden başka kimseyi rahatsız etmiyordu. Şehir uyandığında bu satırların yazıldığı kahve, başka bir kahveye dönüşmüş olacaktı; bu yüzden notlar acele tutulur.
                </p>
                <p>
                  Tezgâhın iki yakasında oturanların paylaştığı, hiçbir resmî kayıtta görünmeyen bir yön duygusu var: kuzeye bakan kapı, batıdan gelen ışık, doğudaki nem. Görsel göstergebilim derslerinde anlatılan üçgen, burada üç fincanla yeniden çizilir.
                </p>
                <h2>Tezgâhın altı</h2>
                <p>
                  Asıl şehir, kasanın altında kuruluyordu. Defterler, makbuzlar, üstüne tükenmez kalemle düşülmüş notlar; bunların her biri, gece boyunca yazılmış bir saha defterinin alfabesi olarak okunabilir.
                </p>
                <p>
                  Foto‑muhabirin görevi, bu alfabeyi bozmadan kayıt altına almaktır. Dronu açmadan, lensi bile çıkarmadan önce, bir saatlik dinleme. Çerçeve, ondan sonra geliyor.
                </p>
                <h3>Üç soru</h3>
                <p>
                  Bir kareyi göndermeden önce sorulması gereken üç soru, bütün etik kodlardan daha eskidir: ben olmasaydım bu görüntü olur muydu, kimi koruyor, kimden saklıyor.
                </p>
              </div>
            </div>

            <aside className="dg-3" style={{ gridColumnStart: 10 }}>
              <dl className="kunye">
                <div className="kunye-row"><dt>Yazı</dt><dd>{w.kindLabel}</dd></div>
                <div className="kunye-row"><dt>Tarih</dt><dd className="tabular-nums">{w.dateShort}</dd></div>
                <div className="kunye-row"><dt>Konum</dt><dd>{w.location || "—"}</dd></div>
                <div className="kunye-row"><dt>Süre</dt><dd className="tabular-nums">{w.readMins} dk</dd></div>
                <div className="kunye-row"><dt>Kontrol</dt><dd>oe<span style={{color:"#b91c1c"}}>.</span></dd></div>
                <div className="kunye-row"><dt>Sürüm</dt><dd className="tabular-nums">v1.0</dd></div>
              </dl>
              <div className="disclosure-box mt-6">
                <p>
                  <strong>Açıklama.</strong> Saha defteri girdileri yayımlanmadan önce bağımsız bir editör tarafından kontrol edilmiştir. İsimler, ilgili kişilerin onayıyla yer almaktadır.
                </p>
              </div>
            </aside>
          </div>
        </div>
      </section>

      {/* Pull quote band */}
      <section className="scene scene--inverse">
        <div className="page-wrap">
          <PullQuote cite={`${w.dateLong} — saha defteri`}>
            Gazetecilik parlamaz, kazınır. Onaylanan satır, sessizce vurulan damgadır.
          </PullQuote>
        </div>
      </section>

      {/* Related */}
      <section className="scene scene--tight">
        <div className="page-wrap">
          <Eyebrow className="mb-5">Yakındaki dosyalar</Eyebrow>
          <div className="mason-2x2">
            {WRITINGS.filter(x => x.id !== w.id).slice(0, 2).map(rel => (
              <a
                key={rel.id}
                href="#"
                className="constellation-card"
                onClick={(e) => { e.preventDefault(); onNavigate("yazi", rel.id); }}
              >
                <TypoCover writing={rel} />
                <p className="constellation-card-lede mt-4">{rel.excerpt}</p>
                <p className="constellation-card-meta">{rel.dateShort} · {rel.readMins} dk</p>
              </a>
            ))}
          </div>
        </div>
      </section>
    </main>
  );
};
