"""Responsive audit: 4 viewports x 5 routes."""
from playwright.sync_api import sync_playwright
from pathlib import Path
import json

OUT = Path("/tmp/responsive-audit")
OUT.mkdir(parents=True, exist_ok=True)

BASE = "http://127.0.0.1:8765"
ROUTES = [
    ("home", "/"),
    ("yazilar-index", "/yazilar"),
    ("yazilar-basin-karti", "/yazilar/basin-karti-bir-kalkan-degildir"),
    ("hakkimda", "/hakkimda"),
    ("iletisim", "/iletisim"),
    ("yazilar-bosalmayi", "/yazilar/bosalmayi-reddeden-bir-sehir"),
]
VIEWPORTS = [
    ("mobile-375", 375, 812),
    ("tablet-768", 768, 1024),
    ("desktop-1280", 1280, 800),
    ("wide-1440", 1440, 900),
]

DIAG_JS = r"""
() => {
  const body = document.body;
  const html = document.documentElement;
  const scrollW = Math.max(body.scrollWidth, html.scrollWidth);
  const clientW = html.clientWidth;
  const hasHScroll = scrollW > clientW + 1;

  // Find elements that overflow horizontally
  const overflowing = [];
  document.querySelectorAll('*').forEach((el) => {
    const r = el.getBoundingClientRect();
    if (r.right > clientW + 1 && r.width > 0 && r.width < 5000) {
      const tag = el.tagName.toLowerCase();
      const cls = (el.className && typeof el.className === 'string') ? el.className.slice(0,80) : '';
      overflowing.push({ tag, cls, right: Math.round(r.right), width: Math.round(r.width) });
    }
  });

  // Check interactive elements for tap target size
  const smallTargets = [];
  document.querySelectorAll('a, button, input[type=button], input[type=submit], [role=button]').forEach((el) => {
    const r = el.getBoundingClientRect();
    if (r.width === 0 || r.height === 0) return; // hidden
    if (r.width < 44 || r.height < 44) {
      const txt = (el.innerText || el.getAttribute('aria-label') || '').slice(0,40).replace(/\s+/g,' ').trim();
      smallTargets.push({ tag: el.tagName.toLowerCase(), w: Math.round(r.width), h: Math.round(r.height), txt });
    }
  });

  // Sample key text sizes
  const samples = {};
  const sel = (k, q) => {
    const el = document.querySelector(q);
    if (el) {
      const s = getComputedStyle(el);
      samples[k] = { fontSize: s.fontSize, lineHeight: s.lineHeight, fontFamily: s.fontFamily.split(',')[0] };
    }
  };
  sel('body', 'body');
  sel('h1', 'h1');
  sel('display-statuesque', '.display-statuesque');
  sel('display-editorial', '.display-editorial');
  sel('dossier-grid', '.dossier-grid');

  // Detect potential mobile drawer/nav
  const nav = document.querySelector('header, nav, .public-header');
  const navInfo = nav ? { tag: nav.tagName, overflows: nav.scrollWidth > nav.clientWidth } : null;

  return {
    viewport: { width: clientW, scrollW, hasHScroll },
    overflowCount: overflowing.length,
    overflowSample: overflowing.slice(0, 10),
    smallTargetCount: smallTargets.length,
    smallTargets: smallTargets.slice(0, 15),
    samples,
    navInfo,
  };
}
"""

results = {}

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    for vp_name, vw, vh in VIEWPORTS:
        ctx = browser.new_context(viewport={"width": vw, "height": vh}, device_scale_factor=1)
        page = ctx.new_page()
        for r_name, path in ROUTES:
            url = BASE + path
            try:
                page.goto(url, wait_until="networkidle", timeout=15000)
            except Exception as e:
                print(f"[WARN] {url} @ {vp_name}: {e}")
            page.wait_for_timeout(400)
            shot = OUT / f"{vp_name}_{r_name}.png"
            try:
                page.screenshot(path=str(shot), full_page=True)
            except Exception as e:
                print(f"[WARN] screenshot {shot}: {e}")
            try:
                diag = page.evaluate(DIAG_JS)
            except Exception as e:
                diag = {"error": str(e)}
            results.setdefault(vp_name, {})[r_name] = diag
            print(f"[OK] {vp_name} :: {r_name}  hScroll={diag.get('viewport',{}).get('hasHScroll')}  overflow={diag.get('overflowCount')}  smallTargets={diag.get('smallTargetCount')}")
        ctx.close()
    browser.close()

with open(OUT / "diag.json", "w", encoding="utf-8") as f:
    json.dump(results, f, indent=2, ensure_ascii=False)

print("DONE ->", OUT)
