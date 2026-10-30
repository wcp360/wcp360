# V4.0.19 — Canonical API Action Registry

## Statut
**NORMATIVE — V4.0.19 TRACEABILITY CLOSURE — NO CODE**

## Base
Cette passe ferme exclusivement le backlog P0 de `PLAN-V4018-18-CLOSURE-BACKLOG.md`. Elle n'ajoute aucune capability produit et ne crée aucun artefact exécutable.

## Règle de fermeture
Chaque cellule obligatoire est `PASS` ou `N/A` avec justification. Aucun `PARTIAL` P0 n'est accepté dans les registries V4.0.19.

## Principes
Les chemins sont des contrats de ressource non exécutables. `SYNC_DB` signifie mutation control-plane sans effet root direct. Toute action système/externe durable utilise un Job. Les collections imbriquées héritent du tenant scope de leur parent.

| Resource | Action | Contract | Execution | Capability | Concurrency / Idempotency |
|---|---|---|---|---|---|
| `tenant` | `list/get` | `GET /tenants[/id]` | `SYNC` | `tenant.view` | GET safe/read-only |
| `tenant` | `create/update/delete` | `POST/PATCH/DELETE /tenants[/id]` | `SYNC_DB` | `tenant.manage` | transaction + resource version |
| `user` | `list/get` | `GET /users[/id]` | `SYNC` | `identity.view` | GET safe/read-only |
| `user` | `create/update/disable/delete` | `POST/PATCH/DELETE /users[/id]` | `SYNC_DB` | `identity.manage` | transaction + resource version |
| `role_binding` | `list/mutate` | `GET/POST/DELETE /role-bindings` | `SYNC_DB` | `rbac.manage` | transaction + resource version |
| `hosting_account` | `create` | `POST /accounts` | `JOB account.provision` | `account.create` | Idempotency-Key + resource version/precondition when mutating existing state |
| `hosting_account` | `suspend/resume` | `POST /accounts/id:suspend|resume` | `JOB account.suspend|resume` | `account.operate` | Idempotency-Key + resource version/precondition when mutating existing state |
| `hosting_account` | `delete` | `DELETE /accounts/id` | `JOB account.delete` | `account.delete` | Idempotency-Key + resource version/precondition when mutating existing state |
| `reseller` | `CRUD` | `/resellers` | `SYNC_DB` | `reseller.manage` | transaction + resource version |
| `plan` | `CRUD` | `/plans` | `SYNC_DB` | `plan.manage` | transaction + resource version |
| `entitlement` | `list/mutate` | `/plans/id/entitlements` | `SYNC_DB` | `plan.manage` | transaction + resource version |
| `domain` | `create/update/delete` | `/domains` | `JOB site.reconfigure + dns.zone.* when managed` | `domain.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `site` | `create/update/delete` | `/sites` | `JOB site.provision|reconfigure|delete` | `site.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `runtime_profile` | `get/update` | `/sites/id/runtime` | `JOB php_pool.render_apply_verify` | `site.runtime.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `certificate` | `issue/renew/revoke/verify` | `/certificates + actions` | `JOB certificate.*` | `certificate.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `dns_zone` | `CRUD/reconcile` | `/dns-zones + :reconcile` | `JOB dns.zone.*` | `dns.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `dns_record` | `CRUD` | `/dns-zones/id/records` | `JOB dns.zone.update` | `dns.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `database` | `create/delete/backup/restore` | `/databases + actions` | `JOB database.*` | `database.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `database_user` | `create/rotate/delete` | `/databases/id/users + actions` | `JOB database_user.*` | `database.user.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `mail_domain` | `create/delete` | `/mail/domains` | `JOB mail_domain.*` | `mail.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `mailbox` | `create/suspend/resume/delete/rotate` | `/mail/mailboxes + actions` | `JOB mailbox.*` | `mailbox.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `mail_alias` | `CRUD` | `/mail/aliases` | `JOB mail_config.reconcile` | `mail.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `mail_policy` | `get/update` | `/mail/policies` | `JOB mail_config.reconcile` | `mail.security.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `backup_policy` | `CRUD` | `/backups/policies` | `SYNC_DB` | `backup.policy.manage` | transaction + resource version |
| `backup_run` | `start/get` | `POST/GET /backups/runs` | `JOB backup.run` | `backup.run` | Idempotency-Key + resource version/precondition when mutating existing state |
| `restore_run` | `start/get` | `POST/GET /backups/restores` | `JOB restore.run` | `backup.restore` | Idempotency-Key + resource version/precondition when mutating existing state |
| `cron_job` | `CRUD` | `/cron-jobs` | `JOB cron.reconcile` | `cron.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `api_token` | `list/create/revoke` | `/api-tokens` | `SYNC_DB` | `token.manage` | transaction + resource version |
| `secret_ref` | `metadata/rotate` | `internal secret service` | `SYNC_DB or owning-resource JOB` | `secret.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `integration` | `CRUD/verify` | `/integrations + :verify` | `JOB integration.verify for external check; CRUD SYNC_DB` | `integration.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `job` | `get/list/retry/cancel` | `/jobs + actions` | `JOB control semantics` | `job.operate` | Idempotency-Key + resource version/precondition when mutating existing state |
| `event` | `list/get` | `/events` | `SYNC_READ` | `event.view` | GET safe/read-only |
| `audit_record` | `list/export` | `/audit` | `SYNC_READ/EXPORT_JOB if large` | `audit.view|audit.export` | Idempotency-Key + resource version/precondition when mutating existing state |
| `notification_route` | `CRUD/test` | `/notification-routes + :test` | `SYNC_DB + JOB notification.test` | `notification.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `security_finding` | `list/get/scan/repair` | `/security/findings + actions` | `JOB security.scan|repair.apply` | `security.manage` | Idempotency-Key + resource version/precondition when mutating existing state |
| `node` | `get/health/readiness/reconcile` | `/system/node,/health,/readiness` | `JOB readiness.evaluate|service.reconcile` | `system.view|system.operate` | Idempotency-Key + resource version/precondition when mutating existing state |

## Fermeture
Toutes les ressources CORE disposent désormais d'une surface API/service ou d'une surface interne explicitement justifiée. **API ACTION GAP = CLOSED.**
