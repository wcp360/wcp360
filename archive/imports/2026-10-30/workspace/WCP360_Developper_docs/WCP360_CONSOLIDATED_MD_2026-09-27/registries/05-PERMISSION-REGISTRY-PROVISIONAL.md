# WCP360 — Permission Registry — Provisional Consolidated View

## Status

`SOURCE REGISTRY PRESERVED — TYPED CLEANUP REQUIRED`

The source registry self-declares canonical authority, but H3/H3-CM found namespace contamination. Therefore the list below is preserved as evidence and candidate data, **not approved for direct implementation** until `H3CM-X001` and `H3CM-X002` are resolved.

### Known conflict examples
- `domains.record.update` is used as an `action_id` in the action contract.
- `dns.change.apply` is used as a `job_type`.
- `dns.record.updated` is used as an audit-event identifier.
- process/server identifiers overlap permission/action/job/daemon-operation semantics.

## Source snapshot

# WCP360 Canonical Permission Registry
## Authority
**CANONICAL SOURCE — supersedes permission identifier lists in earlier documents.**

Earlier files remain as historical/design evidence, but all future implementation contracts must derive permission identifiers from this registry.

## Resolution rule
- explicit `DENY` > account override > role grants > plan/feature envelope > default policy;
- DEMO is an additional hard restriction layer;
- reseller delegation can never exceed the reseller's own effective envelope;
- temporary privilege does not mutate persistent roles;
- approval never replaces execution-time authorization;
- UI visibility never grants permission.

## Registry

