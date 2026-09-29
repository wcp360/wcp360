# Canonical Permission Namespace — Design Freeze

These identifiers are design-level canonical names. Implementation may refine storage/transport names only if one-to-one traceability is preserved.

| Permission | Surface | Meaning |
|---|---|---|
| `applications.read` | WCP-USER | Read application/project context |
| `applications.create` | WCP-USER | Create application/site |
| `applications.deploy` | WCP-USER | Deploy approved release |
| `applications.maintenance` | WCP-USER | Toggle app maintenance within policy |
| `domains.read` | WCP-USER | Read domains/DNS |
| `domains.write` | WCP-USER | Create/edit domain/DNS records |
| `domains.dnssec.manage` | WCP-USER | Manage DNSSEC |
| `databases.read` | WCP-USER | Read DB metadata |
| `databases.create` | WCP-USER | Create DB |
| `databases.users.manage` | WCP-USER | Manage DB users/privileges |
| `databases.restore` | WCP-USER | Restore DB subject to policy |
| `email.read` | WCP-USER | Read mail configuration |
| `email.manage` | WCP-USER | Manage mail domains/mailboxes/aliases |
| `email.auth.manage` | WCP-USER | Manage SPF/DKIM/DMARC |
| `files.read` | WCP-USER | Read within allowed root |
| `files.write` | WCP-USER | Modify within allowed root |
| `files.permissions.manage` | WCP-USER | Change file permissions within policy |
| `backups.read` | WCP-USER | Read backup status/restore points |
| `backups.create` | WCP-USER | Create backup/restore point |
| `backups.restore` | WCP-USER | Restore data |
| `security.sessions.manage` | WCP-USER | Manage own/account sessions |
| `security.keys.manage` | WCP-USER | Manage SSH keys |
| `security.tokens.manage` | WCP-USER | Manage API tokens |
| `security.waf.manage` | WCP-USER | Manage application WAF settings |
| `monitoring.read` | WCP-USER | Read metrics/alerts/incidents |
| `incidents.manage` | WCP-USER/WCP-ADMIN | Acknowledge/update incident |
| `servers.read` | WCP-ADMIN | Read server/fleet state |
| `servers.services.manage` | WCP-ADMIN | Reload/restart managed services |
| `servers.reboot` | WCP-ADMIN | Reboot server with privileged flow |
| `updates.read` | WCP-ADMIN | Read update/package state |
| `updates.apply` | WCP-ADMIN | Apply approved updates |
| `clusters.read` | WCP-ADMIN | Read HA/cluster state |
| `clusters.failover` | WCP-ADMIN | Manual failover |
| `network.read` | WCP-ADMIN | Read interfaces/routes/firewall |
| `network.firewall.manage` | WCP-ADMIN | Manage firewall rules |
| `network.routes.manage` | WCP-ADMIN | Manage routes/NAT/resolvers |
| `iam.users.manage` | WCP-ADMIN/OWNER | Manage users |
| `iam.roles.manage` | WCP-ADMIN/OWNER | Manage roles |
| `iam.plans.manage` | WCP-ADMIN | Manage plans/feature lists |
| `iam.resellers.manage` | WCP-ADMIN | Manage reseller envelopes |
| `jobs.read` | BOTH | Read jobs permitted by resource scope |
| `jobs.cancel` | BOTH | Cancel eligible jobs within scope |
| `api.tokens.manage` | BOTH | Manage API tokens within scope |
| `webhooks.manage` | BOTH | Manage webhooks within scope |
| `automations.manage` | BOTH | Manage automations within scope |
| `notifications.manage` | BOTH | Manage notification preferences/routes within envelope |
| `approvals.request` | BOTH | Request approval for governed action |
| `approvals.decide` | POLICY | Approve/reject governed action if separation policy permits |
| `storage.read` | WCP-ADMIN | Read storage/volume state |
| `storage.expand` | WCP-ADMIN | Expand volume/filesystem |
| `processes.read` | WCP-ADMIN | Read process/worker state |
| `processes.terminate` | WCP-ADMIN | Graceful terminate process within policy |
| `processes.kill` | PRIVILEGED | Force kill process |
| `ssl.read` | BOTH | Read certificate state |
| `ssl.manage` | BOTH | Issue/renew/deploy certificate within scope |
| `support.bundle.create` | BOTH | Generate consented support bundle |
| `audit.read` | POLICY | Read audit events within authorized scope |
| `audit.export` | POLICY | Export filtered audit data |
| `settings.profile.manage` | WCP-USER | Manage own profile/preferences |
| `settings.system.manage` | WCP-ADMIN | Manage platform-wide settings |
| `service_config.read` | WCP-ADMIN | Read managed service configuration |
| `service_config.write` | WCP-ADMIN | Modify schema-managed service configuration |
| `privilege.elevate` | POLICY | Enter temporary privileged session |
| `impersonation.start` | WCP-ADMIN | Start support impersonation |
| `break_glass.use` | EMERGENCY | Emergency access with hard expiry |