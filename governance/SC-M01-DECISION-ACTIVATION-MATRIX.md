# WCP360 — SC-M01 Decision Activation Matrix

**Status:** GOVERNANCE COORDINATION — NON-NORMATIVE DECISION SCHEDULING  
**Reference main:** 778c1cd6e281877f399680f7bb23527f6ded50fa  
**Parent scope:** governance/SC-M01-SCOPE.md  
**Parent gate:** governance/SC-M01-RESUME-CHECKLIST.md / issue #13

Purpose: separate the **global 8/8 resume gate** from the **component-specific semantic decisions** that must be resolved before each foundation slice is implemented.

This matrix does not accept any open owner decision and does not authorize Source Code.

## 1. Unchanged global rule

Before any SC-M01 application implementation:

    REQUIRED CANONICAL ARTIFACTS = 8 / 8
    FINAL / VERSIONED / OWNER-APPROVED = 8 / 8
    PRESENT IN MAIN = 8 / 8
    SOURCE-CODE CONSUMABLE = 8 / 8
    MASTER SC-M01 RESUME = REQUIRED

Current state:

    5 / 8
    SC-M01 = PAUSED
    CODE-BASELINE = NONE

This file never lowers that gate.

## 2. Why activation classes are needed

After the 8/8 gate closes, a missing semantic detail should block the component that needs it, not unrelated foundation work.

Examples already established by SC-M01 scope:

- HA/failover details do not block creation of a unit-test harness;
- mail policy does not block WCP-USER/WCP-ADMIN process separation;
- firewall/grant bindings do not block repository/bootstrap layout;
- an unresolved control wire protocol **does** block implementation of a positive backend→wcpd request path;
- an unresolved HTTP route/DTO **does** block that endpoint, not a daemon-only unit test.

The activation classes below make this operational.

## 3. Activation classes

### A0 — GLOBAL RESUME GATE

Not decision IDs.

    #6 Database final canonical artifact
    #7 QA final canonical artifact
    #9 Engineering final canonical artifact
    + five already-present canonical artifact families

Must be satisfied before Source Code resumes at all.

### A1 — FOUNDATION ENTRY / TOOLCHAIN

Must be owner-resolved before creating the corresponding executable project/toolchain structure.

Typical examples:

    F01 application runtime/framework
    F02 wcpd/wcp-privd implementation language/runtime
    F04 dependency-source/lock policy
    F05 frontend/Node build decision
    F06 canonical build/test entry points
    F07 static-analysis/quality gates
    F08 build/CI environment

These are expected to be resolved largely by the final Engineering/DevOps artifacts.

### A2 — CONTROL-PLANE POSITIVE PATH

Required before implementing a successful authenticated/authorized backend→wcpd→IPC-B control flow.

    C01 control socket physical identity
    C03 peer→surface binding
    C05 trusted authorization-context lifecycle
    C06 control protocol
    C07 backend/wcpd responsibility split

    D01 origin action vs technical action model
    D02 trusted context lifecycle semantics

    T01 I/O timeout phase meaning
    T02 deadline_ms representation
    T03 effective operation budget
    T04 before/after-dispatch timeout/UNKNOWN_OUTCOME boundary

C02 transport access != application authorization is already defined and is an invariant, not an open activation decision.

A daemon/process skeleton that exposes **no positive effectful control path** may be structurally created after the global gate, but it must not invent these A2 behaviors.

### A3 — DURABLE JOB / EFFECT / RECOVERY PATH

Required before implementing persistent effectful job execution, replay protection or crash recovery.

    T05 idempotency/identifier technical binding
    T06 durable pre-effect identity/dedupe/intent/outbox properties
    T07 claim/recovery/fencing/reconciliation persistence
    D04 parent/step audit and uncertain-result correlation

Conditionally:

    C04 worker indirect normal-control-path mechanism
        required before positive worker-originated privileged workflow

    D03 API→IPC target/revision/grant translation
        required before a workflow using those translations is activated

Physical parts of A3 consume Database #6.

A1/A2 foundation work must not fake A3 persistence with in-memory production semantics merely to get a green test.

### A4 — WEB / SESSION / AUTHORIZATION FOUNDATION

Required before deploying/accepting the corresponding browser/session/authz behavior.

Security decisions:

    S01 deployment topology/non-exposure
    S02 wrong/simultaneous cross-surface cookie behavior
    S03 proxy/WSS/cross-surface CSRF/Origin specifics
    S04 measurable cookie non-exposure
    S05 positive-override composition/internal permission algorithm details
    S09 NOT_GRANTED vs DENY / safe external projection details

Already-final Security invariants remain immediately binding:
- WCP-USER != WCP-ADMIN;
- separate session/cookie/namespace/origin identities;
- final AUTHZ is an intersection;
- exact Origin + CSRF for browser mutation;
- unknown binding fails closed;
- no permission bypass from approval/temporary privilege.

