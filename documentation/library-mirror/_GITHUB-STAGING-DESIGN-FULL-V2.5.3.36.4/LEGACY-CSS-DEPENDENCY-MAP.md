# Legacy CSS Dependency Map

Exactly **15** ACTIVE/CANONICAL pages still depend on `assets/styles.css`.

| Page | Current profile | Migration target / decision |
|---|---|---|
| `admin-dashboard.html` | LEGACY-COMPAT | canonical-admin-dashboard.html |
| `audit.html` | LEGACY-COMPAT | audit-explorer.html |
| `components.html` | LEGACY-COMPAT | Keep as design/reference utility; migrate shell only |
| `create-website.html` | LEGACY-COMPAT | Keep workflow; migrate shell only |
| `delete-resource.html` | LEGACY-COMPAT | Keep workflow; migrate shell only |
| `dns.html` | LEGACY-COMPAT | canonical-dns.html |
| `login.html` | LEGACY-COMPAT | Keep auth surface; migrate CSS dependencies only |
| `maintenance.html` | LEGACY-COMPAT | Keep WCP-ADMIN surface; migrate shell only |
| `mfa.html` | LEGACY-COMPAT | Keep auth surface; migrate CSS dependencies only |
| `reseller.html` | LEGACY-COMPAT | Keep reseller surface; migrate shell only |
| `restore-backup.html` | LEGACY-COMPAT | restore-wizard.html / restore-review.html |
| `ssl.html` | LEGACY-COMPAT | ssl-tls.html |
| `terminal.html` | LEGACY-COMPAT | Keep technical surface; migrate CSS dependencies only |
| `theme-system.html` | LEGACY-COMPAT | Keep theme reference; migrate CSS dependencies only |
| `websites.html` | LEGACY-COMPAT | Keep application list; migrate shell only |

## Migration rule
For each page:
1. compare against canonical replacement if one exists;
2. migrate structure/component classes to reference UI;
3. verify Light/Dark;
4. verify responsive behavior;
5. verify accessibility;
6. only then remove the legacy stylesheet.

## No-new-debt rule
A new ACTIVE page loading `styles.css` is a governance failure.