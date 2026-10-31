# WCP360 — SC-M01 Test Baseline v1.0 — MASTER Preparatory Draft

**Status:** `DRAFT — NOT TESTS / QA AUTHORITY`  
**Prepared by:** `WCP360 — MASTER / Project Control`  
**Required owner:** `WCP360 — TESTS / QA`  
**Reference:** `WCP360-BM-0001 FINAL / SC-M01 TARGETED EVIDENCE`

This document is a preparation aid only.

It **must not** be used to satisfy the SC-M01 gate until TESTS / QA explicitly reviews, corrects as needed, publishes its own final canonical artifact, and declares it source-code consumable.

## 1. Baseline metadata

```text
TEST BASELINE VERSION = v1.0
SC-M01 SCOPE = FIRST IMPLEMENTATION FOUNDATION
CODE-BASELINE = NONE

IMPLEMENTATION-DEPENDENT RESULT STATES =
PLANNED / READY / BLOCKED

FABRICATED PASS = FORBIDDEN
FABRICATED FAIL = FORBIDDEN
```

## 2. Canonical source set already present in GitHub

```text
API CONTRACT VERSION = 1.0
IPC CONTRACT VERSION = WCP360-IPC-B/1
PERMISSION REGISTRY VERSION = v1.0
SECURITY BASELINE VERSION = v1.0
DEVOPS BASELINE VERSION = v1.0
```

DB Model v1.0 is owner-final but its exact file body is still pending GitHub materialization under issue #6.

Engineering Ruleset v0.2 is owner-final but its exact file body is still pending GitHub materialization under issue #9.

Therefore detailed DB-physical and Engineering-rule test clauses remain `BLOCKED ON CANONICAL ARTIFACT MATERIALIZATION` where exact wording matters.

## 3. API contract test inventory

Existing canonical references:

```text
COUNT = 86
TEST-API-0001
TEST-API-0002
TEST-API-0003
TEST-API-0004
TEST-API-0005
TEST-API-0006
TEST-API-0007
TEST-API-0008
TEST-API-0101
TEST-API-0102
TEST-API-0103
TEST-API-0104
TEST-API-0105
TEST-API-0106
TEST-API-0201
TEST-API-0202
TEST-API-0203
TEST-API-0204
TEST-API-0205
TEST-API-0301
TEST-API-0302
TEST-API-0303
TEST-API-0304
TEST-API-0305
TEST-API-0306
TEST-API-0307
TEST-API-0308
TEST-API-0401
TEST-API-0402
TEST-API-0403
TEST-API-0404
TEST-API-0405
TEST-API-0406
TEST-API-0407
TEST-API-0501
TEST-API-0502
TEST-API-0503
TEST-API-0504
TEST-API-0505
TEST-API-0601
TEST-API-0602
TEST-API-0603
TEST-API-0604
TEST-API-0605
TEST-API-0701
TEST-API-0702
TEST-API-0703
TEST-API-0704
TEST-API-0705
TEST-API-0706
TEST-API-0707
TEST-API-0708
TEST-API-0709
TEST-API-0801
TEST-API-0802
TEST-API-0803
TEST-API-0804
TEST-API-0805
TEST-API-0806
TEST-API-0807
TEST-API-0808
TEST-API-0809
TEST-API-0810
TEST-API-0811
TEST-API-0812
TEST-API-0813
TEST-API-0814
TEST-API-0815
TEST-API-0816
TEST-API-0901
TEST-API-0902
TEST-API-0903
TEST-API-0904
TEST-API-0905
TEST-API-0906
TEST-API-1001
TEST-API-1002
TEST-API-1003
TEST-API-1004
TEST-API-1005
TEST-API-1006
TEST-API-1007
TEST-API-1008
TEST-API-1009
TEST-API-1010
TEST-API-1011
```

For each API contract reference, validate at minimum the contract-defined:

- surface;
- authentication profile;
- authorization/permission binding;
- tenant/resource scope;
- request schema;
- response schema;
- error profile;
- timeout;
- retry behavior;
- idempotency behavior;
- audit binding;
- security profile;
- compatibility semantics.

Status before implementation:
```text
READY / PLANNED
```

## 4. IPC contract test inventory

Existing canonical references:

