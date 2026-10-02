# WCP360 — SC-M01 QA Canonical Input Pack

**Status:** `OWNER INPUT ONLY — NOT TEST BASELINE AUTHORITY`  
**Producer:** `WCP360 — MASTER / Project Control`  
**Consumer:** `WCP360 — TESTS / QA`  
**Reference:** `WCP360-BM-0001 FINAL / SC-M01 TARGETED EVIDENCE`

This file does **not** replace the TESTS / QA owner artifact.

Its purpose is to remove rediscovery work by extracting test obligations already present in the canonical implementation artifacts committed to GitHub.

## 1. Canonical sources

```text
specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md
```

Only these committed canonical artifacts were used for this input pack.

## 2. Existing API test references

Canonical API contract references:

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

QA must preserve the semantics defined by each corresponding API contract, including authentication, authorization, surface, permission, tenant/resource scope, schema validation, timeout, retry/idempotency, audit, security and compatibility behavior.

## 3. Existing IPC test references

Canonical IPC contract references:

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

For every privileged IPC operation, the canonical IPC artifact requires coverage of:

- peer authentication;
- non-`wcpd` denial;
- protocol and operation version;
- schema validation;
- oversize rejection;
- resource/scope validation;
- bounded primitive selection;
- generic-exec impossibility;
- timeout behavior;
- idempotency;
- concurrency;
- `UNKNOWN_OUTCOME` reconciliation;
- audit durability;
- secret redaction;
- operation-specific postcondition verification.

## 4. Permission / authorization coverage

Canonical permission registry:

```text
ACTIVE permission_id COUNT = 82
```

The final QA artifact must preserve authorization test coverage across the 82 legitimate permission identifiers.

Typed namespace separation must be tested:

```text
permission_id != action_id != job_type != event_id
permission_id != audit_event_id != ipc_operation_id != error_code
```

Explicit cross-namespace exclusions include:

```text
domains.record.update
dns.change.apply
dns.record.updated
process.kill
server.reboot
server.reboot.requested
server.reboot.completed
```

These identifiers must never regain authority merely because lexical names overlap.

## 5. Security negative catalog

The Security Baseline already defines the following privileged negative-test catalog:

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

The catalog covers:

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

## 6. Browser security family

Canonical family:

```text
TEST-SEC-WEB-001 → TEST-SEC-WEB-040 = READY
```

Required areas already defined by Security include:

- cross-surface cookie isolation;
- session fixation;
- session rotation / expiry / revocation;
- CSRF;
- Origin validation;
- CORS;
- XSS;
- CSP;
- framing;
- SSRF;
- open redirects;
- upload/download handling;
- secrets and cache/error leakage;
- MFA / privileged-session expiry;
- reseller non-escalation;
- rate-limit / authorization separation.

## 7. SC-M01 DevOps validation obligations

The final QA artifact must include test requirements for the already-approved DevOps foundation:

### Platform

```text
Debian GNU/Linux 13 (trixie)
amd64 / x86_64
```

### Runtime identity separation

```text
wcp360-user
wcp360-admin
wcpd
wcp-privd / root minimal
wcp360-worker
wcp360-control
wcp360-priv
```

Required validation includes no-login identities, group separation and denial of privileged IPC to non-`wcpd` identities.

### Filesystem authority

```text
/usr/lib/wcp360       = package-managed / runtime write forbidden
/etc/wcp360           = protected configuration
/var/lib/wcp360       = operational materialization / not business-state authority
/var/log/wcp360       = logs where applicable
/var/cache/wcp360     = reconstructable
/run/wcp360           = ephemeral
PostgreSQL            = authoritative logical control-plane state
```

### Privileged IPC physical boundary

```text
/run/wcp360/priv/wcp-privd.sock
owner = root
group = wcp360-priv
mode = 0660
authorized persistent client = wcpd only
peer verification = SO_PEERCRED
```

### Lifecycle foundations

QA requirements must cover, where applicable to SC-M01:

- build reproducibility inputs/provenance;
- package integrity/authenticity boundaries;
- secret exclusion from Git/package/logs;
- installer preflight/fail-closed behavior;
- identity/group/filesystem creation contracts;
- service deployment ordering;
- health/readiness validation;
- upgrade compatibility checks;
- pre-upgrade backup requirements;
- rollback vs compensation distinctions;
- recovery when web surfaces are unavailable;
- uninstall preservation of hosted tenant data.

## 8. Cross-domain invariants to test

```text
WCP-USER != WCP-ADMIN

account_id != organization_id != tenant_id

CONTROL-PLANE POSTGRESQL
!= HOSTED DB ENGINES
!= TENANT / APPLICATION DB CONTENT
```

Privileged chain:

```text
backend
→ wcpd (non-root)
→ bounded authenticated IPC
→ wcp-privd (minimal root)
→ explicit privileged primitive
```

Forbidden generic privilege:

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

Failure semantics:

```text
timeout != definite failure
retry != reconciliation
rollback != compensation
execution success != verified success
```

## 9. Status constraints

Until implementation exists:

```text
CODE-BASELINE = NONE

IMPLEMENTATION-DEPENDENT TEST STATUS =
PLANNED / READY / BLOCKED

FABRICATED PASS = FORBIDDEN
FABRICATED FAIL = FORBIDDEN
```

The owner may mark a test PASS/FAIL only after a concrete implementation target exists and the test is actually executed.

## 10. Required owner publication

TESTS / QA must still publish its own final artifact:

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

This input pack cannot satisfy issue #7 by itself.

## 11. Gate status

```text
SC-M01 CANONICAL EVIDENCE = 5 / 8
ISSUE #7 = OPEN
SOURCE CODE = PAUSED
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```


## 12. Post-audit QA inputs — 2026-10-02

