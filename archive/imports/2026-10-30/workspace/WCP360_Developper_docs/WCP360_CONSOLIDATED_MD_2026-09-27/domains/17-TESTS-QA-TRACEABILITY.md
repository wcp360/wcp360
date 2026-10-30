# WCP360 — Tests / QA / Traceability

## Current status

`H3-CM-C05 / TESTS-QA` was reported finished by project coordination.

Its detailed final package was not available in the accessible source index during generation of this ZIP. Therefore this document does **not** invent the C05 verdict.

Use:
- `PLANNED`
- `READY`
- `BLOCKED`

Do not use implementation `PASS/FAIL` while `CODE-BASELINE = NONE`.

## Required test families
- unit;
- component;
- integration;
- API/contract;
- DB;
- IPC;
- security/auth;
- tenant isolation;
- filesystem/system;
- installer/upgrade/rollback;
- backup/restore;
- failure injection;
- concurrency;
- performance;
- observability;
- E2E;
- regression.

## Adversarial coverage
At minimum:
- cross-tenant access;
- WCP-USER→WCP-ADMIN crossing;
- direct backend→wcp-privd;
- unauthorized socket peer;
- unknown/malformed IPC;
- traversal/symlink/TOCTOU;
- argument/environment injection;
- arbitrary command/signal attempts;
- duplicate request;
- retry after timeout;
- UNKNOWN_OUTCOME;
- daemon crash;
- PostgreSQL failure;
- audit persistence failure;
- service reload failure;
- concurrent mutation;
- compensation failure;
- drift/reconciliation;
- backup/restore inconsistency;
- cross-tenant restore;
- secret leakage;
- metric-cardinality violation.

## Global chain to prove

`Requirement → Module → Role → Permission → Entitlement → Action → Resource → Tenant/ownership → API → Job/Event → IPC → DB/system primitive → Desired State → Observed State → Audit → Test`

Until the detailed C05 result is ingested, `H3V-B09` and `H3V-B10` must not be marked closed.