```text
COUNT = 47
TEST-IPC-1001
TEST-IPC-1002
TEST-IPC-1003
TEST-IPC-1004
TEST-IPC-1005
TEST-IPC-1006
TEST-IPC-1007
TEST-IPC-1008
TEST-IPC-1009
TEST-IPC-1010
TEST-IPC-1011
TEST-IPC-1012
TEST-IPC-1013
TEST-IPC-1014
TEST-IPC-1015
TEST-IPC-1016
TEST-IPC-1017
TEST-IPC-1018
TEST-IPC-1019
TEST-IPC-1020
TEST-IPC-1021
TEST-IPC-1022
TEST-IPC-1023
TEST-IPC-1024
TEST-IPC-1025
TEST-IPC-1026
TEST-IPC-1027
TEST-IPC-1028
TEST-IPC-1029
TEST-IPC-1030
TEST-IPC-1031
TEST-IPC-1032
TEST-IPC-1033
TEST-IPC-1034
TEST-IPC-1035
TEST-IPC-1036
TEST-IPC-1037
TEST-IPC-1038
TEST-IPC-1039
TEST-IPC-1040
TEST-IPC-1041
TEST-IPC-1042
TEST-IPC-1043
TEST-IPC-1044
TEST-IPC-1045
TEST-IPC-1046
TEST-IPC-1047
```

For each IPC operation, validate:

- Unix peer authentication;
- denial of non-`wcpd` caller;
- protocol version;
- operation version;
- strict schema;
- max request/response size behavior;
- resource/scope validation;
- bounded primitive selection;
- generic-exec impossibility;
- timeout behavior;
- idempotency/replay contract;
- concurrency behavior;
- `UNKNOWN_OUTCOME` handling;
- reconciliation requirement;
- audit durability;
- secret redaction;
- operation-specific postcondition verification.

Status before implementation:
```text
READY / PLANNED
```

## 5. Permission / authorization coverage

Canonical active permission count:

```text
82
```

All 82 legitimate `permission_id` values require authorization coverage.

Typed namespaces must remain distinct:

```text
permission_id != action_id != job_type != event_id
permission_id != audit_event_id != ipc_operation_id != error_code
```

Explicit namespace regression targets:

```text
domains.record.update       = action_id
dns.change.apply            = job_type
dns.record.updated          = audit_event_id
process.kill                = job_type / ipc_operation_id
server.reboot               = job_type / ipc_operation_id
server.reboot.requested     = audit_event_id
server.reboot.completed     = audit_event_id
```

Tests must prove these identifiers do not become permissions by lexical overlap.

## 6. Privileged negative-security catalog

Existing catalog:

```text
COUNT = 26
N01
N02
N03
N04
N05
N06
N07
N08
N09
N10
N11
N12
N13
N14
N15
N16
N17
N18
N19
N20
N21
N22
N23
N24
N25
N26
```

Required semantics:

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

Status before implementation:
```text
READY
```

## 7. Browser / web security family

Existing canonical family:

```text
TEST-SEC-WEB-001 → TEST-SEC-WEB-040 = READY
```

Coverage includes:

- WCP-USER / WCP-ADMIN cookie separation;
- session fixation;
- session rotation;
- expiry;
- revocation;
- CSRF;
- exact Origin enforcement;
- CORS;
- XSS;
- CSP;
- clickjacking/framing;
- SSRF;
- open redirects;
- upload handling;
- download reauthorization;
- secret exposure;
- cache/error leakage;
- MFA expiry/freshness;
- privileged-session expiry;
- reseller non-escalation;
- rate-limit vs authorization separation.

## 8. Architecture-boundary tests

Required invariants:

```text
WCP-USER != WCP-ADMIN

backend
→ wcpd (non-root)
→ bounded authenticated IPC
→ wcp-privd (minimal root)
→ explicit privileged primitive
```

Must prove forbidden paths remain impossible:

```text
backend → wcp-privd direct
web/UI → privileged OS command direct
worker → privileged IPC direct
wcp360-user → privileged IPC direct
wcp360-admin → privileged IPC direct
```

## 9. Generic privileged execution prohibition

Tests must demonstrate no accepted contract allows:

```text
shell.exec
arbitrary command
arbitrary executable
generic sudo proxy
generic systemctl proxy
arbitrary filesystem target
arbitrary package-manager argv
arbitrary SQL
arbitrary signal
generic root terminal
```

A test is not satisfied merely because the UI does not expose the operation; the backend/IPC boundary must reject or make it structurally impossible.

## 10. Tenant / ownership boundaries

Required invariants:

```text
account_id != organization_id != tenant_id

membership != ownership
delegation != ownership

Tenant = security/data isolation boundary
```

Cross-tenant access must be denied unless an explicit valid owner/delegation contract authorizes the resource operation.

