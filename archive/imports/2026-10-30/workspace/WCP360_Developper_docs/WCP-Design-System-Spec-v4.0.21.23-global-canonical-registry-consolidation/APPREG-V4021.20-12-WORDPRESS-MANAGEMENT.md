# V4.0.21.20 --- WordPress Management Registry

Resources cover discovered/managed instance, core version, plugin/theme
inventory, maintenance state, security findings, staging relation and
safe-update report.

Operations: `WP-DISCOVER`, `WP-ATTACH`, `WP-INSTALL`, `WP-CORE-UPDATE`,
`WP-PLUGIN-UPDATE`, `WP-THEME-UPDATE`, `WP-MAINTENANCE-SET`,
`WP-STAGING-CREATE`, `WP-SAFE-UPDATE-PLAN`, `WP-SAFE-UPDATE-RUN`,
`WP-SECURITY-CHECK`, `WP-ADMIN-SSO-LAUNCH`.

Filesystem/DB ownership is inherited from site/account contracts. Admin
SSO uses short-lived single-use authorization; stored WP admin password
is not required. Safe Update retains the
clone/test/risk/approval/production/rollback contract.
