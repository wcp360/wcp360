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


## 12. Post-audit owner inputs — 2026-10-02

**Status of this section:** NON-NORMATIVE INPUT ONLY. It records dependencies that the Database owner should map to the exact final DB v1.0 artifact. It does not create or amend the data model.

### Provenance warning

Targeted history recovery found detailed schemas/tables/constraints in closure-proposed/candidate DB material. Those details are **not proven to be the exact final owner body**.

Therefore:

```text
FINAL OWNER METADATA = CONFIRMED
EXACT FINAL BODY = NOT MATERIALIZED
CANDIDATE PHYSICAL DETAILS = RECOVERY AID ONLY
PROMOTION OF CANDIDATE DETAILS INTO SPECIFICATIONS = FORBIDDEN
```

The Database owner may reuse prior work from its own source, but MASTER/SOURCE CODE must not reconstruct the final contract from candidate snippets.

### T06 — durable pre-effect / idempotency persistence

After #6 is materialized, the owner should point to the exact physical references for:

- logical operation / intent identity;
- API Idempotency-Key persistence;
- semantic-request binding / conflict detection;
- durable job / operation relationship;
- uniqueness / duplicate prevention;
- transaction boundary around durable intent;
- outbox / audit durability relationship;
- retained logical result;
- retention / expiry / reuse safety;
- cross-tenant uniqueness/isolation.

Current API semantics are already final:

```text
same Idempotency-Key + same semantic request
→ same logical result

same key + different semantic request
→ IDEMPOTENCY_CONFLICT

UNKNOWN_OUTCOME
→ reconciliation before any retry
```

Database must not redefine those semantics; it supplies the physical persistence and concurrency guarantees.

### T07 — claims / recovery / reconciliation

The owner should identify the exact final structures/rules for:

- claim/executor ownership;
- concurrent workers/executors;
- stale claim / lease / fencing or approved equivalent;
- retryable versus non-retryable state;
- restart recovery;
- late result attachment;
- reconciliation state;
- final result durability;
- retention/cleanup.

No mechanism such as advisory lock, SKIP LOCKED, lease table or fencing token is selected by this pack unless it already exists in the exact final owner artifact.

### D03 — target/revision/grant mappings

Where persistence-backed, the owner should point to the final definitions for:

- resource/revision identity used by API ↔ IPC translation;
- expected revision semantics;
- grant_set representation;
- same database / DB identity / Tenant binding;
- desired/observed state references.

Do not infer that `privileges[]`, `grant_set_id`, desired final set and revoke subset are the same physical object.

### C04-W2 — conditional worker/job path

If API/DevOps/Security retain an indirect worker model:

```text
worker
→ durable job/state
→ authorized control component
→ wcpd
```

Database must point to the already-approved durable producer/consumer/claim/revision structures.

This is CONDITIONAL. The pack does not choose W2.

### S06/S07 — overrides, entitlements and delegation representation

Security remains owner of the authorization semantics.

Database should only identify the final persistence for:

- account overrides;
- role/permission grants;
- plan/feature/entitlement references;
- reseller delegation;
- delegation scope/revocation/revision;
- any temporal privilege/approval persistence owned by DB.

A database row never becomes authorization merely because it exists; Security composes the final decision.

### Evidence linkage

Relevant non-normative evidence now in main or the dedicated recovery publication:

```text
governance/recovery/SC-M01-RECOVERED-DB-FINAL-ARTIFACT-EVIDENCE.md
```

The recovery file preserves exact/near-verbatim fragments retrieved from the prior final-artifact publication, including global rules and selected CORE/IAM/INFRA/HOSTING/OPS structures. It explicitly lists unrecovered sections and cannot close #6.

Relevant cross-domain evidence:

```text
governance/proposals/SC-M01-OWNER-DECISION-MATRIX.md
governance/proposals/SC-M01-RECOVERED-AUTHORIZATION-CONTEXT-OUTCOME.md
governance/proposals/SC-M01-RECOVERED-TRACEABILITY-IDEMPOTENCY.md
governance/audits/AUDIT-WCP-0100/CLOSURE-MATRIX.md
```

These files help identify required properties only. They are not Database authority.

## 13. Exact owner response requested

To close issue #6 without re-audit or redesign, Database should publish or provide the exact final owner body and state:

```text
ARTIFACT =
WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md

VERSION = v1.0
FINAL APPROVED CONTENT = YES
PHYSICAL CONTRACT = INCLUDED
HISTORICAL CONTENT INCLUDED = NO
CONTENT CHANGE = NONE, if exact republication
READY FOR SOURCE CODE CONSUMPTION = YES
BLOCKER = NONE
PROVENANCE =
COMMIT / SOURCE =
```

If the recovered owner source is no longer byte-identical and must be recreated/amended, the owner must declare a real content delta/version impact rather than using `CONTENT CHANGE = NONE`.

This input pack still cannot satisfy issue #6 by itself.


## 14. Recovery discovery — 2026-10-03

Targeted conversation-history recovery established a stronger fact than the earlier input pack:

```text
FINAL DB ARTIFACT BODY WAS PUBLISHED IN A PRIOR WCP360 CONVERSATION
RECOVERED SOURCE EVENT ≈ 2026-09-29 20:26 UTC
FULL ORIGINAL BODY CURRENTLY RECOVERABLE = NO
```

Recovered evidence includes exact fragments for:

- publication metadata and scope/invariants;
- schemas and identifier/common physical rules;
- `wcp_core.tenants`;
- `wcp_iam.tenant_memberships`;
- `wcp_iam.role_assignments`;
- `wcp_infra.managed_resources`;
- HOSTING table inventory;
- `wcp_ops.jobs`;
- `wcp_ops.idempotency_records`;
- `wcp_ops.leases`;
- `wcp_ops.outbox_records`;
- `wcp_ops.event_records`;
- tenant-integrity, delete and uncertain-outcome invariants.

These fragments prove that the richer owner contract existed and materially reduce rediscovery. They still cannot establish every missing table/column/FK/index/transaction rule.

The owner should treat this as **recovery evidence**, not as a license for MASTER to complete missing sections by inference.
