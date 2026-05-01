/* global React */

/* Compose the typo-cover card used wherever a writing has no photo */
window.TypoCover = function TypoCover({ writing, featured = false }) {
  const cls = ["typo-cover", `typo-cover--${writing.kind}`];
  if (featured) cls.push("typo-cover--featured");
  return (
    <div className={cls.join(" ")}>
      <p className="typo-cover-kicker">
        {writing.kindLabel.toUpperCase()}
        {writing.location ? ` · ${writing.location.toUpperCase()}` : null}
      </p>
      <h2 className="typo-cover-title">{writing.title}</h2>
      <p className="typo-cover-mark">
        {writing.date} <span aria-hidden="true">·</span>{" "}
        {featured ? <span>oe<span style={{color:"var(--color-accent)"}}>.</span></span> : `${writing.readMins} DK`}
      </p>
    </div>
  );
};

/* Hero — Editor's Desk: photo-less typographic hero with tagline */
function HeroTypographic({ onNavigate }) {
  return (
    <section className="scene scene--tight" style={{ paddingBlock: "clamp(2rem, 5vw, 4rem)" }}>
      <div className="page-wrap">
        <div className="dossier-grid" style={{ alignItems: "end", rowGap: 36 }}>
          <div className="dg-7">
            <Eyebrow className="mb-4">Saha · 016 dosya · v1.0</Eyebrow>
            <h1 className="display-statuesque" style={{ fontSize: "clamp(2.6rem, 7vw, 6.5rem)" }}>
              Kasanın<br />altındaki şehir.
            </h1>
            <Standfirst className="mt-6">
              Hatay'da bir kahvenin tezgâhında, kasa makinesinin ses çıkarmadığı saatlerde, bir başka şehir kuruluyordu.
            </Standfirst>
            <p className="mt-6" style={{ display: "flex", gap: 18, alignItems: "center" }}>
              <Btn onClick={() => onNavigate("yazilar")}>Tüm dispatch</Btn>
              <LinkQuiet onClick={() => onNavigate("hakkinda")}>Hakkında →</LinkQuiet>
            </p>
          </div>
          <div className="dg-5" style={{ paddingTop: 8 }}>
            <PhotoPlaceholder ratio="3/4" tone="darker" label="PORTRE — OE" subtitle="2026 / İstanbul" />
            <p className="photo-caption mt-3">
              <span className="photo-caption-loc">İstanbul</span>{" "}
              2026 — kontrolü Ozan Efeoğlu{" "}
              <span className="photo-caption-credit">© Arşiv</span>
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}

/* Scene 2 — Featured Dossier (lead) */
function FeaturedDossier({ writing, onNavigate }) {
  return (
    <section className="scene scene--muted">
      <div className="page-wrap">
        <div className="dossier-grid" style={{ alignItems: "start", rowGap: 32 }}>
          <a
            href="#"
            className="dg-7"
            style={{ textDecoration: "none", display: "block", color: "inherit" }}
            onClick={(e) => { e.preventDefault(); onNavigate("yazi", writing.id); }}
          >
            <TypoCover writing={writing} featured />
          </a>
          <div className="dg-5">
            <Kicker accent>
              Son dosya <DateSep />
              <span style={{ color: "var(--color-ink-muted)" }}>{writing.location?.toUpperCase()}</span>
            </Kicker>
            <a
              href="#"
              onClick={(e) => { e.preventDefault(); onNavigate("yazi", writing.id); }}
              className="mt-4"
              style={{ textDecoration: "none", color: "inherit", display: "block" }}
            >
              <h2 className="display-editorial" style={{ marginTop: 14 }}>{writing.title}</h2>
            </a>
            <Standfirst className="mt-5">{writing.excerpt}</Standfirst>
            <Dateline className="mt-6" style={{ display: "flex", flexWrap: "wrap", gap: "0 8px" }}>
              <span>{writing.dateLong}</span>
              <DateSep />
              <span>{writing.kindLabel}</span>
              <DateSep />
              <span className="tabular-nums">{writing.readMins} dk</span>
            </Dateline>
            <p className="mt-6">
              <LinkQuiet onClick={() => onNavigate("yazi", writing.id)} className="text-sm">
                Devamını oku <span aria-hidden="true">→</span>
              </LinkQuiet>
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}

/* Scene 3 — Constellations: 4 selected works (mason 2x2) */
function Constellations({ writings, onNavigate }) {
  return (
    <section className="scene scene--tight">
      <div className="page-wrap">
        <header style={{ display: "flex", alignItems: "baseline", justifyContent: "space-between", gap: 24, marginBottom: 36 }}>
          <div>
            <Eyebrow className="mb-2">Seçki</Eyebrow>
            <h2 className="display-editorial">Seçilmiş çalışmalar</h2>
          </div>
          <LinkQuiet onClick={() => onNavigate("yazilar")} className="text-sm">
            Tüm arşiv <span aria-hidden="true">→</span>
          </LinkQuiet>
        </header>

        <div className="mason-2x2">
          {writings.map((w) => (
            <a
              key={w.id}
              href="#"
              className="constellation-card"
              onClick={(e) => { e.preventDefault(); onNavigate("yazi", w.id); }}
            >
              <TypoCover writing={w} />
              <p className="constellation-card-lede mt-4">{w.excerpt}</p>
              <p className="constellation-card-meta">
                {w.dateShort} <span className="dateline-separator">·</span> {w.readMins} dk
              </p>
            </a>
          ))}
        </div>
      </div>
    </section>
  );
}

/* Scene 3.5 — Contact sheet */
function ContactSheet({ frames, onNavigate }) {
  return (
    <section className="scene scene--muted">
      <div className="page-wrap">
        <header style={{ display: "flex", alignItems: "baseline", justifyContent: "space-between", gap: 24, marginBottom: 28 }}>
          <div>
            <Eyebrow className="mb-2">Arşiv · kontrol baskısı</Eyebrow>
            <h2 className="display-editorial">Son kareler</h2>
          </div>
          <LinkQuiet onClick={() => onNavigate("goruntu")} className="text-sm">
            Tüm görüntü arşivi <span aria-hidden="true">→</span>
          </LinkQuiet>
        </header>

        <div className="contact-sheet">
          {frames.map((f) => (
            <a key={f.idx} href="#" className="contact-sheet-frame" onClick={(e) => { e.preventDefault(); onNavigate("goruntu"); }}>
              <PhotoPlaceholder ratio="3/2" tone="darker" label={`KARE ${String(f.idx).padStart(2, "0")}`} subtitle={f.loc} />
              <div className="contact-sheet-caption">
                <span className="contact-sheet-index tabular-nums">{String(f.idx).padStart(2, "0")}</span>
                <span className="contact-sheet-loc">{f.loc.toUpperCase()}</span>
              </div>
            </a>
          ))}
        </div>
      </div>
    </section>
  );
}

/* Scene 4 — Profile-as-scene */
function ProfileScene({ workareas, onNavigate }) {
  return (
    <section className="scene scene--darker">
      <div className="page-wrap">
        <div className="dossier-grid" style={{ rowGap: 36 }}>
          <div className="dg-5">
            <PullQuote cite="Ozan Efeoğlu — saha defteri">
              Gazetecilik parlamaz, kazınır. Onaylanan satır, sessizce vurulan damgadır.
            </PullQuote>
          </div>
          <div className="dg-7">
            <ul className="atolye-mini">
              {workareas.map((a) => (
                <li key={a.label}>
                  <p className="atolye-mini-label">{a.label}</p>
                  <div>
                    <h3 className="atolye-mini-title">{a.title}</h3>
                    <p className="atolye-mini-lines">{a.lines.join(" · ")}</p>
                  </div>
                </li>
              ))}
            </ul>
            <p className="mt-6">
              <LinkQuiet onClick={() => onNavigate("hakkinda")} className="text-sm">
                Tam biyografi <span aria-hidden="true">→</span>
              </LinkQuiet>
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}

/* Scene 5 — Recent dispatches (writing-row list) */
function RecentDispatches({ writings, onNavigate }) {
  return (
    <section className="scene scene--tight">
      <div className="page-wrap">
        <header style={{ display: "flex", alignItems: "baseline", justifyContent: "space-between", gap: 24, marginBottom: 24 }}>
          <h2 className="display-quiet">Son dispatch</h2>
          <LinkQuiet onClick={() => onNavigate("yazilar")} className="text-sm">
            Tüm arşiv <span aria-hidden="true">→</span>
          </LinkQuiet>
        </header>
        <div style={{ borderTop: "2px solid var(--color-ink)" }}>
          {writings.map((w) => <WritingRow key={w.id} w={w} onNavigate={onNavigate} />)}
        </div>
      </div>
    </section>
  );
}

window.WritingRow = function WritingRow({ w, onNavigate }) {
  return (
    <a
      href="#"
      className="writing-row"
      onClick={(e) => { e.preventDefault(); onNavigate("yazi", w.id); }}
    >
      <div className="writing-row-dateline">
        <span style={{ display: "block" }} className="tabular-nums">{w.monthDay}</span>
        {w.location ? <span style={{ display: "block", marginTop: 2, color: "var(--color-ink-subtle)" }}>{w.location.toUpperCase()}</span> : null}
      </div>
      <div className="writing-row-body">
        <span className="writing-row-kind">{w.kindLabel} · {w.readMins} dk</span>
        <h3 className="writing-row-title">{w.title}</h3>
        {w.excerpt ? <p className="writing-row-lede">{w.excerpt}</p> : null}
      </div>
    </a>
  );
};

/* Scene 6 — Bylines */
function Bylines({ credits }) {
  return (
    <section className="scene scene--closing" style={{ borderTop: "1px solid var(--color-rule)" }}>
      <div className="page-wrap">
        <Eyebrow className="mb-5">Yayımlandığı yerler</Eyebrow>
        <ul style={{
          display: "flex", flexWrap: "wrap", gap: "8px 32px", listStyle: "none", margin: 0, padding: 0,
          fontFamily: "var(--font-mono)", fontSize: "0.72rem",
          textTransform: "uppercase", letterSpacing: "0.18em",
          color: "var(--color-ink-muted)",
        }}>
          {credits.map((c) => <li key={c}>{c}</li>)}
        </ul>
      </div>
    </section>
  );
}

window.HomeScene = function HomeScene({ onNavigate }) {
  const lead = WRITINGS[0];
  const constellation = WRITINGS.slice(1, 5);
  const recent = WRITINGS.slice(0, 5);
  return (
    <main>
      <HeroTypographic onNavigate={onNavigate} />
      <FeaturedDossier writing={lead} onNavigate={onNavigate} />
      <Constellations writings={constellation} onNavigate={onNavigate} />
      <ContactSheet frames={PHOTO_FRAMES} onNavigate={onNavigate} />
      <ProfileScene workareas={WORKAREAS} onNavigate={onNavigate} />
      <RecentDispatches writings={recent} onNavigate={onNavigate} />
      <Bylines credits={CREDITS} />
    </main>
  );
};
