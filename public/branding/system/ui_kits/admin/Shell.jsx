/* global React */

window.AdminSidebar = function AdminSidebar({ route, onNavigate, open, onClose }) {
  return (
    <aside className="admin-sidebar" data-open={open}>
      <a href="#" className="admin-sidebar-brand" onClick={(e) => { e.preventDefault(); onNavigate("dashboard"); }}>
        <BrandMark size={28} />
        <span className="admin-sidebar-brand-text">
          <span className="admin-sidebar-brand-name">Ozan Efeoğlu</span>
          <span className="admin-sidebar-brand-sub">YAZI MASASI</span>
        </span>
      </a>

      {ADMIN_NAV.map((group) => (
        <div className="admin-nav-group" key={group.label}>
          <p className="admin-nav-label">{group.label}</p>
          {group.items.map((it) => (
            <button
              key={it.key}
              className="admin-nav-item"
              aria-current={route === it.key ? "page" : "false"}
              onClick={() => { onNavigate(it.key); if (onClose) onClose(); }}
            >
              <span className="admin-nav-glyph" aria-hidden="true">{it.glyph}</span>
              <span>{it.title}</span>
              {it.count != null ? <span className="admin-nav-count">{it.count}</span> : null}
            </button>
          ))}
        </div>
      ))}

      <div className="admin-sidebar-footer">
        v1.0 · ADR‑016<br />
        Editorial Silence
      </div>
    </aside>
  );
};

window.AdminTopbar = function AdminTopbar({ crumbs, onToggleMenu, onNavigate }) {
  return (
    <div className="admin-topbar">
      <div className="admin-topbar-left">
        <button
          className="icon-btn"
          aria-label="Kenar çubuğunu aç"
          onClick={onToggleMenu}
          style={{ display: window.innerWidth < 880 ? "inline-flex" : "none" }}
        >
          ≡
        </button>
        <nav className="admin-crumbs" aria-label="Konum">
          {crumbs.map((c, i) => (
            <React.Fragment key={i}>
              {i > 0 ? <span aria-hidden="true">·</span> : null}
              <span className={i === crumbs.length - 1 ? "admin-crumbs-current" : ""}>{c}</span>
            </React.Fragment>
          ))}
        </nav>
      </div>
      <div className="admin-topbar-right">
        <input className="admin-search" placeholder="Yazılarda ara…" type="search" />
        <button className="icon-btn" aria-label="Bildirimler">✉</button>
        <a
          href="#"
          className="link-quiet"
          style={{ fontSize: ".82rem" }}
          onClick={(e) => { e.preventDefault(); onNavigate && onNavigate("__public"); }}
        >
          Siteyi gör <span aria-hidden="true">↗</span>
        </a>
        <span className="admin-avatar" aria-hidden="true">oe<span style={{color:"#b91c1c", marginLeft:1}}>.</span></span>
      </div>
    </div>
  );
};
