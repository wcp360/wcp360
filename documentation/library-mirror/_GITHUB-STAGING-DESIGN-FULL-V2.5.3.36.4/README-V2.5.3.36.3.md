# WCP360 V2.5.3.36.3 — Accessibility & HTML Runtime Hardening

## Status
**PROTOTYPE HARDENING / NO PRODUCTION CODE / VISUAL STYLE LOCK PRESERVED**

## Applied to ACTIVE/CANONICAL HTML pages
- explicit `lang` and `dir`;
- skip link;
- `#main-content`;
- `type="button"` on buttons;
- `aria-current="page"` on active navigation/tabs;
- label association or fallback accessible name for form controls;
- table captions;
- `scope="col"` / `scope="row"`;
- dialog semantics for drawers;
- accessible dialog names;
- focus trap;
- focus restore;
- `aria-expanded` synchronization for common triggers;
- polite live-region support for toast containers.

## Scope
Pages processed: **173**

## Important
This closes a large markup gap identified by the Deep Audit, but it is still a static/prototype hardening pass.

Final WCAG 2.2 AA conformance still requires:
- browser testing;
- keyboard walkthroughs;
- screen readers;
- contrast measurements;
- zoom/reflow;
- mobile devices;
- production component semantics.

## Visual non-regression
No redesign was introduced.
V2.5.3.14/V2.5.3.12 restored visual language remains authoritative.