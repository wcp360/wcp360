# WCP360 Security Baseline — Canonical Implementation Export

**Artifact:** `WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md`  
**Owner:** `WCP360 — SECURITY / HARDENING`  
**Version:** `v1.0`  
**Status:** `FINAL APPROVED — CANONICAL IMPLEMENTATION AUTHORITY`  
**Publication date:** `2026-09-29`  
**WCP360 Baseline Manifest:** `WCP360-BM-0001 FINAL`  
**Baseline:** `WCP360-BM-0001 FINAL`  
**DOC-BASELINE:** `v4.0.21.23`  
**DESIGN-BASELINE:** `V2.5.3.36.4`  
**CODE-BASELINE:** `NONE`  
**Permission Registry:** `WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md`  
**SEC-H3V2-001:** `RESOLVED`  
**SEC-H3V2-002:** `RESOLVED`  
**Content change:** `NONE`

## 1. Security invariants

```text
authentication != authorization

permission_id != action_id != job_type != event_id
permission_id != audit_event_id != ipc_operation_id != error_code

WCP-USER != WCP-ADMIN

Tenant = security/data isolation boundary

membership != ownership
delegation != ownership
profile lifecycle dependency != Tenant ownership

job/event/audit/IPC identifiers never grant authority
UI visibility never grants authority
approval never replaces execution-time authorization
```

Canonical authorization:

```text
AUTHZ =
authenticated_identity
∩ surface
∩ action
∩ permission
∩ role_constraints
∩ entitlement
∩ resource
∩ tenant_boundary
∩ ownership/delegation
∩ policy/context
∩ authentication_strength
∩ resource_state
∩ approval_when_required
```

Authorization occurs before mutation, effectful enqueue or privileged dispatch. Sensitive async execution revalidates mutable security invariants before effect.

## 2. Privileged architecture

```text
backend
→ wcpd (non-root)
→ bounded authenticated IPC-B
→ wcp-privd (root minimal)
→ explicit privileged primitive
```

Forbidden:

```text
shell.exec
arbitrary command
arbitrary executable
generic sudo proxy
generic systemctl proxy
arbitrary filesystem write/target
arbitrary package-manager argv
arbitrary SQL
arbitrary signal
generic root terminal
```

Web/UI code never invokes privileged OS commands directly.

## 3. IPC-B

```text
SOCKET = /run/wcp360/priv/wcp-privd.sock
RUNTIME DIR = /run/wcp360/priv
PROTOCOL = WCP360-IPC-B/1

OWNER = root
GROUP = wcp360-priv
MODE = 0660

AUTHORIZED CLIENT = wcpd only
PEER AUTH = SO_PEERCRED exact wcpd identity + expected service context
SHARED BEARER SECRET = NONE

FRAMING = uint32 big-endian length + strict UTF-8 JSON
REQUEST MAX = 65536 bytes
RESPONSE MAX = 262144 bytes
CONNECT TIMEOUT = 1 second
I/O TIMEOUT = 5 seconds
DEFAULT OPERATION TIMEOUT = 30 seconds
MAX OPERATION TIMEOUT = 900 seconds
```

Malformed/unknown requests fail closed with no privileged effect and no generic fallback.

Post-dispatch timeout may become `UNKNOWN_OUTCOME`; destructive operations are never blindly retried.

## 4. Unix identity model

| Component | Identity | Supplementary access |
|---|---|---|
| WCP-USER runtime | `wcp360-user:wcp360-user` | `wcp360-control` |
| WCP-ADMIN runtime | `wcp360-admin:wcp360-admin` | `wcp360-control` |
| daemon | `wcpd:wcpd` | `wcp360-priv` |
| privileged daemon | `root:root` | minimal root service |
| worker | `wcp360-worker:wcp360-worker` | none by default |
| installer/updater | transient root | lifecycle only |
| hosting | `wcp_<short-resource-id>` + private group | no WCP control groups |

Service identities are no-login. Only `wcpd` is the normal IPC-B client.

## 5. Filesystem / state authority

```text
PRODUCT ROOT = /usr/lib/wcp360

PostgreSQL = authoritative logical control-plane state
/var/lib/wcp360 = materialized/node-local/reconstructable
/run/wcp360 = ephemeral runtime
/var/cache/wcp360 = reconstructable cache
Linux/services/filesystem = observed physical state
```

Runtime write to `/usr/lib/wcp360` is denied. `/etc/wcp360` writes require typed registered privileged/config operations.

