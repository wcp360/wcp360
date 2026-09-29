# Exact Action → Permission Mapping

## Applications

| Action | Permission | Additional gate |
|---|---|---|
| View application | `applications.read` | scope |
| Create application | `applications.create` | plan/quota |
| Change runtime | `applications.runtime.manage` | approved runtime set |
| Deploy release | `applications.deploy` | optional approval |
| Rollback release | `applications.deploy` | compatibility check |
| Maintenance mode | `applications.maintenance` | environment scope |

## Domains / DNS

| Action | Permission | Additional gate |
|---|---|---|
| View domain/zone | `domains.read` | scope |
| Add domain | `domains.create` | plan/quota |
| Add/edit/delete record | `domains.write` | validation |
| DNSSEC enable/disable | `domains.dnssec.manage` | provider support |

## Databases

| Action | Permission | Additional gate |
|---|---|---|
| View metadata | `databases.read` | scope |
| Create DB | `databases.create` | plan/quota |
| Delete DB | `databases.delete` | destructive confirm |
| Manage users | `databases.users.manage` | scope |
| Manage grants | `databases.grants.manage` | scope |
| Import | `databases.import` | job |
| Export | `databases.export` | job |
| Restore | `databases.restore` | backup + approval policy |

## Email

| Action | Permission | Additional gate |
|---|---|---|
| View mail config | `email.read` | scope |
| Add mail domain | `email.domains.manage` | plan |
| Create mailbox | `email.mailboxes.manage` | quota |
| Manage aliases | `email.aliases.manage` | scope |
| Manage auth records | `email.auth.manage` | DNS capability |
| Manage antispam | `email.antispam.manage` | scope |

## Files

| Action | Permission | Additional gate |
|---|---|---|
| Read/download | `files.read` | root-bound |
| Upload/create/edit | `files.write` | root-bound |
| Delete | `files.delete` | soft-delete policy |
| Change permissions | `files.permissions.manage` | unsafe-mode block |
| SFTP key/access | `files.access.manage` | security policy |

## Backups

| Action | Permission | Additional gate |
|---|---|---|
| View | `backups.read` | scope |
| Create restore point | `backups.create` | capacity |
| Change policy | `backups.policy.manage` | owner/admin role |
| Restore | `backups.restore` | impact + approval policy |
| Delete restore point | `backups.delete` | retention policy |

## Security

| Action | Permission |
|---|---|
| Manage sessions | `security.sessions.manage` |
| Manage SSH keys | `security.keys.manage` |
| Manage API tokens | `security.tokens.manage` |
| Manage network restrictions | `security.network.manage` |
| Manage WAF | `security.waf.manage` |
| Start privileged session | `privilege.elevate` |
| Start impersonation | `impersonation.start` |
| Break-glass | `break_glass.use` |

## WCP-ADMIN

| Action | Permission |
|---|---|
| View servers | `servers.read` |
| Reload/restart service | `servers.services.manage` |
| Reboot server | `servers.reboot` |
| Apply updates | `updates.apply` |
| Manage firewall | `network.firewall.manage` |
| Manage routes/NAT | `network.routes.manage` |
| Manual failover | `clusters.failover` |
| Expand volume | `storage.expand` |
| TERM process | `processes.terminate` |
| KILL process | `processes.kill` |
| Manage global settings | `settings.system.manage` |
| Change service config | `service_config.write` |

## IAM / plans

| Action | Permission |
|---|---|
| Manage users | `iam.users.manage` |
| Manage roles | `iam.roles.manage` |
| Manage plans | `iam.plans.manage` |
| Manage feature lists | `iam.features.manage` |
| Manage resellers | `iam.resellers.manage` |
| Set account override | `iam.account_overrides.manage` |

## Rule

Backend authorization must evaluate the permission even if the UI has hidden the control.