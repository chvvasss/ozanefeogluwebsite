# Fonts

The brand stipulates **self-hosted** webfonts (KVKK + LCP).

Required files (drop into this folder):

- `SourceSerif4Variable-Roman.woff2` — Adobe Source Serif 4 (SIL OFL 1.1)
- `SourceSerif4Variable-Italic.woff2`
- `IBMPlexSansVar-Roman.woff2` — IBM Plex Sans (SIL OFL 1.1)
- `IBMPlexMono-Regular.woff2`, `-Medium`, `-SemiBold`, `-Bold` — IBM Plex Mono

Sources:
- Source Serif 4: https://github.com/adobe-fonts/source-serif/tree/release/WOFF2
- IBM Plex: https://github.com/IBM/plex-sans / plex-mono

## Substitution flag

For prototype HTML cards in this design system, fonts are loaded from
`https://cdn.jsdelivr.net/...` for portability. **Production code must
use the self-hosted .woff2 files referenced in `colors_and_type.css`**.