Managed filesystem effects use registered resource IDs, reject traversal, prevent symlink/mount escape, revalidate immediately before mutation and use atomic replacement where applicable.

## 6. Tenant / ownership / delegation

```text
Account ↔ Organization = N:M MEMBERSHIP
Organization ↔ Tenant = N:M OPTIONAL GROUPING
Account → Tenant = 1:N OWNERSHIP
Tenant → Hosting Account = 1:N OWNERSHIP
Account → Customer = 0..1 DEPENDENT PROFILE
Account → Reseller = 0..1 DEPENDENT PROFILE
Reseller ↔ Tenant = N:M EXPLICIT REVOCABLE DELEGATION
```

Mandatory inverses:

```text
Tenant → exactly 1 owning Account
Hosting Account → exactly 1 Tenant
Customer → exactly 1 Account
Reseller → exactly 1 Account
```

Organization membership does not itself authorize Tenant access. Reseller delegation never transfers ownership and cannot exceed the reseller's effective envelope.

## 7. Trusted authorization context

Privileged workflows preserve at least:

```text
authorization_context_id
origin_surface
origin_actor_type
origin_actor_reference
origin_tenant_id when applicable
origin_permission_id
origin_action_id
resource_type
resource_id
job_id
correlation_id
ipc_operation_id
authorization_decision = ALLOW
authorization_decided_at
```

This context is not a bearer credential and is not accepted directly from browser surfaces.

Unknown permission/action/IPC binding: `DENY`, no side effect, audit evidence.

## 8. Privileged operation profiles

```text
T = Tenant
S = Server
C = Cluster
G = Global/platform

FS0 = no caller path; fixed/registry target
FST = Tenant managed-root confinement
FSC = WCP/system config; staged/validated/atomic replacement
FSCERT = certificate/secret object refs; no key echo
FSD = storage/device registry IDs only

E1 = validate/read, no external mutation
E2 = reconcilable mutation; uncertain dispatch → UNKNOWN_OUTCOME → observe/reconcile
E3 = destructive; no blind retry
E4 = revision/rollback where safe
E5 = expansion/non-reversible; no shrink-as-rollback
E6 = cluster/HA; operation-ID protected; observe/reconcile; failback separate
```

Universal prohibited caller inputs:

```text
shell/command string
executable path
arbitrary argv/environment
sudo/systemctl command
arbitrary service name
arbitrary absolute/unbounded filesystem path
arbitrary SQL
package-manager argv
signal number
raw device/mount command
unvalidated secret material
```

## 9. Privileged Operation Security Matrix — 47 ACTIVE operations

