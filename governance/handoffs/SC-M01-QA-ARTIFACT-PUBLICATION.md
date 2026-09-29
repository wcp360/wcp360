# WCP360 HANDOFF — SC-M01 Test Baseline Canonical Publication

FROM: `WCP360 — MASTER / Project Control`  
TO: `WCP360 — TESTS / QA`

Reference: `WCP360-BM-0001 FINAL / SC-M01 TARGETED EVIDENCE`

## Required action

Publish the exact final canonical implementation-test artifact for:

```text
TEST BASELINE VERSION = v1.0
SCOPE = SC-M01
```

Do not re-audit the project.  
Do not execute implementation tests while `CODE-BASELINE = NONE`.  
Do not fabricate PASS/FAIL.  
Do not scan Documentation broadly.

## Required SC-M01 test coverage

The final artifact must include applicable requirements for:

- repository/bootstrap foundation;
- architecture boundaries;
- WCP-USER / WCP-ADMIN separation;
- authorization and deny invariants;
- API/IPC contract conformance;
- IPC peer/security boundary;
- Database invariants;
- negative security/adversarial tests;
- privileged-boundary tests;
- impossibility of generic privileged execution;
- audit durability;
- configuration/secrets;
- build/CI quality gates;
- regression-test requirement for every corrected bug.

Preserve:

```text
IMPLEMENTATION-DEPENDENT TESTS = PLANNED / READY
FAKE PASS RESULTS = NONE
CODE-BASELINE = NONE
```

## Publication declaration required

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

GitHub target:

```text
specifications/tests/<EXACT FINAL .md NAME>
```

Tracking: issue #7.

This handoff does not authorize SC-M02 or establish CODE-BASELINE.
