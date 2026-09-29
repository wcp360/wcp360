# Accessibility Runtime Gate

## Closed at prototype level
- [x] language attribute on active pages
- [x] explicit text direction
- [x] skip links
- [x] main content target
- [x] button types
- [x] aria-current on active navigation
- [x] basic form accessible naming
- [x] table captions
- [x] table header scopes
- [x] drawer/dialog semantics
- [x] dialog accessible naming
- [x] focus trap runtime support
- [x] focus restore runtime support
- [x] toast live-region support

## Still requires implementation-level validation
- [ ] real screen-reader test matrix
- [ ] browser-specific keyboard tests
- [ ] measured contrast for all states
- [ ] 200%/400% zoom and reflow
- [ ] mobile/touch target testing
- [ ] RTL full-product walkthrough
- [ ] error announcement behavior in production forms
- [ ] dynamic table/grid semantics where applicable

## Decision
Accessibility design/runtime gate is **CLOSED FOR PROTOTYPE**, not certified for production.

## Final static post-audit

All ACTIVE/CANONICAL pages now pass the static presence checks for `lang`, `dir`, skip link, `#main-content`, button `type`, table captions and table header scopes.