# WCP360 — SC-M01 First Implementation Foundation Scope

**Status:** GOVERNANCE NORMALIZATION — EXISTING DECISIONS ONLY
**Reference date:** 2026-10-02
**Parent gate:** WCP360-BM-0001 FINAL / SC-M01 TARGETED EVIDENCE
**Current execution state:** PAUSED

This file normalizes the already-adopted scope of SC-M01 — FIRST IMPLEMENTATION FOUNDATION.

It does not authorize implementation by itself. The resume condition remains the existing SC-M01 gate and MASTER authorization.

## 1. Purpose

SC-M01 exists to create the **first narrow implementation foundation of WCP360**, not the product's broad functional feature set.

The foundation must make the approved architecture buildable, testable, reviewable and versioned without silently choosing unresolved business, database, security or contract semantics.

When SC-M01 resumes, SOURCE CODE may implement only the foundation described here and the exact owner contracts accepted by the gate.

## 2. Mandatory architectural shape

The implementation foundation preserves:

    WCP-USER != WCP-ADMIN

    backend
    → wcpd (non-root)
    → bounded authenticated IPC
    → wcp-privd (minimal root)
    → explicit privileged primitive

Initial platform:

    Debian GNU/Linux 13 (trixie)
    amd64 / x86_64
    PRODUCT ROOT = /usr/lib/wcp360

Runtime identities remain distinct:

    wcp360-user
    wcp360-admin
    wcpd
    wcp-privd / root minimal service context
    wcp360-worker

Groups:

    wcp360-control
    wcp360-priv

The historical /usr/local/wcp360, obsolete WC-CLIENT / WC-ADMIN, cancelled Sprint 0 and cancelled V4.1 Executable Contracts do not return.

## 3. In-scope foundation work after explicit resume

### 3.1 Repository and project bootstrap

Allowed:

- repository directory structure approved for the foundation;
- package/module boundaries;
- build/test entry points;
- dependency manifests and lock strategy as approved by Engineering/DevOps;
- configuration example structure;
- test directory/harness structure;
- CI workflow implementation and quality gates;
- baseline/version references;
- developer documentation needed to build/test the foundation.

SC-M01 may create the structural locations required for future work without populating them with broad feature implementations.

### 3.2 WCP-USER / WCP-ADMIN separation

Allowed:

- separate application/runtime entry points;
- separate configuration/session namespaces;
- explicit surface identity propagation;
- shared libraries only where ownership and security boundaries remain clear;
- tests proving no accidental surface collapse.

Not allowed:

- merging USER and ADMIN into one trust surface for convenience;
- relying only on UI visibility or routing to enforce permissions.

### 3.3 Backend foundation

Allowed:

- application/backend process skeleton;
- configuration/bootstrap abstractions;
- typed error/result plumbing;
- correlation/request context plumbing;
- dependency injection/interfaces needed for testability;
- contract adapters that implement accepted API/IPC decisions;
- persistence interfaces only where backed by the approved DB contract.

Not allowed:

- broad DNS/mail/backup/database-hosting/billing/HA business functionality;
- database structures invented because an interface needs storage;
- implicit security or authorization decisions not present in owner contracts.

### 3.4 wcpd foundation

Allowed after the control-boundary contract is ratified:

- non-root daemon process skeleton;
- control transport server/client boundary as approved;
- peer identity verification;
- protocol framing/version/schema handling;
- trusted-context resolution/revalidation hooks;
- dispatch registry limited to approved typed operations;
- job/reconciliation integration interfaces defined by accepted contracts;
- tests for unknown operation, wrong peer, wrong surface/context and no generic fallback.

Not allowed:

- generic command forwarding;
- passthrough shell or executable invocation;
- arbitrary service/unit/filesystem/signal/package arguments;
- inventing a worker transport path.

### 3.5 wcp-privd foundation

Allowed only to the extent authorized by the accepted IPC-B contract and SC-M01 resume package:

- minimal root daemon/server skeleton;
- canonical IPC-B socket/framing/authentication;
- typed allowlist/registry plumbing;
- strict schema and bounds validation;
- adapter interfaces for approved primitives;
- tests proving unknown/generic execution is impossible.

**Initial foundation should not require real privileged external effects merely to prove the repository architecture.**

Real system mutation for a specific primitive requires its contract, test expectations and security constraints to be source-ready for that primitive.

### 3.6 Database access and migration framework

Allowed:

- PostgreSQL connection/configuration boundary;
- transaction abstraction;
- repository/unit-of-work patterns if required by Engineering;
- migration framework placement and mechanism;
- migration test harness;
- schema/version bookkeeping mechanism if prescribed by the DB/Engineering contracts.

Not allowed:

- application tables/columns inferred from summaries;
- executable DDL before the exact DB Model v1.0 is materialized and consumed;
- migrations that implement unverified structures;
- treating hosted tenant databases as control-plane PostgreSQL.

The invariant remains:

    CONTROL-PLANE POSTGRESQL
    != HOSTED DB ENGINES
    != TENANT / APPLICATION DB CONTENT

### 3.7 Configuration and secrets foundation

Allowed:

- typed configuration loading;
- safe example files;
- consumer-specific secret references;
- redaction helpers;
- fail-closed missing/invalid configuration behavior;
- tests that secrets are not committed, logged or exposed.

Not allowed:

- production secrets in repository/package/tests;
- universal secret-readable group;
- arbitrary configuration-to-command interpolation.

### 3.8 Test foundation

Allowed and required after resume:

