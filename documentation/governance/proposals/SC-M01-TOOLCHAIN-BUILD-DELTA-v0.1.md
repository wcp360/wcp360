# WCP360 — Toolchain / Build Inputs / Dependency Locks — proposal v0.1

Date: 2026-10-02. **PROPOSAL — REVIEW — NON-NORMATIVE — NOT SOURCE-CODE CONSUMABLE.**

PR #43; finding AUDIT-WCP-0113; dependencies #9 Engineering, #7 QA and DevOps/Security owner review.

This document does not select a framework, runtime version, package manager, linter or CI image. It separates already-established platform constraints from choices that remain unratified.

## 1. Established constraints

Current owner artifacts establish:

    initial OS = Debian GNU/Linux 13 (trixie)
    architecture = amd64 / x86_64
    product root = /usr/lib/wcp360
    PostgreSQL = authoritative logical control-plane state

    WCP-USER != WCP-ADMIN

    backend
    → wcpd non-root
    → bounded authenticated IPC
    → wcp-privd minimal root

DevOps additionally requires official artifacts to be traceable to:

- Git commit;
- release/version identifier;
- documentation/design baselines;
- toolchain;
- dependency/lock state;
- build target;
- artifact digest;
- CI provenance.

The repository does **not** currently contain a final owner decision fixing:

- web/backend implementation language/framework;
- frontend framework;
- PHP version;
- Go version;
- PostgreSQL server/client major version policy;
- Node.js use or non-use;
- npm/pnpm/yarn use;
- PHP dependency manager;
- concrete build/test command set;
- linter/static-analysis tools and versions;
- quantitative coverage/complexity thresholds.

Historical Sprint 0 choices such as native PHP, Go monorepo or “no Node.js” are cancelled and must not be revived by inference.

Active Design evidence mentions a Go daemon and historical authority documents mention PHP↔Go boundaries. This is evidence of prior architecture direction, but this proposal does not convert it into a new final toolchain decision or choose a Go version.

## 2. F01 — application runtime / framework

Engineering + Architecture must explicitly decide the runtime/framework for:

- WCP-USER application surface;
- WCP-ADMIN application surface;
- shared backend/application layer if separate;
- CLI/build/bootstrap utilities where applicable.

Required decision:

    language/runtime =
    framework =
    major/minor support policy =
    package/dependency manager =
    lock file =
    production runtime packaging model =

Criteria:

- Debian 13 compatibility;
- strict separation of USER and ADMIN surfaces;
- testability;
- secure dependency management;
- deterministic build/install;
- no need for root in web application runtime;
- compatibility with the control boundary.

No previous cancelled implementation may be used as implicit approval.

## 3. F02 — wcpd / wcp-privd implementation language

Decision required:

    wcpd language/runtime =
    wcp-privd language/runtime =
    version policy =
    module/dependency lock mechanism =
    compiler provenance =

If Go is ratified, owner artifacts must identify:

- supported Go release policy;
- module mode and lock/checksum expectations;
- CGO allowed/forbidden/conditional;
- static vs dynamic linking policy;
- target GOOS/GOARCH and hardening flags where applicable.

If another language is selected, the same reproducibility/security properties apply.

The IPC contract is language-independent and must not be changed merely to fit a language library.

## 4. F03 — PostgreSQL version and client tooling

Already fixed:

    PostgreSQL = authoritative control-plane logical state

Not fixed:

- supported PostgreSQL major version;
- client library/driver;
- migration client/tool;
- connection pool implementation;
- minimum/maximum compatibility window.

Decision required:

    PostgreSQL major =
    upgrade compatibility policy =
    application driver =
    migration tool/framework =
    schema-version mechanism =
    test database provisioning =

This decision must consume the exact DB Model #6 when materialized. No schema is inferred here.

Hosted PostgreSQL/MariaDB engines remain distinct from control-plane PostgreSQL.

## 5. F04 — dependency-source policy

DevOps/Engineering/Security must decide allowed dependency sources.

At minimum distinguish:

- Debian package repositories;
- language ecosystem registries;
- vendored dependencies;
- Git-based dependencies;
- downloaded binaries;
- build-container images.

Policy must answer:

- immutable version pinning;
- checksum/signature verification;
- lock files;
- transitive dependency recording;
- approved registries/mirrors;
- handling of yanked/compromised dependencies;
- offline/reproducible rebuild requirements where applicable.

Floating “latest” versions are not acceptable inputs for an official release.

## 6. F05 — frontend build toolchain / Node decision

No final evidence currently requires Vue, React, Node.js, npm, pnpm or yarn.

Owner decision must explicitly state one of:

- no Node/frontend compilation in SC-M01;
- Node-based build toolchain required;
- another frontend asset pipeline.

If Node is required:

    Node major/version policy =
    package manager =
    lockfile =
    install mode =
    script execution policy =
    production runtime dependency on Node = YES/NO

A Node build dependency must not silently become a Node production service.

## 7. F06 — canonical build/test entry points

SC-M01 should expose a small deterministic command surface.

Names are owner decisions, but the behaviors must cover:

- dependency restore/install;
- lint/static analysis;
- unit tests;
- contract/integration tests where available;
- build;
- packaging;
- clean/reproducibility check;
- version/report output.

The implementation must not require undocumented workstation files or manual secret setup to perform non-production builds/tests.

