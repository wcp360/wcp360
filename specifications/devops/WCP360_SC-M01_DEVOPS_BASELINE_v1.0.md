# WCP360 — SC-M01 CANONICAL DEVOPS ARTIFACT

**Artifact:** `WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md`  
**Owner:** `WCP360 — DEVOPS / BUILD / RELEASE`  
**Reference:** `WCP360-BM-0001 FINAL / SC-M01 TARGETED EVIDENCE`  
**DevOps Baseline Version:** `v1.0`  
**OS Support Matrix Version:** `v1.0`  
**Status:** `FINAL APPROVED CONTENT`  
**Content Change:** `NONE`

---

## 1. Supported Initial Platform

```text
Distribution = Debian GNU/Linux 13 (trixie)
Architecture = amd64 / x86_64
Support Status = INITIAL OFFICIAL TARGET
```

This is the initial officially supported WCP360 platform target.

---

## 2. Canonical Runtime Architecture

```text
Backend
→ wcpd (non-root)
→ bounded authenticated IPC
→ wcp-privd (root minimal)
→ explicit typed privileged primitive
```

The following are forbidden:

```text
shell.exec
arbitrary command execution
generic sudo proxy
generic systemctl proxy
arbitrary filesystem write
arbitrary executable
arbitrary signal
backend → wcp-privd direct
```

WCP-USER and WCP-ADMIN remain distinct application and security surfaces.

---

## 3. Canonical Unix Identities and Groups

| Logical role | Unix identity | Primary group | Supplementary / access group | Privilege |
|---|---|---|---|---|
| WCP-USER runtime | `wcp360-user` | `wcp360-user` | `wcp360-control` | non-root |
| WCP-ADMIN runtime | `wcp360-admin` | `wcp360-admin` | `wcp360-control` | non-root |
| Control daemon | `wcpd` | `wcpd` | `wcp360-priv` | non-root |
| Privileged daemon | `wcp-privd` service context | `root` | none required | minimal root |
| Worker | `wcp360-worker` | `wcp360-worker` | none by default | non-root |
| Installer / updater | transient `root` execution context | `root` | N/A | temporary lifecycle privilege |
| Hosting identity | per-hosting dedicated identity | private per-hosting group | no control-plane group | non-root |

Persistent control-plane service identities:

```text
interactive login = disabled
shell = /usr/sbin/nologin
```

No persistent privileged installer/updater account is created.

### Security invariants

```text
WCP-USER runtime != WCP-ADMIN runtime
WCP-USER runtime != wcpd
WCP-ADMIN runtime != wcpd
wcpd != wcp-privd
hosting identity != control-plane identity

wcp360-control != wcp360-priv
```

`wcp360-control` grants transport access to the control boundary only. It does not grant application authorization or privileged IPC access.

Only `wcpd` among persistent non-root WCP360 service identities is permitted membership in `wcp360-priv`.

---

## 4. Canonical Filesystem Layout

### 4.1 Package-managed product root

```text
/usr/lib/wcp360
```

Purpose:

- immutable private WCP360 application/service assets;
- package-managed product files;
- private helpers and runtime assets that belong to the installed product.

Ownership:

```text
owner = root
group = root
runtime write access = forbidden
```

Historical `/usr/local/wcp360` is not the active product root.

Historical `/opt/wcp` remains superseded.

### 4.2 Configuration

```text
/etc/wcp360
```

Purpose:

- administrator/system configuration;
- service-specific configuration;
- consumer-specific protected configuration;
- secret storage only where explicitly classified.

Runtime services do not receive recursive configuration write access by default.

### 4.3 Persistent operational state

```text
/var/lib/wcp360
```

Purpose:

- persistent operational materializations;
- service-specific non-DB runtime state;
- reconstructable generated artifacts where approved.

It is **not** an independent authoritative control-plane business-state database.

Authoritative logical control-plane state is PostgreSQL.

### 4.4 Logs

```text
/var/log/wcp360
```

Used only where file logging is required.

Structured journald logging may be used for system services.

Secrets, full tokens, passwords, private keys and sensitive credentials must never be logged.

### 4.5 Cache

```text
/var/cache/wcp360
```

Content must be reconstructable.

Cache loss must not redefine control-plane state.

### 4.6 Runtime

```text
/run/wcp360
```

Purpose:

- Unix sockets;
- runtime metadata;
- node-local locks where explicitly appropriate;
- ephemeral service state.

Runtime data is not backed up and does not survive reboot as authoritative state.

### 4.7 Hosted tenant data

Hosted tenant data is outside the generic WCP360 control-plane state tree and remains bound to tenant/hosting isolation contracts.

```text
CONTROL-PLANE STATE
!= HOSTED TENANT DATA
```

---

## 5. Socket and IPC Physical Requirements

### 5.1 Control boundary

Logical relationship:

```text
WCP-USER / WCP-ADMIN backend runtime
→ control IPC
→ wcpd
```

Control transport access is restricted through:

```text
control group = wcp360-control
```

Transport access does not replace authentication, authorization, entitlement, ownership, delegation, tenant or policy checks.

### 5.2 Privileged IPC-B boundary

Canonical privileged relationship:

```text
wcpd
→ IPC-B
→ wcp-privd
```

Physical contract:

```text
socket path = /run/wcp360/priv/wcp-privd.sock
owner       = root
group       = wcp360-priv
mode        = 0660
authorized client = wcpd only
```

Parent runtime directory:

```text
path  = /run/wcp360/priv
owner = root
group = wcp360-priv
mode  = 0750
```

Peer authentication:

```text
Linux SO_PEERCRED
peer UID must resolve to the Unix identity wcpd
```

