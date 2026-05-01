/* global React */
const { useState } = React;

/* ─────────── primitives — editorial atoms ─────────── */

window.Eyebrow = ({ children, className = "" }) => (
  <p className={`eyebrow ${className}`}>{children}</p>
);

window.Kicker = ({ children, accent = false, className = "" }) => (
  <p className={`kicker ${accent ? "kicker--accent" : ""} ${className}`}>{children}</p>
);

window.DateSep = () => <span className="dateline-separator">·</span>;

window.Dateline = ({ children, className = "" }) => (
  <p className={`dateline ${className}`}>{children}</p>
);

window.Standfirst = ({ children, className = "" }) => (
  <p className={`standfirst ${className}`}>{children}</p>
);

window.PullQuote = ({ children, cite }) => (
  <blockquote className="pull-quote">
    {children}
    {cite ? <cite>{cite}</cite> : null}
  </blockquote>
);

window.LinkQuiet = ({ href = "#", onClick, children, className = "" }) => (
  <a
    href={href}
    onClick={(e) => { if (onClick) { e.preventDefault(); onClick(e); } }}
    className={`link-quiet ${className}`}
  >
    {children}
  </a>
);

window.Btn = ({ children, variant = "primary", size, onClick, className = "", ...rest }) => {
  const cls = [
    "btn",
    variant === "secondary" ? "btn--secondary" : "",
    variant === "danger" ? "btn--danger" : "",
    variant === "ghost" ? "btn--ghost" : "",
    size === "sm" ? "btn--sm" : "",
    size === "lg" ? "btn--lg" : "",
    className,
  ].filter(Boolean).join(" ");
  return <button onClick={onClick} className={cls} {...rest}>{children}</button>;
};

window.IconBtn = ({ children, onClick, label, className = "" }) => (
  <button className={`icon-btn ${className}`} onClick={onClick} aria-label={label}>
    {children}
  </button>
);

/* Brand mark · "oe." — yalın imza · 04 family */
window.BrandMark = ({ size = 28 }) => (
  <svg
    width={size}
    height={size}
    viewBox="0 0 64 64"
    aria-hidden="true"
    style={{ flexShrink: 0 }}
  >
    <text x="6" y="48"
      fontFamily="Source Serif 4 Variable, Source Serif 4, Charter, Iowan Old Style, Georgia, serif"
      fontWeight="600" fontSize="48" letterSpacing="-1.6" fill="var(--color-ink)">oe</text>
    <circle cx="49" cy="46" r="4.4" fill="#b91c1c" />
  </svg>
);

window.BrandLockup = ({ size = 28, onClick }) => (
  <a
    href="#"
    onClick={(e) => { e.preventDefault(); if (onClick) onClick(); }}
    style={{
      textDecoration: "none",
      display: "inline-flex",
      alignItems: "center",
      gap: 12,
      lineHeight: 1,
      color: "inherit",
    }}
  >
    <BrandMark size={size} />
    <span style={{ display: "inline-flex", flexDirection: "column", lineHeight: 1 }}>
      <span style={{
        fontFamily: "var(--font-display)",
        fontWeight: 600,
        fontSize: "1.05rem",
        color: "var(--color-ink)",
        letterSpacing: "var(--tracking-tight)",
      }}>
        Ozan Efeoğlu
      </span>
      <span style={{
        marginTop: 4,
        fontFamily: "var(--font-mono)",
        fontSize: "0.6rem",
        letterSpacing: "0.22em",
        textTransform: "uppercase",
        color: "var(--color-ink-subtle)",
      }}>
        OZANEFEOGLU.COM
      </span>
    </span>
  </a>
);

/* Photo placeholder — typographic empty cover  */
window.PhotoPlaceholder = ({ ratio = "3/2", label = "FOTOĞRAF EKLENMEDİ", subtitle, tone = "muted", style }) => {
  const bg = tone === "darker"
    ? "var(--color-paper-300)"
    : tone === "ink"
      ? "var(--color-ink)"
      : "var(--color-paper-200)";
  const fg = tone === "ink" ? "var(--color-paper-50)" : "var(--color-ink-muted)";
  return (
    <div
      style={{
        background: bg,
        color: fg,
        aspectRatio: ratio,
        display: "flex",
        flexDirection: "column",
        alignItems: "center",
        justifyContent: "center",
        gap: 10,
        fontFamily: "var(--font-mono)",
        fontSize: "0.7rem",
        letterSpacing: "0.22em",
        textTransform: "uppercase",
        border: "1px solid var(--color-rule)",
        ...style,
      }}
    >
      <span>{label}</span>
      {subtitle ? <span style={{ opacity: 0.7, fontSize: "0.62rem" }}>{subtitle}</span> : null}
    </div>
  );
};

/* Field — labeled form row */
window.Field = ({ label, hint, error, children }) => (
  <div className="field">
    {label ? <label className="field-label">{label}</label> : null}
    {children}
    {hint ? <span className="field-hint">{hint}</span> : null}
    {error ? <span className="field-error">{error}</span> : null}
  </div>
);

/* Photo with caption + dateline */
window.PhotoFigure = ({ tone, ratio, label, sublabel, location, credit, dateline }) => (
  <figure style={{ margin: 0 }}>
    <PhotoPlaceholder ratio={ratio} tone={tone} label={label} subtitle={sublabel} />
    <figcaption className="photo-caption" style={{ marginTop: 10, display: "flex", flexWrap: "wrap", gap: "0 14px" }}>
      {location ? <span className="photo-caption-loc">{location}</span> : null}
      {dateline ? <span>{dateline}</span> : null}
      {credit ? <span className="photo-caption-credit">© {credit}</span> : null}
    </figcaption>
  </figure>
);
