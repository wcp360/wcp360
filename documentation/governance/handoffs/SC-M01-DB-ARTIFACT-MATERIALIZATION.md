# WCP360 HANDOFF — SC-M01 DB Canonical Artifact Materialization

FROM: `WCP360 — MASTER / Project Control`  
TO: `WCP360 — DATABASE / DATA MODEL`

Reference: `WCP360-BM-0001 FINAL / SC-M01 TARGETED EVIDENCE`

## Status

```text
OWNER DECISION = COMPLETE
DB MODEL VERSION = v1.0
FINAL APPROVED CONTENT = YES
PHYSICAL CONTRACT = INCLUDED
HISTORICAL CONTENT INCLUDED = NO
READY FOR SOURCE CODE CONSUMPTION = YES
BLOCKER = NONE
```

## Required action

Materialize the **exact already-approved owner artifact**:

```text
WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md
```

No re-audit.  
No redesign.  
No migrations.  
No DDL.  
No Documentation ZIP scan.  
No content reconstruction from older R4/H3-C drafts.

The exact file body must include the approved physical implementation contract already owned by DATABASE, including applicable schemas/tables, identifiers, PK/FK, tenant-aware constraints, UNIQUE/CHECK/nullability, ownership/delete semantics, transaction boundaries, concurrency/revision rules, jobs/outbox persistence, idempotency persistence, audit durability dependencies, reconciliation state and approved indexes.

Preserve:

```text
account_id != organization_id != tenant_id

CONTROL-PLANE POSTGRESQL
!= HOSTED DB ENGINES
!= TENANT / APPLICATION DB CONTENT
```

## GitHub target

```text
specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md
```

Tracking: issue #6.

This handoff does not authorize SC-M02 or establish CODE-BASELINE.
