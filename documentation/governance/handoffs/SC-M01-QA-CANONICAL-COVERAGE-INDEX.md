# WCP360 — SC-M01 Canonical QA Coverage Index

**Status:** DERIVED EVIDENCE — NON-NORMATIVE — NOT THE FINAL TEST BASELINE  
**Generated from main:** `4ff664a3157e662c174260e1fd35429738188f0c`  
**Purpose:** eliminate manual recounting and give TESTS / QA an exact source-derived coverage inventory.

## Verification summary

```text
API contracts                  = 86
unique TEST-API references     = 86
IPC contracts                  = 47
unique TEST-IPC references     = 47
active permissions             = 82
permissions with API bindings  = 82
missing active permission      = 0
unknown API permission         = 0
API TEST suffix mismatches     = 0
IPC TEST suffix mismatches     = 0
```

Source blobs:

- API 1.0: `4893c5191b4928ecfa5a70f4dc14b39a8939fcea`
- IPC WCP360-IPC-B/1: `fec9c74e8413db822662e47c76c40486e282334f`
- Permission Registry v1.0: `81f3d425f36470167aba4bfb7ba069268626342f`

This index proves identifier/reference coverage only. It does **not** prove executable QA coverage, expected outcomes, transport completeness, DB assertions, or test execution.

## API → TEST-API coverage

