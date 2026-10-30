# V4.0.18 — CORE V1 Resource Traceability Matrix

## Statut
**NORMATIVE AUDIT — V4.0.18 — NO CODE**

## Base auditée
V4.0.17 et ses 30 contrats `IMPL-4017-*`. Cette passe n'invente pas les mappings absents : `PASS` signifie explicitement supporté par le corpus ; `PARTIAL` signifie que la surface existe mais que la chaîne n'est pas fermée ; `N/A` doit être justifié ; `BLOCKED` empêche le Repository Entry Gate.

| Resource | API/service | Job | wcpd RPC | Adapter | Capability | Audit | Result | Finding |
|---|---|---|---|---|---|---|---|---|---|
| tenant | /tenants | DB-only lifecycle | N/A | tenancy/control-plane | tenant.* | audit | PARTIAL | API exists; per-action capability/test mapping not enumerated |
| user | /auth,/me + identity surfaces | identity mutation | N/A | identity/control-plane | identity.* | audit | PARTIAL | No complete admin user API/action matrix in IMPL-4017-06 |
| role_binding | identity/RBAC service | DB-only | N/A | control-plane | rbac.* | audit | PARTIAL | No explicit REST resource path or acceptance row |
| hosting_account | /accounts | account.provision/suspend/resume/delete | UserCreate/UserLock/UserUnlock/UserDelete,QuotaApply | filesystem/quota/systemd | account.* | audit | PASS | End-to-end provisioning path explicitly covered |
| reseller | /resellers | DB/control-plane + descendants | N/A | control-plane | reseller.* | audit | PARTIAL | Hierarchy is specified, per-action mapping incomplete |
| plan | /plans | DB/control-plane | N/A | control-plane | plan.* | audit | PARTIAL | Entitlement/quota propagation job semantics not enumerated |
| entitlement | plan/account service | DB/control-plane | N/A | control-plane | entitlement.* | audit | PARTIAL | No explicit endpoint/action/test mapping |
| domain | /domains | resource mutation; DNS/TLS jobs as needed | typed via DNS/config RPC | PowerDNS/Nginx/ACME | domain.* | audit | PARTIAL | Domain lifecycle job names are not explicit in Job Catalog |
| site | /sites | site.provision/reconfigure/delete | ManagedFileInstall,ManagedGenerationActivate,ServiceReload | Nginx/PHP-FPM/filesystem | site.* | audit | PASS | Core path is explicit |
| runtime_profile | site/runtime service | php_pool.render_apply_verify | ManagedGenerationActivate,ServiceReload | PHP-FPM | runtime.* | audit | PARTIAL | No explicit REST resource path/capability matrix |
| certificate | /certificates | certificate.issue/renew/revoke/deploy/verify | config/file/service typed RPC | ACME/Nginx | certificate.* | audit | PASS | Lifecycle jobs explicit |
| dns_zone | /dns-zones | dns.zone.create/update/delete/reconcile | managed config/service RPC as required | PowerDNS | dns.* | audit | PASS | Explicit API family + jobs + adapter |
| dns_record | nested DNS service | dns.zone.update/reconcile | N/A/adapter path | PowerDNS | dns.* | audit | PARTIAL | Record-specific action/locking/audit expectations not enumerated |
| database | /databases | database.create/delete/backup/restore | typed service/credential operations | MariaDB | database.* | audit | PASS | Explicit jobs and adapter |
| database_user | database nested service | database_user.create/rotate/delete | typed credential/service operation | MariaDB | database_user.* | audit | PARTIAL | REST resource path and capability names not fully enumerated |
| mail_domain | /mail | mail_domain.provision/delete | managed config/service RPC | Postfix/Dovecot/Rspamd | mail.* | audit | PASS | Mail lifecycle explicitly covered |
| mailbox | /mail | mailbox.create/suspend/resume/delete/password_rotate | typed mail/filesystem/service operations | Postfix/Dovecot/Rspamd | mailbox.* | audit | PASS | Lifecycle jobs explicit |
| mail_alias | /mail | mail_config.reconcile | managed generation/service RPC | Postfix | mail.* | audit | PARTIAL | Alias-specific action and tests not enumerated |
| mail_policy | /mail,/security | mail_config.reconcile | managed generation/service RPC | Rspamd/Postfix/Dovecot | mail/security.* | audit | PARTIAL | Policy precedence and per-action trace not closed here |
| backup_policy | /backups | policy DB mutation | N/A | backup adapter | backup.* | audit | PARTIAL | Policy CRUD vs execution mapping not enumerated |
| backup_run | /backups | backup.run | typed filesystem/service operations | local/S3-compatible | backup.* | audit | PASS | Execution + failure path covered |
| restore_run | /backups + restore action | restore.run | typed filesystem/service operations | restore/service adapters | backup.restore | audit | PASS | Restore authorization explicitly security-tested |
| cron_job | /cron-jobs | scheduler/config mutation | typed managed file/systemd equivalent if used | scheduler/system | cron.* | audit | PARTIAL | No explicit Job/RPC catalog entry |
| api_token | identity/API service | DB-only token lifecycle | N/A | control-plane | token.* | audit | PARTIAL | Security behavior specified; full CRUD/action trace absent |
| secret_ref | internal/secret service | secret rotation/access workflow | N/A unless system deployment | secret store | secret.* | audit | PARTIAL | Exposure/rotation contract exists, endpoint mapping intentionally restricted |
| integration | /integrations | provider-specific jobs | N/A/provider adapter | external adapters | integration.* | audit | PARTIAL | Generic integration lifecycle/retry/test mapping incomplete |
| job | /jobs | job retry/cancel semantics | N/A | worker/wcpd boundary | job.* | audit | PASS | State/retry/locking contract explicit |
| event | /events | outbox dispatcher | N/A | event/outbox | event.* | audit | PASS | Event/outbox contract explicit |
| audit_record | /audit | append/checkpoint/export | N/A | audit subsystem | audit.view/export | audit | PASS | Audit contract explicit |
| notification_route | operations/notification service | delivery job implied | N/A/provider adapter | notification adapters | notification.* | audit | PARTIAL | No canonical Job/API/action catalog mapping |
| security_finding | /security | security.scan/repair.apply | typed repair RPC when needed | security adapters | security.* | audit | PASS | Scan/repair and release-blocking tests defined |
| node | /system,/health,/readiness | readiness.evaluate/service.reconcile | ServiceStatus etc. | systemd/health | system.* | audit | PARTIAL | Single-node resource exists; explicit node CRUD/action map intentionally minimal |

## Résumé
`PASS` n'est attribué que lorsque la chaîne est suffisamment explicite dans V4.0.17. `PARTIAL` ne signifie pas que la fonctionnalité est absente : il signifie que le contrat d'implémentation n'est pas encore assez déterministe pour le gate.
