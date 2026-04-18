# ADR-006 — İçerik Editörü: TipTap (Markdown export/import desteğiyle hibrit)

## Status
Accepted — 2026-04-18

## Context
Blog yazıları + proje case-study'leri için zengin editör gerekli. Adaylar:
- **Markdown-first** (CodeMirror/Monaco editör + Markdown render) — code-dostu, git-dostu.
- **TipTap** — Headless ProseMirror tabanlı, JSON state, Notion-vari.
- **EditorJS** — Block-based, Notion-benzeri, JSON state.
- **Quill / Trix** — Klasik WYSIWYG, daha basit.
- **Lexical** (Meta) — Yeni nesil, çok hızlı, yine framework-agnostic.

### Hedefler
- **Owner gayri-teknik:** Notion-vari WYSIWYG hissi, "ne görüyorsa o" beklentisi.
- **Code blok zengin:** Yazılarda syntax highlight gerekecek (Faz 5).
- **Görsel embed:** Image, alt text, caption, alignment.
- **Footnote, callout, pull-quote, code-group:** Custom block'lar gerekecek.
- **Markdown çıkış:** İçerik **owner'ın özgürlüğünde** olsun — yarın sistem değişirse Markdown export ile çıkar.
- **A11y:** Klavye erişimi, screen reader uyumu.
- **Bundle:** Editör admin-only, public bundle'a sızmasın. Admin için 100KB altı kabul edilir.

## Decision
**TipTap 3** (en güncel sürüm) headless rich-text editor.

İçerik kaynak gerçeği: **HTML** (TipTap'in serialize çıktısı), DB kolonu `body`. Yanı sıra `body_markdown` shadow kolonu otomatik export — owner istediğinde indirebilir, başka sisteme taşıyabilir.

Custom block'lar (callout, code-group, footnote) TipTap extension olarak yazılır.

## Consequences

### Pozitif
- **Modular:** TipTap'in extension API'sıyla istediğimiz block'ları ekleriz. Heading, image, link, table, code block, callout, embed — hepsi opt-in.
- **JSON intermediate:** TipTap'in document JSON'ı stabil; başka frontend'e taşırken render farklı, veri aynı.
- **A11y olgun:** ProseMirror tabanlı; klavye, ARIA iyi durumda.
- **Maintained:** TipTap Dev (firma + topluluk), aktif release.
- **Free tier yeterli:** Pro extension'lar var (collaboration, comments) — bizim ihtiyacımız değil.

### Negatif / Trade-off
- **Bundle:** TipTap core + StarterKit + bizim 5-6 extension ≈ 90KB gzipped. Admin-only yüklendiği için kabul.
- **HTML output gerektirir özen:** XSS riski sunucu tarafında HTMLPurifier ile sanitize.
- **Markdown ↔ HTML round-trip:** Custom block'lar (callout vs.) Markdown'da standart değil; biz HTML-comment marker veya MDX-vari syntax kullanırız (`> [!NOTE]` GitHub-flavored callout). Lossy export riski mevcut, doc'ta uyarılır.

### Risk
- **TipTap major upgrade:** v2 → v3 geçişinde extension API değişti. Karşı önlem: extension'larımız thin wrapper; upgrade ödevi makul.
- **HTML sanitization eksiği:** Server-side HTMLPurifier `mews/purifier` veya `ezyang/htmlpurifier` zorunlu. Whitelist tag/attr listesi: heading, p, ul, ol, li, blockquote, pre, code, a (href, rel, target=_blank), img (src, alt, width, height, loading), figure, figcaption, table (basic), strong, em, span (data-* for callout/code-group).

## Alternatives Considered

### Markdown-first (CodeMirror + render)
- **Pro:** Git-dostu, plain-text, lossless export.
- **Con:** Owner gayri-teknik; markdown syntax öğrenmek istemez. Image upload, embed UX zayıf.
- **Karar:** Reddedildi (ana editor olarak). Ama Markdown export var, gelecekte plug-in mode.

### EditorJS
- **Pro:** Block-based, JSON state, çok plugin.
- **Con:** Bundle daha büyük (~150KB), geliştirici aktivitesi yavaşladı, custom block yazımı TipTap'ten daha çetrefilli.
- **Karar:** Reddedildi.

### Quill / Trix
- **Pro:** Basit, küçük bundle.
- **Con:** Modern feature'lar (callout, embed, table) kısıtlı; özelleştirmesi pahalı.
- **Karar:** Reddedildi.

### Lexical (Meta)
- **Pro:** Çok hızlı, modüler.
- **Con:** Genç, ekosistem küçük. React-merkezli — vanilla kullanım docs zayıf.
- **Karar:** Reddedildi (şimdilik). Gelecekte yeniden değerlendirilebilir.

### Filament TipTap plugin (`awcodes/filament-tiptap-editor`)
- **Pro:** Hazır, Filament için pre-wired.
- **Con:** Filament'i admin için kullanmıyoruz (ADR-005). Bağımsız TipTap zaten doğrudan paketlenebilir.
- **Karar:** Reddedildi (Filament'a bağımlı olması).

## Görsel embed UX

Editör içi image insertion akışı:
1. Toolbar'da "Image" → modal açılır.
2. Tab 1: "Library" — mevcut medya kütüphanesinden seç (HTMX ile filtreli list).
3. Tab 2: "Upload" — drag-drop alanı; upload progress; alt text **zorunlu** field; kaydet → media library'ye girer.
4. Insert → TipTap node oluşur, `width`/`height` set, `loading="lazy"` default.

## Code block UX
- Toolbar "Code Block" → dil dropdown (TipTap CodeBlockLowlight extension + Lowlight + dil paketleri).
- Sunucu tarafında render Shiki ile (build-time veya request-time cache'lenmiş). Client'ta client-side highlight yapmıyoruz (bundle).

## Custom block örnekleri

```html
<!-- Callout -->
<div class="callout" data-type="info">
  <p>Lorem ipsum...</p>
</div>

<!-- Code group (tabs) -->
<div class="code-group">
  <pre data-lang="bash"><code>...</code></pre>
  <pre data-lang="zsh"><code>...</code></pre>
</div>

<!-- Footnote -->
<sup><a href="#fn-1" id="fnref-1">1</a></sup>
...
<ol class="footnotes">
  <li id="fn-1">Note text. <a href="#fnref-1">↩</a></li>
</ol>
```

## References
- TipTap docs: https://tiptap.dev/docs/editor
- ProseMirror: https://prosemirror.net/
- HTMLPurifier whitelist guide: http://htmlpurifier.org/docs

## İlgili ADR'lar
- ADR-005: Admin yaklaşımı (TipTap admin-only)
- ADR-007: Medya yönetimi (image insert flow)
