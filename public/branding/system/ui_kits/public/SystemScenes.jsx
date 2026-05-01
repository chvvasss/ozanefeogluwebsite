/* global React */

/* ─────────── 404 — sütun bulunamadı ─────────── */
window.NotFoundScene = function NotFoundScene({ onNavigate }) {
  return (
    <main className="scene" style={{ minHeight: "70vh", display: "flex", alignItems: "center" }}>
      <div className="page-wrap" style={{ width: "100%" }}>
        <div className="dossier-grid" style={{ alignItems: "center" }}>
          <div className="dg-7">
            <p style={{ fontFamily: "var(--font-mono)", fontSize: ".74rem", letterSpacing: ".22em", textTransform: "uppercase", color: "var(--color-accent)", margin: 0 }}>
              Hata · 404
            </p>
            <h1 className="display-statuesque mt-4" style={{ fontSize: "clamp(2.6rem, 8vw, 6.5rem)" }}>
              Bu sütun<br />bulunamadı.
            </h1>
            <Standfirst className="mt-6" style={{ maxWidth: "52ch" }}>
              Aradığınız yazı silinmiş, taşınmış ya da hiç yayımlanmamış olabilir. Bu sayfa, dizgicinin son provasında çıkardığı sütun gibidir — yerinde sadece beyaz alan kaldı.
            </Standfirst>
            <p className="mt-8" style={{ display: "flex", gap: 18, alignItems: "center", flexWrap: "wrap" }}>
              <Btn onClick={() => onNavigate("home")}>Anasayfaya dön</Btn>
              <LinkQuiet onClick={() => onNavigate("yazilar")}>Tüm yazılar →</LinkQuiet>
              <LinkQuiet onClick={() => onNavigate("iletisim")}>Bildir →</LinkQuiet>
            </p>
          </div>
          <div className="dg-4" style={{ gridColumnStart: 9 }}>
            <div style={{
              border: "1px solid var(--color-rule)", padding: "32px 28px",
              background: "var(--color-bg-elevated)",
              fontFamily: "var(--font-mono)", fontSize: ".8rem",
              lineHeight: 1.7, color: "var(--color-ink-muted)",
            }}>
              <p style={{ margin: 0, fontSize: ".62rem", letterSpacing: ".22em", textTransform: "uppercase", color: "var(--color-ink-subtle)" }}>Editör notu</p>
              <p style={{ margin: "12px 0 0" }}>
                URL: <span style={{ color: "var(--color-ink)" }}>/yazi/bilinmeyen</span><br />
                Durum: 404 not‑found<br />
                Zaman: 26.04.2026 14:32<br />
                Öneri: arşive göz atın
              </p>
            </div>
          </div>
        </div>
      </div>
    </main>
  );
};

/* ─────────── 500 — sakin hata ─────────── */
window.ServerErrorScene = function ServerErrorScene({ onNavigate }) {
  return (
    <main className="scene" style={{ minHeight: "70vh", display: "flex", alignItems: "center" }}>
      <div className="page-wrap" style={{ width: "100%" }}>
        <div style={{ maxWidth: "60ch" }}>
          <p style={{ fontFamily: "var(--font-mono)", fontSize: ".74rem", letterSpacing: ".22em", textTransform: "uppercase", color: "var(--color-danger)", margin: 0 }}>
            Hata · 500
          </p>
          <h1 className="display-statuesque mt-4" style={{ fontSize: "clamp(2.4rem, 7vw, 5.5rem)" }}>
            Mürekkep ıslak.
          </h1>
          <Standfirst className="mt-6">
            Sunucu bu sayfayı dizgi sırasında düşürdü. Birkaç dakika içinde matbaa yeniden açılacaktır. Acil bir durum varsa lütfen yazı işleriyle iletişime geçin.
          </Standfirst>
          <p className="mt-8" style={{ display: "flex", gap: 18, alignItems: "center", flexWrap: "wrap" }}>
            <Btn onClick={() => location.reload()}>Yeniden dene</Btn>
            <LinkQuiet onClick={() => onNavigate("home")}>Anasayfaya dön →</LinkQuiet>
            <LinkQuiet onClick={() => onNavigate("iletisim")}>Yazı işleri →</LinkQuiet>
          </p>
          <pre style={{
            marginTop: 36, padding: "12px 14px", background: "var(--color-bg-muted)",
            border: "1px solid var(--color-rule)", borderRadius: "var(--radius-xs)",
            fontFamily: "var(--font-mono)", fontSize: ".74rem", color: "var(--color-ink-muted)",
          }}>
{`request_id: 1f4a‑e8b2‑c9d0
timestamp:  2026‑04‑26T14:32:08+03:00
trace:      retained 14d`}
          </pre>
        </div>
      </div>
    </main>
  );
};

