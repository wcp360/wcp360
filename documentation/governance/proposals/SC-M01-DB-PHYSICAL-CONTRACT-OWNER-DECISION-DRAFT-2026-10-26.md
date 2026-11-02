# WCP360 — SC-M01 Database Physical Contract: Owner Decision Draft

> Historical integration note (2026-11-02): this dated branch document is retained
> as development-source evidence. Its original decisions, pending states, path
> literals and execution claims describe the recorded snapshot; they do not
> override the current Core implementation or grant owner approval, independent
> E3 review, a CODE-BASELINE, deployment or production qualification. See the
> [tooling integration guide](../maintenance/BRANCH-TOOLING-INTEGRATION-2026-11-02.md)
> for current paths and separate current/historical CI scopes.
**Date:** 2026-10-26  
**Status:** PROPOSAL / DATABASE OWNER REVIEW REQUIRED / NON-CANONICAL  
**Tracking:** #6, #13, AUDIT-WCP-0102, AUDIT-WCP-0106, AUDIT-WCP-0108  
**Reference:** WCP360-BM-0001 FINAL; DB Model v1.0 owner metadata; DOC v4.0.21.23; DESIGN V2.5.3.36.4  
**Baseline change:** NONE  
**DDL / migrations:** NONE — this is **not** a substitute for a physical owner-approved schema.

## Purpose and provenance

The previously approved Database v1.0 body is not fully recoverable. The recovered fragments in
governance/recovery/SC-M01-RECOVERED-DB-FINAL-ARTIFACT-EVIDENCE.md
and the DB-R01..DB-R15 conformance report preserve useful evidence but do not establish exact final content.

This document makes the remaining **physical implementation decisions reviewable**. All proposals in this document are **new candidate choices**, not recovered final decisions. DATABASE may reject, amend or ratify each proposal and must declare a real content/version delta if rebuilding the missing body. MASTER and SOURCE CODE must not promote this draft.

## A. Confirmed / historically recovered invariants (not newly decided here)

1. Control-plane PostgreSQL is separate from customer-hosted MariaDB/PostgreSQL engines and their data files.
2. Account, Organization and Tenant are distinct identities. Account→Tenant ownership is 1:N; Organization↔Tenant is optional N:M; reseller delegation is explicit/revocable.
3. Eight recovered schemas: wcp_core, wcp_iam, wcp_infra, wcp_hosting, wcp_commercial, wcp_ops, wcp_audit, wcp_backup.
4. Durable identifiers are UUID (UUIDv7 generation); typed registry IDs are TEXT of maximum 128 characters using ^[a-z][a-z0-9_.-]{1,127}$.
5. TIMESTAMPTZ is the authoritative instant; applicable mutable aggregates have BIGINT revision >= 1; JSONB is limited to typed/versioned payload roles.
6. ON DELETE RESTRICT is the default unless an explicit owner-approved exception exists; no universal deleted_at.
7. Tenant-aware composite relationships must preserve tenant/hosting ownership; authorization is not inferred merely from a tenant_id.
8. No arbitrary shell/systemd/filesystem operation is introduced by the data model.
9. Durable intent / idempotency / jobs / audit / transactional outbox / reconciliation must preserve no-blind-retry semantics and UNKNOWN_OUTCOME.
10. Mandatory audit/outbox persistence for a pure control-plane mutation is in the source mutation's durable transaction; event delivery is at-least-once with duplicate-safe consumers.
11. Existing recovered tables include wcp_core.tenants/accounts, wcp_iam.role_assignments/tenant_memberships, wcp_infra.managed_resources, and wcp_ops.jobs/idempotency_records/leases/outbox_records/event_records/job_attempts.

These facts are only as complete as their cited recovered or current canonical source; **no additional columns, unique keys or indexes are implied**.

## B. DB-R01..DB-R15 decision table

All rows below are **UNAPPROVED PROPOSALS**. An exact retained prior final owner source supersedes the need to recreate corresponding rows.