A basic process/UI shell can be created after A0/A1 without inventing production hostnames or full authorization-engine internals, provided it does not claim deployable authenticated behavior.

### A5 — HTTP API ENDPOINT ACTIVATION

Required before implementing each API-WCP endpoint or shared transport profile that uses the decision.

    H01 HTTP version representation
    H02 method/route
    H03 parameter locations/serialization
    H04 Idempotency-Key HTTP representation
    H05 DTO fields/types/success status
    H06 HTTP error/status/media map
    H07 concrete session/token transport and multi-auth rule
    H08 operation discriminators
    H09 pagination/cursor
    H10 any cacheable-read exceptions

Many H items are already partially defined by API/Security semantic rules, but the HTTP projection still must be deterministic for an activated endpoint.

No requirement exists to implement all 86 API contracts during SC-M01.

### A6 — FEATURE / OPERATION ACTIVATION

Required only before implementing the affected functional operation/module.

Authorization bindings:

    B01 service reload
    B02 service restart
    B03 firewall apply
    B04 grants apply
    B05 grants revoke

Other conditional decisions include:

    D03 exact grant_set/revision translations for affected workflows
    S06 positive account-override ceiling before account-override feature
    S07 persisted reseller-delegation computation before reseller feature
    S08 privilege/approval physical lifecycle before affected privileged feature

Broad module behavior such as DNS/mail/backup/HA/billing remains outside SC-M01 unless separately activated.

These items must **not** be used as generic blockers for repository/bootstrap work.

### A7 — RELEASE / PRODUCTION PACKAGING

Required before the associated release/install mechanism, not before ordinary source/bootstrap work.

    F09 configuration/generated-artifact final authority rules
        when build generation/package content uses them

    F10 artifact identity/provenance/signing boundary
        production release/signing activation

Also:
- production systemd units;
- installer/upgrade/rollback;
- release-signing infrastructure;
- production deployment automation.

Those remain separately authorized by DevOps/Release scope.

## 4. Decision-by-decision classification

| Decision | Activation class | Current evidence state | Blocks |
|---|---|---|---|
| D01 | A2 | OPEN / owner harmonization; typed/current identifiers exist | positive origin→technical dispatch |
| D02 | A2 | OPEN | trusted positive authorization context |
| D03 | A3/A6 | OPEN / DB-dependent | workflows using revision/grant translation |
| D04 | A3 | OPEN / PARTIALLY DEFINED | durable parent/step/audit/reconciliation implementation |
| T01 | A2 | OPEN / PARTIALLY DEFINED | exact IPC I/O timeout implementation |
| T02 | A2 | OPEN / PARTIALLY DEFINED | deadline parsing/validation |
| T03 | A2 | OPEN / PARTIALLY DEFINED | operation budget enforcement |
| T04 | A2 | OPEN / PARTIALLY DEFINED | timeout outcome boundary |
| T05 | A3 | OPEN / PARTIALLY DEFINED | durable idempotency/replay bridge |
| T06 | A3 | BLOCKED ON #6 | durable intent/dedupe/outbox implementation |
| T07 | A3 | BLOCKED ON #6 | claim/fencing/reconciliation persistence |
| C01 | A2 | RECOVERED OWNER CONSENSUS; republication/confirmation required | control socket creation/positive client |
| C02 | invariant | ALREADY DEFINED | all control access |
| C03 | A2 | OPEN / PARTIALLY DEFINED | peer/surface-positive path |
| C04 | A3 conditional | OPEN / PARTIALLY DEFINED | worker-originated privileged workflow |
| C05 | A2 | OPEN / PARTIALLY DEFINED | trusted context positive path |
| C06 | A2 | OPEN | control wire protocol |
| C07 | A2 | OPEN / PARTIALLY DEFINED | backend/wcpd workflow implementation |
| H01 | A5 | OPEN / PARTIALLY DEFINED | HTTP endpoint version binding |
| H02 | A5 | OPEN | individual HTTP endpoint |
| H03 | A5 | OPEN / PARTIALLY DEFINED | endpoint serialization |
| H04 | A5/A3 | OPEN / PARTIALLY DEFINED | HTTP mutation idempotency transport |
| H05 | A5 | OPEN / PARTIALLY DEFINED | endpoint response serialization |
| H06 | A5 | OPEN / PARTIALLY DEFINED | HTTP error projection |
| H07 | A4/A5 | OPEN / PARTIALLY DEFINED | authenticated HTTP endpoint |
| H08 | A5/A6 | OPEN | operation-style endpoints |
| H09 | A5 | OPEN / PARTIALLY DEFINED | paginated list endpoints |
| H10 | A5/A7 | OPEN / PARTIALLY DEFINED | cacheable HTTP behavior |
| S01 | A4 | OPEN | production/deployable session topology |
| S02 | A4 | OPEN / PARTIALLY DEFINED | cross-surface credential handling |
| S03 | A4 | OPEN / PARTIALLY DEFINED | proxy/WSS/cross-surface request handling |
| S04 | A4 | OPEN | executable cookie non-exposure proof |
| S05 | A4 | OPEN / PARTIALLY DEFINED | final permission-engine implementation detail |
| S06 | A6 | OPEN | positive account-override feature |
| S07 | A6/A3 | OPEN / PARTIALLY DEFINED | reseller delegation persistence/execution |
| S08 | A4/A6 | OPEN / PARTIALLY DEFINED | privileged-session/approval persistence and feature path |
| S09 | A4/A5 | OPEN / PARTIALLY DEFINED | external/internal denial projection |
| F01 | A1 | BLOCKED/PARTIAL ON #9 | application project creation |
| F02 | A1 | BLOCKED/PARTIAL ON #9 | daemon implementation project creation |
| F03 | A1 DB slice | OPEN / #6-dependent | DB connection/migration implementation |
| F04 | A1 | BLOCKED/PARTIAL ON #9 | dependency manifests/locks |
| F05 | A1 | OPEN | frontend build setup if used |
| F06 | A1 | BLOCKED/PARTIAL ON #9 | canonical build/test command surface |
| F07 | A1 | BLOCKED ON #9 | final static-analysis quality gate |
| F08 | A1 | BLOCKED/PARTIAL ON #9 | product build CI environment |
| F09 | A1/A7 conditional | OPEN | generated artifacts if used; final packaging |
| F10 | A7 | OPEN | release provenance/signing |