/* ─────────── Bakım modu ─────────── */
window.MaintenanceScene = function MaintenanceScene() {
  return (
    <main className="scene" style={{ minHeight: "100vh", display: "flex", alignItems: "center", background: "var(--layer-bg-darker)" }}>
      <div className="page-wrap" style={{ width: "100%", textAlign: "center", maxWidth: 720 }}>
        <BrandMark size={48} />
        <p className="mt-6" style={{ fontFamily: "var(--font-mono)", fontSize: ".74rem", letterSpacing: ".22em", textTransform: "uppercase", color: "var(--color-accent)" }}>
          Yazı işleri kapalı
        </p>
        <h1 className="display-statuesque mt-4" style={{ fontSize: "clamp(2.4rem, 6vw, 4.5rem)" }}>
          Tezgâh bakımda.
        </h1>
        <Standfirst className="mt-5" style={{ maxWidth: "50ch", marginInline: "auto" }}>
          Sayfaları yeniden diziyoruz. Tahmini açılış: bugün 18:00. Aciliyet halinde e‑posta açıktır.
        </Standfirst>
        <p className="mt-6">
          <a href="mailto:ozan@ozanefeoglu.com" className="link-quiet" style={{ fontFamily: "var(--font-mono)" }}>
            ozan@ozanefeoglu.com
          </a>
        </p>
        <p className="mt-10" style={{ fontFamily: "var(--font-mono)", fontSize: ".66rem", letterSpacing: ".18em", color: "var(--color-ink-subtle)", textTransform: "uppercase" }}>
          OZANEFEOGLU.COM · v1.0
        </p>
      </div>
    </main>
  );
};

