# WCP360 — SC-M01 Engineering Canonical Input Pack

**Status:** `OWNER INPUT ONLY — NOT ENGINEERING RULESET AUTHORITY`  
**Producer:** `WCP360 — MASTER / Project Control`  
**Consumer:** `WCP360 — ENGINEERING / CODE REVIEW`  
**Reference:** `WCP360-BM-0001 FINAL / SC-M01 TARGETED EVIDENCE`

This file does **not** replace the ENGINEERING owner export.

Logical identity:

```text
ENG-WCP-RULESET-0001 v0.2
```

Exact final owner export identified by project history:

```text
WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md
```

Owner status already confirmed:

```text
FINAL APPROVED CONTENT = YES
HISTORICAL CONTENT INCLUDED = NO
CONTENT CHANGE = NONE
READY FOR SOURCE CODE CONSUMPTION = YES
BLOCKER = NONE
```

## 1. Canonical repository context

Current project state:

```text
WCP360-BM-0001 = FINAL
SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Repository authority is defined by:

```text
governance/AUTHORITY-PRECEDENCE.md
governance/SC-M01-RESUME-CHECKLIST.md
governance/WORKSTREAMS.md
governance/PROJECT-STATUS.md
```

## 2. Repository architecture constraints

Engineering rules must preserve:

- explicit owner-domain boundaries;
- canonical artifacts under `specifications/`;
- documentary evidence separate from implementation authority;
- PR-based traceability;
- no silent contract redefinition;
- no direct reuse of cancelled Sprint 0;
- no reactivation of cancelled V4.1 Executable Contracts.

## 3. Naming / typed namespace rules

```text
permission_id != action_id != job_type != event_id
permission_id != audit_event_id != ipc_operation_id != error_code
```

Lexical equality never collapses semantic types.

## 4. Application/security boundaries

```text
WCP-USER != WCP-ADMIN
```

They must remain distinct application/security surfaces.

Privileged architecture:

```text
backend
→ wcpd (non-root)
→ bounded authenticated IPC
→ wcp-privd (minimal root)
→ explicit privileged primitive
```

Forbidden engineering patterns include:

```text
shell.exec
arbitrary command execution
arbitrary executable
generic sudo proxy
generic systemctl proxy
arbitrary filesystem target/write
arbitrary package-manager argv
arbitrary signal
generic root terminal
```

## 5. Dependency rules

Implementation must not invert or bypass owner boundaries.

Examples:

- web/backend must not call `wcp-privd` directly;
- worker identities must not gain direct privileged IPC;
- UI must not invent permission identifiers;
- implementation must consume canonical contracts rather than redefine them;
- database structure must follow DATABASE owner artifact;
- tests must follow TESTS / QA baseline;
- release/install behavior must follow DEVOPS baseline.

## 6. Error / failure handling

Canonical distinctions:

```text
timeout != definite failure
retry != reconciliation
rollback != compensation
execution success != verified success
```

Unknown outcomes must remain explicit.

No broad exception swallowing that converts uncertain effects into success/failure guesses.

## 7. Configuration and secrets

Engineering rules must enforce:

- no secrets in Git;
- no production credentials in fixtures;
- no private keys in test artifacts;
- no sensitive values in logs or audit payloads;
- typed configuration rather than arbitrary command/config injection;
- runtime code must not write package-managed product root `/usr/lib/wcp360`.

## 8. Testability

Code must be structured so canonical contracts can be validated through:

- unit tests;
- API contract tests;
- IPC contract/security tests;
- DB invariant tests;
- authorization tests;
- negative security tests;
- failure/recovery tests;
- install/upgrade/rollback tests where applicable;
- regression tests for every fixed bug.

No implementation-dependent test may be marked PASS before execution against actual code.

## 9. Review gates

Independent Engineering review must verify, at minimum:

- readability;
- maintainability;
- complexity;
- duplication;
- architecture/boundaries;
- dependencies;
- naming/conventions;
- error handling;
- security;
- performance implications;
- concurrency;
- tests;
- alignment with canonical owner contracts.

SOURCE CODE produces; ENGINEERING / CODE REVIEW controls.

## 10. First implementation constraints

When SC-M01 eventually resumes:

- only the first implementation foundation is authorized;
- broad functional implementation is not automatically authorized;
- SC-M02 remains unauthorized;
- creating repository code does not establish CODE-BASELINE;
- CODE-BASELINE requires review, tests, fixes, verification and MASTER approval.

## 11. Prohibited substitutions

Do not satisfy issue #9 using:

- SOCIAL NETWORK engineering rules;
- old WCP360 Engineering candidates;
- generic coding standards;
- MASTER-generated replacement ruleset.

Only the exact Engineering owner export closes the gate.

## 12. GitHub target

```text
specifications/engineering/WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md
```

## 13. Gate status

```text
ISSUE #9 = OPEN
SC-M01 CANONICAL EVIDENCE = 5 / 8
SOURCE CODE = PAUSED
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```


## 14. Post-audit Engineering inputs — 2026-10-02

**Status of this section:** NON-NORMATIVE OWNER INPUT. This section summarizes gaps the exact Engineering v0.2 ruleset should already cover or explicitly resolve. It does not recreate the missing owner ruleset.

### Current governance sources

The Engineering owner should also review:

    governance/SC-M01-SCOPE.md
    governance/REVIEW-MERGE-POLICY.md
    governance/SC-M01-CURRENT-READINESS-2026-10-02.md
    governance/audits/AUDIT-WCP-0100/CLOSURE-MATRIX.md

SC-M01-SCOPE normalizes the already-approved FIRST IMPLEMENTATION FOUNDATION only.

REVIEW-MERGE-POLICY distinguishes:

    E0 = authored
    E1 = automated checks
    E2 = same-author verification
    E3 = independent technical review

For implementation and repository/security tooling, same-author comments and green CI do not become E3.

### PR #42 status

Repository guardrails PR #42 is class R1.

It contains:

- .gitignore corrections and path regressions;
- publication/inventory checks;
- branding archive checks;
- targeted repository secret scanning;
- staged-index checking;
- opt-in pre-commit hook;
- read-only CI jobs.

It has repeatedly demonstrated E1 automated evidence and E2 same-author verification.

It remains OPEN because E3 independent review is missing under the current review policy.

**PR #42 must not be used as a substitute for ENG-WCP-RULESET-0001 v0.2.**

Engineering should review PR #42 only after/with its real ruleset available, or clearly state the limited criteria applied.

### Toolchain / build decisions still unresolved

The audit-derived Toolchain/Build proposal identifies F01–F10:

    F01 application runtime/framework
    F02 wcpd / wcp-privd implementation language/runtime
    F03 PostgreSQL/client/migration version policy
    F04 dependency-source and lock policy
    F05 frontend/Node decision
    F06 canonical build/test entry points
    F07 static-analysis / quality gates
    F08 build environment / CI runner policy
    F09 configuration/generated-artifact policy
    F10 artifact identity/provenance/signing boundary

Current final facts include:

    Debian 13 amd64
    /usr/lib/wcp360
    PostgreSQL as control-plane authority
    WCP-USER != WCP-ADMIN
    backend → wcpd → bounded IPC → wcp-privd

Not final in currently materialized artifacts:

- exact web/backend framework;
- exact runtime versions;
- exact Go/runtime compiler version;
- Node use/non-use;
- package managers;
- lockfile policy details;
- static-analysis tools;
- coverage/complexity thresholds;
- canonical build command names.

Cancelled Sprint 0 choices must not be silently reused.

### Engineering decisions that must not be invented before #9

Do not declare final compliance with:

- directory/layering architecture;
- dependency direction rules beyond already-canonical owner boundaries;
- complexity limits;
- duplication limits;
- formatter/linter/static-analysis tools;
- coverage thresholds;
- build warnings-as-errors rules;
- generated-code policy;
- Definition of Done;
- required review count;
- merge quality thresholds;
- release quality gates;

unless those are present in the exact v0.2 owner ruleset or the owner explicitly issues a versioned amendment.

### SC-M01 implementation-class expectations

When SC-M01 resumes, Engineering should classify implementation PRs at least by the foundation areas already normalized:

- repository/bootstrap;
- WCP-USER / WCP-ADMIN surface skeleton;
- backend foundation;
- wcpd foundation;
- wcp-privd foundation;
- DB/migration framework and approved schema;
- configuration/secrets;
- test harness;
- build/CI/package foundation.

Broad functional module implementation remains outside SC-M01 unless separately authorized.

### Exact-SHA review requirement

Engineering review should record:

    PR =
    REVIEWED SHA =
    REVIEWER =
    FILES / SCOPE =
    OWNER CONTRACTS CONSUMED =
    TEST RUNS / EVIDENCE =
    FINDINGS =
    REQUIRED CHANGES =
    VERDICT =
    RESERVATIONS =
    FOLLOW-UP SHA =

A review on an earlier SHA does not automatically approve later substantive code changes.

### Provenance caution

Historical Engineering candidates, generic coding standards, another project's rules and assistant-generated summaries remain non-authoritative.

If the original final v0.2 body cannot be recovered byte-for-byte and Engineering must republish from its own retained source/context, the owner must explicitly state:

    exact republication / no content change

or:

    real content delta + version impact

MASTER must not manufacture the missing ruleset.

## 15. Exact owner response requested

Issue #9 closes only when Engineering publishes/provides the exact final owner export or an explicitly versioned owner republication:

    ENGINEERING RULESET = ENG-WCP-RULESET-0001
    VERSION = v0.2
    FINAL APPROVED CONTENT = YES
    HISTORICAL CONTENT INCLUDED = NO
    READY FOR SOURCE CODE CONSUMPTION = YES
    BLOCKER = NONE
    PROVENANCE =
    CONTENT CHANGE =
    COMMIT / SOURCE =

This input pack remains non-authoritative and cannot close #9.
