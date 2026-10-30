# V4.0.19 — Full CORE V1 Traceability Matrix

## Statut
**NORMATIVE — V4.0.19 TRACEABILITY CLOSURE — NO CODE**

## Base
Cette passe ferme exclusivement le backlog P0 de `PLAN-V4018-18-CLOSURE-BACKLOG.md`. Elle n'ajoute aucune capability produit et ne crée aucun artefact exécutable.

## Règle de fermeture
Chaque cellule obligatoire est `PASS` ou `N/A` avec justification. Aucun `PARTIAL` P0 n'est accepté dans les registries V4.0.19.

## Chain
`Resource → Action → API/service → Capability → Job/N/A → RPC/N/A → Adapter/N/A → Audit/Event → Acceptance`.

| Resource | Action | API/service | Capability | Execution | RPC/Adapter decision | Audit | Acceptance | Result |
|---|---|---|---|---|---|---|---|---|---|
| `tenant` | `list/get` | `GET /tenants[/id]` | `tenant.view` | `SYNC` | N/A mutation RPC — read/control semantics | `tenant.view` | `SEC-ACCEPT-001` | **PASS** |
| `tenant` | `create/update/delete` | `POST/PATCH/DELETE /tenants[/id]` | `tenant.manage` | `SYNC_DB` | N/A root RPC — control-plane transaction only | `tenant.mutate` | `E2E-ACCEPT-001` | **PASS** |
| `user` | `list/get` | `GET /users[/id]` | `identity.view` | `SYNC` | N/A mutation RPC — read/control semantics | `identity.view` | `SEC-ACCEPT-002` | **PASS** |
| `user` | `create/update/disable/delete` | `POST/PATCH/DELETE /users[/id]` | `identity.manage` | `SYNC_DB` | N/A root RPC — control-plane transaction only | `identity.mutate` | `SEC-ACCEPT-003` | **PASS** |
| `role_binding` | `list/mutate` | `GET/POST/DELETE /role-bindings` | `rbac.manage` | `SYNC_DB` | N/A root RPC — control-plane transaction only | `rbac.binding.mutate` | `SEC-ACCEPT-004` | **PASS** |
| `hosting_account` | `create` | `POST /accounts` | `account.create` | `JOB account.provision` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `account.create` | `E2E-ACCEPT-002` | **PASS** |
| `hosting_account` | `suspend/resume` | `POST /accounts/id:suspend|resume` | `account.operate` | `JOB account.suspend|resume` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `account.state.change` | `E2E-ACCEPT-003` | **PASS** |
| `hosting_account` | `delete` | `DELETE /accounts/id` | `account.delete` | `JOB account.delete` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `account.delete` | `E2E-ACCEPT-004` | **PASS** |
| `reseller` | `CRUD` | `/resellers` | `reseller.manage` | `SYNC_DB` | N/A root RPC — control-plane transaction only | `reseller.mutate` | `SEC-ACCEPT-005` | **PASS** |
| `plan` | `CRUD` | `/plans` | `plan.manage` | `SYNC_DB` | N/A root RPC — control-plane transaction only | `plan.mutate` | `E2E-ACCEPT-005` | **PASS** |
| `entitlement` | `list/mutate` | `/plans/id/entitlements` | `plan.manage` | `SYNC_DB` | N/A root RPC — control-plane transaction only | `entitlement.mutate` | `E2E-ACCEPT-005` | **PASS** |
| `domain` | `create/update/delete` | `/domains` | `domain.manage` | `JOB site.reconfigure + dns.zone.* when managed` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `domain.mutate` | `E2E-ACCEPT-006` | **PASS** |
| `site` | `create/update/delete` | `/sites` | `site.manage` | `JOB site.provision|reconfigure|delete` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `site.mutate` | `E2E-ACCEPT-007` | **PASS** |
| `runtime_profile` | `get/update` | `/sites/id/runtime` | `site.runtime.manage` | `JOB php_pool.render_apply_verify` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `runtime.mutate` | `E2E-ACCEPT-008` | **PASS** |
| `certificate` | `issue/renew/revoke/verify` | `/certificates + actions` | `certificate.manage` | `JOB certificate.*` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `certificate.action` | `E2E-ACCEPT-009` | **PASS** |
| `dns_zone` | `CRUD/reconcile` | `/dns-zones + :reconcile` | `dns.manage` | `JOB dns.zone.*` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `dns.zone.mutate` | `E2E-ACCEPT-010` | **PASS** |
| `dns_record` | `CRUD` | `/dns-zones/id/records` | `dns.manage` | `JOB dns.zone.update` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `dns.record.mutate` | `E2E-ACCEPT-010` | **PASS** |
| `database` | `create/delete/backup/restore` | `/databases + actions` | `database.manage` | `JOB database.*` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `database.action` | `E2E-ACCEPT-011` | **PASS** |
| `database_user` | `create/rotate/delete` | `/databases/id/users + actions` | `database.user.manage` | `JOB database_user.*` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `database.user.action` | `SEC-ACCEPT-006` | **PASS** |
| `mail_domain` | `create/delete` | `/mail/domains` | `mail.manage` | `JOB mail_domain.*` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `mail.domain.mutate` | `E2E-ACCEPT-012` | **PASS** |
| `mailbox` | `create/suspend/resume/delete/rotate` | `/mail/mailboxes + actions` | `mailbox.manage` | `JOB mailbox.*` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `mailbox.action` | `E2E-ACCEPT-013` | **PASS** |
| `mail_alias` | `CRUD` | `/mail/aliases` | `mail.manage` | `JOB mail_config.reconcile` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `mail.alias.mutate` | `E2E-ACCEPT-014` | **PASS** |
| `mail_policy` | `get/update` | `/mail/policies` | `mail.security.manage` | `JOB mail_config.reconcile` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `mail.policy.mutate` | `SEC-ACCEPT-007` | **PASS** |
| `backup_policy` | `CRUD` | `/backups/policies` | `backup.policy.manage` | `SYNC_DB` | N/A root RPC — control-plane transaction only | `backup.policy.mutate` | `E2E-ACCEPT-015` | **PASS** |
| `backup_run` | `start/get` | `POST/GET /backups/runs` | `backup.run` | `JOB backup.run` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `backup.run` | `E2E-ACCEPT-016` | **PASS** |
| `restore_run` | `start/get` | `POST/GET /backups/restores` | `backup.restore` | `JOB restore.run` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `restore.run` | `SEC-ACCEPT-008` | **PASS** |
| `cron_job` | `CRUD` | `/cron-jobs` | `cron.manage` | `JOB cron.reconcile` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `cron.mutate` | `E2E-ACCEPT-017` | **PASS** |
| `api_token` | `list/create/revoke` | `/api-tokens` | `token.manage` | `SYNC_DB` | N/A root RPC — control-plane transaction only | `token.mutate` | `SEC-ACCEPT-009` | **PASS** |
| `secret_ref` | `metadata/rotate` | `internal secret service` | `secret.manage` | `SYNC_DB or owning-resource JOB` | N/A mutation RPC — read/control semantics | `secret.rotate` | `SEC-ACCEPT-010` | **PASS** |
| `integration` | `CRUD/verify` | `/integrations + :verify` | `integration.manage` | `JOB integration.verify for external check; CRUD SYNC_DB` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `integration.mutate` | `SEC-ACCEPT-011` | **PASS** |
| `job` | `get/list/retry/cancel` | `/jobs + actions` | `job.operate` | `JOB control semantics` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `job.action` | `E2E-ACCEPT-018` | **PASS** |
| `event` | `list/get` | `/events` | `event.view` | `SYNC_READ` | N/A mutation RPC — read/control semantics | `event.view` | `SEC-ACCEPT-012` | **PASS** |
| `audit_record` | `list/export` | `/audit` | `audit.view|audit.export` | `SYNC_READ/EXPORT_JOB if large` | N/A mutation RPC — read/control semantics | `audit.access` | `SEC-ACCEPT-013` | **PASS** |
| `notification_route` | `CRUD/test` | `/notification-routes + :test` | `notification.manage` | `SYNC_DB + JOB notification.test` | N/A mutation RPC — read/control semantics | `notification.route.mutate` | `E2E-ACCEPT-019` | **PASS** |
| `security_finding` | `list/get/scan/repair` | `/security/findings + actions` | `security.manage` | `JOB security.scan|repair.apply` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `security.finding.action` | `SEC-ACCEPT-014` | **PASS** |
| `node` | `get/health/readiness/reconcile` | `/system/node,/health,/readiness` | `system.view|system.operate` | `JOB readiness.evaluate|service.reconcile` | Job registry → typed RPC when OS-root needed; otherwise named service/provider adapter; verification mandatory | `system.node.action` | `E2E-ACCEPT-020` | **PASS** |

## Verdict
Aucune ligne CORE ne contient un `PARTIAL` ou un blanc inexpliqué. Les `N/A` sont réservés aux opérations control-plane/read qui ne doivent précisément **pas** franchir la frontière root. **CORE TRACEABILITY = CLOSED.**