- unit test harness;
- contract-test harness;
- IPC test harness;
- database integration-test harness using approved schema/migrations;
- security/negative test harness;
- fixture/factory utilities;
- CI execution of the accepted QA baseline;
- regression tests for every bug fixed.

Before an implementation exists, tests remain PLANNED / READY / BLOCKED. Once a candidate exists, tests are executed against an exact SHA/environment; CODE-BASELINE = NONE does not prohibit testing a candidate.

### 3.9 Build / packaging foundation

Allowed:

- deterministic build entry points;
- dependency lock consumption;
- package layout metadata;
- artifact/version/digest plumbing;
- package-content tests;
- CI packaging proof where authorized by DevOps.

Not automatically included:

- production installer implementation;
- production upgrade/rollback machinery;
- systemd units;
- release signing infrastructure;
- production deployment automation.

Those require their explicit DevOps/release authorization and tests.

## 4. Explicitly out of SC-M01 broad functional scope

Unless MASTER issues a separate, owner-backed authorization, SC-M01 does **not** mean implementation of complete modules such as:

    DNS/domain management
    mail hosting
    hosted database lifecycle
    file manager
    backup/restore product workflows
    billing/commercial workflows
    monitoring product UI
    firewall/network management
    server update management
    cluster/HA/failover
    support bundle product flows
    webhooks/automations/notifications
    full certificate lifecycle
    full reseller/customer feature set

Their contracts can be documented/test-planned in advance, but their implementation is activated by later milestone/module decisions.

## 5. Explicitly forbidden shortcuts

SC-M01 must never introduce:

    shell.exec
    arbitrary command execution
    caller-controlled executable
    generic sudo proxy
    generic systemctl proxy
    arbitrary signal
    arbitrary SQL
    arbitrary privileged filesystem path
    generic root terminal
    hidden superuser API route
    authorization by UI visibility
    authorization by tenant_id alone

Nor may it:

- reinterpret a missing DB/API/IPC/Security rule in code;
- copy historical mirror material into implementation authority;
- replace an owner decision with a convenient framework default;
- silently expand worker/control groups;
- turn a proposed PR #43 decision into a canonical contract without owner ratification.

## 6. SC-M01 dependency classes

A dependency is **blocking for SC-M01** only when the foundation component being implemented cannot be written or tested deterministically without it.

### Gate artifacts — global resume blockers

    API Contract
    IPC Contract
    Permission Registry
    Security Baseline
    DB Model
    Test Baseline
    DevOps Baseline
    Engineering Ruleset

All 8 must satisfy the existing gate.

### Component-level semantic blockers

After 8/8, a component remains blocked if an accepted behavior needed by that component is still unresolved.

Examples:

- control daemon transport blocked by unresolved control-boundary decisions;
- HTTP endpoint blocked by unresolved method/route/DTO mapping;
- durable job/idempotency implementation blocked by unresolved DB persistence semantics;
- USER/ADMIN session deployment blocked by unresolved topology if the chosen deployment depends on it.

### Future-module blockers

A missing detail in a future functional module does **not** block unrelated repository/bootstrap foundation work.

Examples:

- HA fencing details do not block test harness creation;
- mail policy details do not block WCP-USER/WCP-ADMIN process separation;
- backup retention tuning does not block config loader architecture;
- future performance targets do not block a deterministic build skeleton unless a specific capacity requirement changes the design.

## 7. SC-M01 deliverable classes

| Class | Examples | Acceptance type |
|---|---|---|
| Repository | layout, manifests, locks, configuration examples | Engineering/DevOps review |
| Surface skeleton | WCP-USER / WCP-ADMIN entry points | Security + tests |
| Backend foundation | error/context/config/persistence boundaries | Engineering + contract tests |
| Daemon foundation | wcpd/wcp-privd skeletons and bounded protocols | API/IPC + Security + tests |
| DB foundation | approved schema/migration mechanism | Database + tests |
| Test foundation | unit/contract/integration/security harness | QA |
| Build/CI | deterministic commands, CI checks, artifacts | DevOps/Engineering |
| Documentation | exact build/test/architecture consumption docs | owner cross-check |

A single giant commit is not required or preferred. The implementation may be split into reviewable PRs while remaining within SC-M01.

## 8. SC-M01 exit criteria

SC-M01 is not complete because a repository skeleton exists.

At minimum the foundation candidate must have:

- implementation tied to accepted owner contracts;
- no forbidden generic privileged execution;
- tests executed against exact commits;
- required QA cases for implemented foundation behavior;
- DB migrations/schema matching the exact DB artifact where DB structures are implemented;
- Engineering review on the final candidate SHA;
- Security review for trust boundaries;
- DevOps/build verification on the supported target where applicable;
- corrected findings receiving regression tests;
- documentation updated to match the implementation;
- MASTER verification of unresolved blockers.

Only then can MASTER decide whether the first CODE-BASELINE may be established.

## 9. Relationship to SC-M02

    SC-M01 completion != SC-M02 authorization

SC-M02 requires a separate decision.

No functional module, production rollout or second milestone becomes authorized automatically when the foundation passes.

## 10. Current state

    THIS FILE = SCOPE NORMALIZATION ONLY
    SC-M01 EXECUTION = PAUSED
    CANONICAL EVIDENCE = 5 / 8
    SOURCE CODE = PAUSED
    SC-M02 = NOT AUTHORIZED
    CODE-BASELINE = NONE

This file is intended to resolve ambiguity about **what SC-M01 means**, not to bypass the evidence gate.
