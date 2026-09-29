# Runtime Normalization

## JavaScript
Canonical active runtime:
- `assets/reference-ui.js`

Legacy runtime:
- `assets/app.js` retained only for history/archive.

ACTIVE/CANONICAL pages must not load both.

## CSS
Canonical:
- `assets/reference-ui.css`

Legacy compatibility:
- `assets/styles.css` on the bounded migration list only.

## Keyboard ownership
`reference-ui.js` owns global active keyboard behavior.

## Data attribute
`data-wcp-style-profile` documents the page's style profile.

## Future implementation rule
Before repository skeleton/code, runtime assets should be machine-checkable and no ACTIVE page may introduce undeclared runtime dependencies.