/* ─────────── Hukuksal — KVKK / Gizlilik / Künye ortak gövde ─────────── */
function LegalLayout({ kicker, title, lede, sections, updated, onNavigate }) {
  return (
    <main>
      <section className="scene scene--tight">
        <div className="page-wrap">
          <p className="mb-4">
            <LinkQuiet onClick={() => onNavigate("home")} className="text-sm">← Anasayfa</LinkQuiet>
          </p>
          <Eyebrow className="mb-3">{kicker}</Eyebrow>
          <h1 className="display-statuesque" style={{ fontSize: "clamp(2.4rem, 6vw, 5rem)" }}>{title}</h1>
          <Standfirst className="mt-6" style={{ maxWidth: "55ch" }}>{lede}</Standfirst>
          <Dateline className="mt-6">Son güncelleme: {updated}</Dateline>
        </div>
      </section>

      <section className="scene scene--tight" style={{ paddingTop: 0 }}>
        <div className="page-wrap">
          <div className="dossier-grid">
            <nav className="dg-3" aria-label="Bölümler" style={{ position: "sticky", top: 100, alignSelf: "start" }}>
              <ol style={{ listStyle: "none", margin: 0, padding: 0, fontFamily: "var(--font-mono)", fontSize: ".72rem", letterSpacing: ".1em" }}>
                {sections.map((s, i) => (
                  <li key={i} style={{ padding: "8px 0", borderBottom: "1px solid var(--color-rule)" }}>
                    <a href={`#sec-${i + 1}`} className="link-quiet" style={{ display: "flex", gap: 10 }}>
                      <span className="tabular-nums" style={{ color: "var(--color-ink-subtle)" }}>{String(i + 1).padStart(2, "0")}</span>
                      <span>{s.title}</span>
                    </a>
                  </li>
                ))}
              </ol>
            </nav>
            <div className="dg-8" style={{ gridColumnStart: 5 }}>
              <div className="prose">
                {sections.map((s, i) => (
                  <section id={`sec-${i + 1}`} key={i}>
                    <h2>
                      <span style={{ fontFamily: "var(--font-mono)", fontSize: ".7em", letterSpacing: ".18em", color: "var(--color-ink-subtle)", marginRight: ".6em" }}>
                        {String(i + 1).padStart(2, "0")}
                      </span>
                      {s.title}
                    </h2>
                    {s.body.map((p, j) => <p key={j}>{p}</p>)}
                  </section>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  );
}

window.KvkkScene = function KvkkScene({ onNavigate }) {
  return <LegalLayout
    kicker="Hukuksal · 6698 sayılı KVKK"
    title="Aydınlatma metni."
    lede="Bu sayfa, ozanefeoglu.com'a iletilen kişisel verilerin hangi amaçla işlendiğini, ne kadar süreyle saklandığını ve haklarınızı sade bir dilde anlatır."
    updated="01.04.2026"
    onNavigate={onNavigate}
    sections={[
      { title: "Veri sorumlusu", body: [
        "Veri sorumlusu, bu sitenin tek operatörü Ozan Efeoğlu'dur. İletişim adresi: ozan@ozanefeoglu.com.",
        "Mesaj defteri formuyla paylaştığınız ad, e‑posta ve içerik metni; iletişim talebinizi yanıtlamak amacıyla işlenir, üçüncü taraflara aktarılmaz.",
      ]},
      { title: "İşlenen veriler", body: [
        "Yalnızca form alanlarında girdiğiniz veriler ve isteğin teknik kaydı (IP, tarayıcı, zaman damgası) işlenir.",
        "Çerez politikası gereği yalnızca sayfa tercihleri (örn. tema) tarayıcınızda saklanır; analiz veya reklam izleme çerezi yoktur.",
      ]},
      { title: "Saklama süresi", body: [
        "İletişim mesajları en fazla 24 ay saklanır, sonrasında otomatik olarak imha edilir.",
        "Teknik kayıtlar (audit log) 12 ay sonra anonimleştirilir.",
      ]},
      { title: "Haklarınız", body: [
        "KVKK 11. madde kapsamında verilerinize erişme, düzeltme ve silme hakkınız vardır. Talebinizi e‑posta ile iletebilirsiniz.",
        "Aydınlatma yükümlülüğü ile uyumsuzluk durumunda Kişisel Verileri Koruma Kurumu'na şikâyet hakkınız saklıdır.",
      ]},
      { title: "Değişiklikler", body: [
        "Bu metin yıllık olarak gözden geçirilir; önemli bir değişiklik halinde bu sayfada ve gerekirse e‑posta ile bildirilir.",
      ]},
    ]}
  />;
};

window.GizlilikScene = function GizlilikScene({ onNavigate }) {
  return <LegalLayout
    kicker="Hukuksal · gizlilik politikası"
    title="Gizlilik politikası."
    lede="Bu site, mümkün olan en az veriyi toplayacak şekilde inşa edilmiştir. Bu sayfa, neyin tutulduğunu ve neyin tutulmadığını listeler."
    updated="01.04.2026"
    onNavigate={onNavigate}
    sections={[
      { title: "Tutulmayanlar", body: [
        "Reklam izleme çerezi, üçüncü taraf analitik (Google Analytics dahil), parmak izi, lokasyon, sosyal medya pikseli — yok.",
        "Yorum sistemi, beğeni, paylaşım sayacı, dış komut yüklenen widget — yok.",
      ]},
      { title: "Tutulanlar", body: [
        "İletişim formu metni (24 ay) ve site sahibinin oturum bilgileri (admin paneli için).",
        "Teknik kayıtlar 12 ay sonra anonimleştirilir.",
      ]},
      { title: "Üçüncü taraflar", body: [
        "Barındırma: Hetzner GmbH (AB). Yedekleme: AWS S3 eu‑central‑1. E‑posta: kendi SMTP relay'i.",
        "Bu üç sağlayıcı dışında hiçbir veri paylaşımı yoktur.",
      ]},
      { title: "Çerezler", body: [
        "Yalnızca tercih çerezleri: tema seçimi (light/dark) ve sayfa kapasitesi tercihi. Hiçbiri kişisel kimliklendirme yapmaz.",
      ]},
    ]}
  />;
};

window.KunyeScene = function KunyeScene({ onNavigate }) {
  return (
    <main>
      <section className="scene scene--tight">
        <div className="page-wrap">
          <p className="mb-4">
            <LinkQuiet onClick={() => onNavigate("home")} className="text-sm">← Anasayfa</LinkQuiet>
          </p>
          <Eyebrow className="mb-3">Hukuksal · künye</Eyebrow>
          <h1 className="display-statuesque" style={{ fontSize: "clamp(2.4rem, 6vw, 5rem)" }}>Künye.</h1>
          <Standfirst className="mt-6" style={{ maxWidth: "55ch" }}>
            Bu yayının yazarı, barındırıcısı ve iletişim adresi — gazete sayfasının son sütununda dururdu, burada da öyle.
          </Standfirst>

          <div className="mt-10" style={{ display: "grid", gap: 8, maxWidth: 720 }}>
            <dl className="kunye" style={{ borderTop: "2px solid var(--color-ink)", paddingTop: "1.4rem" }}>
              <div className="kunye-row"><dt>Yayın adı</dt><dd>ozanefeoglu.com</dd></div>
              <div className="kunye-row"><dt>İmtiyaz sahibi</dt><dd>Ozan Efeoğlu</dd></div>
              <div className="kunye-row"><dt>Yazı işleri</dt><dd>Ozan Efeoğlu</dd></div>
              <div className="kunye-row"><dt>Tasarım</dt><dd>Editorial Silence v1.0</dd></div>
              <div className="kunye-row"><dt>Dizgi</dt><dd>Source Serif 4 · IBM Plex Sans/Mono</dd></div>
              <div className="kunye-row"><dt>Barındırma</dt><dd>Hetzner GmbH · Frankfurt</dd></div>
              <div className="kunye-row"><dt>Yedekleme</dt><dd>AWS S3 eu‑central‑1</dd></div>
              <div className="kunye-row"><dt>E‑posta</dt><dd>ozan@ozanefeoglu.com</dd></div>
              <div className="kunye-row"><dt>Posta</dt><dd>PK 016, Beyoğlu PTT, 34421 İstanbul</dd></div>
              <div className="kunye-row"><dt>Yayın kuralı</dt><dd>ADR‑016 · Field Dossier</dd></div>
              <div className="kunye-row"><dt>İlk yayın</dt><dd>2024.03 · v1.0 · 2026.04</dd></div>
            </dl>
          </div>

          <div className="disclosure-box mt-10" style={{ maxWidth: 720 }}>
            <p>
              <strong>Bağımsızlık beyanı.</strong> Bu yayında yer alan tüm yazılar, yazarın kendi imkânlarıyla üretilmiştir. Saha çalışmalarında dış destek alınmışsa, ilgili yazının künyesinde açıkça belirtilir. Bu yayın hiçbir kuruluşa bağlı değildir.
            </p>
          </div>
        </div>
      </section>
    </main>
  );
};

/* ─────────── Empty states (yazılar yokken / görüntü yokken) ─────────── */
window.EmptyStateDemo = function EmptyStateDemo({ onNavigate }) {
  return (
    <main>
      <section className="scene scene--tight">
        <div className="page-wrap">
          <Eyebrow className="mb-3">Boş durumlar · referans</Eyebrow>
          <h1 className="display-editorial">Henüz hiçbir şey yok.</h1>
          <Standfirst className="mt-5" style={{ maxWidth: "55ch" }}>
            Veri yüklenmemişse, içerik silinmişse ya da yeni bir koleksiyon başlatılmışsa kullanılan üç editöryal boş durum.
          </Standfirst>
        </div>
      </section>

      {/* Yazılar — boş */}
      <section className="scene scene--muted scene--tight">
        <div className="page-wrap">
          <Eyebrow className="mb-4">/yazılar — arşiv boşken</Eyebrow>
          <div style={{
            border: "1px dashed var(--color-rule-strong)", padding: "60px 40px",
            background: "var(--color-bg-elevated)", textAlign: "center",
            maxWidth: 640, marginInline: "auto",
          }}>
            <p style={{ fontFamily: "var(--font-mono)", fontSize: ".68rem", letterSpacing: ".22em", textTransform: "uppercase", color: "var(--color-ink-subtle)", margin: 0 }}>
              Defter henüz açılmadı
            </p>
            <h2 className="display-quiet mt-4">Bu rafta henüz yazı yok.</h2>
            <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", color: "var(--color-ink-muted)", marginTop: 12, fontSize: ".95rem" }}>
              İlk dosya yayımlandığında burası dolacak.
            </p>
          </div>
        </div>
      </section>

      {/* Görüntü — boş */}
      <section className="scene scene--tight">
        <div className="page-wrap">
          <Eyebrow className="mb-4">/görüntü — arşiv boşken</Eyebrow>
          <div className="contact-sheet">
            {[1, 2, 3, 4, 5, 6].map((i) => (
              <div key={i} className="contact-sheet-frame">
                <div style={{
                  aspectRatio: "3/2", border: "1px dashed var(--color-rule-strong)",
                  background: "var(--color-bg-muted)",
                  display: "flex", alignItems: "center", justifyContent: "center",
                  fontFamily: "var(--font-mono)", fontSize: ".62rem",
                  letterSpacing: ".22em", textTransform: "uppercase",
                  color: "var(--color-ink-subtle)",
                }}>
                  KARE YOK
                </div>
                <div className="contact-sheet-caption">
                  <span className="tabular-nums" style={{ color: "var(--color-ink-subtle)" }}>{String(i).padStart(2, "0")}</span>
                  <span>—</span>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Mesaj kutusu — boş */}
      <section className="scene scene--muted scene--tight">
        <div className="page-wrap">
          <Eyebrow className="mb-4">İletişim — mesaj defteri sessizken</Eyebrow>
          <div style={{
            border: "1px solid var(--color-rule)", padding: "48px 40px",
            background: "var(--color-bg-elevated)", maxWidth: 640,
            display: "flex", gap: 24, alignItems: "flex-start",
          }}>
            <div style={{ width: 6, alignSelf: "stretch", background: "var(--color-ink)" }} />
            <div>
              <h2 className="display-quiet" style={{ margin: 0 }}>Mesaj defteri sessiz.</h2>
              <p style={{ fontFamily: "var(--font-display)", fontStyle: "italic", color: "var(--color-ink-muted)", marginTop: 8 }}>
                Bugün yeni bir saha çağrısı yok. Önceki mesajlar arşivde tutulur.
              </p>
            </div>
          </div>
        </div>
      </section>
    </main>
  );
};
