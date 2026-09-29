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