| `ipc_operation_id` | Required `permission_id` | Required `action_id` / approved parent binding | Surface | Scope | `audit_event_id` | Failure |
|---|---|---|---|---|---|---|
| `linux_user.create` | `applications.create` | `applications.create` | WCP-USER/SYSTEM | `T` | `system.linux_user.created` | `E2` |
| `linux_user.lock` | `applications.runtime.manage` | `applications.runtime.manage` | WCP-USER/SYSTEM | `T` | `system.linux_user.locked` | `E2` |
| `linux_user.unlock` | `applications.runtime.manage` | `applications.runtime.manage` | WCP-USER/SYSTEM | `T` | `system.linux_user.unlocked` | `E2` |
| `linux_user.delete` | `applications.runtime.manage` | `applications.runtime.manage` | WCP-USER/SYSTEM | `T` | `system.linux_user.deleted` | `E3` |
| `filesystem.directory.create` | `files.write + approved provisioning binding` | `same approved parent action` | WCP-USER/SYSTEM | `T` | `system.filesystem.directory_created` | `E2` |
| `filesystem.ownership.apply` | `files.permissions.manage + approved provisioning binding` | `same approved parent action` | WCP-USER/SYSTEM | `T` | `system.filesystem.ownership_applied` | `E2` |
| `filesystem.permissions.apply` | `files.permissions.manage + approved provisioning binding` | `same approved parent action` | WCP-USER/SYSTEM | `T` | `system.filesystem.permissions_applied` | `E2` |
| `filesystem.quota.apply` | `iam.account_overrides.manage OR applications.runtime.manage` | `same approved parent action` | WCP-ADMIN or tenant workflow | `T` | `system.filesystem.quota_applied` | `E2` |
| `nginx_vhost.validate` | `approved app/domain/admin binding` | `same approved parent action` | WCP-USER/WCP-ADMIN/SYSTEM | `T/S` | `system.nginx_vhost.validated` | `E1` |
| `nginx_vhost.apply` | `approved app/domain/admin binding` | `same approved parent action` | WCP-USER/WCP-ADMIN/SYSTEM | `T/S` | `system.nginx_vhost.applied` | `E4` |
| `nginx_vhost.remove` | `approved app/domain/admin binding` | `same approved parent action` | WCP-USER/WCP-ADMIN/SYSTEM | `T/S` | `system.nginx_vhost.removed` | `E3/E4` |
| `nginx.reload` | `approved technical parent OR servers.services.manage` | `same approved parent action` | WCP-USER-parent/WCP-ADMIN/SYSTEM | `T/S` | `system.nginx.reloaded` | `E2` |
| `php_pool.validate` | `approved app/admin binding` | `same approved parent action` | WCP-USER/WCP-ADMIN/SYSTEM | `T/S` | `system.php_pool.validated` | `E1` |
| `php_pool.apply` | `approved app/admin binding` | `same approved parent action` | WCP-USER/WCP-ADMIN/SYSTEM | `T/S` | `system.php_pool.applied` | `E4` |
| `php_pool.remove` | `approved app/admin binding` | `same approved parent action` | WCP-USER/WCP-ADMIN/SYSTEM | `T/S` | `system.php_pool.removed` | `E3/E4` |
| `php_fpm.reload` | `approved technical parent OR servers.services.manage` | `same approved parent action` | WCP-USER-parent/WCP-ADMIN/SYSTEM | `T/S` | `system.php_fpm.reloaded` | `E2` |
| `certificate.deploy` | `ssl.manage` | `certificate.deploy` | BOTH/SYSTEM | `T/S` | `certificate.deployed` | `E4` |
| `certificate.remove` | `ssl.manage` | `certificate.remove` | BOTH/SYSTEM | `T/S` | `certificate.removed` | `E3/E4` |
| `database.create` | `databases.create` | `databases.create` | WCP-USER/SYSTEM | `T` | `database.created` | `E2` |
| `database.drop` | `databases.delete` | `databases.delete` | WCP-USER/SYSTEM | `T` | `database.deleted` | `E3` |
| `database_user.create` | `databases.users.manage` | `databases.users.manage` | WCP-USER/SYSTEM | `T` | `database.user_created` | `E2` |
| `database_user.drop` | `databases.users.manage` | `databases.users.manage` | WCP-USER/SYSTEM | `T` | `database.user_deleted` | `E3` |
| `database_grant.apply` | `databases.grants.manage` | `databases.grants.manage` | WCP-USER/SYSTEM | `T` | `database.grant_applied` | `E2` |
| `database_grant.revoke` | `databases.grants.manage` | `databases.grants.manage` | WCP-USER/SYSTEM | `T` | `database.grant_revoked` | `E2` |
| `firewall.policy.validate` | `network.firewall.manage` | `network.firewall.manage` | WCP-ADMIN | `S` | `network.firewall.validated` | `E1` |
| `firewall.policy.apply` | `network.firewall.manage` | `network.firewall.manage` | WCP-ADMIN | `S` | `network.firewall.applied` | `E4` |
| `firewall.policy.rollback` | `network.firewall.manage` | `network.firewall.manage` | WCP-ADMIN | `S` | `network.firewall.rolled_back` | `E4` |
| `service.status` | `servers.read` | `servers.read` | WCP-ADMIN/SYSTEM | `S` | `system.service.observed` | `E1` |
| `service.reload` | `servers.services.manage` | `servers.services.manage` | WCP-ADMIN/SYSTEM | `S` | `system.service.reloaded` | `E2` |
| `service.restart` | `servers.services.manage` | `servers.services.manage` | WCP-ADMIN/SYSTEM | `S` | `system.service.restarted` | `E2` |
| `package.refresh_metadata` | `updates.read` | `updates.read` | WCP-ADMIN/SYSTEM | `S/G` | `updates.metadata_refreshed` | `E2` |
| `package.plan` | `updates.read` | `updates.read` | WCP-ADMIN/SYSTEM | `S/G` | `updates.plan_generated` | `E1` |
| `package.apply_plan` | `updates.apply` | `updates.apply` | WCP-ADMIN/SYSTEM | `S/G` | `updates.plan_applied` | `E3` |
| `package.hold` | `updates.apply` | `updates.apply` | WCP-ADMIN/SYSTEM | `S/G` | `updates.package_held` | `E2` |
| `package.unhold` | `updates.apply` | `updates.apply` | WCP-ADMIN/SYSTEM | `S/G` | `updates.package_unheld` | `E2` |
| `server.reboot` | `servers.reboot` | `servers.reboot` | WCP-ADMIN/SYSTEM | `S` | `server.reboot.requested + server.reboot.completed` | `E3` |
| `server.shutdown` | `servers.reboot` | `servers.shutdown` | WCP-ADMIN/SYSTEM | `S` | `server.shutdown.requested + server.shutdown.completed` | `E3` |
| `process.term` | `processes.terminate` | `processes.terminate` | WCP-ADMIN/SYSTEM | `S` | `process.terminated` | `E2` |
| `process.kill` | `processes.kill` | `processes.kill` | WCP-ADMIN/SYSTEM | `S` | `process.killed` | `E3` |
| `volume.expand` | `storage.expand` | `storage.expand` | WCP-ADMIN/SYSTEM | `S` | `storage.volume_expanded` | `E5` |
| `filesystem.expand` | `storage.expand` | `storage.expand` | WCP-ADMIN/SYSTEM | `S` | `storage.filesystem_expanded` | `E5` |
| `mount.verify` | `storage.read` | `storage.read` | WCP-ADMIN/SYSTEM | `S` | `storage.mount_verified` | `E1` |
| `node.drain` | `clusters.failover` | `clusters.failover` | WCP-ADMIN/SYSTEM | `C` | `cluster.node.drained` | `E6` |
| `node.uncordon` | `clusters.failover` | `clusters.failover` | WCP-ADMIN/SYSTEM | `C` | `cluster.node.uncordoned` | `E6` |
| `cluster.failover.prepare` | `clusters.failover` | `clusters.failover` | WCP-ADMIN/SYSTEM | `C` | `cluster.failover.prepared` | `E6` |
| `cluster.failover.execute` | `clusters.failover` | `clusters.failover` | WCP-ADMIN/SYSTEM | `C` | `cluster.failover.executed` | `E6` |
| `cluster.failover.verify` | `clusters.failover` | `clusters.failover` | WCP-ADMIN/SYSTEM | `C` | `cluster.failover.verified` | `E1/E6` |