| Contract | Test | Surface | Auth | Permission | Action | Resource | Timeout | Retry | Idempotency |
|---|---|---|---|---|---|---|---|---|---|
| `API-WCP-0101` | `TEST-API-0101` | WCP-USER | `AUTH-U` | `applications.read` | `applications.read` | `application` | 10s | `RR` | `none` |
| `API-WCP-0102` | `TEST-API-0102` | WCP-USER | `AUTH-U` | `applications.create` | `applications.create` | `application` | 10s accept | `RK` | `key-required` |
| `API-WCP-0103` | `TEST-API-0103` | WCP-USER | `AUTH-U` | `applications.runtime.manage` | `applications.runtime.update` | `application` | 10s accept | `RK` | `key-required` |
| `API-WCP-0104` | `TEST-API-0104` | WCP-USER | `AUTH-U` | `applications.deploy` | `applications.deploy` | `application` | 10s accept | `RK` | `key-required` |
| `API-WCP-0105` | `TEST-API-0105` | WCP-USER | `AUTH-U` | `applications.deploy` | `applications.rollback` | `application` | 10s accept | `RN` | `key-required` |
| `API-WCP-0106` | `TEST-API-0106` | WCP-USER | `AUTH-U` | `applications.maintenance` | `applications.maintenance.set` | `application` | 10s accept | `RK` | `key-required` |
| `API-WCP-0201` | `TEST-API-0201` | WCP-USER | `AUTH-U` | `domains.read` | `domains.read` | `domain` | 10s | `RR` | `none` |
| `API-WCP-0202` | `TEST-API-0202` | WCP-USER | `AUTH-U` | `domains.create` | `domains.create` | `domain` | 10s accept | `RK` | `key-required` |
| `API-WCP-0203` | `TEST-API-0203` | WCP-USER | `AUTH-U` | `domains.write` | `domains.record.create` | `dns_record` | 10s accept | `RK` | `key-required` |
| `API-WCP-0001` | `TEST-API-0001` | WCP-USER | `AUTH-U` | `domains.write` | `domains.record.update` | `dns_record` | 10s accept | `RK` | `key-required` |
| `API-WCP-0204` | `TEST-API-0204` | WCP-USER | `AUTH-U` | `domains.write` | `domains.record.delete` | `dns_record` | 10s accept | `RK` | `key-required` |
| `API-WCP-0205` | `TEST-API-0205` | WCP-USER | `AUTH-U` | `domains.dnssec.manage` | `domains.dnssec.set` | `domain` | 10s accept | `RK` | `key-required` |
| `API-WCP-0301` | `TEST-API-0301` | WCP-USER | `AUTH-U` | `databases.read` | `databases.read` | `database` | 10s | `RR` | `none` |
| `API-WCP-0302` | `TEST-API-0302` | WCP-USER | `AUTH-U` | `databases.create` | `databases.create` | `database` | 10s accept | `RK` | `key-required` |
| `API-WCP-0303` | `TEST-API-0303` | WCP-USER | `AUTH-U` | `databases.delete` | `databases.delete` | `database` | 10s accept | `RN` | `key-required` |
| `API-WCP-0304` | `TEST-API-0304` | WCP-USER | `AUTH-U` | `databases.users.manage` | `databases.users.manage` | `database_identity` | 10s accept | `RK` | `key-required` |
| `API-WCP-0305` | `TEST-API-0305` | WCP-USER | `AUTH-U` | `databases.grants.manage` | `databases.grants.set` | `grant_set` | 10s accept | `RK` | `key-required` |
| `API-WCP-0306` | `TEST-API-0306` | WCP-USER | `AUTH-U` | `databases.import` | `databases.import` | `database` | 10s accept | `RN` | `key-required` |
| `API-WCP-0307` | `TEST-API-0307` | WCP-USER | `AUTH-U` | `databases.export` | `databases.export` | `database` | 10s accept | `RK` | `key-required` |
| `API-WCP-0308` | `TEST-API-0308` | WCP-USER | `AUTH-U` | `databases.restore` | `databases.restore` | `database` | 10s accept | `RN` | `key-required` |
| `API-WCP-0401` | `TEST-API-0401` | WCP-USER | `AUTH-U` | `email.read` | `email.read` | `mail_config` | 10s | `RR` | `none` |
| `API-WCP-0402` | `TEST-API-0402` | WCP-USER | `AUTH-U` | `email.manage` | `email.settings.update` | `mail_config` | 15s | `RK` | `key-required` |
| `API-WCP-0403` | `TEST-API-0403` | WCP-USER | `AUTH-U` | `email.domains.manage` | `email.domains.manage` | `mail_domain` | 10s accept | `RK` | `key-required` |
| `API-WCP-0404` | `TEST-API-0404` | WCP-USER | `AUTH-U` | `email.mailboxes.manage` | `email.mailboxes.manage` | `mailbox` | 10s accept | `RK` | `key-required` |
| `API-WCP-0405` | `TEST-API-0405` | WCP-USER | `AUTH-U` | `email.aliases.manage` | `email.aliases.manage` | `mail_alias` | 10s accept | `RK` | `key-required` |
| `API-WCP-0406` | `TEST-API-0406` | WCP-USER | `AUTH-U` | `email.auth.manage` | `email.auth.apply` | `mail_domain` | 10s accept | `RK` | `key-required` |
| `API-WCP-0407` | `TEST-API-0407` | WCP-USER | `AUTH-U` | `email.antispam.manage` | `email.antispam.update` | `mail_config` | 10s accept | `RK` | `key-required` |
| `API-WCP-0501` | `TEST-API-0501` | WCP-USER | `AUTH-U` | `files.read` | `files.read` | `file` | 10s | `RR` | `none` |
| `API-WCP-0502` | `TEST-API-0502` | WCP-USER | `AUTH-U` | `files.write` | `files.write` | `file` | 10s accept | `RK` | `key-required` |
| `API-WCP-0503` | `TEST-API-0503` | WCP-USER | `AUTH-U` | `files.delete` | `files.delete` | `file` | 10s accept | `RN` | `key-required` |
| `API-WCP-0504` | `TEST-API-0504` | WCP-USER | `AUTH-U` | `files.permissions.manage` | `files.permissions.update` | `file` | 10s accept | `RK` | `key-required` |
| `API-WCP-0505` | `TEST-API-0505` | WCP-USER | `AUTH-U` | `files.access.manage` | `files.access.manage` | `file_access` | 10s accept | `RK` | `key-required` |
| `API-WCP-0601` | `TEST-API-0601` | WCP-USER | `AUTH-U` | `backups.read` | `backups.read` | `backup` | 10s | `RR` | `none` |
| `API-WCP-0602` | `TEST-API-0602` | WCP-USER | `AUTH-U` | `backups.create` | `backups.create` | `backup` | 10s accept | `RK` | `key-required` |
| `API-WCP-0603` | `TEST-API-0603` | WCP-USER | `AUTH-U` | `backups.policy.manage` | `backups.policy.update` | `backup_policy` | 15s | `RK` | `key-required` |
| `API-WCP-0604` | `TEST-API-0604` | WCP-USER | `AUTH-U` | `backups.restore` | `backups.restore` | `backup` | 10s accept | `RN` | `key-required` |
| `API-WCP-0605` | `TEST-API-0605` | WCP-USER | `AUTH-U` | `backups.delete` | `backups.delete` | `backup` | 10s accept | `RK` | `key-required` |
| `API-WCP-0701` | `TEST-API-0701` | BOTH | `AUTH-B` | `security.sessions.manage` | `security.sessions.manage` | `session` | 15s | `RK` | `key when mutating` |
| `API-WCP-0702` | `TEST-API-0702` | BOTH | `AUTH-B` | `security.keys.manage` | `security.keys.manage` | `key` | 15s | `RK` | `key when mutating` |
| `API-WCP-0703` | `TEST-API-0703` | BOTH | `AUTH-B` | `security.tokens.manage` | `security.tokens.manage` | `security_token` | 15s | `RK` | `key when mutating` |
| `API-WCP-0704` | `TEST-API-0704` | BOTH | `AUTH-B` | `security.network.manage` | `security.network.update` | `network_policy` | 15s | `RK` | `key-required` |
| `API-WCP-0705` | `TEST-API-0705` | BOTH | `AUTH-B` | `security.waf.manage` | `security.waf.update` | `waf_policy` | 10s accept | `RK` | `key-required` |
| `API-WCP-0706` | `TEST-API-0706` | POLICY | `AUTH-P` | `privilege.elevate` | `privilege.elevate` | `privileged_session` | 15s | `RK` | `key-required` |
| `API-WCP-0707` | `TEST-API-0707` | WCP-ADMIN | `AUTH-PRIV` | `impersonation.start` | `impersonation.start` | `impersonation_session` | 15s | `RN` | `key-required` |
| `API-WCP-0708` | `TEST-API-0708` | EMERGENCY | `AUTH-E` | `break_glass.use` | `break_glass.start` | `break_glass_session` | 15s | `RN` | `key-required` |
| `API-WCP-0709` | `TEST-API-0709` | BOTH | `AUTH-B` | `api.tokens.manage` | `api.tokens.manage` | `api_token` | 15s | `RK` | `key when mutating` |
| `API-WCP-0801` | `TEST-API-0801` | WCP-ADMIN | `AUTH-A` | `servers.read` | `servers.read` | `server` | 10s | `RR` | `none` |
| `API-WCP-0802` | `TEST-API-0802` | WCP-ADMIN | `AUTH-PRIV` | `servers.services.manage` | `servers.service.reload` | `service` | 10s accept | `RN` | `key-required` |
| `API-WCP-0803` | `TEST-API-0803` | WCP-ADMIN | `AUTH-PRIV` | `servers.services.manage` | `servers.service.restart` | `service` | 10s accept | `RN` | `key-required` |
| `API-WCP-0002` | `TEST-API-0002` | WCP-ADMIN | `AUTH-PRIV` | `servers.reboot` | `servers.reboot` | `server` | 10s accept | `RN` | `key-required` |
| `API-WCP-0804` | `TEST-API-0804` | WCP-ADMIN | `AUTH-A` | `updates.read` | `updates.read` | `update_plan` | 10s | `RR` | `none` |
| `API-WCP-0805` | `TEST-API-0805` | WCP-ADMIN | `AUTH-PRIV` | `updates.apply` | `updates.apply` | `update_plan` | 10s accept | `RN` | `key-required` |
| `API-WCP-0806` | `TEST-API-0806` | WCP-ADMIN | `AUTH-A` | `clusters.read` | `clusters.read` | `cluster` | 10s | `RR` | `none` |
| `API-WCP-0807` | `TEST-API-0807` | WCP-ADMIN | `AUTH-PRIV` | `clusters.failover` | `clusters.failover` | `cluster` | 10s accept | `RN` | `key-required` |
| `API-WCP-0808` | `TEST-API-0808` | WCP-ADMIN | `AUTH-A` | `network.read` | `network.read` | `network` | 10s | `RR` | `none` |
| `API-WCP-0809` | `TEST-API-0809` | WCP-ADMIN | `AUTH-PRIV` | `network.firewall.manage` | `network.firewall.apply` | `firewall_policy` | 10s accept | `RN` | `key-required` |
| `API-WCP-0810` | `TEST-API-0810` | WCP-ADMIN | `AUTH-PRIV` | `network.routes.manage` | `network.routes.update` | `route_policy` | 10s accept | `RN` | `key-required` |
| `API-WCP-0811` | `TEST-API-0811` | WCP-ADMIN | `AUTH-A` | `storage.read` | `storage.read` | `storage` | 10s | `RR` | `none` |
| `API-WCP-0812` | `TEST-API-0812` | WCP-ADMIN | `AUTH-PRIV` | `storage.expand` | `storage.expand` | `storage` | 10s accept | `RN` | `key-required` |
| `API-WCP-0813` | `TEST-API-0813` | WCP-ADMIN | `AUTH-A` | `processes.read` | `processes.read` | `process` | 10s | `RR` | `none` |
| `API-WCP-0003` | `TEST-API-0003` | WCP-ADMIN | `AUTH-PRIV` | `processes.terminate` | `processes.terminate` | `process` | 10s accept | `RN` | `key-required` |
| `API-WCP-0007` | `TEST-API-0007` | WCP-ADMIN | `AUTH-PRIV` | `processes.kill` | `processes.kill` | `process` | 10s accept | `RN` | `key-required` |
| `API-WCP-0814` | `TEST-API-0814` | WCP-ADMIN | `AUTH-PRIV` | `settings.system.manage` | `settings.system.update` | `system_settings` | 15s | `RK` | `key-required` |
| `API-WCP-0815` | `TEST-API-0815` | WCP-ADMIN | `AUTH-A` | `service_config.read` | `service_config.read` | `service_config` | 10s | `RR` | `none` |
| `API-WCP-0816` | `TEST-API-0816` | WCP-ADMIN | `AUTH-PRIV` | `service_config.write` | `service_config.apply` | `service_config` | 10s accept | `RN` | `key-required` |
| `API-WCP-0901` | `TEST-API-0901` | WCP-ADMIN | `AUTH-A` | `iam.users.manage` | `iam.users.manage` | `principal` | 15s | `RK` | `key when mutating` |
| `API-WCP-0902` | `TEST-API-0902` | WCP-ADMIN | `AUTH-A` | `iam.roles.manage` | `iam.roles.manage` | `role` | 15s | `RK` | `key when mutating` |
| `API-WCP-0903` | `TEST-API-0903` | WCP-ADMIN | `AUTH-A` | `iam.plans.manage` | `iam.plans.manage` | `plan` | 15s | `RK` | `key when mutating` |
| `API-WCP-0904` | `TEST-API-0904` | WCP-ADMIN | `AUTH-A` | `iam.features.manage` | `iam.features.manage` | `feature_set` | 15s | `RK` | `key-required` |
| `API-WCP-0905` | `TEST-API-0905` | WCP-ADMIN | `AUTH-A` | `iam.resellers.manage` | `iam.resellers.manage` | `delegation` | 15s | `RK` | `key-required` |
| `API-WCP-0906` | `TEST-API-0906` | WCP-ADMIN | `AUTH-A` | `iam.account_overrides.manage` | `iam.account_overrides.manage` | `account_override` | 15s | `RK` | `key-required` |
| `API-WCP-0004` | `TEST-API-0004` | BOTH | `AUTH-B` | `jobs.read` | `jobs.read` | `job` | 10s | `RR` | `none` |
| `API-WCP-0005` | `TEST-API-0005` | BOTH | `AUTH-B` | `jobs.cancel` | `jobs.cancel` | `job` | 15s | `RK` | `key-required` |
| `API-WCP-1001` | `TEST-API-1001` | BOTH | `AUTH-B` | `webhooks.manage` | `webhooks.manage` | `webhook` | 15s | `RK` | `key when mutating` |
| `API-WCP-1002` | `TEST-API-1002` | BOTH | `AUTH-B` | `automations.manage` | `automations.manage` | `automation` | 15s | `RK` | `key when mutating` |
| `API-WCP-1003` | `TEST-API-1003` | BOTH | `AUTH-B` | `notifications.manage` | `notifications.manage` | `notification_route` | 15s | `RK` | `key when mutating` |
| `API-WCP-1004` | `TEST-API-1004` | POLICY | `AUTH-P` | `approvals.request` | `approvals.request` | `approval` | 15s | `RK` | `key-required` |
| `API-WCP-1005` | `TEST-API-1005` | POLICY | `AUTH-P` | `approvals.decide` | `approvals.decide` | `approval` | 15s | `RK` | `key-required` |
| `API-WCP-1006` | `TEST-API-1006` | BOTH | `AUTH-B` | `monitoring.read` | `monitoring.read` | `monitoring` | 10s | `RR` | `none` |
| `API-WCP-1007` | `TEST-API-1007` | BOTH | `AUTH-B` | `incidents.manage` | `incidents.manage` | `incident` | 15s | `RK` | `key when mutating` |
| `API-WCP-1008` | `TEST-API-1008` | BOTH | `AUTH-B` | `ssl.read` | `ssl.read` | `certificate` | 10s | `RR` | `none` |
| `API-WCP-1009` | `TEST-API-1009` | BOTH | `AUTH-B` | `ssl.manage` | `ssl.manage` | `certificate` | 10s accept | `RN` | `key-required` |
| `API-WCP-1010` | `TEST-API-1010` | BOTH | `AUTH-B` | `support.bundle.create` | `support.bundle.create` | `support_bundle` | 10s accept | `RK` | `key-required` |
| `API-WCP-0006` | `TEST-API-0006` | POLICY | `AUTH-P` | `audit.read` | `audit.read` | `audit_record` | 10s | `RR` | `none` |
| `API-WCP-0008` | `TEST-API-0008` | POLICY | `AUTH-P` | `audit.export` | `audit.export` | `audit_export` | 10s accept | `RK` | `key-required` |
| `API-WCP-1011` | `TEST-API-1011` | WCP-USER | `AUTH-U` | `settings.profile.manage` | `settings.profile.update` | `profile` | 15s | `RK` | `key-required` |

