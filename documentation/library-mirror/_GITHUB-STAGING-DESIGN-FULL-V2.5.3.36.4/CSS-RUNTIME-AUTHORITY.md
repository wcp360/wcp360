# WCP360 CSS Runtime Authority

## Canonical decision

### Primary active stylesheet
`assets/reference-ui.css`

This is the authoritative visual stylesheet for the locked WCP360 design language.

### Legacy compatibility stylesheet
`assets/styles.css`

This is **not** a second canonical design system. It remains only for a bounded set of legacy-compatible ACTIVE pages until migrated without visual regression.

## Cascade order for legacy-compatible pages
1. `assets/styles.css`
2. `assets/reference-ui.css`

The order must not be reversed casually.

## Style profiles
ACTIVE/CANONICAL pages carry:
- `data-wcp-style-profile="reference"`
- or `data-wcp-style-profile="legacy-compat"`

This metadata does not intentionally alter rendering.

## Invariants
1. No new ACTIVE page may depend on `assets/styles.css`.
2. New components belong in the reference design language.
3. Legacy pages migrate one by one.
4. Migration must preserve the locked V2.5.3.14/V2.5.3.12 visual baseline.
5. `styles.css` may be removed only when zero ACTIVE/CANONICAL pages reference it.
6. ARCHIVE pages may retain historical CSS for fidelity.