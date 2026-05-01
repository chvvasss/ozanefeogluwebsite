/* global React */

/* ─────────── Photo library ─────────── */
const PHOTO_LIBRARY = [
  { id: "ph01", title: "Kasanın altı — kare 12", date: "2026.04.26", series: "Hatay", ratio: "3/2", filename: "kasanin-alti-12.tif", size: "84 MB" },
  { id: "ph02", title: "Defter sayfası", date: "2026.04.13", series: "Röportaj", ratio: "2/3", filename: "defter-09.tif", size: "62 MB" },
  { id: "ph03", title: "Saf siyah — test", date: "2026.04.03", series: "Atölye", ratio: "1/1", filename: "siyah-test-04.tif", size: "48 MB" },
  { id: "ph04", title: "Kabin — sabah", date: "2026.03.21", series: "Saha 016", ratio: "3/2", filename: "kabin-sabah-02.tif", size: "92 MB" },
  { id: "ph05", title: "Zeytin Dalı — 03", date: "2026.03.08", series: "Hatay", ratio: "3/2", filename: "zeytin-03.tif", size: "78 MB" },
  { id: "ph06", title: "Yedi muhabir — 11", date: "2026.04.22", series: "Röportaj", ratio: "2/3", filename: "yedi-11.tif", size: "71 MB" },
  { id: "ph07", title: "Eski sokak — yeni isim", date: "2026.04.20", series: "İstanbul", ratio: "3/2", filename: "sokak-isim-05.tif", size: "66 MB" },
  { id: "ph08", title: "Uludağ — kar", date: "2026.02.18", series: "Bursa", ratio: "3/2", filename: "uludag-kar-08.tif", size: "104 MB" },
  { id: "ph09", title: "Arşiv — 2024", date: "2024.11.12", series: "Anadolu", ratio: "1/1", filename: "arsiv-2024-22.tif", size: "58 MB" },
  { id: "ph10", title: "Pencere — Kasım", date: "2025.11.04", series: "Notlar", ratio: "2/3", filename: "pencere-kasim.tif", size: "53 MB" },
  { id: "ph11", title: "Mürekkep iz", date: "2025.10.18", series: "Atölye", ratio: "1/1", filename: "murekkep-iz.tif", size: "44 MB" },
  { id: "ph12", title: "Liman — sabah 06:00", date: "2025.09.02", series: "İstanbul", ratio: "3/2", filename: "liman-06.tif", size: "88 MB" },
];

