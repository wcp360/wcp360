# CSS Selector Debt Report

## Active runtime
- ACTIVE/CANONICAL pages: **173**
- Reference-only pages: **158**
- Legacy-compatible pages: **15**

## `assets/styles.css`
- lines: **1531**
- unique selectors: **427**
- duplicated selectors: **179**
- `!important`: **347**
- media queries: **18**

## `assets/reference-ui.css`
- lines: **1446**
- unique selectors: **978**
- duplicated selectors: **173**
- `!important`: **54**
- media queries: **44**

## Cross-file exact selector collisions
**43**

### Sample collisions
- `*`
- `.btn`
- `.btn-soft`
- `.btn-strong`
- `.btn.primary`
- `.card`
- `.cockpit-main`
- `.command-create`
- `.command-header`
- `.context-panel`
- `.dashboard-band`
- `.health-strip`
- `.identity-rail`
- `.main`
- `.muted`
- `.nav`
- `.nav.active`
- `.nav:hover`
- `.operational-surface`
- `.quick-action`
- `.resource-subtitle`
- `.resource-title`
- `.sidebar`
- `.small`
- `.state-ribbon`
- `.sub`
- `.surface`
- `.surface-meta`
- `.table td`
- `.table th`
- `.tiny`
- `.title`
- `.workspace-nav .nav`
- `.workspace-nav .nav.active`
- `.workspace-sidebar`
- `a`
- `body`
- `button`
- `html`
- `html[data-theme="dark"]`
- `html[data-theme="system"]`
- `input`
- `select`

## Decision
The debt is now bounded and governed. The next cleanup target is the finite legacy compatibility set, not a global rewrite.