**Status of this section:** NON-NORMATIVE OWNER INPUT. The cases below are proposal-derived test obligations. They do not reserve canonical TEST-* identifiers and they are not PASS/FAIL evidence.

### Proposal-case inventory

Current non-normative proposal families in main:

    Authorization bindings        C01  → C17   = 17
    IPC timeout / idempotency     TQ01 → TQ10  = 10
    Control boundary              CQ01 → CQ12  = 12
    API transport                 HQ01 → HQ15  = 15
    Security Web/Authz            SQ01 → SQ16  = 16
    Toolchain / Build             FQ01 → FQ15  = 15
    ------------------------------------------------
    TOTAL                                     = 85

All 85 remain:

    PLANNED
    APPLICATION EXECUTION = 0
    CANONICAL TEST ID ASSIGNMENT = NONE

QA may reuse, split, merge, rename or reject these cases when creating the final owner artifact. MASTER must not pre-assign TEST-* IDs on QA's behalf.

### Already-final semantics that tests should not treat as design choices

#### UNKNOWN_OUTCOME

IPC-B/1 already requires:

    timeout != definite failure

    execution may have occurred but cannot be proven
    → UNKNOWN_OUTCOME
    → reconciliation
    → no blind retry

    execution success != verified success

QA should test this behavior, not ask implementation to choose whether it exists.

Open test details still depend on owner decisions for the exact dispatch boundary, pre-dispatch error priority, late result retrieval and persistence.

#### API idempotency

API 1.0 already requires for RK:

    Idempotency-Key required for mutations

    same key + same semantic request
    → same logical result

    same key + different semantic request
    → IDEMPOTENCY_CONFLICT

RN additionally forbids blind redispatch after UNKNOWN_OUTCOME.

Open details remain HTTP transport location/duplication and physical DB persistence/concurrency.

#### Trusted authorization context

Security v1.0 already defines the context fields and requires:

- no direct browser bearer use;
- unknown permission/action/IPC binding = DENY / no effect;
- wcpd validates origin permission/action;
- wcp-privd validates bounded IPC operation/target.

Recovered final evidence also indicates authorization_context_id is created/validated by wcpd.

Open lifecycle details remain storage/resolver, integrity, expiry/revocation, execution-time freshness, dependency outage and replay/substitution.

#### Traceability

Current/final inputs distinguish:

    audit_event_id  = semantic type
    audit_record_id = immutable occurrence
    correlation_id/request_id/job_id = linkage identifiers, not authorization

Durable pre-effect intent and durable post-effect result/audit remain required.

Open details remain intermediate-step event naming, partial-effect representation, late-result/reconciliation linkage and physical persistence relations.

### Control-boundary recovery

Recovered final M1 evidence, still requiring current owner republication/confirmation for C01:

    /run/wcp360/wcpd.sock
    owner = wcpd
    group = wcp360-control
    mode = 0660
    default clients = wcp360-user, wcp360-admin
    default denied = wcp360-worker, hosting identities

C02 transport access != application authorization is already defined in current DevOps/Security.

C03/C04/C06/C07 remain open; C05 is OPEN / PARTIALLY DEFINED.

QA must not mark control-boundary cases READY if their expected result still depends on an unresolved owner decision.

### Authorization-binding provenance

Current FINAL API/IPC exports contain the relevant API-WCP/IPC-WCP identifiers for reload/restart/firewall/grants.

However the historical FROZEN_BY_API_IPC_OWNER label was traced to an assistant-authored H3-V2 freeze/output, not an independently recovered explicit owner-ratification statement.

Therefore:

    B01–B05 = owner harmonization required
    D01 = OPEN

QA must test only the mapping ultimately ratified by API/Security owners.

## 13. QA result template requested for the final owner artifact

For each implementation-dependent case, the final QA baseline should make the observable contract explicit:

    TEST ID =
    REQUIREMENT / CONTRACT =
    PRECONDITIONS =
    AUTH/SURFACE =
    TENANT / RESOURCE =
    INPUT =
    FAULT INJECTION, if applicable =
    EXPECTED RESPONSE / OUTCOME =
    EXPECTED DB STATE =
    EXPECTED EXTERNAL EFFECT =
    FORBIDDEN EFFECTS =
    EXPECTED AUDIT / CORRELATION =
    RECOVERY / RECONCILIATION =
    ENVIRONMENT =
    TARGET SHA =
    STATUS = PLANNED / READY / BLOCKED / PASS / FAIL
    EVIDENCE =

Do not require DB assertions before #6 provides the physical schema. Mark those assertions BLOCKED rather than inventing table/column names.

## 14. Repository-tooling tests versus product QA

PR #42 contains real executed tests for repository tooling:

- gitignore path behavior;
- publication inventory;
- archive validation;
- targeted secret-pattern scanning;
- staged-index behavior;
- pre-commit hook behavior.

These are legitimate E1 evidence for the repository-tooling PR.

They are **not**:

- the final WCP360 QA artifact;
- TEST-API/TEST-IPC execution;
- application integration tests;
- proof of SC-M01 product behavior.

The QA owner may reference them for repository/bootstrap obligations after reviewing their scope, but must not count them as broad product coverage.

## 15. Final QA owner publication remains required

Issue #7 closes only with a TESTS / QA owner artifact that clearly identifies:

    TEST BASELINE VERSION = v1.0
    SCOPE = SC-M01 FIRST IMPLEMENTATION FOUNDATION
    OWNER = TESTS / QA
    FINAL APPROVED CONTENT = YES
    HISTORICAL CONTENT INCLUDED = NO
    READY FOR SOURCE CODE CONSUMPTION = YES

If the final QA artifact incorporates accepted audit-derived cases, it should record their source and resulting canonical TEST IDs.

This input pack remains non-authoritative and cannot close #7.