| ID | Missing physical decision | Proposed owner resolution to review | Required cross-domain check |
|---|---|---|---|
| DB-R01 CORE | Complete columns/relationships, ownership and delete exceptions | Publish explicit Account, Tenant, Organization, Hosting Account, customer/reseller profile, membership and delegation relation inventory. Preserve composite tenant ownership, RESTRICT default and N:M link uniqueness. Do not collapse Account/Organization/Tenant. | MODULES, Security |
| DB-R02 IAM | Full IAM table/FK/role scope constraints | Define scoped role-assignment FKs and check constraints for GLOBAL/ACCOUNT/TENANT, DIRECT/DELEGATED/SYSTEM, delegated tenant equality and revocation. Never use membership as an implicit permission. | Security, API |
| DB-R03 INFRA | Server/node/resource relationships | Bind managed_resources to known resource registry and exact owner scope; define scope-kind field-nullability matrix and lineage. Do not use generic JSON privilege dispatch. | DevOps, Security |
| DB-R04 HOSTING | Complete domain/site/DNS/mail/DB resource schema | Declare tenant + hosting account composite FKs and lifecycle/revision placement for each resource. Customer DB engine metadata is not the physical customer DB. | MODULES, API |
| DB-R05 COMMERCIAL | Plan/feature/entitlement/quota schema | Model effective envelope inputs and history explicitly. Do **not** assume a positive account override can exceed the plan: Security S06 remains unresolved. | Security S06, Modules |
| DB-R06 OPS | Complete jobs, attempts, idempotency, leases, outbox | Ratify exact keys, unique scopes, transactional claim/fencing, result durability and crash-recovery transitions (see section C). | API/IPC, Security, SRE |
| DB-R07 AUDIT | Immutable occurrence constraints/indexes/retention | Ratify append-only audit occurrence identity, safe redacted payloads, required reference types, retention and immutable evidence; never cascade-delete audit. | Security, SRE, QA |
| DB-R08 BACKUP | Backup/restore entities and retention | Separate control-plane DB backup, tenant workload backup and hosted engine data. Define restore operation/resource reference and no misleading success state. | DevOps, Security |
| DB-R09 GLOBAL | Complete PK/FK/UNIQUE/CHECK/nullability/delete | Produce table-by-table physical catalogue including ownership columns, tenant composite key checks, change restrictions and explicit exceptions. | All owners |
| DB-R10 INDEXES | Approved correctness/performance indexes | List every uniqueness index and key query index with exact columns, predicate, uniqueness and reason; distinguish correctness from speculative optimization. | Performance, API |
| DB-R11 TRANSACTIONS | Atomicity of mutation, audit, outbox, idempotency | Specify transaction sequences and failure points for pure DB mutations and external-effect workflows; neither commit an effect intent without required evidence nor pretend external rollback is atomic. | API/IPC, Security |
| DB-R12 CONCURRENCY | Revision / leasing / stale claim fencing | Require optimistic revision checks, monotonic fencing and protection against stale executor updates; decide exact DB transaction/lock strategy, not a symbolic “single worker” claim. | API/IPC, QA |
| DB-R13 RETENTION | Exact durations and partitioning | Publish per-record class policy (audit, outbox, attempts, idempotency, jobs, backups); keep durations UNDECIDED until owner ratification. Partitioning is not automatically required for v1.0. | Security, SRE |
| DB-R14 RESTORE | Restore consistency and authority boundaries | Define identity, ownership, version and temporal consistency for control-plane restore, including outbox/leases/UNKNOWN_OUTCOME reconciliation after restore. | DevOps, SRE |
| DB-R15 MIGRATIONS | Migration/DDL implementation boundary | Keep final physical contract normative but non-executable; future migrations must be additive/versioned/audited, include backup and downgrade classification and cannot invent model columns. | DevOps, Engineering, QA |

## C. Concrete choices requiring owner selection

### C1 — Scope-aware idempotency uniqueness (DB-R06/09/10)

**Recovered evidence:** idempotency_id UUID PK; scope_kind GLOBAL/ACCOUNT/TENANT; account_id/tenant_id nullable; operation_namespace, idempotency_key, request_fingerprint BYTEA, state, result_reference, timestamps, revision; scope-specific uniqueness exists but exact SQL form was not recovered.

**PROPOSAL (not final):** encode mutually exclusive scope fields by CHECK and implement three scope-specific uniqueness predicates, or introduce a typed immutable scope reference with one composite UNIQUE. Either must distinguish GLOBAL, ACCOUNT and TENANT without NULL uniqueness leakage. Preserve same key + differing semantic fingerprint → IDEMPOTENCY_CONFLICT. Expiry/reuse safety, key canonicalization and concurrency conflict behavior require API + DB joint acceptance.

**DO NOT:** treat a UNIQUE(scope_kind, account_id, tenant_id, key) with nullable identities as sufficient without proving NULL semantics; do not use tenant_id as authorization proof.

**Owner to decide:** exact uniqueness DDL shape, scope normalization, key reuse/expiry, acquisition transaction, result-reference FK.

### C2 — Durable job and outbox boundaries (DB-R06/11)

**Recovered evidence:** wcp_ops.jobs has typed job_type/action_id, tenant/resource/requester identifiers, states/outcomes, attempts, availability, correlation and revision. outbox_records has typed event, payload version, delivery state and at-least-once delivery. job_attempts exists but full columns are unrecovered.