No Makefile, task runner or shell framework is mandated by this proposal.

## 8. F07 — static analysis and quality gates

Engineering v0.2 remains missing, so exact tooling/thresholds are blocked on #9.

Owner decisions must eventually define, by implementation language:

- formatter;
- linter;
- type/static analyzer;
- security-oriented analyzer where justified;
- unit/integration test command;
- race/concurrency analyzer where relevant;
- generated-code policy;
- dependency/license/vulnerability check policy;
- coverage thresholds if the owner chooses quantitative thresholds;
- complexity/duplication thresholds if any.

Do not invent numeric thresholds before Engineering publishes or ratifies them.

## 9. F08 — build environment and CI runner

Decision required:

    canonical build OS =
    runner/container/base image =
    architecture =
    privileged build = forbidden/exception policy
    network access policy =
    cache policy =
    reproducibility evidence =

Default security expectation:

- build runs non-root where practical;
- no production credentials;
- no privileged Docker/socket access merely for convenience;
- caches are optimization only and cannot change outputs;
- release artifact is built once and promoted, not rebuilt independently per environment.

GitHub Actions is already used for repository guardrails, but its presence does not automatically make it the sole canonical product build system.

## 10. F09 — configuration and generated artifacts

Build-time and runtime configuration must remain distinct.

Owner decision must classify:

- committed defaults/examples;
- generated config templates;
- runtime configuration;
- secrets;
- generated source/assets;
- compiled/minified assets;
- schema/code generation outputs.

Required properties:

- generated artifacts have a reproducible source;
- no generated file becomes a second editable authority;
- secrets are never embedded in generated frontend/package artifacts;
- production configuration is not baked into a generic release artifact unless explicitly designed that way.

## 11. F10 — artifact identity, SBOM/provenance and signing boundary

DevOps already requires digest/provenance/signing principles. The mechanism is still not fully fixed.

Decision required for the release milestone:

- artifact naming/version;
- digest algorithm;
- build provenance format;
- dependency/SBOM format if required;
- signing mechanism;
- root-of-trust location/rotation;
- verification before install/upgrade;
- separation of signing credentials from CI/runtime.

SC-M01 may implement the metadata plumbing and testable interfaces, but production signing infrastructure is not automatically in SC-M01 scope unless separately authorized.

## 12. Fifteen proposed QA scenarios

Local IDs FQ01–FQ15. No canonical TEST-* IDs are reserved. **PLANNED; application executions = 0.**

| Case | Condition | Expected owner result |
|---|---|---|
| FQ01 | clean checkout + declared toolchain | build/test bootstrap succeeds without undeclared local files |
| FQ02 | missing lock file | official build rejected or owner-defined deterministic failure |
| FQ03 | lock changed without manifest/dependency update | CI detects inconsistency |
| FQ04 | dependency version floats to latest | release build policy rejects non-locked input |
| FQ05 | unsupported runtime major | preflight/build fails clearly |
| FQ06 | wrong architecture | build or package target rejected |
| FQ07 | production secret present in source/config example | repository/security gate fails |
| FQ08 | dependency from unapproved source | build fails before artifact promotion |
| FQ09 | cache cleared | output remains semantically/bitwise reproducible according to chosen policy |
| FQ10 | same commit + same inputs built twice | digest/reproducibility expectation holds |
| FQ11 | frontend toolchain absent when declared unnecessary | backend/daemon foundation still builds |
| FQ12 | Node present only as build dependency | production package has no undeclared Node runtime dependency |
| FQ13 | static analysis tool failure | CI gate fails according to Engineering rule |
| FQ14 | generated artifact edited without source change | drift check detects or regeneration overwrites deterministically |
| FQ15 | artifact digest/signature mismatch | install/promotion path rejects artifact |

## 13. Handoff

### DEVOPS / BUILD / RELEASE

Primary decisions: F03–F06, F08–F10.

### ENGINEERING / CODE REVIEW

Primary decisions: F01–F02, F04–F08 plus exact quality tools/thresholds after #9 materialization.

### SECURITY / HARDENING

Review dependency sources, build privilege, secrets, script execution, signing-key isolation and supply-chain controls.

### TESTS / QA

Map FQ01–FQ15 to the final Test Baseline after #7; no PASS before implementation.

### DATABASE / DATA MODEL

F03 migration/schema details consume #6 only. This proposal creates no DDL/migration.

## 14. Exit condition for AUDIT-WCP-0113

For the SC-M01 component being implemented, the repository must contain a versioned toolchain/build manifest or equivalent owner-approved source that states:

- exact required runtimes/tools or explicit supported ranges;
- dependency managers and lock files;
- canonical build/test commands;
- target OS/architecture;
- package/dependency source policy;
- static-analysis/test gates;
- artifact identity/provenance;
- relevant service/file permissions.

Then CI must demonstrate the foundation builds/tests from declared inputs on the supported target.

    PROPOSAL STATUS = REVIEW
    OWNER DECISIONS F01–F10 = NOT RECEIVED
    TOOLCHAIN VERSIONS CHOSEN = NONE
    HISTORICAL SPRINT 0 STACK REUSED = NO
    APPLICATION TESTS EXECUTED = 0
    SC-M01 = PAUSED — TARGETED EVIDENCE
    CODE-BASELINE = NONE
