# WCP360 — SC-M01 Database Canonical Input Pack

**Status:** `OWNER INPUT ONLY — NOT DB MODEL AUTHORITY`  
**Producer:** `WCP360 — MASTER / Project Control`  
**Consumer:** `WCP360 — DATABASE / DATA MODEL`  
**Reference:** `WCP360-BM-0001 FINAL / SC-M01 TARGETED EVIDENCE`

This file does **not** replace the DATABASE owner artifact.

Required final owner artifact:

```text
WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md
```

Owner status already confirmed:

```text
DB MODEL VERSION = v1.0
FINAL APPROVED CONTENT = YES
PHYSICAL CONTRACT = INCLUDED
HISTORICAL CONTENT INCLUDED = NO
READY FOR SOURCE CODE CONSUMPTION = YES
BLOCKER = NONE
```

## 1. Canonical source inputs already in GitHub

```text
specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md
manifests/WCP360-BM-0001.md
```

## 2. Identity / tenancy invariants

DATABASE final materialization must preserve:

```text
account_id != organization_id != tenant_id
```

Canonical relationship graph:

```text
Account ↔ Organization = N:M MEMBERSHIP
Organization ↔ Tenant = N:M OPTIONAL GROUPING
Account → Tenant = 1:N OWNERSHIP
Tenant → Hosting Account = 1:N OWNERSHIP
Account → Customer = 0..1 DEPENDENT PROFILE
Account → Reseller = 0..1 DEPENDENT PROFILE
Reseller ↔ Tenant = N:M EXPLICIT REVOCABLE DELEGATION
```

Mandatory inverses:

```text
Tenant → exactly 1 owning Account
Hosting Account → exactly 1 Tenant
Customer → exactly 1 Account
Reseller → exactly 1 Account
```

Organization membership does not itself authorize Tenant access.

Reseller delegation never transfers ownership.

## 3. Data-authority boundary

```text
CONTROL-PLANE POSTGRESQL
!= HOSTED DB ENGINES
!= TENANT / APPLICATION DB CONTENT
```

Canonical state authority:

```text
PostgreSQL = authoritative logical control-plane state
/var/lib/wcp360 = materialized / node-local / reconstructable
/run/wcp360 = ephemeral runtime
/var/cache/wcp360 = reconstructable cache
Linux / services / filesystem = observed physical state
```

No second independent writable authority may silently exist for the same logical state.

## 4. Persistence obligations already implied by canonical contracts

The physical DB contract must include the already-approved persistence required for:

- accounts / organizations / tenants;
- ownership / membership / delegation;
- resources and revisions;
- API idempotency keys and semantic-request binding;
- durable jobs;
- job state transitions;
- outbox / dispatch intent where applicable;
- audit occurrence durability dependencies;
- reconciliation state;
- operation uncertainty / `UNKNOWN_OUTCOME`;
- approvals where persisted;
- configuration desired state;
- runtime desired/observed reconciliation references.

## 5. Failure and transaction semantics

Canonical distinctions:

```text
timeout != definite failure
retry != reconciliation
rollback != compensation
execution success != verified success
```

Pure control-plane DB mutations requiring mandatory audit must be atomically durable with the corresponding audit/outbox contract where applicable.

External effects must not be wrapped in fictional cross-system ACID.

Required pattern:

```text
durable intent
→ dispatch
→ observe
→ durable result
```

If final state cannot be proven:

```text
UNKNOWN_OUTCOME
→ observe
→ reconcile
→ final durable outcome
```

## 6. Concurrency / revision requirements

The final physical contract must preserve approved concurrency semantics, including applicable:

- expected revision / optimistic concurrency;
- uniqueness protection;
- duplicate/idempotency protection;
- explicit locking only where needed;
- no lost-update semantics;
- no blind destructive retry after uncertain dispatch.

## 7. Audit identity distinction

```text
audit_event_id = semantic event type
audit_record_id = immutable persisted occurrence
correlation_id = relationship identifier, never authorization
```

## 8. Required physical contract content

The owner artifact is expected to contain, where applicable:

- schemas;
- tables;
- identifiers;
- PK/FK;
- tenant-aware constraints;
- UNIQUE / CHECK / nullability;
- ownership relations;
- delete semantics;
- transaction boundaries;
- concurrency / revision rules;
- jobs / outbox persistence;
- idempotency persistence;
- audit durability dependencies;
- reconciliation state;
- approved indexes.

## 9. Prohibited substitutions

Do not satisfy issue #6 using:

- Phase C findings;
- R4 candidate packages;
- historical DB notes;
- Design System DB examples;
- inferred schemas;
- generated DDL;
- migrations.

Only the exact DATABASE owner artifact closes the gate.

## 10. GitHub target

```text
specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md
```

## 11. Gate status

```text
ISSUE #6 = OPEN
SC-M01 CANONICAL EVIDENCE = 5 / 8
SOURCE CODE = PAUSED
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```