Technical sub-operations such as `nginx.reload`, `php_fpm.reload` and bounded filesystem provisioning may execute as steps of an already-authorized parent workflow. They do not require an unrelated WCP-ADMIN permission merely because the underlying system primitive is privileged.

`wcpd` validates the origin `(permission_id, action_id)` binding; `wcp-privd` enforces the bounded `ipc_operation_id` and target constraints.

## 10. Operation-specific hardening rules

- Unix accounts: no arbitrary supplementary groups, home path or shell; hosting identity is registry-derived.
- Filesystem: no absolute/raw caller path; no `0777` by default; no setuid/setgid unless separately approved.
- Nginx/PHP config: staged validation, revision binding, atomic replacement, bounded service reload.
- Certificates: certificate/secret IDs only; private keys never echoed; target services allowlisted.
- Hosted database operations: registry-backed engine/resource IDs; no arbitrary SQL payload.
- Firewall: structured policy only; no raw nftables/iptables command text.
- Services: registered `service_id` only; no arbitrary systemd unit name.
- Packages: WCP-generated immutable plans; no caller package-manager argv.
- Server lifecycle: privileged session + fresh MFA/policy + audit/job correlation.
- Process TERM/KILL: process resource + PID generation/start identity revalidation; no arbitrary signal.
- Storage: registry-backed volume/filesystem/mount IDs; shrink absent.
- Cluster/HA: registered targets; operation-ID protection; failback is separate.

## 11. Audit security

```text
audit_event_id = semantic event TYPE
audit_record_id = immutable persisted OCCURRENCE
```

Mandatory audit intent is durable before effectful non-DB dispatch.

External effect flow:

```text
durable intent/outbox
→ typed dispatch
→ observe
→ durable final outcome
```

If effect may have occurred but final state is uncertain:

```text
UNKNOWN_OUTCOME
→ no blind retry
→ inspect observed state
→ reconcile
→ final audit
```

Required audit is never silently sampled/dropped.

