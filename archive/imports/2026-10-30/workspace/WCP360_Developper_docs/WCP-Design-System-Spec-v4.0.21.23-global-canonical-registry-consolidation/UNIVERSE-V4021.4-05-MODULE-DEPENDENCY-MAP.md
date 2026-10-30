# V4.0.21.4 --- V1 Module Dependency Map

## Primary dependency graph

`identity → authorization` `accounts → reseller/plans/quotas/secrets`
`web → webserver/php/tls/dns/files` `mail → dns/mail_security/webmail`
`database/cache → backup/secrets`
`apps/wordpress/node/python/ruby/go → web/files/database/git/workers`
`backup → storage/secrets/jobs`
`security/firewall → logs/notifications/jobs`
`demo/trial → plans/authorization/quotas`
`billing → accounts/plans/trial/api`
`migration → accounts/web/dns/mail/database/files/apps`
`installer/updates/system → managed services/security/readiness`

## Rule

Dependencies do not grant entitlements. They only express technical
prerequisites. A child operation still requires its own RBAC,
entitlement and health checks.

## Disable semantics

Disabling a parent capability with existing children must use the
existing non-destructive rules: `EXISTING_ONLY`, `NONCOMPLIANT`,
migration or explicit deletion workflow; never implicit destructive
cascade.
