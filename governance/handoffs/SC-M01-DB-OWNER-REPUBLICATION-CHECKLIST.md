# WCP360 — Database v1.0 Owner Republication Checklist

**Status:** OWNER RECOVERY AID — NON-CANONICAL  
**Owner:** WCP360 — DATABASE / DATA MODEL  
**Issue:** #6  
**Reference main:** e51333cff4e3ab42b976aafda557cc4b4ebc648d

This checklist is derived from the recovered final-artifact evidence already in main.

It exists to prevent silent reconstruction.

## Confirmed final metadata

    ARTIFACT = WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md
    DB MODEL VERSION = v1.0
    FINAL APPROVED CONTENT = YES
    PHYSICAL CONTRACT = INCLUDED
    HISTORICAL CONTENT INCLUDED = NO
    READY FOR SOURCE CODE CONSUMPTION = YES
    BLOCKER = NONE

Recovered final source existed previously. The full exact body is not recoverable enough for canonical import.

## Confirmed high-level structure

Recovered schemas:

    wcp_core
    wcp_iam
    wcp_infra
    wcp_hosting
    wcp_commercial
    wcp_ops
    wcp_audit
    wcp_backup

Confirmed baseline rules include:

- UUID durable identifiers / UUIDv7 generation;
- typed TEXT registry identifiers;
- TIMESTAMPTZ authoritative instants;
- BIGINT revision >= 1 for applicable mutable aggregates;
- typed state fields;
- JSONB limited to typed/versioned payload roles;
- ON DELETE RESTRICT default unless explicitly authorized;
- no universal deleted_at;
- tenant-aware composite relationships;
- desired/observed state separation;
- durable jobs, attempts, idempotency, leases, outbox, audit and reconciliation concepts;
- no executable DDL/migrations in the canonical artifact.

## Missing material that blocks exact republication

Database must recover or explicitly re-decide the following; MASTER must not fill them in.

### DB-R01 — CORE completeness
- unrecovered columns/constraints for remaining CORE tables;
- all exact indexes;
- all delete exceptions.

### DB-R02 — IAM completeness
- complete tables beyond recovered fragments;
- all FKs;
- role/permission/assignment relations;
- nullability/checks/indexes;
- delete/retention semantics.

### DB-R03 — INFRA completeness
- complete server/resource tables;
- all tenant/server/resource constraints;
- indexes and delete semantics.

### DB-R04 — HOSTING completeness
- exact full column definitions for all hosting resources;
- all composite tenant-aware FKs;
- all indexes;
- lifecycle/revision placement;
- delete exceptions.

### DB-R05 — COMMERCIAL completeness
- exact tables/columns;
- FK/check/unique/nullability;
- entitlement/plan/feature/quota relationships;
- indexes and retention.

### DB-R06 — OPS completeness
Recovered evidence is substantial for jobs/idempotency/outbox/leases, but owner must confirm:
- exact final columns/types;
- uniqueness scopes;
- claim/fencing/concurrency rules;
- all indexes;
- retention;
- transaction boundaries.

### DB-R07 — AUDIT completeness
- exact final fields;
- FK rules;
- retention;
- partitioning;
- indexes;
- immutable occurrence relationships.

### DB-R08 — BACKUP completeness
- exact field names/types/checks;
- resource/restore-point relations;
- retention;
- backup/restore state relations;
- indexes.

### DB-R09 — global constraints
Owner must provide the complete final set of:
- PK/FK;
- UNIQUE;
- CHECK;
- nullability;
- composite tenant constraints;
- ownership immutability;
- delete behavior.

### DB-R10 — approved index catalogue
Every correctness/performance index declared in v1.0 must be republished exactly or explicitly re-approved.

### DB-R11 — transaction boundaries
Exact final transaction rules are required for:
- logical mutation + durable audit/outbox;
- idempotency acquisition/result;
- job enqueue;
- state/revision mutation;
- reconciliation state.

### DB-R12 — concurrency / locking
Exact approved rules for:
- revision conflict;
- duplicate submission;
- worker/job claim;
- lease/fencing;
- stale executor;
- late result.

### DB-R13 — retention / partitioning
Exact v1.0 policy required for:
- audit;
- events;
- jobs/attempts;
- idempotency;
- outbox;
- reconciliation;
- backups where represented.

### DB-R14 — backup/restore
Exact control-plane backup/restore boundaries and data-model rules.

### DB-R15 — migration boundary
Exact rules separating:
- schema contract;
- migration implementation;
- upgrade/downgrade compatibility;
- control-plane DB from hosted tenant/application DBs.

## Owner publication choices

Database must choose one:

### Option A — exact source recovered

If original final v1.0 source exists:

    REPUBLICATION TYPE = EXACT
    CONTENT CHANGE = NONE
    PROVENANCE =
    ORIGINAL SOURCE/DIGEST =
    TARGET COMMIT =

### Option B — reconstruction/amendment required

If missing material must be recreated:

    REPUBLICATION TYPE = OWNER RECONSTRUCTION / AMENDMENT
    CONTENT CHANGE = YES
    VERSION IMPACT =
    BEFORE/AFTER =
    CROSS-DOMAIN REVIEW =
    QA IMPACT =

Do not label reconstructed content CONTENT CHANGE=NONE.

## Acceptance block

Issue #6 can close only when:

    COMPLETE OWNER BODY = YES
    ALL DB-R01..R15 = RESOLVED OR NOT-APPLICABLE WITH RATIONALE
    CANONICAL PATH = specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md
    FINAL OWNER STATUS = YES
    SOURCE CODE CONSUMABLE = YES
    HISTORICAL EVIDENCE EXCLUDED FROM AUTHORITY = YES
    DDL/MIGRATIONS CREATED BY REPUBLICATION = NO

Until then:

    #6 = OPEN
    SC-M01 = PAUSED
    CANONICAL EVIDENCE = 5 / 8
