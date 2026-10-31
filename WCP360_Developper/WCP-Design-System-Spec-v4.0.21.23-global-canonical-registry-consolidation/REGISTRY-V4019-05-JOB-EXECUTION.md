# V4.0.19 — Canonical Job Execution Registry

## Statut
**NORMATIVE — V4.0.19 TRACEABILITY CLOSURE — NO CODE**

## Base
Cette passe ferme exclusivement le backlog P0 de `PLAN-V4018-18-CLOSURE-BACKLOG.md`. Elle n'ajoute aucune capability produit et ne crée aucun artefact exécutable.

## Règle de fermeture
Chaque cellule obligatoire est `PASS` ou `N/A` avec justification. Aucun `PARTIAL` P0 n'est accepté dans les registries V4.0.19.

## Cancellation
Cancellation reste coopérative et interdite après le point-of-no-return propre au Job. Un timeout avec effet externe possible devient `AMBIGUOUS/RECONCILE_REQUIRED`.

| Job type | Retry | Lock scope | RPC decision | Adapter | Verification |
|---|---|---|---|---|---|
| `account.provision` | `RECONCILE_BEFORE_RETRY` | `account` | UserCreate,DirectoryEnsure,QuotaApply,PermissionReconcile | filesystem/quota/systemd | verify uid/gid,path,quota |
| `account.suspend|resume|delete` | `RECONCILE_BEFORE_RETRY` | `account` | UserLock|UserUnlock|UserDelete | filesystem/systemd | observe identity/process access |
| `site.provision|reconfigure|delete` | `RECONCILE_BEFORE_RETRY` | `site` | ManagedFileInstall,ManagedGenerationActivate,ServiceReload | Nginx/PHP-FPM/filesystem | syntax+functional request |
| `php_pool.render_apply_verify` | `RECONCILE_BEFORE_RETRY` | `site` | ManagedFileInstall,ManagedGenerationActivate,ServiceReload | PHP-FPM | pool status/socket |
| `dns.zone.*` | `EXTERNAL_BACKOFF` | `dns-zone` | N/A: PowerDNS adapter API/DB path; ServiceStatus when local health needed | PowerDNS | authoritative query/serial |
| `certificate.*` | `EXTERNAL_BACKOFF` | `certificate` | ManagedFileInstall,PermissionReconcile,ServiceReload | ACME/certificate+Nginx | TLS handshake/cert match |
| `database.*` | `RECONCILE_BEFORE_RETRY` | `database` | N/A: DB adapter privileged credential, not OS root RPC | MariaDB | connect/object existence/restore checks |
| `database_user.*` | `RECONCILE_BEFORE_RETRY` | `database-user` | N/A: MariaDB adapter | MariaDB | grant/auth metadata check |
| `mail_domain.*|mailbox.*|mail_config.reconcile` | `RECONCILE_BEFORE_RETRY` | `mail` | ManagedFileInstall,ManagedGenerationActivate,DirectoryEnsure,PermissionReconcile,ServiceReload | Postfix/Dovecot/Rspamd/filesystem | config test+protocol/local delivery evidence |
| `backup.run` | `EXTERNAL_BACKOFF` | `backup` | N/A unless bounded filesystem helper required | backup local/S3 | manifest+checksum+remote object evidence |
| `restore.run` | `NO_AUTOMATIC_RETRY` | `restore` | typed filesystem/config RPC according dataset | restore + service adapters | post-restore functional verification |
| `cron.reconcile` | `RECONCILE_BEFORE_RETRY` | `cron` | ManagedFileInstall,ManagedGenerationActivate | scheduler/systemd or managed cron adapter | installed schedule observed |
| `integration.verify|notification.test` | `EXTERNAL_BACKOFF` | `integration` | N/A | provider adapter | provider response evidence |
| `security.scan` | `SAFE_RETRY` | `security` | N/A or bounded scanner adapter | security scanner | finding set/evidence |
| `repair.apply` | `RECONCILE_BEFORE_RETRY` | `repair` | typed RPC only for selected remediation | affected adapter | finding re-evaluation |
| `update.stage|apply|rollback` | `NO_AUTOMATIC_RETRY` | `system` | typed package/service helper only | release/update adapter | release+health/readiness |
| `service.reconcile` | `RECONCILE_BEFORE_RETRY` | `service` | ServiceStatus/Reload/Restart + managed config RPC | systemd + service adapter | desired vs observed |
| `readiness.evaluate` | `SAFE_RETRY` | `system` | ServiceStatus/ListenerInspect read-only where required | health/readiness | finding set |

## Fermeture
Chaque famille de Job CORE a retry class, lock scope, décision RPC/N/A, adapter et verification. **JOB EXECUTION GAP = CLOSED.**