Detailed physical DB constraint tests remain:

```text
BLOCKED ON ISSUE #6
```

until the exact DB Model v1.0 artifact is in GitHub.

## 11. Control-plane / hosted-database separation

Required invariant:

```text
CONTROL-PLANE POSTGRESQL
!= HOSTED DB ENGINES
!= TENANT / APPLICATION DB CONTENT
```

Tests must ensure control-plane contracts never silently treat hosted DB content as control-plane schema/state.

Detailed DB physical tests remain blocked on issue #6.

## 12. Failure / recovery semantics

Required invariants:

```text
timeout != definite failure
retry != reconciliation
rollback != compensation
execution success != verified success
```

Supported outcomes include:

```text
SUCCEEDED
FAILED
PARTIALLY_SUCCEEDED
UNKNOWN_OUTCOME
CANCELLED
TIMED_OUT
COMPENSATION_REQUIRED
RECONCILIATION_REQUIRED
```

Tests must verify that uncertain external effects are not blindly retried.

## 13. Audit durability tests

Required classes:

### Pure control-plane DB mutation

```text
business mutation + mandatory audit/outbox
= same transaction where applicable
fail closed if mandatory durability cannot be achieved
```

### External / privileged effect

```text
durable intent before dispatch
→ typed dispatch
→ observe
→ durable final result/audit
```

If effect may have happened but final state cannot be proven:

```text
UNKNOWN_OUTCOME
→ no blind retry
→ observe
→ reconcile
→ final audit
```

Secret material must never appear in audit payloads.

## 14. DevOps / platform tests

Initial supported platform:

```text
Debian GNU/Linux 13 (trixie)
amd64 / x86_64
```

Required runtime identities:

```text
wcp360-user
wcp360-admin
wcpd
wcp-privd / root minimal
wcp360-worker
```

Required groups:

```text
wcp360-control
wcp360-priv
```

Filesystem/state authority:

```text
/usr/lib/wcp360   = package-managed; runtime write forbidden
/etc/wcp360       = protected configuration
/var/lib/wcp360   = operational materialization
/var/cache/wcp360 = reconstructable
/run/wcp360       = ephemeral
PostgreSQL        = authoritative logical control-plane state
```

IPC-B physical boundary:

```text
/run/wcp360/priv/wcp-privd.sock
owner = root
group = wcp360-priv
mode = 0660
authorized client = wcpd only
peer authentication = SO_PEERCRED
```

Test families must cover where applicable to SC-M01:

- platform preflight;
- identity/group separation;
- no-login service identities;
- filesystem ownership/mode expectations;
- runtime-write denial to package root;
- socket ownership/mode;
- peer denial;
- secret exclusion from Git/packages/logs;
- build input/provenance requirements;
- artifact integrity/authenticity verification;
- install fail-closed behavior;
- service dependency/startup ordering;
- health/readiness verification;
- upgrade compatibility;
- pre-upgrade backup requirement;
- rollback compatibility distinctions;
- recovery without web surfaces;
- uninstall preserving hosted tenant data.

## 15. Repository / bootstrap validation

The final QA owner artifact should define tests for the first implementation foundation, including:

- expected repository structure;
- separation of WCP-USER, WCP-ADMIN, backend/daemon and privileged boundaries;
- configuration-example safety;
- secret-exclusion checks;
- dependency lock/reproducibility expectations;
- static contract-reference checks;
- unit/integration test harness availability;
- CI gate behavior;
- no cancelled Sprint 0 code;
- no cancelled V4.1 executable-contract code.

Exact Engineering-specific quality gates remain blocked on issue #9 until the canonical Engineering export body is present.

## 16. Bug / regression rule

Every corrected bug must receive a regression test before closure.

No bug may be marked verified based only on code inspection when an executable regression test is applicable.

## 17. Final owner action required

TESTS / QA must review this draft and publish an owner artifact that declares:

```text
OWNER = TESTS / QA
ARTIFACT = <EXACT FINAL .md NAME>
TEST BASELINE VERSION = v1.0
SC-M01 TEST REQUIREMENTS = INCLUDED
FINAL APPROVED CONTENT = YES
HISTORICAL CONTENT INCLUDED = NO
CONTENT CHANGE = NONE
READY FOR SOURCE CODE CONSUMPTION = YES
BLOCKER = NONE
```

Until that owner publication exists:

```text
ISSUE #7 = OPEN
SC-M01 CANONICAL EVIDENCE = 5 / 8
SOURCE CODE = PAUSED
CODE-BASELINE = NONE
```
