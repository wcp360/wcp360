# WCP360 — Database v1.0 Recovery / Republication Conformance Matrix

**Status:** DERIVED RECOVERY EVIDENCE — NON-CANONICAL  
**Reference main:** ad3a3df25ee7d20ea8e1c726c785a6c483790668  
**Issue:** #6  
**Owner:** WCP360 — DATABASE / DATA MODEL

Sources:

- \`governance/handoffs/SC-M01-DB-OWNER-REPUBLICATION-CHECKLIST.md\`
- \`governance/recovery/SC-M01-RECOVERED-DB-FINAL-ARTIFACT-EVIDENCE.md\`

Purpose: show exactly how much of DB-R01…DB-R15 is historically recoverable, without turning recovery evidence into Database authority.

## 1. Recovery result

    DB-R items examined = 15
    items with some recoverable evidence = 15
    items exact-complete enough for autonomous canonical republication = 0
    safe MASTER reconstruction = NO
    owner action required = YES

The recovery is substantial enough to prevent blank-slate redesign, but not complete enough to recreate the exact final v1.0 physical contract.

## 2. State definitions

- **SUBSTANTIAL PARTIAL RECOVERY** — several exact fragments and final invariants are recovered, but complete physical rules are missing.
- **PARTIAL RECOVERY** — one or more final fragments/invariants are recovered, but most exact detail is missing.
- **CORE RULE RECOVERED / DETAILS MISSING** — the final high-level decision is recoverable, but exact implementation wording/fields/catalogue is not.
- **NOT EXACTLY RECOVERED** — historical existence is known, but enough final detail is absent that owner must recover/re-decide.

None of these means canonical completion.

## 3. DB-R01…DB-R15

| ID | Topic | Recovery state | Recovered evidence | Still missing / owner action |
|---|---|---|---|---|
| DB-R01 | CORE completeness | SUBSTANTIAL PARTIAL RECOVERY | exact fragments for tenants, accounts, customer_profiles, reseller_profiles, organizations, account_organization_memberships, organization_tenant_memberships, hosting_accounts, reseller_tenant_delegations; common UUID/revision/state/delete rules | remaining CORE fields/constraints, every index, delete exceptions, exact final catalogue/order |
| DB-R02 | IAM completeness | PARTIAL RECOVERY | exact fragments for tenant_memberships and role_assignments; tenant/ownership/delegation invariants | complete IAM table set, all fields/FKs/nullability/checks/indexes, delete/retention semantics |
| DB-R03 | INFRA completeness | PARTIAL RECOVERY | managed_resources fragment and control/resource ownership concepts | complete server/node/resource schema, all tenant/server/resource constraints, indexes/delete semantics |
| DB-R04 | HOSTING completeness | SUBSTANTIAL PARTIAL RECOVERY | exact recovered fragments for domains, websites, dns_zones, dns_records, mail_domains, mailboxes, certificates, hosted_db_engine_instances, hosted_databases, hosted_db_principals | full final columns/types for all hosting resources, complete composite FKs, lifecycle/revision placement, all indexes/delete exceptions |
| DB-R05 | COMMERCIAL completeness | PARTIAL RECOVERY | plans, plan_entitlements, subscriptions, subscription_entitlements fragments | complete commercial tables/fields, FK/CHECK/UNIQUE/nullability, full plan/feature/quota relations, indexes/retention |
| DB-R06 | OPS completeness | SUBSTANTIAL PARTIAL RECOVERY | jobs, job_attempts, idempotency_records, leases, outbox_records, event_records; idempotency/outbox rules; job-claim/durability concepts | exact final columns/types/scopes, full claim/fencing rules, all indexes, retention and complete transaction boundaries |
| DB-R07 | AUDIT completeness | PARTIAL RECOVERY | audit_records fragment, audit durability concepts, some correctness-index evidence | exact final fields/FKs, immutable relationships, retention, partitioning decision application, full index catalogue |
| DB-R08 | BACKUP completeness | PARTIAL RECOVERY | backups, backup_objects, restore_operations; control-plane/hosted/workload backup-class separation; restore re-observation/reconciliation rules | exact final fields/types/checks, restore-point/resource relations, state model, retention, indexes |
| DB-R09 | global constraints | SUBSTANTIAL PARTIAL RECOVERY | UUIDv7 identity baseline, typed registry IDs, TIMESTAMPTZ/revision/state/JSONB rules, tenant-aware integrity, ON DELETE RESTRICT default, ownership rules | exhaustive PK/FK/UNIQUE/CHECK/nullability, all composite tenant constraints, every delete exception/immutability clause |
| DB-R10 | approved index catalogue | PARTIAL RECOVERY | classes of correctness indexes and some uniqueness/single-active constraints are recovered | every exact approved index, exact names, columns/order/predicates |
| DB-R11 | transaction boundaries | PARTIAL RECOVERY | durable intent concepts; source mutation + required outbox intent atomicity; idempotency/job/audit transactional requirements recovered in part | complete final transaction-boundary catalogue/order for mutations, idempotency, enqueue, revisions, reconciliation |
| DB-R12 | concurrency / locking | SUBSTANTIAL PARTIAL RECOVERY | revision conflicts, duplicate-submission controls, jobs/attempts, leases, stale-executor/late-result concepts | exact final locking/claim/fencing algorithms, constraints/index support and all race-resolution rules |
| DB-R13 | retention / partitioning | CORE RULE RECOVERED / DETAILS MISSING | v1.0 rule: partitioning NOT REQUIRED; future partitioning requires workload evidence + Performance review | exact retention durations/policy text; final per-table retention rules; any owner-specific cleanup clauses |
| DB-R14 | backup / restore | CORE RULE RECOVERED / DETAILS MISSING | backup classes remain distinct; restore followed by physical re-observation, drift detection, safe reconciliation; no blind replay | complete data-model relations, exact state transitions/constraints, retention/index details and original wording |
| DB-R15 | migration boundary | CORE RULE RECOVERED / DETAILS MISSING | DB Model v1.0 is input for future migrations; canonical artifact itself creates no DDL/MIG-WCP package; control-plane DB remains distinct from hosted DBs | complete final migration/compatibility wording, exact upgrade/downgrade constraints and owner-specific exceptions |

## 4. Recovered final decisions that should not be redesigned casually

The owner should treat the following as strong historical final evidence and either confirm or explicitly supersede them:

- eight PostgreSQL schemas:
  \`wcp_core\`, \`wcp_iam\`, \`wcp_infra\`, \`wcp_hosting\`, \`wcp_commercial\`, \`wcp_ops\`, \`wcp_audit\`, \`wcp_backup\`;
- UUID durable identifiers / UUIDv7 generation;
- typed registry identifiers distinct from UUID entity IDs;
- TIMESTAMPTZ authoritative instants;
- revision >= 1 for applicable mutable aggregates;
- state fields as controlled identifiers;
- JSONB restricted to typed/versioned payload purposes;
- ON DELETE RESTRICT default unless explicit exception;
- no universal deleted_at;
- tenant-aware composite relationships;
- desired state != observed state;
- durable jobs/idempotency/leases/outbox/audit/reconciliation;
- same idempotency key + same semantic request => same logical result;
- same key + different semantic fingerprint => conflict;
- source mutation + required outbox intent committed atomically;
- outbox delivery = at least once; duplicate-safe consumers required;
- partitioning is not required by DB Model v1.0;
- backup classes control-plane / hosted database / workload remain distinct;
- migration implementation is future work, not part of the canonical model publication.

This list is evidence, not permission for MASTER to generate missing physical details.

## 5. Owner path

### Path A — retained exact source

If Database possesses the complete previously-approved final source:

    REPUBLICATION TYPE = EXACT
    CONTENT CHANGE = NONE
    VERSION = v1.0
    PROVENANCE = exact retained source
    DB-R01..R15 = resolved by source

Then publish at the canonical target and verify against current owner contracts.

### Path B — original source unavailable

Database should use the recovery file as retrieval/context input and explicitly decide all still-missing portions.

Required:

    REPUBLICATION TYPE = OWNER RECONSTRUCTION / AMENDMENT
    CONTENT CHANGE = REAL DELTA
    VERSION IMPACT = OWNER DECISION
    DB-R01..R15 = owner decisions / not-applicable with rationale
    CROSS-DOMAIN REVIEW = required where behavior affects API/Security/QA/DevOps

Do not call this exact v1.0 recovery unless the owner can establish it.

## 6. Closure readiness

    HISTORICAL RECOVERY EXHAUSTED ENOUGH TO STOP SEARCHING = YES
    OWNER CHECKLIST COMPLETE = YES
    OWNER DECISION / EXACT SOURCE = MISSING
    CANONICAL DATABASE ARTIFACT = MISSING
    ISSUE #6 = OPEN

The next meaningful Database action is owner republication/reconstruction, not another generic recovery search.
