/* global React */

window.PublicFooter = function PublicFooter({ onNavigate }) {
  const year = new Date().getFullYear();
  return (
    <footer className="public-footer">
      <div className="page-wrap">
        <div className="footer-grid">
          <div>
            <BrandLockup onClick={() => onNavigate("home")} />
            <p className="mt-4" style={{
              fontSize: "var(--text-sm)",
              color: "var(--color-ink-muted)",
              lineHeight: 1.6,
              maxWidth: "44ch",
            }}>
              Foto‑muhabir, araştırmacı, yayıncı. İstanbul Aydın Üniversitesi'nde drone haberciliği üzerine yüksek lisans. Saha yazıları, röportajlar, denemeler.
            </p>
          </div>

          <div>
            <Eyebrow className="mb-3">Sayfalar</Eyebrow>
            <ul className="footer-list">
              <li><LinkQuiet onClick={() => onNavigate("yazilar")}>Yazılar</LinkQuiet></li>
              <li><LinkQuiet onClick={() => onNavigate("goruntu")}>Görüntü</LinkQuiet></li>
              <li><LinkQuiet onClick={() => onNavigate("hakkinda")}>Hakkında</LinkQuiet></li>
              <li><LinkQuiet onClick={() => onNavigate("iletisim")}>İletişim</LinkQuiet></li>
              <li><LinkQuiet>KVKK</LinkQuiet></li>
              <li><LinkQuiet>Gizlilik</LinkQuiet></li>
              <li><LinkQuiet>Künye</LinkQuiet></li>
              <li><LinkQuiet>RSS</LinkQuiet></li>
            </ul>
          </div>

          <div>
            <Eyebrow className="mb-3">İletişim</Eyebrow>
            <ul className="footer-list">
              <li>
                <a href="mailto:ozan@ozanefeoglu.com" className="link-quiet" style={{ fontFamily: "var(--font-mono)", fontSize: ".85rem" }}>
                  ozan@ozanefeoglu.com
                </a>
              </li>
              <li><LinkQuiet onClick={() => onNavigate("iletisim")}>Tüm kanallar →</LinkQuiet></li>
            </ul>
          </div>
        </div>

        <div className="footer-bottom">
          <span>© {year} Ozan Efeoğlu</span>
          <span className="right">OZANEFEOGLU.COM</span>
        </div>
      </div>
    </footer>
  );
};