Direct access is forbidden for:

```text
wcp360-user
wcp360-admin
wcp360-worker
hosting identities
arbitrary local users
```

Privileged IPC uses the approved typed-operation contract. Unknown operations fail closed. No generic privileged fallback exists.

---

## 6. Secrets

Secrets are consumer-specific.

Canonical requirements:

- no universal WCP360 secret-readable group;
- runtime services may read only the secrets explicitly assigned to that consumer;
- runtime services do not write or rotate their own secrets by default;
- bootstrap/rotation is performed by controlled lifecycle mechanisms;
- release signing material is not available to normal WCP360 runtime services;
- secrets must not appear in Git, package metadata, logs, traces, metric labels or frontend assets.

Filesystem secret roots, where used, remain under the protected WCP360 configuration hierarchy.

---

## 7. Service / Daemon Identities

### `wcpd`

```text
identity = wcpd
privilege = non-root
role = control/orchestration daemon
privileged IPC role = client
generic sudo = forbidden
generic command execution = forbidden
```

### `wcp-privd`

```text
service = wcp-privd
execution identity = root
role = minimal privileged broker
privileged IPC role = server
authorized runtime caller = wcpd only
```

Root execution does not imply an unrestricted service contract. The broker may execute only explicitly approved typed privileged operations.

### Worker

```text
identity = wcp360-worker
privilege = non-root
direct privileged IPC = forbidden
```

Workers that require privileged effects must use the normal control path rather than direct access to `wcp-privd`.

---

## 8. Build Foundation

Official build/release artifacts must be traceable to:

- Git commit;
- release/version identifier;
- approved documentation baseline;
- approved design baseline;
- toolchain;
- dependency/lock state;
- build target;
- artifact digest;
- CI provenance.

Official artifacts must not depend on undeclared files from a developer workstation.

Builds are expected to be reproducible from declared inputs.

---

## 9. Package / Release Foundation

WCP360 release artifacts must support:

```text
build
→ validate
→ package
→ verify
→ sign
→ publish
→ promote the same artifact
```

Required principles:

- package-managed code is immutable at runtime;
- package contents never include production secrets;
- artifact integrity is verified with cryptographic digests;
- artifact authenticity is verified by the approved signing mechanism;
- release-signing keys are outside the source repository and runtime product tree;
- upgrades must verify release compatibility before mutation;
- WCP-USER, WCP-ADMIN, `wcpd`, `wcp-privd`, workers and migrations must retain their security boundaries regardless of physical package granularity.

---

## 10. Installation Foundation

Installation must be structured around:

```text
preflight
→ platform validation
→ dependency validation
→ artifact verification
→ system identity/group creation
→ filesystem creation
→ ownership/permission application
→ package installation
→ configuration/bootstrap
→ secret bootstrap
→ control-plane database bootstrap/migration orchestration
→ service deployment
→ startup in dependency order
→ health/readiness verification
→ installation completion
```

The installer must fail closed on critical incompatibility or artifact-verification failure.

No generic persistent root installer daemon is part of the approved architecture.

---

## 11. Upgrade / Rollback / Recovery Foundation

Upgrade must anticipate:

- release authenticity/integrity verification;
- compatibility checks;
- pre-upgrade backup;
- database migration orchestration;
- filesystem/configuration migration where required;
- service ordering;
- health/readiness validation;
- recovery after partial failure.

Required distinctions:

```text
retry != reconciliation
rollback != compensation
execution success != verified success
```

Rollback must distinguish binary, configuration, filesystem/service and database/state compatibility.

Recovery must remain possible when the WCP-USER/WCP-ADMIN web surfaces are unavailable.

---

## 12. Uninstall Foundation

Normal uninstall must distinguish:

```text
REMOVE WCP360 SOFTWARE
!= PURGE CONTROL-PLANE STATE
!= DELETE HOSTED/TENANT DATA
```

Tenant websites, hosted databases, mail data, backups, certificates and other hosted customer content must not be destroyed implicitly by ordinary WCP360 package removal.

System identities must not be removed while owned files/processes requiring them still remain.

---

## 13. State-Authority Constraint

```text
PostgreSQL
= authoritative logical control-plane state

/var/lib/wcp360
= persistent operational materialization / node-local or reconstructable state

/run/wcp360
= ephemeral runtime

/var/cache/wcp360
= reconstructable cache

Linux / services / filesystem
= observed physical state
```

There must not be two independent writable authorities for the same logical state without an explicitly approved consistency contract.

---

## 14. SC-M01 Implementation Consumption Rules

SOURCE CODE may consume this artifact only together with the remaining canonical owner artifacts and the final `WCP360-BM-0001`.

This artifact does not itself authorize implementation.

It does not create:

- a repository;
- source code;
- installer code;
- systemd unit files;
- packages;
- migrations;
- database DDL;
- `CODE-BASELINE`.

```text
SOURCE CODE = PAUSED
CODE-BASELINE = NONE
SC-M02 = NOT AUTHORIZED
```

---

## 15. Canonical Publication Metadata

```text
OWNER =
DEVOPS / BUILD / RELEASE

ARTIFACT =
WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md

DEVOPS BASELINE VERSION =
v1.0

OS SUPPORT MATRIX VERSION =
v1.0

SUPPORTED OS =
Debian GNU/Linux 13 (trixie) / amd64 (x86_64)

FINAL APPROVED CONTENT =
YES

HISTORICAL CONTENT INCLUDED =
NO

CONTENT CHANGE =
NONE

READY FOR SOURCE CODE CONSUMPTION =
YES

BLOCKER =
NONE
```