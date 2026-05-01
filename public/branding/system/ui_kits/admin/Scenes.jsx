/* global React */

/* ─────────── Dashboard ─────────── */
window.AdminDashboard = function AdminDashboard({ onNavigate }) {
  const recentDrafts = ADMIN_WRITINGS.filter(w => w.status === "draft" || w.status === "scheduled").slice(0, 3);
  const recentPub = ADMIN_WRITINGS.filter(w => w.status === "published").slice(0, 4);
  const newMsgs = ADMIN_INBOX.filter(m => m.status === "new").slice(0, 3);

  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">26 Nisan 2026 · Pazar · İstanbul</Eyebrow>
          <h1 className="admin-page-title">İyi günler, Ozan.</h1>
          <p className="admin-page-sub">Bugün dört yeni mesaj, bir taslak ve bir zamanlanmış yazı var.</p>
        </div>
        <div className="admin-page-actions">
          <Btn variant="secondary" size="sm" onClick={() => onNavigate("writings")}>Tüm yazılar</Btn>
          <Btn size="sm" onClick={() => onNavigate("compose")}>+ Yeni yazı</Btn>
        </div>
      </header>

      <div className="stat-grid mb-8">
        <div className="stat-cell">
          <p className="stat-label">Yayında</p>
          <p className="stat-value">31</p>
          <p className="stat-delta stat-delta--up">↑ 2 bu hafta</p>
        </div>
        <div className="stat-cell">
          <p className="stat-label">Taslak</p>
          <p className="stat-value">12</p>
          <p className="stat-delta">son: bugün 23:50</p>
        </div>
        <div className="stat-cell">
          <p className="stat-label">Mesaj kutusu</p>
          <p className="stat-value">4</p>
          <p className="stat-delta stat-delta--up">↑ 3 yeni</p>
        </div>
        <div className="stat-cell">
          <p className="stat-label">Aylık okur</p>
          <p className="stat-value">14.2K</p>
          <p className="stat-delta stat-delta--up">↑ %18</p>
        </div>
      </div>

      <div className="dossier-grid" style={{ rowGap: 32 }}>
        <section className="dg-7">
          <header style={{ display: "flex", alignItems: "baseline", justifyContent: "space-between", marginBottom: 14 }}>
            <h2 className="display-quiet" style={{ margin: 0 }}>Son yayımlananlar</h2>
            <LinkQuiet onClick={() => onNavigate("writings")} className="text-sm">Hepsi →</LinkQuiet>
          </header>
          <div className="admin-table-wrap">
            <table className="admin-table">
              <thead><tr>
                <th>Başlık</th><th>Tür</th><th>Tarih</th><th style={{ textAlign: "right" }}>Görüntülenme</th>
              </tr></thead>
              <tbody>
                {recentPub.map((w) => (
                  <tr key={w.id}>
                    <td><div className="col-title">{w.title}</div><div className="col-meta">{w.location}</div></td>
                    <td className="col-num">{w.kind}</td>
                    <td className="col-num">{w.date}</td>
                    <td className="col-num" style={{ textAlign: "right" }}>{w.views.toLocaleString("tr-TR")}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </section>

        <aside className="dg-5">
          <header style={{ display: "flex", alignItems: "baseline", justifyContent: "space-between", marginBottom: 14 }}>
            <h2 className="display-quiet" style={{ margin: 0 }}>Açık taslaklar</h2>
            <LinkQuiet onClick={() => onNavigate("writings")} className="text-sm">→</LinkQuiet>
          </header>
          <ul style={{ listStyle: "none", margin: 0, padding: 0, border: "1px solid var(--color-rule)", background: "var(--color-bg-elevated)" }}>
            {recentDrafts.map((w) => (
              <li key={w.id} style={{ padding: "12px 14px", borderBottom: "1px solid var(--color-rule)", display: "flex", alignItems: "baseline", justifyContent: "space-between", gap: 12 }}>
                <div>
                  <div style={{ fontFamily: "var(--font-display)", fontSize: ".95rem", color: "var(--color-ink)" }}>{w.title}</div>
                  <div style={{ fontFamily: "var(--font-mono)", fontSize: ".68rem", color: "var(--color-ink-subtle)", marginTop: 2 }}>
                    son düzenleme {w.updated}
                  </div>
                </div>
                <span className="status-pip" data-status={w.status}>{w.status === "draft" ? "Taslak" : "Zamanlandı"}</span>
              </li>
            ))}
          </ul>

          <header style={{ display: "flex", alignItems: "baseline", justifyContent: "space-between", margin: "24px 0 14px" }}>
            <h2 className="display-quiet" style={{ margin: 0 }}>Yeni mesajlar</h2>
            <LinkQuiet onClick={() => onNavigate("inbox")} className="text-sm">Gelen kutusu →</LinkQuiet>
          </header>
          <ul style={{ listStyle: "none", margin: 0, padding: 0, border: "1px solid var(--color-rule)", background: "var(--color-bg-elevated)" }}>
            {newMsgs.map((m) => (
              <li key={m.id} style={{ padding: "12px 14px", borderBottom: "1px solid var(--color-rule)" }}>
                <div style={{ display: "flex", justifyContent: "space-between", alignItems: "baseline", gap: 10 }}>
                  <div style={{ fontFamily: "var(--font-display)", fontSize: ".95rem", color: "var(--color-ink)", fontWeight: 600 }}>{m.name}</div>
                  <div style={{ fontFamily: "var(--font-mono)", fontSize: ".66rem", color: "var(--color-ink-subtle)" }}>{m.date.split(" ")[0].slice(0, 5)}</div>
                </div>
                <div style={{ fontFamily: "var(--font-display)", fontStyle: "italic", fontSize: ".88rem", color: "var(--color-ink-muted)", marginTop: 2 }}>{m.subject}</div>
              </li>
            ))}
          </ul>
        </aside>
      </div>
    </div>
  );
};

/* ─────────── Writings table ─────────── */
window.AdminWritings = function AdminWritings({ onNavigate }) {
  const [filter, setFilter] = React.useState("hepsi");
  const list = filter === "hepsi" ? ADMIN_WRITINGS : ADMIN_WRITINGS.filter(w => w.status === filter);
  const counts = {
    hepsi: ADMIN_WRITINGS.length,
    published: ADMIN_WRITINGS.filter(w => w.status === "published").length,
    draft: ADMIN_WRITINGS.filter(w => w.status === "draft").length,
    scheduled: ADMIN_WRITINGS.filter(w => w.status === "scheduled").length,
    archived: ADMIN_WRITINGS.filter(w => w.status === "archived").length,
  };

  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">İçerik · arşiv</Eyebrow>
          <h1 className="admin-page-title">Yazılar</h1>
          <p className="admin-page-sub">Saha yazıları, röportajlar, denemeler, notlar — tek bir yatakta.</p>
        </div>
        <div className="admin-page-actions">
          <Btn variant="secondary" size="sm">Dışa aktar</Btn>
          <Btn size="sm" onClick={() => onNavigate("compose")}>+ Yeni yazı</Btn>
        </div>
      </header>

      <div className="admin-table-wrap">
        <div className="admin-table-toolbar">
          <div className="filters">
            {[
              ["hepsi", "Hepsi"],
              ["published", "Yayında"],
              ["draft", "Taslak"],
              ["scheduled", "Zamanlanmış"],
              ["archived", "Arşivde"],
            ].map(([k, label]) => (
              <button key={k} className="chip" aria-pressed={filter === k} onClick={() => setFilter(k)}>
                {label} <span className="chip-count">{counts[k]}</span>
              </button>
            ))}
          </div>
          <div style={{ marginLeft: "auto" }}>
            <input className="admin-search" placeholder="Başlıkta ara…" />
          </div>
        </div>
        <table className="admin-table">
          <thead><tr>
            <th style={{ width: "40%" }}>Başlık</th>
            <th>Tür</th>
            <th>Durum</th>
            <th>Tarih</th>
            <th style={{ textAlign: "right" }}>Görüntülenme</th>
            <th aria-label="işlemler"></th>
          </tr></thead>
          <tbody>
            {list.map((w) => (
              <tr key={w.id}>
                <td>
                  <div className="col-title">{w.title}</div>
                  <div className="col-meta">{w.location} · son: {w.updated}</div>
                </td>
                <td className="col-num">{w.kind}</td>
                <td><span className="status-pip" data-status={w.status}>{
                  w.status === "published" ? "Yayında" :
                  w.status === "draft" ? "Taslak" :
                  w.status === "scheduled" ? "Zamanlandı" : "Arşivde"
                }</span></td>
                <td className="col-num">{w.date || "—"}</td>
                <td className="col-num" style={{ textAlign: "right" }}>{w.views ? w.views.toLocaleString("tr-TR") : "—"}</td>
                <td>
                  <div className="row-actions">
                    <button className="row-action" aria-label="Düzenle" onClick={() => onNavigate("compose")}>✎</button>
                    <button className="row-action" aria-label="Önizle">↗</button>
                    <button className="row-action" aria-label="Arşivle">⬒</button>
                    <button className="row-action" aria-label="Sil">×</button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};

/* ─────────── Compose form ─────────── */
window.AdminCompose = function AdminCompose({ onNavigate }) {
  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">Yeni yazı · taslak v1</Eyebrow>
          <h1 className="admin-page-title">Tezgâhın altı.</h1>
        </div>
        <div className="admin-page-actions">
          <span style={{ fontFamily: "var(--font-mono)", fontSize: ".7rem", color: "var(--color-ink-subtle)", letterSpacing: ".14em" }}>
            otomatik kayıt · 14:32
          </span>
          <Btn variant="secondary" size="sm">Önizle</Btn>
          <Btn variant="secondary" size="sm">Taslak kaydet</Btn>
          <Btn size="sm">Yayımla</Btn>
        </div>
      </header>

      <div className="compose-grid">
        <div className="compose-canvas">
          <div className="compose-card">
            <p className="compose-card-title">Eyebrow / kicker</p>
            <input className="input" placeholder="Saha · 016 dosya" defaultValue="Saha · 017 dosya" />
          </div>

          <div className="compose-card">
            <p className="compose-card-title">Başlık</p>
            <input className="compose-title-input" placeholder="Yazının başlığı (sentence case)" defaultValue="Tezgâhın altı" />
          </div>

          <div className="compose-card">
            <p className="compose-card-title">Standfirst (italik)</p>
            <textarea className="compose-standfirst-input" placeholder="Bir veya iki cümle. Yazının havasını verir." defaultValue="Bir öğleden sonra, kasanın altındaki defterin son sayfasında, hâlâ silinmemiş bir adres."></textarea>
          </div>

          <div className="compose-card" style={{ padding: 0 }}>
            <div style={{ padding: "16px 20px 0" }}>
              <p className="compose-card-title">Gövde</p>
            </div>
            <div className="compose-toolbar">
              <button className="compose-tool-btn"><b>B</b></button>
              <button className="compose-tool-btn"><i>I</i></button>
              <span className="compose-tool-sep" />
              <button className="compose-tool-btn">H2</button>
              <button className="compose-tool-btn">H3</button>
              <span className="compose-tool-sep" />
              <button className="compose-tool-btn">"  "</button>
              <button className="compose-tool-btn">— bölüm</button>
              <button className="compose-tool-btn">◨ foto</button>
              <span className="compose-tool-sep" />
              <button className="compose-tool-btn">↗ link</button>
            </div>
            <textarea
              className="compose-body-input"
              defaultValue={"HATAY — Sabah altıyı henüz geçmişti. Tezgâhın altındaki kasanın metal tıkırtısı, yan masada uyuyan kediden başka kimseyi rahatsız etmiyordu.\n\nNotlar acele tutulur; bu satırların yazıldığı kahve, başka bir kahveye dönüşmüş olacaktı.\n\n## Tezgâhın altı\n\nAsıl şehir, kasanın altında kuruluyordu."}
            />
          </div>
        </div>

        <aside className="compose-meta">
          <div className="compose-card">
            <p className="compose-card-title">Yayın</p>
            <Field label="Durum">
              <select className="input" defaultValue="draft">
                <option value="draft">Taslak</option>
                <option value="scheduled">Zamanlanmış</option>
                <option value="published">Yayımla</option>
                <option value="archived">Arşivle</option>
              </select>
            </Field>
            <Field label="Yayın tarihi">
              <input className="input" type="datetime-local" defaultValue="2026-04-28T09:00" />
            </Field>
            <Field label="Tür">
              <select className="input" defaultValue="saha_yazisi">
                <option value="saha_yazisi">Saha yazısı</option>
                <option value="roportaj">Röportaj</option>
                <option value="deneme">Deneme</option>
                <option value="not">Not</option>
              </select>
            </Field>
          </div>

          <div className="compose-card">
            <p className="compose-card-title">Künye</p>
            <Field label="Konum">
              <input className="input" placeholder="Hatay" defaultValue="Hatay" />
            </Field>
            <Field label="Okuma süresi (dk)" hint="Boş bırakılırsa otomatik hesaplanır.">
              <input className="input" type="number" defaultValue="9" />
            </Field>
            <Field label="Etiketler">
              <input className="input" placeholder="saha, defter, mahalle" defaultValue="saha, defter, mahalle" />
            </Field>
          </div>

          <div className="compose-card">
            <p className="compose-card-title">Kapak</p>
            <PhotoPlaceholder ratio="3/2" tone="darker" label="KAPAK YOK" subtitle="opsiyonel — tipografik kapak" />
            <Btn variant="secondary" size="sm" className="mt-3">Fotoğraf seç</Btn>
          </div>

          <div className="compose-card">
            <p className="compose-card-title">URL</p>
            <code style={{ fontFamily: "var(--font-mono)", fontSize: ".78rem", color: "var(--color-ink-muted)", display: "block", padding: "8px 10px", background: "var(--color-bg-muted)", borderRadius: "var(--radius-xs)" }}>
              /yazi/tezgahin-alti
            </code>
          </div>

          <div className="compose-card">
            <p className="compose-card-title">Açıklama metni</p>
            <p style={{ fontFamily: "var(--font-sans)", fontSize: ".85rem", color: "var(--color-ink-muted)", lineHeight: 1.5, margin: 0 }}>
              Saha yazıları için <strong>seyahat ve sponsorluk</strong> alanını doldurmayı unutmayın.
            </p>
            <Field label="Disclosure">
              <textarea className="input" rows="3" defaultValue="Bu yazı için seyahat ve konaklama kendi imkânlarımla karşılandı; herhangi bir kuruluş tarafından sponsorluk verilmedi."></textarea>
            </Field>
          </div>
        </aside>
      </div>
    </div>
  );
};

/* ─────────── Inbox ─────────── */
window.AdminInbox = function AdminInbox() {
  const [selected, setSelected] = React.useState(ADMIN_INBOX[0].id);
  const msg = ADMIN_INBOX.find(m => m.id === selected) || ADMIN_INBOX[0];

  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">İletişim · gelen kutusu</Eyebrow>
          <h1 className="admin-page-title">Mesaj defteri</h1>
          <p className="admin-page-sub">{ADMIN_INBOX.filter(m => m.status === "new").length} yeni mesaj. 48 saat içinde dönüş hedefi.</p>
        </div>
        <div className="admin-page-actions">
          <Btn variant="secondary" size="sm">Dışa aktar</Btn>
        </div>
      </header>

      <div className="inbox-grid">
        <div className="inbox-list">
          {ADMIN_INBOX.map((m) => (
            <button
              key={m.id}
              className="inbox-item"
              aria-selected={m.id === selected}
              onClick={() => setSelected(m.id)}
            >
              <div className="inbox-item-head">
                <span className="inbox-item-name">{m.name}</span>
                <span className="inbox-item-date">{m.date.split(" ")[0].slice(0, 5)}</span>
              </div>
              <div className="inbox-item-subj">{m.subject}</div>
              <div className="inbox-item-snip">{m.short}</div>
              <div className="inbox-item-meta">
                <span className="status-pip" data-status={m.status}>{m.status === "new" ? "Yeni" : "Yanıtlandı"}</span>
                <span style={{ fontFamily: "var(--font-mono)", fontSize: ".62rem", color: "var(--color-ink-subtle)", letterSpacing: ".14em", textTransform: "uppercase" }}>{m.topic}</span>
              </div>
            </button>
          ))}
        </div>

        <div className="inbox-detail">
          <div className="inbox-detail-head">
            <h3 className="inbox-detail-name">{msg.name}</h3>
            <p className="inbox-detail-meta">
              {msg.email} <span aria-hidden="true">·</span> {msg.date} <span aria-hidden="true">·</span> {msg.topic}
            </p>
          </div>
          <p className="inbox-detail-subj">{msg.subject}</p>
          <div className="inbox-detail-body">{msg.body}</div>
          <div className="inbox-actions">
            <Btn size="sm">Yanıtla</Btn>
            <Btn variant="secondary" size="sm">Yanıtlandı işaretle</Btn>
            <Btn variant="secondary" size="sm">Arşivle</Btn>
            <Btn variant="danger" size="sm">Sil</Btn>
          </div>
        </div>
      </div>
    </div>
  );
};

/* ─────────── Settings ─────────── */
window.AdminSettings = function AdminSettings() {
  const [tab, setTab] = React.useState("genel");
  const sections = [
    ["genel", "Genel"],
    ["site", "Site"],
    ["yayin", "Yayın varsayılanları"],
    ["seo", "SEO"],
    ["e-posta", "E‑posta"],
    ["yedekleme", "Yedekleme"],
  ];

  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">Site · ayarlar</Eyebrow>
          <h1 className="admin-page-title">Ayarlar</h1>
          <p className="admin-page-sub">Bu ayarlar tüm site yüzlerinde uygulanır.</p>
        </div>
      </header>

      <div className="settings-grid">
        <nav className="settings-nav" aria-label="Ayarlar bölümleri">
          {sections.map(([k, label]) => (
            <button key={k} className="settings-nav-item" aria-current={tab === k} onClick={() => setTab(k)}>
              {label}
            </button>
          ))}
        </nav>

        <div>
          <div className="settings-section">
            <h2 className="settings-section-title">Site kimliği</h2>
            <p className="settings-section-sub">Adres çubuğunda, RSS akışında ve OG kartlarında görünür.</p>
            <Field label="Site adı">
              <input className="input" defaultValue="Ozan Efeoğlu" />
            </Field>
            <Field label="Tagline" hint="Footer ve OG meta'da kullanılır.">
              <input className="input" defaultValue="Yazılı sözün ağırlığı." />
            </Field>
            <Field label="Birincil dil">
              <select className="input" defaultValue="tr">
                <option value="tr">Türkçe</option>
                <option value="en">İngilizce</option>
              </select>
            </Field>
          </div>

          <div className="settings-section">
            <h2 className="settings-section-title">Yayın varsayılanları</h2>
            <p className="settings-section-sub">Yeni yazılar bu ayarlarla başlar.</p>
            <Field label="Varsayılan tür">
              <select className="input" defaultValue="saha_yazisi">
                <option value="saha_yazisi">Saha yazısı</option>
                <option value="roportaj">Röportaj</option>
                <option value="deneme">Deneme</option>
                <option value="not">Not</option>
              </select>
            </Field>
            <div style={{ display: "flex", gap: 18, alignItems: "center", marginTop: 8 }}>
              <label style={{ display: "flex", alignItems: "center", gap: 8, fontSize: ".9rem", color: "var(--color-ink-muted)" }}>
                <input type="checkbox" defaultChecked /> Disclosure alanını zorunlu kıl
              </label>
              <label style={{ display: "flex", alignItems: "center", gap: 8, fontSize: ".9rem", color: "var(--color-ink-muted)" }}>
                <input type="checkbox" defaultChecked /> Yayım öncesi editör onayı iste
              </label>
            </div>
          </div>

          <div className="settings-section">
            <h2 className="settings-section-title">Yedekleme</h2>
            <p className="settings-section-sub">Veritabanı + dosyalar; günlük 04:00, S3 hedefi.</p>
            <p style={{ fontFamily: "var(--font-mono)", fontSize: ".78rem", color: "var(--color-ink-muted)", margin: "0 0 12px", letterSpacing: ".04em" }}>
              Son yedekleme: <strong style={{ color: "var(--color-ink)" }}>26.04.2026 04:00</strong> · 412 MB · ok
            </p>
            <Btn variant="secondary" size="sm">Şimdi yedekle</Btn>
          </div>
        </div>
      </div>
    </div>
  );
};

/* ─────────── Audit ─────────── */
window.AdminAudit = function AdminAudit() {
  return (
    <div className="admin-page">
      <header className="admin-page-header">
        <div>
          <Eyebrow className="mb-2">Site · denetim kaydı</Eyebrow>
          <h1 className="admin-page-title">Denetim kaydı</h1>
          <p className="admin-page-sub">Sondan başa, yedi günlük pencere. ASVS L2 kayıt.</p>
        </div>
        <div className="admin-page-actions">
          <select className="input" style={{ width: "auto" }}>
            <option>Son 7 gün</option>
            <option>Son 30 gün</option>
            <option>Son 90 gün</option>
          </select>
          <Btn variant="secondary" size="sm">CSV indir</Btn>
        </div>
      </header>

      <div className="audit-list">
        {ADMIN_AUDIT.map((row, i) => (
          <div className="audit-row" key={i}>
            <span className="audit-time">{row.time}</span>
            <span className="audit-actor">{row.actor}</span>
            <span className="audit-action"><b>{row.action}</b> · {row.target}</span>
          </div>
        ))}
      </div>
    </div>
  );
};