Never log passwords, private keys, full tokens, session cookies, DB/SMTP/DNS secrets, MFA seeds, recovery codes or full sensitive request/IPC payloads.

## 12. Metric-label policy

```text
tenant_id      = FORBIDDEN BY DEFAULT
resource_id    = FORBIDDEN BY DEFAULT
request_id     = FORBIDDEN BY DEFAULT
correlation_id = FORBIDDEN BY DEFAULT
job_id         = FORBIDDEN BY DEFAULT
```

`tenant_id` exception only after explicit Security + SRE + Performance review for a bounded documented metric family.

Tenant diagnostics remain available through structured logs, audit, traces, job history, reconciliation records, authorized queries and tenant-scoped diagnostic views.

## 13. Browser / Web Security Contract

### Surface/session isolation

```text
WCP-USER session != WCP-ADMIN session
```

Required: separate origin, cookie name, server-side session namespace, surface claim and runtime Unix identity.

| Policy | WCP-USER | WCP-ADMIN |
|---|---|---|
| cookie | `__Host-wcp_user_session` | `__Host-wcp_admin_session` |
| idle timeout | 30 min | 15 min |
| absolute lifetime | 12 h | 8 h |
| remember-me | disabled baseline | forbidden |
| MFA at login | policy/account dependent | mandatory |
| fresh MFA high-risk | <=10 min when required | <=5 min |
| privileged child context | unavailable | idle 5 min / absolute 15 min |
| break-glass | unavailable | max 15 min, no auto-extension |

Cookie policy:

```text
Secure
HttpOnly
SameSite=Strict
Path=/
host-only
no Domain attribute
```

Session identifier: opaque, >=128 bits cryptographically unpredictable, rotated after login, MFA/auth-strength change, privilege elevation, impersonation start/end and recovery/reset flows.

Normal WCP session authentication is never stored in URL/query/fragment/localStorage/sessionStorage.

### CSRF / Origin

State-changing browser requests require:

```text
valid session
+ synchronizer CSRF token
+ exact expected Origin
```

AJAX uses `X-WCP-CSRF`. Invalid/missing CSRF or Origin → `403`, no mutation, security evidence.

`GET/HEAD/OPTIONS` must not produce business mutation.

### CORS

Session APIs are same-origin by default. Credentialed wildcard CORS is forbidden. Future cross-origin APIs require a separate API-auth contract and exact origin allowlist.

### XSS / CSP / clickjacking

Untrusted content is data, not executable markup. Contextual encoding is mandatory. Raw untrusted HTML is forbidden by default; approved rich HTML must be sanitized.

Forbidden without separately reviewed component: untrusted `innerHTML`, untrusted `v-html`, `eval`, `new Function`, `javascript:` URLs, data-derived event-handler attributes.

Minimum CSP:

```text
default-src 'self';
base-uri 'none';
object-src 'none';
frame-ancestors 'none';
form-action 'self';
script-src 'self' 'nonce-<per-response-nonce>';
style-src 'self' 'nonce-<per-response-nonce>';
img-src 'self' data: blob:;
font-src 'self';
connect-src 'self' <explicit-current-surface-wss-endpoints>;
frame-src 'none';
```

No `unsafe-eval`, no global `unsafe-inline`, no arbitrary external CDN scripts.

Headers include:

```text
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
Referrer-Policy: no-referrer
Cross-Origin-Opener-Policy: same-origin
Cross-Origin-Resource-Policy: same-origin
Strict-Transport-Security: max-age=31536000
```

### SSRF

Remote access uses typed adapters; generic untrusted `fetch(url)` into privileged/internal services is forbidden.

Default WCP-USER deny: loopback, private, link-local, unspecified, multicast, metadata and other non-global targets.

Reject unapproved schemes including `file`, `ftp`, `gopher`, `data`, `javascript`, `unix`.

Resolve DNS and validate every address before connection. Maximum redirects: 3; each redirect is re-parsed, re-resolved and revalidated.

WCP-ADMIN internal diagnostics use registry-backed server/node/service/endpoint IDs, never arbitrary internal URLs/IPs.

### Open redirects

`return_to`, `next`, `redirect`, `continue` resolve only to named internal routes or normalized same-origin relative paths. External/scheme-relative/encoded/backslash/userinfo-host bypasses are rejected.

### Reauthentication / privileged sessions

WCP-ADMIN MFA is mandatory. High-impact infrastructure actions require fresh MFA <=5 minutes.