## IPC → TEST-IPC coverage

| Contract | Test | Action | Job type | IPC operation | Resource | Timeout | Idempotency | Concurrency |
|---|---|---|---|---|---|---|---|---|
| `IPC-WCP-1001` | `TEST-IPC-1001` | `linux_user.create` | `linux_user.create` | `linux_user.create` | `hosting_account` | 60s | `key-required` | `hosting_account` |
| `IPC-WCP-1002` | `TEST-IPC-1002` | `linux_user.lock` | `linux_user.lock` | `linux_user.lock` | `unix_identity` | 15s | `replay-safe` | `unix_identity` |
| `IPC-WCP-1003` | `TEST-IPC-1003` | `linux_user.unlock` | `linux_user.unlock` | `linux_user.unlock` | `unix_identity` | 15s | `replay-safe` | `unix_identity` |
| `IPC-WCP-1004` | `TEST-IPC-1004` | `linux_user.delete` | `linux_user.delete` | `linux_user.delete` | `unix_identity` | 120s | `key-protected/no blind retry` | `unix_identity` |
| `IPC-WCP-1005` | `TEST-IPC-1005` | `filesystem.directory.create` | `filesystem.directory.create` | `filesystem.directory.create` | `managed_path_ref` | 10s | `replay-safe` | `managed_path_ref` |
| `IPC-WCP-1006` | `TEST-IPC-1006` | `filesystem.ownership.apply` | `filesystem.ownership.apply` | `filesystem.ownership.apply` | `managed_path_ref` | 30s | `revision-protected` | `managed_path_ref` |
| `IPC-WCP-1007` | `TEST-IPC-1007` | `filesystem.permissions.apply` | `filesystem.permissions.apply` | `filesystem.permissions.apply` | `managed_path_ref` | 30s | `revision-protected` | `managed_path_ref` |
| `IPC-WCP-1008` | `TEST-IPC-1008` | `filesystem.quota.apply` | `filesystem.quota.apply` | `filesystem.quota.apply` | `hosting_account` | 30s | `desired-value protected` | `hosting_account` |
| `IPC-WCP-1009` | `TEST-IPC-1009` | `nginx_vhost.validate` | `N/A` | `nginx_vhost.validate` | `application_revision` | 15s | `replay-safe` | `application/revision` |
| `IPC-WCP-1010` | `TEST-IPC-1010` | `nginx_vhost.apply` | `nginx_vhost.apply` | `nginx_vhost.apply` | `application_revision` | 30s | `revision-protected` | `application-vhost` |
| `IPC-WCP-1011` | `TEST-IPC-1011` | `nginx_vhost.remove` | `nginx_vhost.remove` | `nginx_vhost.remove` | `application_revision` | 30s | `key-protected` | `application-vhost` |
| `IPC-WCP-1012` | `TEST-IPC-1012` | `nginx.reload` | `nginx.reload` | `nginx.reload` | `service` | 30s | `replay-safe same revision` | `service` |
| `IPC-WCP-1013` | `TEST-IPC-1013` | `php_pool.validate` | `N/A` | `php_pool.validate` | `application_revision` | 15s | `replay-safe` | `application/revision` |
| `IPC-WCP-1014` | `TEST-IPC-1014` | `php_pool.apply` | `php_pool.apply` | `php_pool.apply` | `application_revision` | 30s | `revision-protected` | `application-pool` |
| `IPC-WCP-1015` | `TEST-IPC-1015` | `php_pool.remove` | `php_pool.remove` | `php_pool.remove` | `application_revision` | 30s | `key-protected` | `application-pool` |
| `IPC-WCP-1016` | `TEST-IPC-1016` | `php_fpm.reload` | `php_fpm.reload` | `php_fpm.reload` | `service` | 30s | `replay-safe same revision` | `service` |
| `IPC-WCP-1017` | `TEST-IPC-1017` | `certificate.deploy` | `certificate.deploy` | `certificate.deploy` | `certificate_deployment` | 60s | `revision-protected` | `deployment_target` |
| `IPC-WCP-1018` | `TEST-IPC-1018` | `certificate.remove` | `certificate.remove` | `certificate.remove` | `certificate_deployment` | 30s | `key-protected` | `deployment_target` |
| `IPC-WCP-1019` | `TEST-IPC-1019` | `database.create` | `database.create` | `database.create` | `database` | 120s | `key-required` | `database_id` |
| `IPC-WCP-1020` | `TEST-IPC-1020` | `database.drop` | `database.drop` | `database.drop` | `database` | 120s | `not blindly retryable` | `database_id` |
| `IPC-WCP-1021` | `TEST-IPC-1021` | `database_user.create` | `database_user.create` | `database_user.create` | `db_identity` | 60s | `key-required` | `database/db_identity` |
| `IPC-WCP-1022` | `TEST-IPC-1022` | `database_user.drop` | `database_user.drop` | `database_user.drop` | `db_identity` | 60s | `not blindly retryable` | `db_identity` |
| `IPC-WCP-1023` | `TEST-IPC-1023` | `database_grant.apply` | `database_grant.apply` | `database_grant.apply` | `grant_set` | 60s | `revision-protected` | `database/db_identity` |
| `IPC-WCP-1024` | `TEST-IPC-1024` | `database_grant.revoke` | `database_grant.revoke` | `database_grant.revoke` | `grant_set` | 60s | `revision-protected` | `database/db_identity` |
| `IPC-WCP-1025` | `TEST-IPC-1025` | `firewall.policy.validate` | `N/A` | `firewall.policy.validate` | `firewall_revision` | 15s | `replay-safe` | `policy_revision` |
| `IPC-WCP-1026` | `TEST-IPC-1026` | `network.firewall.apply` | `firewall.policy.apply` | `firewall.policy.apply` | `firewall_revision` | 30s | `revision-protected` | `server-firewall` |
| `IPC-WCP-1027` | `TEST-IPC-1027` | `firewall.policy.rollback` | `firewall.policy.rollback` | `firewall.policy.rollback` | `firewall_revision` | 30s | `revision-protected` | `server-firewall` |
| `IPC-WCP-1028` | `TEST-IPC-1028` | `servers.service.status` | `N/A` | `service.status` | `service` | 10s | `replay-safe` | `read` |
| `IPC-WCP-1029` | `TEST-IPC-1029` | `servers.service.reload` | `service.reload` | `service.reload` | `service` | 60s | `revision/replay protected` | `service` |
| `IPC-WCP-1030` | `TEST-IPC-1030` | `servers.service.restart` | `service.restart` | `service.restart` | `service` | 120s | `operation-ID protected` | `service` |
| `IPC-WCP-1031` | `TEST-IPC-1031` | `updates.metadata.refresh` | `package.refresh_metadata` | `package.refresh_metadata` | `package_repository_set` | 120s | `replay-safe` | `server-package-manager` |
| `IPC-WCP-1032` | `TEST-IPC-1032` | `updates.plan` | `package.plan` | `package.plan` | `package_plan` | 120s | `replay-safe same snapshot` | `server-package-manager` |
| `IPC-WCP-1033` | `TEST-IPC-1033` | `updates.apply` | `package.apply_plan` | `package.apply_plan` | `package_plan` | 900s | `plan-ID protected` | `server-package-manager` |
| `IPC-WCP-1034` | `TEST-IPC-1034` | `updates.hold` | `package.hold` | `package.hold` | `package` | 60s | `desired-state protected` | `package_id` |
| `IPC-WCP-1035` | `TEST-IPC-1035` | `updates.unhold` | `package.unhold` | `package.unhold` | `package` | 60s | `desired-state protected` | `package_id` |
| `IPC-WCP-1036` | `TEST-IPC-1036` | `servers.reboot` | `server.reboot` | `server.reboot` | `server` | 90s | `NOT_SAFELY_RETRYABLE` | `server-power` |
| `IPC-WCP-1037` | `TEST-IPC-1037` | `servers.shutdown` | `server.shutdown` | `server.shutdown` | `server` | 60s | `NOT_SAFELY_RETRYABLE` | `server-power` |
| `IPC-WCP-1038` | `TEST-IPC-1038` | `processes.terminate` | `process.term` | `process.term` | `process_instance` | 10s | `target-instance protected` | `process_instance` |
| `IPC-WCP-1039` | `TEST-IPC-1039` | `processes.kill` | `process.kill` | `process.kill` | `process_instance` | 10s | `NOT_RETRY_LOOPED` | `process_instance` |
| `IPC-WCP-1040` | `TEST-IPC-1040` | `storage.volume.expand` | `volume.expand` | `volume.expand` | `volume` | 900s | `target-size protected` | `volume` |
| `IPC-WCP-1041` | `TEST-IPC-1041` | `storage.filesystem.expand` | `filesystem.expand` | `filesystem.expand` | `filesystem` | 300s | `target-size protected` | `filesystem` |
| `IPC-WCP-1042` | `TEST-IPC-1042` | `storage.mount.verify` | `N/A` | `mount.verify` | `mount` | 30s | `replay-safe` | `read` |
| `IPC-WCP-1043` | `TEST-IPC-1043` | `cluster.node.drain` | `node.drain` | `node.drain` | `cluster_node` | 900s | `operation-ID protected` | `node` |
| `IPC-WCP-1044` | `TEST-IPC-1044` | `cluster.node.uncordon` | `node.uncordon` | `node.uncordon` | `cluster_node` | 60s | `desired-state protected` | `node` |
| `IPC-WCP-1045` | `TEST-IPC-1045` | `cluster.failover.prepare` | `cluster.failover` | `cluster.failover.prepare` | `cluster` | 300s | `operation-ID protected` | `cluster` |
| `IPC-WCP-1046` | `TEST-IPC-1046` | `clusters.failover` | `cluster.failover` | `cluster.failover.execute` | `cluster` | 900s | `NOT_BLINDLY_RETRYABLE` | `cluster` |
| `IPC-WCP-1047` | `TEST-IPC-1047` | `cluster.failover.verify` | `cluster.failover` | `cluster.failover.verify` | `cluster` | 300s | `replay-safe` | `read/cluster` |

