# WCP360 — Recovered DB Model v1.0 Final-Artifact Evidence

**Status:** HISTORICAL RECOVERY EVIDENCE ONLY — NON-NORMATIVE — INCOMPLETE  
**Recovery date:** 2026-10-03  
**Recovered source event:** prior WCP360 assistant publication on 2026-09-29 at approximately 20:26 UTC  
**Target owner artifact:** \`WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md\`  
**Owner:** WCP360 — DATABASE / DATA MODEL  
**Issue:** #6

This file preserves exact or near-verbatim fragments recoverable from the prior publication of the final Database artifact.

It is **not** the canonical Database artifact because the complete original body has not yet been recovered byte-for-byte. It must not be copied into \`specifications/database/\` or consumed by SOURCE CODE as authority.

The purpose is evidence preservation and owner-assisted republication.

## 1. Recovered publication metadata

Recovered exact publication header:

\`\`\`text
ARTIFACT = WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md
DB MODEL VERSION = v1.0
STATUS = FINAL APPROVED
CONTENT CHANGE = NONE
\`\`\`

Recovered final-status facts:

\`\`\`text
PHYSICAL CONTRACT = INCLUDED
FINAL APPROVED CONTENT = YES
HISTORICAL CONTENT INCLUDED = NO
READY FOR SOURCE CODE CONSUMPTION = YES
BLOCKER = NONE
\`\`\`

No executable DDL or migrations were part of the artifact.

## 2. Section 1 — recovered exact scope and invariants

Recovered heading:

\`\`\`text
## 1. Scope and invariants
\`\`\`

Recovered text:

> This artifact is the canonical physical implementation contract for the WCP360 Control-plane PostgreSQL model.  
> It does not contain executable DDL or migrations.

Recovered mandatory invariants:

\`\`\`text
account_id != organization_id != tenant_id
Tenant = security/data isolation boundary
ownership != membership != delegation
Reseller delegation != Tenant ownership

CONTROL-PLANE POSTGRESQL
!= HOSTED DB ENGINES
!= TENANT / APPLICATION DB CONTENT
\`\`\`

Recovered state-authority block:

\`\`\`text
PostgreSQL
= authoritative logical control-plane state

/var/lib/wcp360
= materialized / node-local / reconstructable

/run/wcp360
= ephemeral runtime

/var/cache/wcp360
= reconstructable cache

Linux / services / filesystem
= observed physical state
\`\`\`

## 3. Section 2 — recovered PostgreSQL schemas

Recovered heading:

\`\`\`text
# 2. PostgreSQL schemas
\`\`\`

Recovered canonical schema list:

\`\`\`text
wcp_core
wcp_iam
wcp_infra
wcp_hosting
wcp_commercial
wcp_ops
wcp_audit
wcp_backup
\`\`\`

Recovered rule:

\`\`\`text
public must not contain WCP360 business/control-plane tables
\`\`\`

## 4. Section 3 — recovered physical identifier baseline

Recovered heading:

\`\`\`text
# 3. Physical identifier baseline
\`\`\`

Recovered identifier rules:

\`\`\`text
PostgreSQL durable identifier type = uuid
generation = UUIDv7
\`\`\`

Typed registry identifiers use:

\`\`\`text
physical type = TEXT
maximum = 128 characters
pattern = ^[a-z][a-z0-9_.-]{1,127}$
\`\`\`

Recovered invariant:

\`\`\`text
typed namespaces remain distinct
\`\`\`

This recovery does not reconstruct every identifier class that may have appeared in the full artifact.

## 5. Section 4 — recovered common physical rules

Recovered heading:

\`\`\`text
# 4. Common physical rules
\`\`\`

### 4.1 Timestamps

Recovered rule:

\`\`\`text
authoritative instants = TIMESTAMPTZ
local-time timestamps are not authoritative
\`\`\`

### 4.2 Revision

Recovered rule:

\`\`\`text
revision BIGINT NOT NULL
CHECK (revision >= 1)
\`\`\`

Successful mutable-aggregate mutations increment revision.

A stale expected revision fails instead of overwriting newer state.

### 4.3 State fields

Recovered rule:

\`\`\`text
TEXT NOT NULL
+ canonical-registry CHECK
\`\`\`

Free-form states are forbidden.

### 4.4 JSONB

Recovered permitted uses:

- schema-versioned typed desired state;
- schema-versioned typed observed state;
- event payload;
- safe metadata;
- entitlement value;
- notification payload.

Recovered prohibited use as the primary representation for:

- relational ownership;
- membership;
- delegation;
- permissions;
- roles;
- resource identity;
- secret identity.

### 4.5 Delete default

Recovered rule:

\`\`\`text
ON DELETE RESTRICT
\`\`\`

unless explicitly authorized by the table/relationship contract.

Recovered rule:

\`\`\`text
no universal deleted_at
\`\`\`

## 6. Section 5 — CORE schema: recovered structure

Recovered heading:

\`\`\`text
# 5. CORE schema
\`\`\`

Recovered table set:

\`\`\`text
wcp_core.accounts
wcp_core.customer_profiles
wcp_core.reseller_profiles
wcp_core.organizations
wcp_core.account_organization_memberships
wcp_core.tenants
wcp_core.organization_tenant_memberships
wcp_core.hosting_accounts
wcp_core.reseller_tenant_delegations
\`\`\`

The complete exact column definitions for all CORE tables have **not** yet been recovered. They must not be guessed.

### 6.1 Exact recovered \`wcp_core.tenants\` fragment

Recovered columns:

\`\`\`text
tenant_id UUID NOT NULL PK
account_id UUID NOT NULL
name TEXT NOT NULL
tenant_key TEXT NOT NULL
lifecycle_state TEXT NOT NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered FK:

\`\`\`text
account_id → accounts(account_id) ON DELETE RESTRICT
\`\`\`

Recovered unique constraints:

\`\`\`text
UNIQUE (account_id, tenant_key)
UNIQUE (tenant_id, account_id)
\`\`\`

No additional columns or constraints are reconstructed here.

## 7. IAM schema — exact recovered fragments

### 7.1 \`wcp_iam.tenant_memberships\`

Recovered columns:

\`\`\`text
tenant_id UUID NOT NULL
user_id UUID NOT NULL
membership_state TEXT NOT NULL
created_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered PK:

\`\`\`text
PRIMARY KEY (tenant_id, user_id)
\`\`\`

Recovered invariants:

\`\`\`text
Tenant membership != permission
Tenant membership != ownership
\`\`\`

The exact final publication fragment recovered here did not expose an explicit FK/delete rule for this table. None is invented.

### 7.2 \`wcp_iam.role_assignments\`

Recovered columns:

\`\`\`text
assignment_id UUID NOT NULL PK
user_id UUID NOT NULL
role_id UUID NOT NULL
scope_kind TEXT NOT NULL
account_id UUID NULL
tenant_id UUID NULL
delegation_id UUID NULL
source_kind TEXT NOT NULL
assignment_state TEXT NOT NULL
valid_from TIMESTAMPTZ NULL
valid_until TIMESTAMPTZ NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered scope rules include:

\`\`\`text
scope_kind ∈ GLOBAL / ACCOUNT / TENANT
source_kind ∈ DIRECT / DELEGATED / SYSTEM
\`\`\`

Recovered delegated-assignment constraints:

\`\`\`text
DELEGATED
→ delegation_id IS NOT NULL
→ scope_kind = TENANT

non-DELEGATED
→ delegation_id IS NULL
\`\`\`

Recovered invariant:

\`\`\`text
delegated assignments bind to the same Tenant as the delegation
\`\`\`

No unrecovered FK/delete rules are invented here.

## 8. INFRA schema — exact recovered fragment

### 8.1 \`wcp_infra.managed_resources\`

Recovered columns:

\`\`\`text
resource_id UUID NOT NULL PK
resource_type TEXT NOT NULL
scope_kind TEXT NOT NULL
tenant_id UUID NULL
hosting_account_id UUID NULL
server_id UUID NULL
lifecycle_state TEXT NOT NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered scope kinds:

\`\`\`text
GLOBAL
SERVER
TENANT
HOSTING_ACCOUNT
TENANT_SERVER
\`\`\`

Recovered tenant-aware FK:

\`\`\`text
(tenant_id, hosting_account_id)
→ hosting_accounts(tenant_id, hosting_account_id)
\`\`\`

Recovered unique constraint:

\`\`\`text
UNIQUE (tenant_id, resource_id)
\`\`\`

Recovered invariant:

\`\`\`text
managed_resources is not a generic JSON store
managed_resources is not a generic privileged-operation store
\`\`\`

## 9. HOSTING schema — recovered inventory

Recovered heading:

\`\`\`text
# 8. HOSTING schema
\`\`\`

Recovered table names:

\`\`\`text
wcp_hosting.domains
wcp_hosting.websites
wcp_hosting.dns_zones
wcp_hosting.dns_records
wcp_hosting.mail_domains
wcp_hosting.mailboxes
wcp_hosting.certificates
wcp_hosting.hosted_db_engine_instances
wcp_hosting.hosted_databases
wcp_hosting.hosted_db_principals
\`\`\`

Recovered architectural rule:

\`\`\`text
typed hosting resources link to managed_resources
lifecycle authority remains there
\`\`\`

Recovered global HOSTING semantics:

- tenant-scoped child references use composite \`(tenant_id, parent_id)\` FKs;
- tenant ownership columns are immutable;
- applicable mutable rows use \`revision BIGINT NOT NULL\` and \`CHECK (revision >= 1)\`;
- default delete behavior remains \`ON DELETE RESTRICT\`;
- audit evidence is never cascade-deleted.

The exact full column definitions for these ten tables are not yet recoverable and are not reconstructed.

## 10. OPS schema — exact recovered fragments

Recovered OPS inventory includes:

\`\`\`text
wcp_ops.jobs
wcp_ops.job_attempts
wcp_ops.idempotency_records
wcp_ops.leases
wcp_ops.outbox_records
wcp_ops.event_records
\`\`\`

### 10.1 \`wcp_ops.jobs\`

Recovered columns:

\`\`\`text
job_id UUID NOT NULL PK
tenant_id UUID NULL
job_type TEXT NOT NULL
action_id TEXT NOT NULL
resource_id UUID NULL
requested_by_user_id UUID NULL
job_state TEXT NOT NULL
outcome_class TEXT NULL
error_code TEXT NULL
attempt_count INTEGER NOT NULL
max_attempts INTEGER NOT NULL
available_at TIMESTAMPTZ NOT NULL
started_at TIMESTAMPTZ NULL
completed_at TIMESTAMPTZ NULL
timeout_at TIMESTAMPTZ NULL
correlation_id UUID NOT NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered CHECK rules:

\`\`\`text
attempt_count >= 0
max_attempts >= 1
attempt_count <= max_attempts
revision >= 1
\`\`\`

Recovered outcome classes include:

\`\`\`text
SUCCEEDED
FAILED
PARTIALLY_SUCCEEDED
UNKNOWN_OUTCOME
TIMED_OUT
\`\`\`

The complete exact state registry and all FKs/indexes for \`jobs\` have not yet been recovered. None are invented.

### 10.2 \`wcp_ops.idempotency_records\`

Recovered columns:

\`\`\`text
idempotency_id UUID NOT NULL PK
scope_kind TEXT NOT NULL
account_id UUID NULL
tenant_id UUID NULL
operation_namespace TEXT NOT NULL
idempotency_key TEXT NOT NULL
request_fingerprint BYTEA NOT NULL
idempotency_state TEXT NOT NULL
result_reference UUID NULL
created_at TIMESTAMPTZ NOT NULL
expires_at TIMESTAMPTZ NOT NULL
revision BIGINT NOT NULL
\`\`\`

Recovered scope classes:

\`\`\`text
GLOBAL
ACCOUNT
TENANT
\`\`\`

Recovered constraints:

- scope-specific unique keys;
- \`expires_at > created_at\`;
- same key with a fingerprint mismatch is a conflict.

Recovered semantic relationship is consistent with current API:

\`\`\`text
same key + same semantic request
→ same logical result

same key + different semantic request
→ IDEMPOTENCY_CONFLICT
\`\`\`

The exact SQL form of the scope-specific unique constraints is not reconstructed here.

### 10.3 \`wcp_ops.leases\`

Recovered columns:

\`\`\`text
lease_id UUID NOT NULL PK
target_kind TEXT NOT NULL
job_id UUID NULL
resource_id UUID NULL
owner_worker_id TEXT NOT NULL
fencing_token BIGINT NOT NULL
acquired_at TIMESTAMPTZ NOT NULL
expires_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered constraints:

\`\`\`text
exactly one of job_id / resource_id is non-null
fencing_token >= 1
expires_at > acquired_at
\`\`\`

Recovered concurrency semantics:

- only one active lease per target;
- fencing tokens increase monotonically for successive owners;
- stale workers must not overwrite newer state;
- lease expiry/reclaim is required.

The exact implementation of “one active lease” is not reconstructed.

### 10.4 \`wcp_ops.outbox_records\`

Recovered columns:

\`\`\`text
outbox_id UUID NOT NULL PK
event_id TEXT NOT NULL
tenant_id UUID NULL
aggregate_type TEXT NOT NULL
aggregate_id UUID NOT NULL
schema_version INTEGER NOT NULL
payload JSONB NOT NULL
delivery_state TEXT NOT NULL
attempt_count INTEGER NOT NULL
available_at TIMESTAMPTZ NOT NULL
published_at TIMESTAMPTZ NULL
last_error_code TEXT NULL
correlation_id UUID NOT NULL
created_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered checks:

\`\`\`text
schema_version >= 1
attempt_count >= 0
\`\`\`

Recovered durability semantics:

\`\`\`text
source mutation + outbox persistence = atomic
delivery = AT LEAST ONCE
consumers = duplicate-safe
\`\`\`

### 10.5 \`wcp_ops.event_records\`

Recovered columns:

\`\`\`text
event_record_id UUID NOT NULL PK
event_id TEXT NOT NULL
event_version INTEGER NOT NULL
tenant_id UUID NULL
resource_id UUID NULL
payload JSONB NOT NULL
occurred_at TIMESTAMPTZ NOT NULL
correlation_id UUID NOT NULL
source_outbox_id UUID NULL
\`\`\`

Recovered uniqueness:

\`\`\`text
source_outbox_id UNIQUE when present
\`\`\`

Recovered identity semantics:

\`\`\`text
event_id = semantic type
event_record_id = occurrence
correlation_id != event_record_id
\`\`\`

Recovered durability semantics:

- durable occurrence IDs are UUIDv7/UUID;
- authoritative instants use \`TIMESTAMPTZ\`;
- event payloads may be schema-versioned JSONB;
- required event/outbox persistence is atomic with the source PostgreSQL mutation;
- mandatory audit/outbox persistence failure causes rollback for pure control-plane DB mutation;
- transactional outbox delivery is \`AT LEAST ONCE\`;
- consumers must be duplicate-safe;
- external effects require durable intent, correlation, result/audit evidence, and reconciliation when the final result is uncertain.

### 10.6 \`wcp_ops.job_attempts\`

The table name is confirmed as part of the final artifact, but a sufficiently complete exact column/constraint fragment has not yet been recovered.

No definition is reconstructed here.

## 11. Tenant-integrity recovery

Recovered final-artifact rule:

\`\`\`text
every tenant-scoped parent exposes UNIQUE (tenant_id, entity_id)
children use composite (tenant_id, parent_id) foreign keys
cross-tenant corruption is rejected by PostgreSQL
\`\`\`

This is a structural invariant, not permission logic.

## 12. Delete-semantics recovery

Recovered final-artifact rules:

\`\`\`text
default ON DELETE = RESTRICT
no universal deleted_at
tenant ownership columns = immutable
audit evidence = never cascade-deleted
\`\`\`

Any exceptions must be recovered from the exact owner artifact; this evidence file does not invent them.

## 13. Recovered execution/durability invariants

Recovered exact or owner-consistent invariants include:

\`\`\`text
timeout != definite failure
retry != reconciliation
rollback != compensation
execution success != verified success
\`\`\`

Recovered external-effect pattern:

\`\`\`text
durable intent
→ dispatch
→ observe
→ durable result
\`\`\`

When final state cannot be proven:

\`\`\`text
UNKNOWN_OUTCOME
→ observe
→ reconcile
→ final durable outcome
\`\`\`

Blind destructive retry after uncertain dispatch is forbidden.

## 14. What is still missing from the recovered final artifact

The following are known to have existed in the complete publication, but are not sufficiently recoverable here to republish canonically:

- complete definitions of every CORE table;
- complete IAM schema;
- complete INFRA schema;
- full HOSTING columns/constraints;
- full COMMERCIAL schema;
- full AUDIT schema;
- full BACKUP schema;
- complete \`job_attempts\` definition;
- all FKs and delete exceptions;
- all CHECK/UNIQUE/nullability clauses;
- every approved index;
- complete transaction-boundary section;
- complete retention/partitioning section;
- full backup/restore and migration-boundary wording;
- all final publication-status/footer sections;
- exact byte ordering, spacing and all prose of the original artifact.

Therefore:

\`\`\`text
EXACT ORIGINAL ARTIFACT = NOT FULLY RECOVERED
CANONICAL IMPORT = FORBIDDEN
ISSUE #6 = OPEN
\`\`\`

## 15. Owner-assisted republication rule

Database may use this file only to locate/recover its own prior final source.

If Database republishes the exact original artifact:

\`\`\`text
CONTENT CHANGE = NONE
\`\`\`

is appropriate only when the owner can establish that the content is the original final content.

If Database must recreate or amend missing material:

- it must declare a real content delta;
- it must state version impact;
- it must cross-check API/IPC/Security/QA dependencies;
- it must not claim byte-identical republication.

## 16. Gate state

\`\`\`text
DB MODEL VERSION = v1.0
OWNER FINAL METADATA = CONFIRMED
FINAL BODY EXISTED IN PRIOR CONVERSATION = CONFIRMED
FULL BODY RECOVERED NOW = NO
GITHUB CANONICAL IMPORT = NO
ISSUE #6 = OPEN

SC-M01 EVIDENCE = 5 / 8
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
\`\`\`