WCP-USER high-risk actions use policy-driven fresh auth <=10 minutes where required.

Starting privileged ADMIN context requires `privilege.elevate`, fresh MFA, explicit scope, audit start and expiry. Privilege does not mutate persistent roles and never bypasses action permissions.

### Rate limits

Security-sensitive endpoints declare a rate-limit class:

```text
AUTH_LOGIN
MFA_CHALLENGE
ACCOUNT_RECOVERY
SENSITIVE_MUTATION
PRIVILEGED_MUTATION
UPLOAD
EXPORT
```

Rate limiting is additional to authorization and must not reveal account existence.

### Upload / download

Uploads bind to actor, surface, Tenant/resource, permission, purpose and API-defined maximum size.

Temporary upload staging is non-executable and never under `/usr/lib/wcp360`, `/etc/wcp360`, `/run/wcp360`, secret directories or control-plane web roots.

Client filename is metadata, not filesystem authority. Archives reject traversal, absolute members, symlink/hardlink escape and device nodes.

Downloads reauthorize at download time. Browser raw filesystem path is forbidden. Cross-Tenant download is denied.

### Secrets / errors / cache

Secret policies:

```text
MASKED
SHOW_ONCE
NEVER_REVEAL
```

Secrets never appear in URLs, logs, audit payloads, notifications, email alerts, support bundles, exported diagnostics or client error traces.

SHOW_ONCE responses are `Cache-Control: no-store`; NEVER_REVEAL has no retrieval endpoint.

Normal errors expose only stable safe `error_code`, safe message, `correlation_id` and safe field details. No stack trace, SQL, filesystem path, command line, environment, secret/token/session ID/private key or raw upstream credential response.

Authenticated HTML and sensitive APIs use:

```text
Cache-Control: no-store
```

### Logout/revocation

Logout revokes server session, cookie, CSRF state and session-bound long-lived channels. ADMIN logout also revokes privileged, impersonation and break-glass contexts.

Role/permission/Tenant/delegation revocation is enforced from current authoritative state and is not deferred until session expiry.

## 14. Negative security test traceability

Privileged negative catalog:

```text
N01 unauthorized Unix peer
N02 wrong surface
N03 bad permission/action binding
N04 cross-Tenant target
N05 invalid ownership/membership/delegation
N06 malformed schema
N07 unknown IPC operation
N08 arbitrary command/executable
N09 path traversal
N10 symlink/mount/TOCTOU escape
N11 arbitrary signal
N12 arbitrary systemd unit
N13 arbitrary package argv/tampered plan
N14 duplicate/idempotency violation
N15 timeout/lost ACK → UNKNOWN_OUTCOME
N16 concurrent conflict
N17 mandatory audit unavailable
N18 reconciliation after uncertainty
N19 secret echo
N20 parameter boundary
N21 arbitrary SQL
N22 stale PID/PID reuse
N23 unregistered service ID
N24 invalid/stale resource ID
N25 destructive blind retry
N26 privilege downgrade/fallback
```

Browser test family:

```text
TEST-SEC-WEB-001 → TEST-SEC-WEB-040 = READY
```

Covers cross-surface cookies, fixation, session rotation/expiry/revocation, CSRF, Origin, CORS, XSS, CSP, framing, SSRF, redirects, upload/download, secrets, cache/error leakage, MFA/privileged expiry, reseller non-escalation and rate-limit/authorization separation.

Implementation-dependent tests remain `PLANNED` or `READY`. No fabricated PASS/FAIL while `CODE-BASELINE = NONE`.

## 15. Final status

```text
PUBLICATION DATE = 2026-09-29
BASELINE MANIFEST = WCP360-BM-0001 FINAL
DOC-BASELINE = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4
CODE-BASELINE = NONE
```

```text
PERMISSION REGISTRY VERSION = v1.0
SECURITY BASELINE VERSION = v1.0

PERMISSION COUNT = 82
PRIVILEGED IPC OPERATIONS = 47

SEC-H3V2-001 = RESOLVED
SEC-H3V2-002 = RESOLVED

API / IPC ALIGNMENT = CONFIRMED
DATABASE / TENANT ALIGNMENT = CONFIRMED
TEST TRACEABILITY = COMPLETE

FINAL APPROVED CONTENT = YES
HISTORICAL CONTENT INCLUDED = NO
CONTENT CHANGE = NONE
READY FOR SOURCE CODE CONSUMPTION = YES

SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```