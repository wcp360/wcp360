# WCP360 — SC-M01 Resume Checklist

Reference: `WCP360-BM-0001 FINAL / SC-M01 TARGETED EVIDENCE`

## Current status

```text
CANONICAL EVIDENCE = 8 / 8 ACCEPTED
SOURCE CODE = STARTED ON DRAFT PR #108 — SOURCE/daemon/
SC-M01 = LIMITED RESUME — INERT DAEMON FOUNDATION ONLY
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

## Required artifacts

- [x] API Contract 1.0
- [x] IPC Contract WCP360-IPC-B/1
- [x] Permission Registry v1.0
- [x] Security Baseline v1.0
- [x] DB Model v1.0 — corrected owner artifact accepted; blob 2660e057bad1d00592c57f354b1f3745ccccbe7d
- [x] Test Baseline v1.0 / SC-M01 requirements — owner-final publication accepted
- [x] DevOps Baseline v1.0
- [x] Engineering Ruleset ENG-WCP-RULESET-0001 v0.3 — successor to v0.2 explicitly accepted in manifest

Latest MASTER evidence decision: `manifests/WCP360-BM-0001.md`, 2026-10-28. All eight publication inputs are accepted. Selected-slice F02/F04/F06/F07/F08 are accepted under MASTER limited-resume commit 3fd2f8007baa49c2454cc89c0a5488a57c77911b and owner toolchain addendum blob f8608d6258f87b8c1099b9b3a7e32ec95c72dc27. Draft PR #108 contains implementation and exact-run CI evidence. E3 remains required before merge; the conditions below continue to apply to any additional slice.

## Per-artifact acceptance test

Every required artifact must satisfy all of:

```text
EXACT OWNER ARTIFACT IDENTIFIED = YES
VERSION IDENTIFIED = YES
FINAL APPROVED CONTENT = YES
HISTORICAL CONTENT INCLUDED = NO
PUBLICATION / CONTENT-CHANGE DECLARATION = CONSISTENT WITH OWNER PROVENANCE
VERSION IMPACT = EXPLICIT
PRESENT IN GITHUB MAIN = YES
READY FOR SOURCE CODE CONSUMPTION = YES
BLOCKER = NONE
```

No artifact may be reconstructed from older audits, candidate documents or general knowledge to satisfy this gate.

`CONTENT CHANGE = NONE` is **not a universal gate condition**. It is valid for an exact republication only when owner provenance establishes the retained/original final content. If an owner recreates or amends missing content, the owner must declare the real delta and version impact. If a final body did not previously exist (as current evidence indicates for QA #7), the owner must classify first canonical materialization honestly.

## Scope before resume

The authorized implementation scope is normalized in:

`governance/SC-M01-SCOPE.md`

This scope file is **not a ninth artifact**. The global evidence gate remains 8/8.

Before implementation starts, MASTER must also confirm that any semantic decision required by the specific foundation component being started has an accepted owner source. A future-module detail that is unrelated to the foundation component does not become a generic blocker.

## Cross-check before resume

Before MASTER resumes SC-M01, verify:

- typed namespaces remain distinct;
- WCP-USER and WCP-ADMIN remain separate security surfaces;
- `account_id != organization_id != tenant_id`;
- control-plane PostgreSQL remains distinct from hosted DB engines and tenant DB content;
- privileged chain remains:
  `backend → wcpd(non-root) → bounded authenticated IPC → wcp-privd(root minimal) → explicit privileged primitive`;
- generic privileged execution remains forbidden;
- IPC-B remains `WCP360-IPC-B/1`;
- 82 legitimate permissions remain authoritative;
- 47 ACTIVE privileged IPC operations remain authoritative;
- product root remains `/usr/lib/wcp360`;
- supported initial OS remains Debian GNU/Linux 13 (trixie) / amd64;
- implementation-dependent tests remain PLANNED/READY until code exists.

## Resume action

Only when all eight artifacts pass:

```text
SC-M01 TARGETED EVIDENCE = SATISFIED
SC-M01 RESUME = AUTHORIZED
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

SOURCE CODE may then create the first implementation foundation only, within `governance/SC-M01-SCOPE.md`.

Broad functional modules, production rollout and SC-M02 remain separately authorized work.

Creating code does not itself establish a CODE-BASELINE. The first CODE-BASELINE requires implementation, tests, independent Engineering review, fixes, verification and MASTER approval.