**PROPOSAL:** define an explicit transaction boundary for (authorization-checked logical mutation + mandatory audit + outbox + durable job intent). For an external effect, persist pre-dispatch intent before any privileged dispatch; persist observed result afterward; on lost reply, maintain UNKNOWN_OUTCOME and reconcile before retry. Do not claim atomicity across PostgreSQL and Linux.

**Owner to decide:** exact job_attempt columns/uniqueness, outbox dedupe, persistence ordering, versioned events, late result application.

### C3 — Lease/fencing safety (DB-R06/12)

**Recovered evidence:** leases have lease_id, target_kind, exactly one of job_id/resource_id, owner_worker_id, fencing_token >=1, acquired_at, expires_at; only one active owner per target and monotonically increasing tokens are required.

**PROPOSAL:** use a durable target-identity/sequence mechanism plus transactionally serialized lease acquisition and an explicit compare-and-fence check on every state update. Define expiry/reclaim and the recovery path for a process crash.

**DO NOT:** rely on a partial unique index with a non-immutable NOW() predicate; do not assume lease expiry physically prevents stale workers from committing.

**Owner to decide:** target key/locking strategy, token sequence and rollover, lease expiry semantics, claimant identity and max concurrency.

### C4 — Tenant-aware relationships (DB-R01/02/03/04/09)

**Recovered evidence:** wcp_core.tenants UNIQUE(tenant_id, account_id) and account→tenant ownership; hosting and infra relationships include tenant-aware composite references. role_assignments have GLOBAL/ACCOUNT/TENANT scope and a delegated assignment must bind to the delegation's tenant.

**PROPOSAL:** record for *each* tenant-owned table the composite parent key used to prevent cross-tenant FK attachment; require explicit reassignment semantics and audit evidence rather than silent account_id/tenant_id updates.

**Owner to decide:** per-table nullability and composite FK list, global resources, cross-account transfer and revocation propagation.

### C5 — Audit evidence and retention (DB-R07/11/13)

**Recovered evidence:** audit_record_id is immutable occurrence ID, audit_event_id is semantic type, correlation_id is separate; required audit cannot be best effort; safe_payload is JSONB with redaction obligations.

**PROPOSAL:** maintain append-only audit occurrence records with independently declared retention, pruning/export authority and no cascading from deleted business records. State error classes must distinguish persistence failure vs outcome uncertainty.

**Owner to decide:** FK delete behavior for deleted principals/resources, retention policy values, archive/export, partitioning threshold and correctness indexes.

## D. Recovered exact fragments that must not be overwritten by a proposal

- wcp_core.tenants: (tenant_id PK UUID, account_id UUID FK→accounts RESTRICT, name, tenant_key, lifecycle_state, revision, created_at, updated_at); UNIQUE(account_id,tenant_key); UNIQUE(tenant_id,account_id).
- wcp_core.accounts: account_id UUID PK, display_name, lifecycle_state, revision, created_at, updated_at; revision >= 1; account deletion restricted while tenants exist.
- wcp_iam.tenant_memberships: (tenant_id,user_id) PK; membership_state, created_at; membership != authorization.
- wcp_iam.role_assignments: assignment_id PK, user_id, role_id, scope_kind, account_id?, tenant_id?, delegation_id?, source_kind, state, validity, revision and timestamps.
- wcp_infra.managed_resources: resource_id PK, resource_type/scope, nullable tenant_id/hosting_account_id/server_id, lifecycle, revision/timestamps; UNIQUE(tenant_id,resource_id) and tenant+hosting account FK.
- wcp_ops.jobs/idempotency_records/leases/outbox_records/event_records and wcp_audit.audit_records have recovered column fragments in the historical evidence file; use those fragments verbatim when available, not guessed equivalents.

## E. Publication decision form — Database owner only

For each DB-R01..15, DATABASE must return:
- ACCEPT EXISTING FINAL / AMEND PROPOSAL / REJECT / NEEDS OTHER OWNER;
- exact normative table/column/index/constraint/transaction decisions;
- references to owner agreements on API/IPC/Security/DevOps;
- dependency and change/risk/test list.

Then declare one of:

**A — EXACT REPUBLICATION:** retained exact prior final body, original digest/provenance, CONTENT CHANGE = NONE, DB MODEL v1.0.

**B — OWNER RECONSTRUCTION/AMENDMENT:** prior exact source unavailable, list of *newly decided content*, honest CONTENT CHANGE = YES and DB version impact requiring MASTER ratification.

The final output must be the complete self-contained owner-approved contract under specifications/database/ with exact Git commit and SHA. This candidate is **not** that artifact.

## Gate

ISSUE #6 = OPEN. SC-M01 = PAUSED — TARGETED EVIDENCE. CODE-BASELINE = NONE. No DDL, migrations or privileged code is authorized by this draft.