B01–B05 are tracked binding harmonization items outside the 47-ID count and are A6.

## 5. Minimal safe implementation order after 8/8 + MASTER resume

### Slice 1 — repository/toolchain

Resolve A1 items needed by chosen stack.

Implement:
- component directories/modules;
- build/test entrypoints;
- dependency locks;
- typed configuration skeleton;
- WCP-USER/WCP-ADMIN process separation;
- test harness.

No positive privileged dispatch required.

### Slice 2 — daemon process skeletons

Implement:
- wcpd process;
- wcp-privd process skeleton;
- canonical IPC-B parsing/rejection/security invariants;
- unknown/generic operation denial;
- no real external effect required.

Before positive control flow, resolve A2.

### Slice 3 — positive control transport

After A2:
- control socket/protocol;
- peer/surface binding;
- trusted context;
- bounded positive IPC-B dispatch with test-only/no-op or explicitly approved safe operation.

Do not add worker positive path until C04/A3 is resolved.

### Slice 4 — durable effect/recovery

After A3 + DB canonical model:
- durable intent/job/idempotency;
- claims/fencing;
- outbox/audit;
- reconciliation;
- failure injection.

### Slice 5 — authenticated Web/API endpoint slices

Resolve only the A4/A5 decisions needed by each activated endpoint.

Do not implement all 86 contracts as part of SC-M01 merely because the registry exists.

### Slice 6 — feature operations

Resolve A6 per module/operation before enabling real service/firewall/grant/etc effects.

### Slice 7 — release/install

Resolve A7 before production package/install/upgrade/signing work.

## 6. QA activation

QA Test Baseline should mark test clauses according to activation:

- A1 unresolved → repository/build tests BLOCKED;
- A2 unresolved → positive control-protocol tests BLOCKED, negative fail-closed invariants may be READY;
- A3 unresolved → durable recovery/idempotency physical tests BLOCKED;
- A4 unresolved → deployment/browser topology tests BLOCKED;
- A5 unresolved → affected TEST-API transport assertions BLOCKED;
- A6 unresolved → only affected feature tests BLOCKED;
- A7 unresolved → release/install/signing tests BLOCKED.

A future-module A6/A7 blocker must not mark the entire SC-M01 test baseline FAIL.

## 7. MASTER use

MASTER should evaluate readiness in two layers:

### Global resume

    8 / 8 canonical artifacts
    + SC-M01 scope
    + no unresolved contradiction that prevents any safe first slice
    → SC-M01 RESUME decision

### Slice activation

Before each implementation PR:
- identify activated component/slice;
- select applicable decision IDs;
- require those owner decisions to be normative;
- require corresponding QA cases READY;
- leave unrelated future decisions OPEN without silently implementing them.

## 8. Current conclusion

    GLOBAL GATE = 5 / 8 — PAUSED
    DECISION IDS TRACKED = 47
    ALL 47 REQUIRED BEFORE FIRST CODE = NO
    OWNER DECISION REQUIRED BEFORE AFFECTED COMPONENT = YES
    FUTURE-MODULE DECISION MAY REMAIN OPEN = YES, if unrelated to active SC-M01 slice
    SOURCE CODE AUTHORIZED NOW = NO

This matrix is scheduling/coordination only. It does not downgrade any owner requirement or close any audit finding.