window.AdminPhotos = function AdminPhotos() {
  const [view, setView] = React.useState("contact");
  const [selected, setSelected] = React.useState(new Set());
  const toggle = (id) => {
    const next = new Set(selected);
    next.has(id) ? next.delete(id) : next.add(id);
    setSelected(next);
  };
  const series = ["Hepsi", "Hatay", "İstanbul", "Bursa", "Atölye", "Röportaj", "Notlar"];
  const [filter, setFilter] = React.useState("Hepsi");
  const list = filter === "Hepsi" ? PHOTO_LIBRARY : PHOTO_LIBRARY.filter(p => p.series === filter);

  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">Yayın · medya</Eyebrow>
          <h1 className="admin-page-title">Fotoğraflar</h1>
          <p className="admin-page-sub">Arşivde {PHOTO_LIBRARY.length} kare. Kontak baskısı ya da liste görünümünde.</p>
        </div>
        <div className="admin-page-actions">
          <Btn variant="secondary" size="sm">İçe aktar</Btn>
          <Btn size="sm">+ Kare ekle</Btn>
        </div>
      </header>

      <div className="admin-table-toolbar" style={{ marginBottom: 16, border: "1px solid var(--color-rule)" }}>
        <div className="filters" style={{ flex: 1 }}>
          {series.map((s) => (
            <button key={s} className="chip" aria-pressed={filter === s} onClick={() => setFilter(s)}
              style={filter === s ? { background: "var(--color-ink)", color: "var(--color-bg-elevated)", borderColor: "var(--color-ink)" } : null}>
              {s}
            </button>
          ))}
        </div>
        <div className="seg" role="tablist" aria-label="Görünüm">
          <button role="tab" aria-selected={view === "contact"} onClick={() => setView("contact")}>Kontak</button>
          <button role="tab" aria-selected={view === "list"} onClick={() => setView("list")}>Liste</button>
        </div>
      </div>

      {selected.size > 0 && (
        <div className="bulk-bar">
          <span><strong>{selected.size}</strong> kare seçildi</span>
          <span className="bulk-bar-sep" />
          <button>Yazıya ekle</button>
          <button>Etiketle</button>
          <button>Dışa aktar</button>
          <button data-danger>Sil</button>
          <button onClick={() => setSelected(new Set())} style={{ marginLeft: "auto" }}>İptal</button>
        </div>
      )}

      {view === "contact" ? (
        <div className="photo-grid">
          {list.map((p, i) => (
            <button
              key={p.id}
              className={`photo-cell${selected.has(p.id) ? " is-selected" : ""}`}
              onClick={() => toggle(p.id)}
              type="button"
            >
              <div className="photo-thumb" style={{ aspectRatio: p.ratio }}>
                <span className="photo-num">{String(i + 1).padStart(2, "0")}</span>
                <span className="photo-ratio">{p.ratio}</span>
                <span className="photo-check" aria-hidden>✓</span>
              </div>
              <div className="photo-meta">
                <div className="photo-title">{p.title}</div>
                <div className="photo-sub">{p.date} · {p.series}</div>
              </div>
            </button>
          ))}
        </div>
      ) : (
        <div className="admin-table-wrap">
          <table className="admin-table">
            <thead><tr>
              <th style={{ width: 28 }}></th><th>Dosya</th><th>Seri</th><th>Boyut</th><th>Oran</th><th>Tarih</th>
            </tr></thead>
            <tbody>
              {list.map((p) => (
                <tr key={p.id}>
                  <td><input type="checkbox" checked={selected.has(p.id)} onChange={() => toggle(p.id)} /></td>
                  <td><div className="col-title">{p.title}</div><div className="col-meta">{p.filename}</div></td>
                  <td className="col-num">{p.series}</td>
                  <td className="col-num">{p.size}</td>
                  <td className="col-num">{p.ratio}</td>
                  <td className="col-num">{p.date}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
};

/* ─────────── Pages ─────────── */
const ADMIN_PAGES = [
  { id: 1, title: "Hakkında", slug: "/hakkinda", updated: "2026.04.18", words: 612, status: "published" },
  { id: 2, title: "İletişim", slug: "/iletisim", updated: "2026.04.10", words: 184, status: "published" },
  { id: 3, title: "Künye", slug: "/kunye", updated: "2026.04.01", words: 240, status: "published" },
  { id: 4, title: "KVKK aydınlatma", slug: "/kvkk", updated: "2026.04.01", words: 480, status: "published" },
  { id: 5, title: "Gizlilik politikası", slug: "/gizlilik", updated: "2026.04.01", words: 360, status: "published" },
  { id: 6, title: "Atölye programı (taslak)", slug: "/atolye", updated: "2026.04.22", words: 920, status: "draft" },
];

window.AdminPages = function AdminPages() {
  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">İçerik · sabit sayfalar</Eyebrow>
          <h1 className="admin-page-title">Sayfalar</h1>
          <p className="admin-page-sub">Hakkında, İletişim, Künye, hukuksal — yazılardan ayrı, sabit gövdeler.</p>
        </div>
        <div className="admin-page-actions">
          <Btn size="sm">+ Yeni sayfa</Btn>
        </div>
      </header>

      <div className="admin-table-wrap">
        <table className="admin-table">
          <thead><tr>
            <th>Sayfa</th><th>Slug</th><th>Durum</th><th>Söz</th><th>Son güncelleme</th><th></th>
          </tr></thead>
          <tbody>
            {ADMIN_PAGES.map((p) => (
              <tr key={p.id}>
                <td><div className="col-title">{p.title}</div></td>
                <td className="col-meta">{p.slug}</td>
                <td><span className="status-pip" data-status={p.status}>{p.status === "draft" ? "Taslak" : "Yayında"}</span></td>
                <td className="col-num">{p.words.toLocaleString("tr-TR")}</td>
                <td className="col-num">{p.updated}</td>
                <td style={{ textAlign: "right" }}><LinkQuiet className="text-sm">Düzenle →</LinkQuiet></td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};

/* ─────────── Publications ─────────── */
const PUBLICATIONS = [
  { id: "rss", name: "RSS yayını", target: "/feed.xml", count: 31, last: "2026.04.26 09:14", state: "active" },
  { id: "atom", name: "Atom yayını", target: "/atom.xml", count: 31, last: "2026.04.26 09:14", state: "active" },
  { id: "newsletter", name: "Aylık bülten", target: "1.247 abone", count: 12, last: "2026.04.01 08:00", state: "active" },
  { id: "sitemap", name: "Site haritası", target: "/sitemap.xml", count: 47, last: "2026.04.26 09:14", state: "active" },
  { id: "json-feed", name: "JSON Feed", target: "/feed.json", count: 31, last: "2026.04.26 09:14", state: "active" },
  { id: "webmention", name: "Webmention", target: "in / out", count: 84, last: "2026.04.25 22:30", state: "active" },
  { id: "archive-org", name: "Internet Archive", target: "auto‑arşiv", count: 31, last: "2026.04.26 11:00", state: "active" },
  { id: "mastodon", name: "Mastodon syndik.", target: "@ozan@kolektif.social", count: 0, last: "—", state: "paused" },
];

window.AdminPublications = function AdminPublications() {
  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">Yayın · akışlar</Eyebrow>
          <h1 className="admin-page-title">Yayınlar</h1>
          <p className="admin-page-sub">Bu sitenin dışarıya açtığı kanallar — RSS, bülten, harita, syndikasyon.</p>
        </div>
        <div className="admin-page-actions">
          <Btn variant="secondary" size="sm">Test et</Btn>
          <Btn size="sm">+ Yeni akış</Btn>
        </div>
      </header>

      <div className="dossier-grid" style={{ rowGap: 14 }}>
        {PUBLICATIONS.map((p) => (
          <div key={p.id} className="dg-6" style={{
            border: "1px solid var(--color-rule)", padding: "18px 20px",
            background: "var(--color-bg-elevated)",
          }}>
            <div style={{ display: "flex", justifyContent: "space-between", alignItems: "baseline", gap: 12 }}>
              <h3 style={{ fontFamily: "var(--font-display)", fontSize: "1.05rem", margin: 0, color: "var(--color-ink)" }}>{p.name}</h3>
              <span className="status-pip" data-status={p.state === "active" ? "published" : "archived"}>
                {p.state === "active" ? "Aktif" : "Pasif"}
              </span>
            </div>
            <p style={{ fontFamily: "var(--font-mono)", fontSize: ".74rem", color: "var(--color-ink-muted)", margin: "4px 0 14px" }}>
              {p.target}
            </p>
            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 8, fontFamily: "var(--font-mono)", fontSize: ".7rem", color: "var(--color-ink-subtle)", borderTop: "1px solid var(--color-rule)", paddingTop: 10 }}>
              <div><div style={{ textTransform: "uppercase", letterSpacing: ".18em" }}>Kayıt</div><div className="tabular-nums" style={{ color: "var(--color-ink)", marginTop: 2 }}>{p.count}</div></div>
              <div><div style={{ textTransform: "uppercase", letterSpacing: ".18em" }}>Son</div><div className="tabular-nums" style={{ color: "var(--color-ink)", marginTop: 2 }}>{p.last}</div></div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

/* ─────────── Users ─────────── */
const USERS = [
  { id: 1, name: "Ozan Efeoğlu", email: "ozan@ozanefeoglu.com", role: "Editör (sahip)", last: "Şimdi · İstanbul", state: "active" },
  { id: 2, name: "Ayşe Kaya", email: "ayse@editor.tr", role: "Yazı editörü", last: "Bugün 11:42 · İzmir", state: "active" },
  { id: 3, name: "Konuk · 2 sezon", email: "konuk@geçici.dev", role: "Misafir yazar", last: "12.03.2026 · pasif", state: "expired" },
];

window.AdminUsers = function AdminUsers() {
  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">Hesap · ekip</Eyebrow>
          <h1 className="admin-page-title">Kullanıcılar</h1>
          <p className="admin-page-sub">Bu sitenin yazı işleri kadrosu. Tek kişilik bir yayın olsa da rol ayrımı tutulur.</p>
        </div>
        <div className="admin-page-actions">
          <Btn size="sm">+ Davet et</Btn>
        </div>
      </header>

      <div className="admin-table-wrap">
        <table className="admin-table">
          <thead><tr>
            <th>Ad</th><th>E‑posta</th><th>Rol</th><th>Son etkinlik</th><th>Durum</th><th></th>
          </tr></thead>
          <tbody>
            {USERS.map((u) => (
              <tr key={u.id}>
                <td><div className="col-title">{u.name}</div></td>
                <td className="col-meta">{u.email}</td>
                <td className="col-num">{u.role}</td>
                <td className="col-num">{u.last}</td>
                <td>
                  <span className="status-pip" data-status={u.state === "active" ? "published" : "archived"}>
                    {u.state === "active" ? "Aktif" : "Süresi doldu"}
                  </span>
                </td>
                <td style={{ textAlign: "right" }}><LinkQuiet className="text-sm">Düzenle →</LinkQuiet></td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="disclosure-box mt-8" style={{ maxWidth: 720 }}>
        <p>
          <strong>Rol mantığı.</strong> Editör (sahip) tüm yetkilere sahiptir. Yazı editörü taslak yazabilir, yayımlayamaz. Misafir yazar yalnızca kendi taslaklarını görür ve süreli erişime sahiptir.
        </p>
      </div>
    </div>
  );
};

/* ─────────── Profile ─────────── */
window.AdminProfile = function AdminProfile() {
  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">Hesap · profil</Eyebrow>
          <h1 className="admin-page-title">Profil</h1>
          <p className="admin-page-sub">Yazı işleri masasında görünen ad, künye satırı ve avatar.</p>
        </div>
      </header>

      <div className="dossier-grid" style={{ rowGap: 18 }}>
        <section className="dg-7">
          <div className="compose-card">
            <p className="compose-card-title">Kimlik</p>
            <Field label="Görünen ad"><input className="input" defaultValue="Ozan Efeoğlu" /></Field>
            <Field label="Künye satırı"><input className="input" defaultValue="Foto‑muhabir, yazar — İstanbul" /></Field>
            <Field label="E‑posta"><input className="input" type="email" defaultValue="ozan@ozanefeoglu.com" /></Field>
            <Field label="Konum"><input className="input" defaultValue="İstanbul, TR" /></Field>
          </div>

          <div className="compose-card" style={{ marginTop: 18 }}>
            <p className="compose-card-title">Bağlantılar</p>
            <Field label="Mastodon"><input className="input" defaultValue="@ozan@kolektif.social" /></Field>
            <Field label="Are.na"><input className="input" defaultValue="ozan-efeoglu" /></Field>
            <Field label="GitHub"><input className="input" defaultValue="ozanefeoglu" /></Field>
          </div>

          <div className="compose-card" style={{ marginTop: 18 }}>
            <p className="compose-card-title">Yazı işleri imzası</p>
            <Field label="Bültende ve mailde kullanılır">
              <textarea className="input" rows="4" defaultValue={"Selamlar,\nOzan\n— ozanefeoglu.com"}></textarea>
            </Field>
          </div>

          <p style={{ marginTop: 20, display: "flex", gap: 12 }}>
            <Btn>Kaydet</Btn>
            <Btn variant="secondary">İptal</Btn>
          </p>
        </section>

        <aside className="dg-4" style={{ gridColumnStart: 9 }}>
          <div className="compose-card">
            <p className="compose-card-title">Avatar</p>
            <div style={{
              width: 120, height: 120, marginTop: 4,
              border: "1px solid var(--color-rule-strong)",
              background: "var(--color-bg-muted)",
              display: "flex", alignItems: "center", justifyContent: "center",
              fontFamily: "var(--font-display)", fontSize: "2.6rem", color: "var(--color-ink)",
            }}>
              <span style={{ fontWeight: 600, letterSpacing: "-0.04em" }}>oe</span><span style={{ color: "#b91c1c", marginLeft: 4 }}>.</span>
            </div>
            <p style={{ fontFamily: "var(--font-mono)", fontSize: ".7rem", color: "var(--color-ink-muted)", marginTop: 14 }}>
              Site genelinde sadece künyede ve yazı sonu imzasında kullanılır.
            </p>
            <p style={{ marginTop: 14, display: "flex", gap: 8 }}>
              <Btn variant="secondary" size="sm">Yükle</Btn>
              <Btn variant="ghost" size="sm">Kaldır</Btn>
            </p>
          </div>
        </aside>
      </div>
    </div>
  );
};

/* ─────────── Sessions ─────────── */
const SESSIONS = [
  { id: 1, agent: "Safari 17 · macOS Sonoma", ip: "192.168.1.4", loc: "İstanbul · TR", first: "20.04.2026 09:14", last: "Şimdi", current: true },
  { id: 2, agent: "Firefox 124 · Linux", ip: "85.96.x.x", loc: "İstanbul · TR", first: "12.04.2026 06:30", last: "Bugün 11:42", current: false },
  { id: 3, agent: "Mobile Safari · iOS 17.4", ip: "78.183.x.x", loc: "Hatay · TR", first: "26.04.2026 09:00", last: "Bugün 09:14", current: false },
  { id: 4, agent: "CLI · curl/8.4", ip: "—", loc: "API anahtarı", first: "01.03.2026", last: "Otomatik", current: false },
];

window.AdminSessions = function AdminSessions() {
  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">Hesap · güvenlik</Eyebrow>
          <h1 className="admin-page-title">Açık oturumlar</h1>
          <p className="admin-page-sub">Bu hesap şu an dört yerden açık. Tanımadığınız bir satır varsa hemen kapatın.</p>
        </div>
        <div className="admin-page-actions">
          <Btn variant="secondary" size="sm">Tüm diğerlerini kapat</Btn>
        </div>
      </header>

      <div className="admin-table-wrap">
        <table className="admin-table">
          <thead><tr>
            <th>Cihaz / istemci</th><th>IP</th><th>Konum</th><th>İlk</th><th>Son</th><th></th>
          </tr></thead>
          <tbody>
            {SESSIONS.map((s) => (
              <tr key={s.id}>
                <td>
                  <div className="col-title">
                    {s.agent}
                    {s.current && (
                      <span className="status-pip" data-status="published" style={{ marginLeft: 10 }}>
                        Bu cihaz
                      </span>
                    )}
                  </div>
                </td>
                <td className="col-meta">{s.ip}</td>
                <td className="col-num">{s.loc}</td>
                <td className="col-num">{s.first}</td>
                <td className="col-num">{s.last}</td>
                <td style={{ textAlign: "right" }}>
                  {s.current ? (
                    <span style={{ fontFamily: "var(--font-mono)", fontSize: ".7rem", color: "var(--color-ink-subtle)" }}>—</span>
                  ) : (
                    <button className="link-quiet text-sm" style={{ color: "var(--color-danger)", background: "transparent", border: 0, cursor: "pointer", padding: 0 }}>
                      Kapat →
                    </button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="disclosure-box mt-8" style={{ maxWidth: 720 }}>
        <p>
          <strong>Güvenlik notu.</strong> Oturumlar 30 gün sonra otomatik kapanır. API anahtarı için zaman sınırı yoktur — Ayarlar → API'den iptal edilebilir.
        </p>
      </div>
    </div>
  );
};

/* ─────────── 2FA setup ─────────── */
window.AdminTwoFactorSetup = function AdminTwoFactorSetup({ onNavigate }) {
  const [step, setStep] = React.useState(1);
  const codes = ["a4f2-9b1c", "7e2d-c8a0", "16f4-2b9e", "9c3d-7a51", "b8e1-4f02", "2d9a-c7e6", "5f60-1ab3", "e7c4-902d"];
  return (
    <div className="admin-page" style={{ maxWidth: 720 }}>
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">Hesap · iki faktör</Eyebrow>
          <h1 className="admin-page-title">İki faktörlü kimlik doğrulama</h1>
          <p className="admin-page-sub">Adım {step} / 3 — Tezgâhın tek anahtarı sizdesiniz, ikinci kilit önerilir.</p>
        </div>
      </header>

      <ol className="step-rail" aria-label="Kurulum adımları">
        {["Uygulama", "Doğrula", "Yedek kodlar"].map((label, i) => (
          <li key={label} className={`step-rail-item${step === i + 1 ? " is-active" : ""}${step > i + 1 ? " is-done" : ""}`}>
            <span className="step-num tabular-nums">{String(i + 1).padStart(2, "0")}</span>
            <span className="step-label">{label}</span>
          </li>
        ))}
      </ol>

      {step === 1 && (
        <div className="compose-card">
          <p className="compose-card-title">Adım 01 — Uygulamayı bağla</p>
          <p style={{ fontFamily: "var(--font-display)", color: "var(--color-ink-muted)", margin: "0 0 18px", fontStyle: "italic" }}>
            1Password, Aegis veya Google Authenticator'da QR'i tarayın. Manuel anahtar bir alt satırdadır.
          </p>
          <div style={{ display: "flex", gap: 28, alignItems: "center", flexWrap: "wrap" }}>
            <div style={{
              width: 160, height: 160, background: "var(--color-bg-muted)",
              border: "1px solid var(--color-rule-strong)",
              backgroundImage: "repeating-linear-gradient(0deg, var(--color-ink) 0 8px, transparent 8px 16px), repeating-linear-gradient(90deg, var(--color-ink) 0 8px, transparent 8px 16px)",
              backgroundSize: "16px 16px", opacity: .85,
            }} aria-label="QR kod placeholder" />
            <div>
              <p className="text-xs" style={{ fontFamily: "var(--font-mono)", letterSpacing: ".22em", textTransform: "uppercase", color: "var(--color-ink-subtle)", margin: 0 }}>
                Manuel anahtar
              </p>
              <p style={{
                fontFamily: "var(--font-mono)", fontSize: "1rem",
                letterSpacing: ".06em", marginTop: 8,
                background: "var(--color-bg-muted)", padding: "10px 14px",
                border: "1px solid var(--color-rule)",
              }}>
                JBSW Y3DP EHPK 3PXP JBSW Y3DP
              </p>
            </div>
          </div>
          <p style={{ marginTop: 24, display: "flex", gap: 12 }}>
            <Btn onClick={() => setStep(2)}>Devam</Btn>
            <Btn variant="ghost" onClick={() => onNavigate && onNavigate("dashboard")}>İptal</Btn>
          </p>
        </div>
      )}

      {step === 2 && (
        <div className="compose-card">
          <p className="compose-card-title">Adım 02 — Altı haneli kodu doğrula</p>
          <p style={{ fontFamily: "var(--font-display)", color: "var(--color-ink-muted)", margin: "0 0 18px", fontStyle: "italic" }}>
            Uygulamada görünen kodu girin. Kod her 30 saniyede yenilenir.
          </p>
          <div style={{ display: "flex", gap: 8 }}>
            {[0,1,2,3,4,5].map((i) => (
              <input key={i} maxLength={1} inputMode="numeric" pattern="[0-9]*"
                defaultValue={["3","4","9","2","7","1"][i]}
                style={{
                  width: 56, height: 64, textAlign: "center",
                  fontFamily: "var(--font-mono)", fontSize: "1.6rem",
                  border: "1px solid var(--color-rule-strong)",
                  background: "var(--color-bg-elevated)", color: "var(--color-ink)",
                }}
              />
            ))}
          </div>
          <p style={{ fontFamily: "var(--font-mono)", fontSize: ".7rem", color: "var(--color-ink-subtle)", marginTop: 14 }}>
            Bir sonraki kod 23 saniye içinde geçerli olacak.
          </p>
          <p style={{ marginTop: 24, display: "flex", gap: 12 }}>
            <Btn onClick={() => setStep(3)}>Doğrula</Btn>
            <Btn variant="ghost" onClick={() => setStep(1)}>Geri</Btn>
          </p>
        </div>
      )}

      {step === 3 && (
        <div className="compose-card">
          <p className="compose-card-title">Adım 03 — Yedek kodları sakla</p>
          <p style={{ fontFamily: "var(--font-display)", color: "var(--color-ink-muted)", margin: "0 0 18px", fontStyle: "italic" }}>
            Bu sekiz kod, telefonunuza erişiminiz olmadığında giriş yapmanızı sağlar. Her biri tek kullanımlıktır.
          </p>
          <div style={{
            display: "grid", gridTemplateColumns: "repeat(2, 1fr)", gap: 10,
            border: "1px solid var(--color-rule)", padding: 18,
            background: "var(--color-bg-muted)",
          }}>
            {codes.map((c, i) => (
              <div key={c} style={{
                fontFamily: "var(--font-mono)", fontSize: ".95rem",
                display: "flex", justifyContent: "space-between",
                paddingBottom: 6, borderBottom: i < 6 ? "1px dashed var(--color-rule)" : "none",
              }}>
                <span style={{ color: "var(--color-ink-subtle)" }}>{String(i + 1).padStart(2, "0")}</span>
                <span className="tabular-nums" style={{ color: "var(--color-ink)" }}>{c}</span>
              </div>
            ))}
          </div>
          <p style={{ marginTop: 24, display: "flex", gap: 12 }}>
            <Btn>Kodları indir</Btn>
            <Btn variant="secondary">Yazdır</Btn>
            <Btn variant="ghost" onClick={() => onNavigate && onNavigate("dashboard")}>Tamam</Btn>
          </p>
          <div className="disclosure-box mt-6">
            <p><strong>Saklama önerisi.</strong> Şifre kasanıza kaydedin ya da yazdırıp arşiv klasörüne koyun. Bu sayfayı kapatınca kodlar tekrar gösterilmez.</p>
          </div>
        </div>
      )}
    </div>
  );
};

/* ─────────── 2FA disable ─────────── */
window.AdminTwoFactorDisable = function AdminTwoFactorDisable({ onNavigate }) {
  return (
    <div className="admin-page" style={{ maxWidth: 640 }}>
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2" style={{ color: "var(--color-danger)" }}>Hesap · iki faktör · kapat</Eyebrow>
          <h1 className="admin-page-title">Bu kilidi açmak istediğinize emin misiniz?</h1>
          <p className="admin-page-sub">İki faktör kapatılırsa hesaba yalnızca parola ile girilebilir.</p>
        </div>
      </header>

      <div style={{
        border: "1px solid var(--color-danger)",
        borderLeft: "4px solid var(--color-danger)",
        padding: "20px 24px", background: "var(--color-bg-elevated)",
      }}>
        <p style={{ fontFamily: "var(--font-display)", fontSize: "1.05rem", fontStyle: "italic", color: "var(--color-ink)", margin: 0 }}>
          Tezgâhın ikinci kilidi 02.03.2026'dan beri açık. Kapatmak, parolanızın çalınması durumunda hesabın savunmasız kalması anlamına gelir.
        </p>
        <ul style={{
          fontFamily: "var(--font-display)", fontSize: ".95rem",
          color: "var(--color-ink-muted)", margin: "16px 0 0 1.2rem",
          padding: 0, lineHeight: 1.7,
        }}>
          <li>Mevcut yedek kodlar geçersiz olur.</li>
          <li>Bağlı uygulamadan kayıt silinir.</li>
          <li>Açık oturumlar etkilenmez.</li>
        </ul>
      </div>

      <div className="compose-card" style={{ marginTop: 24 }}>
        <p className="compose-card-title">Onay</p>
        <Field label="Parolanızı tekrar girin">
          <input className="input" type="password" placeholder="••••••••" />
        </Field>
        <Field label='"İKİ FAKTÖRÜ KAPAT" yazın'>
          <input className="input" placeholder="İKİ FAKTÖRÜ KAPAT" />
        </Field>
      </div>

      <p style={{ marginTop: 20, display: "flex", gap: 12 }}>
        <Btn variant="danger">Evet, kapat</Btn>
        <Btn variant="ghost" onClick={() => onNavigate && onNavigate("twofa")}>Vazgeç</Btn>
      </p>
    </div>
  );
};

/* ─────────── Backup ─────────── */
const BACKUPS = [
  { id: "b1", date: "2026.04.26 04:00", size: "412 MB", target: "S3 · eu‑central‑1", state: "ok", duration: "00:42" },
  { id: "b2", date: "2026.04.25 04:00", size: "411 MB", target: "S3 · eu‑central‑1", state: "ok", duration: "00:39" },
  { id: "b3", date: "2026.04.24 04:00", size: "411 MB", target: "S3 · eu‑central‑1", state: "ok", duration: "00:41" },
  { id: "b4", date: "2026.04.23 04:00", size: "—", target: "S3 · eu‑central‑1", state: "fail", duration: "—" },
  { id: "b5", date: "2026.04.22 04:00", size: "410 MB", target: "S3 · eu‑central‑1", state: "ok", duration: "00:40" },
  { id: "b6", date: "2026.04.21 04:00", size: "409 MB", target: "S3 · eu‑central‑1", state: "ok", duration: "00:38" },
  { id: "b7", date: "2026.04.20 04:00", size: "409 MB", target: "S3 · eu‑central‑1", state: "ok", duration: "00:43" },
];

window.AdminBackup = function AdminBackup() {
  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">Site · yedekleme</Eyebrow>
          <h1 className="admin-page-title">Yedekleme</h1>
          <p className="admin-page-sub">Her gün 04:00'te otomatik yedek alınır. Son 30 gün saklanır.</p>
        </div>
        <div className="admin-page-actions">
          <Btn variant="secondary" size="sm">Geri yükle</Btn>
          <Btn size="sm">Şimdi yedekle</Btn>
        </div>
      </header>

      <div className="stat-grid mb-8">
        <div className="stat-cell">
          <p className="stat-label">Son yedek</p>
          <p className="stat-value" style={{ fontSize: "1.4rem" }}>26.04 · 04:00</p>
          <p className="stat-delta stat-delta--up">Başarılı</p>
        </div>
        <div className="stat-cell">
          <p className="stat-label">Toplam boyut</p>
          <p className="stat-value">412 MB</p>
          <p className="stat-delta">Sıkıştırılmış (tar.gz)</p>
        </div>
        <div className="stat-cell">
          <p className="stat-label">Tutuluyor</p>
          <p className="stat-value">30 gün</p>
          <p className="stat-delta">FIFO rotasyon</p>
        </div>
        <div className="stat-cell">
          <p className="stat-label">Hedef</p>
          <p className="stat-value" style={{ fontSize: "1.2rem" }}>S3 · Frankfurt</p>
          <p className="stat-delta">eu‑central‑1, AES‑256</p>
        </div>
      </div>

      <h2 className="display-quiet" style={{ margin: "0 0 14px" }}>Son yedekler</h2>
      <div className="admin-table-wrap">
        <table className="admin-table">
          <thead><tr>
            <th>Zaman</th><th>Hedef</th><th>Boyut</th><th>Süre</th><th>Durum</th><th></th>
          </tr></thead>
          <tbody>
            {BACKUPS.map((b) => (
              <tr key={b.id}>
                <td className="col-title">{b.date}</td>
                <td className="col-meta">{b.target}</td>
                <td className="col-num">{b.size}</td>
                <td className="col-num">{b.duration}</td>
                <td>
                  <span className="status-pip" data-status={b.state === "ok" ? "published" : "archived"}
                    style={b.state === "fail" ? { color: "var(--color-danger)" } : null}>
                    {b.state === "ok" ? "Başarılı" : "Hata"}
                  </span>
                </td>
                <td style={{ textAlign: "right" }}>
                  {b.state === "ok"
                    ? <LinkQuiet className="text-sm">Geri yükle →</LinkQuiet>
                    : <LinkQuiet className="text-sm">Kaydı gör →</LinkQuiet>}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="disclosure-box mt-8" style={{ maxWidth: 720 }}>
        <p>
          <strong>Geri yükleme.</strong> Bir yedeği geri yüklerken site otomatik olarak bakım moduna alınır. Operasyon ortalama 6 dakika sürer ve denetim kaydına işlenir.
        </p>
      </div>
    </div>
  );
};
