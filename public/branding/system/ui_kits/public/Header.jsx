/* global React */

const NAV = [
  { key: "yazilar", label: "Yazılar" },
  { key: "goruntu", label: "Görüntü" },
  { key: "hakkinda", label: "Hakkında" },
  { key: "iletisim", label: "İletişim" },
];

window.PublicHeader = function PublicHeader({ route, onNavigate, theme = "light", onToggleTheme }) {
  const [scrolled, setScrolled] = React.useState(false);
  React.useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24);
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  return (
    <header
      className="public-header"
      data-scrolled={scrolled}
      style={{ paddingBlock: scrolled ? "12px" : "20px" }}
    >
      <div className="page-wrap" style={{ display: "flex", alignItems: "center", justifyContent: "space-between", gap: 24 }}>
        <BrandLockup onClick={() => onNavigate("home")} />

        <nav aria-label="Ana navigasyon" style={{ display: "flex", alignItems: "center", gap: 28 }}>
          {NAV.map((n) => (
            <a
              key={n.key}
              href="#"
              className="nav-link"
              aria-current={route === n.key ? "page" : "false"}
              onClick={(e) => { e.preventDefault(); onNavigate(n.key); }}
            >
              {n.label}
            </a>
          ))}
        </nav>

        <div style={{ display: "flex", alignItems: "center", gap: 8 }}>
          <IconBtn label={`Tema değiştir, şu an ${theme === "dark" ? "karanlık" : "aydınlık"}`} onClick={onToggleTheme}>
            {theme === "dark" ? "◑" : "◐"}
          </IconBtn>
          <Btn size="sm" onClick={() => onNavigate("admin")}>
            Masa <span aria-hidden="true" style={{ opacity: 0.6 }}>↗</span>
          </Btn>
        </div>
      </div>
    </header>
  );
};