| Permission ID | Surface | Status |
|---|---|---|
| `api.tokens.manage` | BOTH / scope-bound | ACTIVE |
| `applications.create` | WCP-USER | ACTIVE |
| `applications.deploy` | WCP-USER | ACTIVE |
| `applications.maintenance` | WCP-USER | ACTIVE |
| `applications.read` | WCP-USER | ACTIVE |
| `applications.runtime.manage` | WCP-USER | ACTIVE |
| `approvals.decide` | POLICY / privileged | ACTIVE |
| `approvals.request` | POLICY / privileged | ACTIVE |
| `audit.export` | POLICY / privileged | ACTIVE |
| `audit.read` | POLICY / privileged | ACTIVE |
| `automations.manage` | BOTH / scope-bound | ACTIVE |
| `backups.create` | WCP-USER | ACTIVE |
| `backups.delete` | WCP-USER | ACTIVE |
| `backups.policy.manage` | WCP-USER | ACTIVE |
| `backups.read` | WCP-USER | ACTIVE |
| `backups.restore` | WCP-USER | ACTIVE |
| `break_glass.use` | POLICY / privileged | ACTIVE |
| `clusters.failover` | WCP-ADMIN | ACTIVE |
| `clusters.read` | WCP-ADMIN | ACTIVE |
| `databases.create` | WCP-USER | ACTIVE |
| `databases.delete` | WCP-USER | ACTIVE |
| `databases.export` | WCP-USER | ACTIVE |
| `databases.grants.manage` | WCP-USER | ACTIVE |
| `databases.import` | WCP-USER | ACTIVE |
| `databases.read` | WCP-USER | ACTIVE |
| `databases.restore` | WCP-USER | ACTIVE |
| `databases.users.manage` | WCP-USER | ACTIVE |
| `dns.change.apply` | POLICY | ACTIVE |
| `dns.record.updated` | POLICY | ACTIVE |
| `domains.create` | WCP-USER | ACTIVE |
| `domains.dnssec.manage` | WCP-USER | ACTIVE |
| `domains.read` | WCP-USER | ACTIVE |
| `domains.record.update` | WCP-USER | ACTIVE |
| `domains.write` | WCP-USER | ACTIVE |
| `email.aliases.manage` | WCP-USER | ACTIVE |
| `email.antispam.manage` | WCP-USER | ACTIVE |
| `email.auth.manage` | WCP-USER | ACTIVE |
| `email.domains.manage` | WCP-USER | ACTIVE |
| `email.mailboxes.manage` | WCP-USER | ACTIVE |
| `email.manage` | WCP-USER | ACTIVE |
| `email.read` | WCP-USER | ACTIVE |
| `files.access.manage` | WCP-USER | ACTIVE |
| `files.delete` | WCP-USER | ACTIVE |
| `files.permissions.manage` | WCP-USER | ACTIVE |
| `files.read` | WCP-USER | ACTIVE |
| `files.write` | WCP-USER | ACTIVE |
| `iam.account_overrides.manage` | WCP-ADMIN / OWNER | ACTIVE |
| `iam.features.manage` | WCP-ADMIN / OWNER | ACTIVE |
| `iam.plans.manage` | WCP-ADMIN / OWNER | ACTIVE |
| `iam.resellers.manage` | WCP-ADMIN / OWNER | ACTIVE |
| `iam.roles.manage` | WCP-ADMIN / OWNER | ACTIVE |
| `iam.users.manage` | WCP-ADMIN / OWNER | ACTIVE |
| `impersonation.start` | WCP-ADMIN / OWNER | ACTIVE |
| `incidents.manage` | BOTH / scope-bound | ACTIVE |
| `jobs.cancel` | BOTH / scope-bound | ACTIVE |
| `jobs.read` | BOTH / scope-bound | ACTIVE |
| `monitoring.read` | BOTH / scope-bound | ACTIVE |
| `network.firewall.manage` | WCP-ADMIN | ACTIVE |
| `network.read` | WCP-ADMIN | ACTIVE |
| `network.routes.manage` | WCP-ADMIN | ACTIVE |
| `notifications.manage` | BOTH / scope-bound | ACTIVE |
| `privilege.elevate` | POLICY / privileged | ACTIVE |
| `process.kill` | POLICY | ACTIVE |
| `processes.kill` | WCP-ADMIN | ACTIVE |
| `processes.read` | WCP-ADMIN | ACTIVE |
| `processes.terminate` | WCP-ADMIN | ACTIVE |
| `security.keys.manage` | BOTH / scope-bound | ACTIVE |
| `security.network.manage` | BOTH / scope-bound | ACTIVE |
| `security.sessions.manage` | BOTH / scope-bound | ACTIVE |
| `security.tokens.manage` | BOTH / scope-bound | ACTIVE |
| `security.waf.manage` | BOTH / scope-bound | ACTIVE |
| `server.reboot` | POLICY | ACTIVE |
| `server.reboot.completed` | POLICY | ACTIVE |
| `server.reboot.requested` | POLICY | ACTIVE |
| `servers.read` | WCP-ADMIN | ACTIVE |
| `servers.reboot` | WCP-ADMIN | ACTIVE |
| `servers.services.manage` | WCP-ADMIN | ACTIVE |
| `service_config.read` | WCP-ADMIN | ACTIVE |
| `service_config.write` | WCP-ADMIN | ACTIVE |
| `settings.profile.manage` | WCP-USER | ACTIVE |
| `settings.system.manage` | WCP-ADMIN | ACTIVE |
| `ssl.manage` | BOTH / scope-bound | ACTIVE |
| `ssl.read` | BOTH / scope-bound | ACTIVE |
| `storage.expand` | WCP-ADMIN | ACTIVE |
| `storage.read` | WCP-ADMIN | ACTIVE |
| `support.bundle.create` | BOTH / scope-bound | ACTIVE |
| `updates.apply` | WCP-ADMIN | ACTIVE |
| `updates.read` | WCP-ADMIN | ACTIVE |
| `webhooks.manage` | BOTH / scope-bound | ACTIVE |

## Canonical rules

1. No permission identifier may be introduced only in UI code.
2. Every state-changing action must reference exactly one base permission plus optional policy gates.
3. New permission IDs require a versioned documentation amendment.
4. Aliases are prohibited in implementation; historical aliases must map to one canonical ID.
5. Action matrices are derived views, not independent sources of truth.
