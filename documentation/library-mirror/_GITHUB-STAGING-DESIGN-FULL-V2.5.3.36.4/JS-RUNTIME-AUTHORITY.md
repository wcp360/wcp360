# JavaScript Runtime Authority
## Decision

`assets/reference-ui.js` is the **only active prototype runtime** for current WCP360 surfaces.

`assets/app.js` is retained only as historical/legacy material and MUST NOT be loaded by ACTIVE or CANONICAL pages.

## Pages corrected in V2.5.3.36.2
- `admin-dashboard.html`
- `audit.html`
- `components.html`
- `create-website.html`
- `delete-resource.html`
- `dns.html`
- `maintenance.html`
- `reseller.html`
- `restore-backup.html`
- `ssl.html`
- `terminal.html`
- `theme-system.html`
- `websites.html`

## Ownership
`reference-ui.js` owns:
- Command Center;
- theme;
- drawers;
- notifications;
- profile popover;
- Job Center;
- Create drawer;
- density;
- table filtering;
- toasts;
- operations dock;
- keyboard shortcuts.

## Accessibility follow-up
A later markup/runtime hardening pass must still implement:
- focus trap;
- focus restore;
- `aria-expanded`;
- `aria-controls`;
- drawer accessible names;
- live-region semantics.