## Active permission → API/test coverage

| Permission | Surface | API binding count | API/Test bindings |
|---|---|---:|---|
| `api.tokens.manage` | BOTH / scope-bound | 1 | `API-WCP-0709` / `TEST-API-0709` / `api.tokens.manage` |
| `applications.create` | WCP-USER | 1 | `API-WCP-0102` / `TEST-API-0102` / `applications.create` |
| `applications.deploy` | WCP-USER | 2 | `API-WCP-0104` / `TEST-API-0104` / `applications.deploy`<br>`API-WCP-0105` / `TEST-API-0105` / `applications.rollback` |
| `applications.maintenance` | WCP-USER | 1 | `API-WCP-0106` / `TEST-API-0106` / `applications.maintenance.set` |
| `applications.read` | WCP-USER | 1 | `API-WCP-0101` / `TEST-API-0101` / `applications.read` |
| `applications.runtime.manage` | WCP-USER | 1 | `API-WCP-0103` / `TEST-API-0103` / `applications.runtime.update` |
| `approvals.decide` | POLICY / privileged | 1 | `API-WCP-1005` / `TEST-API-1005` / `approvals.decide` |
| `approvals.request` | POLICY / privileged | 1 | `API-WCP-1004` / `TEST-API-1004` / `approvals.request` |
| `audit.export` | POLICY / privileged | 1 | `API-WCP-0008` / `TEST-API-0008` / `audit.export` |
| `audit.read` | POLICY / privileged | 1 | `API-WCP-0006` / `TEST-API-0006` / `audit.read` |
| `automations.manage` | BOTH / scope-bound | 1 | `API-WCP-1002` / `TEST-API-1002` / `automations.manage` |
| `backups.create` | WCP-USER | 1 | `API-WCP-0602` / `TEST-API-0602` / `backups.create` |
| `backups.delete` | WCP-USER | 1 | `API-WCP-0605` / `TEST-API-0605` / `backups.delete` |
| `backups.policy.manage` | WCP-USER | 1 | `API-WCP-0603` / `TEST-API-0603` / `backups.policy.update` |
| `backups.read` | WCP-USER | 1 | `API-WCP-0601` / `TEST-API-0601` / `backups.read` |
| `backups.restore` | WCP-USER | 1 | `API-WCP-0604` / `TEST-API-0604` / `backups.restore` |
| `break_glass.use` | POLICY / privileged | 1 | `API-WCP-0708` / `TEST-API-0708` / `break_glass.start` |
| `clusters.failover` | WCP-ADMIN | 1 | `API-WCP-0807` / `TEST-API-0807` / `clusters.failover` |
| `clusters.read` | WCP-ADMIN | 1 | `API-WCP-0806` / `TEST-API-0806` / `clusters.read` |
| `databases.create` | WCP-USER | 1 | `API-WCP-0302` / `TEST-API-0302` / `databases.create` |
| `databases.delete` | WCP-USER | 1 | `API-WCP-0303` / `TEST-API-0303` / `databases.delete` |
| `databases.export` | WCP-USER | 1 | `API-WCP-0307` / `TEST-API-0307` / `databases.export` |
| `databases.grants.manage` | WCP-USER | 1 | `API-WCP-0305` / `TEST-API-0305` / `databases.grants.set` |
| `databases.import` | WCP-USER | 1 | `API-WCP-0306` / `TEST-API-0306` / `databases.import` |
| `databases.read` | WCP-USER | 1 | `API-WCP-0301` / `TEST-API-0301` / `databases.read` |
| `databases.restore` | WCP-USER | 1 | `API-WCP-0308` / `TEST-API-0308` / `databases.restore` |
| `databases.users.manage` | WCP-USER | 1 | `API-WCP-0304` / `TEST-API-0304` / `databases.users.manage` |
| `domains.create` | WCP-USER | 1 | `API-WCP-0202` / `TEST-API-0202` / `domains.create` |
| `domains.dnssec.manage` | WCP-USER | 1 | `API-WCP-0205` / `TEST-API-0205` / `domains.dnssec.set` |
| `domains.read` | WCP-USER | 1 | `API-WCP-0201` / `TEST-API-0201` / `domains.read` |
| `domains.write` | WCP-USER | 3 | `API-WCP-0203` / `TEST-API-0203` / `domains.record.create`<br>`API-WCP-0001` / `TEST-API-0001` / `domains.record.update`<br>`API-WCP-0204` / `TEST-API-0204` / `domains.record.delete` |
| `email.aliases.manage` | WCP-USER | 1 | `API-WCP-0405` / `TEST-API-0405` / `email.aliases.manage` |
| `email.antispam.manage` | WCP-USER | 1 | `API-WCP-0407` / `TEST-API-0407` / `email.antispam.update` |
| `email.auth.manage` | WCP-USER | 1 | `API-WCP-0406` / `TEST-API-0406` / `email.auth.apply` |
| `email.domains.manage` | WCP-USER | 1 | `API-WCP-0403` / `TEST-API-0403` / `email.domains.manage` |
| `email.mailboxes.manage` | WCP-USER | 1 | `API-WCP-0404` / `TEST-API-0404` / `email.mailboxes.manage` |
| `email.manage` | WCP-USER | 1 | `API-WCP-0402` / `TEST-API-0402` / `email.settings.update` |
| `email.read` | WCP-USER | 1 | `API-WCP-0401` / `TEST-API-0401` / `email.read` |
| `files.access.manage` | WCP-USER | 1 | `API-WCP-0505` / `TEST-API-0505` / `files.access.manage` |
| `files.delete` | WCP-USER | 1 | `API-WCP-0503` / `TEST-API-0503` / `files.delete` |
| `files.permissions.manage` | WCP-USER | 1 | `API-WCP-0504` / `TEST-API-0504` / `files.permissions.update` |
| `files.read` | WCP-USER | 1 | `API-WCP-0501` / `TEST-API-0501` / `files.read` |
| `files.write` | WCP-USER | 1 | `API-WCP-0502` / `TEST-API-0502` / `files.write` |
| `iam.account_overrides.manage` | WCP-ADMIN / OWNER | 1 | `API-WCP-0906` / `TEST-API-0906` / `iam.account_overrides.manage` |
| `iam.features.manage` | WCP-ADMIN / OWNER | 1 | `API-WCP-0904` / `TEST-API-0904` / `iam.features.manage` |
| `iam.plans.manage` | WCP-ADMIN / OWNER | 1 | `API-WCP-0903` / `TEST-API-0903` / `iam.plans.manage` |
| `iam.resellers.manage` | WCP-ADMIN / OWNER | 1 | `API-WCP-0905` / `TEST-API-0905` / `iam.resellers.manage` |
| `iam.roles.manage` | WCP-ADMIN / OWNER | 1 | `API-WCP-0902` / `TEST-API-0902` / `iam.roles.manage` |
| `iam.users.manage` | WCP-ADMIN / OWNER | 1 | `API-WCP-0901` / `TEST-API-0901` / `iam.users.manage` |
| `impersonation.start` | WCP-ADMIN / OWNER | 1 | `API-WCP-0707` / `TEST-API-0707` / `impersonation.start` |
| `incidents.manage` | BOTH / scope-bound | 1 | `API-WCP-1007` / `TEST-API-1007` / `incidents.manage` |
| `jobs.cancel` | BOTH / scope-bound | 1 | `API-WCP-0005` / `TEST-API-0005` / `jobs.cancel` |
| `jobs.read` | BOTH / scope-bound | 1 | `API-WCP-0004` / `TEST-API-0004` / `jobs.read` |
| `monitoring.read` | BOTH / scope-bound | 1 | `API-WCP-1006` / `TEST-API-1006` / `monitoring.read` |
| `network.firewall.manage` | WCP-ADMIN | 1 | `API-WCP-0809` / `TEST-API-0809` / `network.firewall.apply` |
| `network.read` | WCP-ADMIN | 1 | `API-WCP-0808` / `TEST-API-0808` / `network.read` |
| `network.routes.manage` | WCP-ADMIN | 1 | `API-WCP-0810` / `TEST-API-0810` / `network.routes.update` |
| `notifications.manage` | BOTH / scope-bound | 1 | `API-WCP-1003` / `TEST-API-1003` / `notifications.manage` |
| `privilege.elevate` | POLICY / privileged | 1 | `API-WCP-0706` / `TEST-API-0706` / `privilege.elevate` |
| `processes.kill` | WCP-ADMIN | 1 | `API-WCP-0007` / `TEST-API-0007` / `processes.kill` |
| `processes.read` | WCP-ADMIN | 1 | `API-WCP-0813` / `TEST-API-0813` / `processes.read` |
| `processes.terminate` | WCP-ADMIN | 1 | `API-WCP-0003` / `TEST-API-0003` / `processes.terminate` |
| `security.keys.manage` | BOTH / scope-bound | 1 | `API-WCP-0702` / `TEST-API-0702` / `security.keys.manage` |
| `security.network.manage` | BOTH / scope-bound | 1 | `API-WCP-0704` / `TEST-API-0704` / `security.network.update` |
| `security.sessions.manage` | BOTH / scope-bound | 1 | `API-WCP-0701` / `TEST-API-0701` / `security.sessions.manage` |
| `security.tokens.manage` | BOTH / scope-bound | 1 | `API-WCP-0703` / `TEST-API-0703` / `security.tokens.manage` |
| `security.waf.manage` | BOTH / scope-bound | 1 | `API-WCP-0705` / `TEST-API-0705` / `security.waf.update` |
| `servers.read` | WCP-ADMIN | 1 | `API-WCP-0801` / `TEST-API-0801` / `servers.read` |
| `servers.reboot` | WCP-ADMIN | 1 | `API-WCP-0002` / `TEST-API-0002` / `servers.reboot` |
| `servers.services.manage` | WCP-ADMIN | 2 | `API-WCP-0802` / `TEST-API-0802` / `servers.service.reload`<br>`API-WCP-0803` / `TEST-API-0803` / `servers.service.restart` |
| `service_config.read` | WCP-ADMIN | 1 | `API-WCP-0815` / `TEST-API-0815` / `service_config.read` |
| `service_config.write` | WCP-ADMIN | 1 | `API-WCP-0816` / `TEST-API-0816` / `service_config.apply` |
| `settings.profile.manage` | WCP-USER | 1 | `API-WCP-1011` / `TEST-API-1011` / `settings.profile.update` |
| `settings.system.manage` | WCP-ADMIN | 1 | `API-WCP-0814` / `TEST-API-0814` / `settings.system.update` |
| `ssl.manage` | BOTH / scope-bound | 1 | `API-WCP-1009` / `TEST-API-1009` / `ssl.manage` |
| `ssl.read` | BOTH / scope-bound | 1 | `API-WCP-1008` / `TEST-API-1008` / `ssl.read` |
| `storage.expand` | WCP-ADMIN | 1 | `API-WCP-0812` / `TEST-API-0812` / `storage.expand` |
| `storage.read` | WCP-ADMIN | 1 | `API-WCP-0811` / `TEST-API-0811` / `storage.read` |
| `support.bundle.create` | BOTH / scope-bound | 1 | `API-WCP-1010` / `TEST-API-1010` / `support.bundle.create` |
| `updates.apply` | WCP-ADMIN | 1 | `API-WCP-0805` / `TEST-API-0805` / `updates.apply` |
| `updates.read` | WCP-ADMIN | 1 | `API-WCP-0804` / `TEST-API-0804` / `updates.read` |
| `webhooks.manage` | BOTH / scope-bound | 1 | `API-WCP-1001` / `TEST-API-1001` / `webhooks.manage` |

## QA consumption rules

1. The final QA owner artifact may reference this index instead of manually duplicating the 86/47/82 counts.
2. Each TEST-API/TEST-IPC record still needs preconditions, expected result/error, forbidden effects, audit/DB/observability assertions, dependencies and evidence.
3. API transport assertions remain BLOCKED where H01–H10 are not owner-ratified.
4. DB physical assertions remain BLOCKED on #6.
5. Engineering quality/toolchain assertions remain BLOCKED on #9 where exact v0.2 rules are unavailable.
6. The 85 audit-derived proposal scenarios remain an intake backlog, not canonical IDs.
7. No PASS/FAIL is created by this index.

## Drift rule

If any of the three source blobs changes, this derived index must be regenerated/reverified. A source update invalidates the counts until the index is refreshed.

Machine-readable companion:
`governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.json`.

Issue #7 remains OPEN. SC-M01 remains PAUSED; CODE-BASELINE remains NONE.
