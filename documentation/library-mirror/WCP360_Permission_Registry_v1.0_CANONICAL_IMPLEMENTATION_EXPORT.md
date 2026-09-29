# WCP360 Permission Registry — Canonical Implementation Export

**Artifact:** `WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md`  
**Owner:** `WCP360 — SECURITY / HARDENING`  
**Version:** `v1.0`  
**Status:** `FINAL APPROVED — CANONICAL IMPLEMENTATION AUTHORITY`  
**Publication date:** `2026-09-29`  
**WCP360 Baseline Manifest:** `WCP360-BM-0001 FINAL`  
**Baseline:** `WCP360-BM-0001 FINAL`  
**DOC-BASELINE:** `v4.0.21.23`  
**DESIGN-BASELINE:** `V2.5.3.36.4`  
**CODE-BASELINE:** `NONE`  
**Content change:** `NONE`

## Authority

This is the final implementation Permission Registry for Security Baseline `v1.0`.

Typed namespaces remain distinct:

```text
permission_id != action_id != job_type != event_id
permission_id != audit_event_id != ipc_operation_id != error_code
```

Resolution order:

```text
explicit DENY
> account override
> role grants
> plan / feature envelope
> default policy
```

Additional rules:
- DEMO is an additional hard restriction.
- Reseller delegation cannot exceed the reseller's effective envelope.
- Temporary privilege does not mutate persistent roles.
- Approval never replaces execution-time authorization.
- UI visibility never grants permission.
- Context switching never changes permission.
- Automation uses service identities.
- Every state-changing action references one base `permission_id` plus optional policy gates.
- UI code cannot invent permission identifiers.

## Canonical Registry — 82 permission_id

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
| `domains.create` | WCP-USER | ACTIVE |
| `domains.dnssec.manage` | WCP-USER | ACTIVE |
| `domains.read` | WCP-USER | ACTIVE |
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
| `processes.kill` | WCP-ADMIN | ACTIVE |
| `processes.read` | WCP-ADMIN | ACTIVE |
| `processes.terminate` | WCP-ADMIN | ACTIVE |
| `security.keys.manage` | BOTH / scope-bound | ACTIVE |
| `security.network.manage` | BOTH / scope-bound | ACTIVE |
| `security.sessions.manage` | BOTH / scope-bound | ACTIVE |
| `security.tokens.manage` | BOTH / scope-bound | ACTIVE |
| `security.waf.manage` | BOTH / scope-bound | ACTIVE |
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

## Explicit namespace exclusions

| Identifier | Canonical type |
|---|---|
| `domains.record.update` | `action_id` |
| `dns.change.apply` | `job_type` |
| `dns.record.updated` | `audit_event_id` |
| `process.kill` | `job_type` / `ipc_operation_id` according to contract position |
| `server.reboot` | `job_type` / `ipc_operation_id` according to contract position |
| `server.reboot.requested` | `audit_event_id` |
| `server.reboot.completed` | `audit_event_id` |

Canonical permissions used in the corresponding domains include:

```text
domains.write
processes.terminate
processes.kill
servers.reboot
```

A job, event, audit event, IPC operation, UI action, route, resource name or correlation ID never grants authority by itself.

## Publication status

```text
PUBLICATION DATE = 2026-09-29
BASELINE MANIFEST = WCP360-BM-0001 FINAL
DOC-BASELINE = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4
CODE-BASELINE = NONE
```

```text
PERMISSION REGISTRY VERSION = v1.0
PERMISSION COUNT = 82
82 / 82 PERMISSION COVERAGE = PRESERVED
HISTORICAL 89-ROW REGISTRY = NOT IMPLEMENTATION AUTHORITY
FINAL APPROVED CONTENT = YES
HISTORICAL CONTENT INCLUDED = NO
CONTENT CHANGE = NONE
READY FOR SOURCE CODE CONSUMPTION = YES
```