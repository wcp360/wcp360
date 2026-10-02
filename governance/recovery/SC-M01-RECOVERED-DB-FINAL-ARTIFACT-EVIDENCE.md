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


### 6.2 Exact recovered \`wcp_core.accounts\` fragment

\`\`\`text
account_id UUID NOT NULL PK
display_name TEXT NOT NULL
lifecycle_state TEXT NOT NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
CHECK (revision >= 1)
\`\`\`

Recovered delete rule:

\`\`\`text
Account deletion = RESTRICT while dependent Tenants remain
\`\`\`

No additional UNIQUE/FK beyond the PK was present in the recovered fragment.

### 6.3 Exact recovered \`wcp_core.customer_profiles\` fragment

\`\`\`text
account_id UUID NOT NULL PK
lifecycle_state TEXT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered PK/FK:

\`\`\`text
account_id
→ wcp_core.accounts(account_id)
ON DELETE CASCADE
\`\`\`

Recovered invariant:

\`\`\`text
customer profile != Tenant ownership
customer profile != authorization
\`\`\`

### 6.4 Exact recovered \`wcp_core.reseller_profiles\` fragment

\`\`\`text
account_id UUID NOT NULL PK
lifecycle_state TEXT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered PK/FK:

\`\`\`text
account_id
→ wcp_core.accounts(account_id)
ON DELETE CASCADE
\`\`\`

Recovered invariants:

\`\`\`text
reseller profile != Tenant ownership
reseller profile alone != authorization
\`\`\`

### 6.5 Exact recovered \`wcp_core.organizations\` fragment

\`\`\`text
organization_id UUID NOT NULL PK
name TEXT NOT NULL
lifecycle_state TEXT NOT NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
CHECK (revision >= 1)
\`\`\`

Recovered invariants:

\`\`\`text
Organization = grouping / membership
Organization != Tenant security boundary
\`\`\`

Default delete behavior remains \`ON DELETE RESTRICT\`.

### 6.6 Exact recovered \`wcp_core.account_organization_memberships\` fragment

\`\`\`text
account_id UUID NOT NULL
organization_id UUID NOT NULL
membership_state TEXT NOT NULL
created_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered PK:

\`\`\`text
PRIMARY KEY (account_id, organization_id)
\`\`\`

Recovered FKs:

\`\`\`text
account_id → wcp_core.accounts(account_id)
organization_id → wcp_core.organizations(organization_id)
\`\`\`

Recovered relationship delete semantics:

- membership row may cascade when either parent is removed;
- removing the membership does not delete the Account;
- removing the membership does not delete a Tenant.

### 6.7 Exact recovered \`wcp_core.organization_tenant_memberships\` fragment

\`\`\`text
organization_id UUID NOT NULL
tenant_id UUID NOT NULL
membership_state TEXT NOT NULL
created_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered PK:

\`\`\`text
PRIMARY KEY (organization_id, tenant_id)
\`\`\`

Recovered FKs:

\`\`\`text
organization_id → organizations
tenant_id → tenants
\`\`\`

Recovered invariant:

\`\`\`text
Organization ↔ Tenant = optional N:M grouping / membership
membership != ownership
\`\`\`

Recovered delete semantics:

- relation row may cascade with either parent;
- relation deletion does not mutate Tenant ownership.

### 6.8 Exact recovered \`wcp_core.hosting_accounts\` fragment

\`\`\`text
hosting_account_id UUID NOT NULL PK
tenant_id UUID NOT NULL
system_key TEXT NOT NULL
display_name TEXT NOT NULL
lifecycle_state TEXT NOT NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered FK:

\`\`\`text
tenant_id → tenants(tenant_id) ON DELETE RESTRICT
\`\`\`

Recovered UNIQUE constraints:

\`\`\`text
UNIQUE (tenant_id, system_key)
UNIQUE (tenant_id, hosting_account_id)
\`\`\`

Recovered invariants:

\`\`\`text
Hosting Account → exactly 1 Tenant
ownership reassignment = forbidden / immutable
revision >= 1
successful mutations increment revision
\`\`\`

### 6.9 Exact recovered \`wcp_core.reseller_tenant_delegations\` fragment

\`\`\`text
delegation_id UUID NOT NULL PK
reseller_account_id UUID NOT NULL
tenant_id UUID NOT NULL
granted_by_account_id UUID NOT NULL
delegation_state TEXT NOT NULL
valid_from TIMESTAMPTZ NOT NULL
valid_until TIMESTAMPTZ NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered FKs:

\`\`\`text
reseller_account_id
→ reseller_profiles(account_id)
ON DELETE RESTRICT

tenant_id
→ tenants(tenant_id)
ON DELETE RESTRICT

(tenant_id, granted_by_account_id)
→ tenants(tenant_id, account_id)
\`\`\`

Recovered CHECK rules:

\`\`\`text
valid_until IS NULL OR valid_until > valid_from
revision >= 1
\`\`\`

Recovered uniqueness/semantics:

\`\`\`text
only one active logical delegation
for (reseller_account_id, tenant_id)
unless future approved independent scopes exist

delegation != ownership
\`\`\`

Delegation deletion/revocation removes delegated access only and does not transfer/delete Tenant ownership or Hosting Accounts.

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


### 9.1 Exact recovered \`wcp_hosting.domains\`

\`\`\`text
domain_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NOT NULL
canonical_name TEXT NOT NULL UNIQUE
\`\`\`

Recovered tenant-aware FK:

\`\`\`text
(tenant_id, resource_id)
→ wcp_infra.managed_resources(tenant_id, resource_id)
\`\`\`

Recovered unique:

\`\`\`text
UNIQUE (tenant_id, domain_id)
\`\`\`

Lifecycle/revision/timestamps are not duplicated locally; lifecycle authority is \`managed_resources\`. Default delete is RESTRICT.

### 9.2 Exact recovered \`wcp_hosting.websites\`

\`\`\`text
website_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NOT NULL
hosting_account_id UUID NOT NULL
primary_domain_id UUID NULL
display_name TEXT NOT NULL
runtime_profile_id TEXT NOT NULL
\`\`\`

Recovered tenant-aware relationships include composite FKs to:

- \`managed_resources\`;
- \`hosting_accounts\`;
- \`domains\`.

No recovered local lifecycle/revision/timestamps: lifecycle authority remains \`managed_resources\`. No additional recovered UNIQUE/CHECK beyond PK/resource uniqueness and FK/nullability.

### 9.3 Exact recovered \`wcp_hosting.dns_zones\`

\`\`\`text
zone_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NOT NULL
domain_id UUID NOT NULL
canonical_name TEXT NOT NULL UNIQUE
\`\`\`

Recovered FKs:

\`\`\`text
(tenant_id, resource_id)
→ managed_resources(tenant_id, resource_id)

(tenant_id, domain_id)
→ domains(tenant_id, domain_id)
\`\`\`

Recovered unique:

\`\`\`text
UNIQUE (tenant_id, zone_id)
\`\`\`

Lifecycle authority remains \`managed_resources\`; no DNS-zone cascade was specified.

### 9.4 Exact recovered \`wcp_hosting.dns_records\`

\`\`\`text
record_id UUID NOT NULL PK
tenant_id UUID NOT NULL
zone_id UUID NOT NULL
record_type TEXT NOT NULL
owner_name TEXT NOT NULL
record_value TEXT NOT NULL
ttl_seconds INTEGER NOT NULL
priority INTEGER NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered FK:

\`\`\`text
(tenant_id, zone_id)
→ dns_zones(tenant_id, zone_id)
\`\`\`

Recovered checks:

\`\`\`text
ttl_seconds >= 0
priority IS NULL OR priority >= 0
\`\`\`

\`record_type\` uses the typed DNS-type registry. Default delete is RESTRICT.

### 9.5 Exact recovered \`wcp_hosting.mail_domains\`

\`\`\`text
mail_domain_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NOT NULL
domain_id UUID NOT NULL
canonical_name TEXT NOT NULL UNIQUE
\`\`\`

Recovered FKs:

\`\`\`text
(tenant_id, resource_id) → wcp_infra.managed_resources
(tenant_id, domain_id) → wcp_hosting.domains
\`\`\`

Recovered unique:

\`\`\`text
UNIQUE (tenant_id, mail_domain_id)
\`\`\`

Lifecycle authority remains \`managed_resources\`; timestamps/revision are not duplicated locally; default delete is RESTRICT.

### 9.6 Exact recovered \`wcp_hosting.mailboxes\`

\`\`\`text
mailbox_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NOT NULL
mail_domain_id UUID NOT NULL
local_part TEXT NOT NULL
auth_kind TEXT NOT NULL
credential_ref TEXT NULL
revision BIGINT NOT NULL
\`\`\`

Recovered FKs:

\`\`\`text
(tenant_id, resource_id) → managed_resources
(tenant_id, mail_domain_id) → mail_domains
\`\`\`

Recovered check:

\`\`\`text
auth_kind = 'LOCAL' → credential_ref IS NOT NULL
\`\`\`

Recovered secret invariant:

\`\`\`text
raw mailbox password = forbidden
\`\`\`

### 9.7 Exact recovered \`wcp_hosting.certificates\`

\`\`\`text
certificate_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NULL
server_id UUID NULL
common_name TEXT NOT NULL
serial_number TEXT NOT NULL
issuer_name TEXT NOT NULL
not_before TIMESTAMPTZ NOT NULL
not_after TIMESTAMPTZ NOT NULL
public_certificate TEXT NOT NULL
private_key_ref TEXT NOT NULL
revision BIGINT NOT NULL
\`\`\`

Recovered check:

\`\`\`text
not_after > not_before
\`\`\`

Recovered secret invariant:

\`\`\`text
private-key material never inline
\`\`\`

### 9.8 Exact recovered \`wcp_hosting.hosted_db_engine_instances\`

\`\`\`text
engine_instance_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
server_id UUID NOT NULL
engine_type TEXT NOT NULL
engine_version TEXT NOT NULL
service_id TEXT NOT NULL
revision BIGINT NOT NULL
\`\`\`

Recovered check:

\`\`\`text
engine_type IN ('POSTGRESQL', 'MARIADB')
\`\`\`

Recovered FKs:

\`\`\`text
resource_id → wcp_infra.managed_resources
server_id → wcp_infra.servers
\`\`\`

Recovered invariant:

\`\`\`text
hosted database engine != control-plane PostgreSQL
\`\`\`

### 9.9 Exact recovered \`wcp_hosting.hosted_databases\`

\`\`\`text
hosted_database_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NOT NULL
engine_instance_id UUID NOT NULL
database_name TEXT NOT NULL
charset_name TEXT NULL
collation_name TEXT NULL
revision BIGINT NOT NULL
\`\`\`

Recovered FKs:

\`\`\`text
(tenant_id, resource_id) → managed_resources
engine_instance_id → hosted_db_engine_instances
\`\`\`

Recovered unique constraints:

\`\`\`text
UNIQUE (engine_instance_id, database_name)
UNIQUE (tenant_id, hosted_database_id)
\`\`\`

Recovered invariant:

\`\`\`text
tenant / application database content is not mirrored into control-plane PostgreSQL
\`\`\`

### 9.10 Exact recovered \`wcp_hosting.hosted_db_principals\`

\`\`\`text
principal_id UUID NOT NULL PK
tenant_id UUID NOT NULL
hosted_database_id UUID NOT NULL
principal_name TEXT NOT NULL
privilege_profile TEXT NOT NULL
credential_ref TEXT NOT NULL
revision BIGINT NOT NULL
\`\`\`

Recovered FK:

\`\`\`text
(tenant_id, hosted_database_id)
→ hosted_databases(tenant_id, hosted_database_id)
\`\`\`

Recovered unique:

\`\`\`text
UNIQUE (hosted_database_id, principal_name)
\`\`\`

Recovered security invariants:

\`\`\`text
raw DB password = forbidden
raw SQL privilege expression = forbidden
credential_ref = reference, not plaintext secret
privilege_profile = typed / bounded
\`\`\`

Lifecycle authority remains \`wcp_infra.managed_resources\`.

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


## 14. COMMERCIAL schema — additional recovered fragments

Recovered final-artifact heading:

\`\`\`text
# 9. COMMERCIAL schema
\`\`\`

Recovered canonical tables:

\`\`\`text
wcp_commercial.plans
wcp_commercial.plan_entitlements
wcp_commercial.subscriptions
wcp_commercial.subscription_entitlements
\`\`\`

### 14.1 \`wcp_commercial.plans\`

Recovered columns:

\`\`\`text
plan_id UUID NOT NULL PK
plan_key TEXT NOT NULL UNIQUE
display_name TEXT NOT NULL
lifecycle_state TEXT NOT NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
\`\`\`

### 14.2 \`wcp_commercial.plan_entitlements\`

Recovered columns:

\`\`\`text
plan_id UUID NOT NULL
entitlement_id TEXT NOT NULL
value_payload JSONB NOT NULL
created_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered PK:

\`\`\`text
PRIMARY KEY (plan_id, entitlement_id)
\`\`\`

### 14.3 \`wcp_commercial.subscriptions\`

Recovered columns:

\`\`\`text
subscription_id UUID NOT NULL PK
account_id UUID NOT NULL
tenant_id UUID NULL
plan_id UUID NOT NULL
subscription_state TEXT NOT NULL
starts_at TIMESTAMPTZ NOT NULL
ends_at TIMESTAMPTZ NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered tenant-scope relationship:

\`\`\`text
when tenant_id is set:
(tenant_id, account_id)
→ tenants(tenant_id, account_id)
\`\`\`

Recovered check:

\`\`\`text
ends_at IS NULL OR ends_at > starts_at
\`\`\`

### 14.4 \`wcp_commercial.subscription_entitlements\`

Recovered columns:

\`\`\`text
subscription_id UUID NOT NULL
entitlement_id TEXT NOT NULL
override_payload JSONB NULL
state TEXT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered PK:

\`\`\`text
PRIMARY KEY (subscription_id, entitlement_id)
\`\`\`

Recovered invariant:

\`\`\`text
entitlement != permission
\`\`\`

Unrecovered FKs/delete rules are not inferred.

## 15. AUDIT schema — additional recovered fragments

Recovered final-artifact headings include:

\`\`\`text
## 11. AUDIT schema
## wcp_audit.audit_records
## 21. Audit durability dependencies
## 23. Delete semantics
## 24. Approved correctness indexes
\`\`\`

### 15.1 \`wcp_audit.audit_records\`

Recovered columns:

\`\`\`text
audit_record_id UUID NOT NULL PK
audit_event_id TEXT NOT NULL
event_version INTEGER NOT NULL
occurred_at TIMESTAMPTZ NOT NULL
actor_type TEXT NOT NULL
actor_user_id UUID NULL
tenant_id UUID NULL
resource_id UUID NULL
action_id TEXT NULL
request_id UUID NULL
job_id UUID NULL
workflow_id UUID NULL
trace_id UUID NULL
outcome_class TEXT NULL
correlation_id UUID NOT NULL
security_classification TEXT NULL
safe_payload JSONB NOT NULL
persisted_at TIMESTAMPTZ NOT NULL
\`\`\`

Recovered identity invariant:

\`\`\`text
audit_event_id = semantic audit TYPE
audit_record_id = immutable persisted OCCURRENCE
\`\`\`

Recovered durability rule:

- mandatory audit is not best-effort;
- for pure control-plane PostgreSQL mutation, required audit/outbox durability is part of the durable transaction contract;
- audit evidence is never cascade-deleted;
- external-effect uncertainty uses durable intent/result/reconciliation rather than fictional rollback.

The exact retention/index/FK clauses remain incomplete.

## 16. BACKUP schema — additional recovered fragments

Recovered final-artifact heading:

\`\`\`text
# 12. BACKUP schema
\`\`\`

Recovered tables:

\`\`\`text
wcp_backup.backups
wcp_backup.backup_objects
wcp_backup.restore_operations
\`\`\`

### 16.1 \`wcp_backup.backups\`

Recovered columns/attributes:

\`\`\`text
backup_id UUID NOT NULL PK
backup_class TEXT NOT NULL
tenant_id UUID NULL
resource_id UUID NULL
backup_state TEXT NOT NULL
storage_ref TEXT NOT NULL
encryption_key_ref TEXT NULL
checksum / checksums
size
started_at TIMESTAMPTZ
completed_at TIMESTAMPTZ NULL
retention_until TIMESTAMPTZ
revision BIGINT
\`\`\`

Recovered classes:

\`\`\`text
CONTROL_PLANE
HOSTED_DATABASE
WORKLOAD
\`\`\`

Recovered security invariant:

\`\`\`text
raw encryption keys are forbidden in the row
\`\`\`

The exact checksum/size column names/types and all nullability/check clauses are not sufficiently recoverable and are therefore not reconstructed.

### 16.2 \`wcp_backup.backup_objects\`

Recovered structure includes:

\`\`\`text
backup_object_id
backup_id
object metadata
checksum metadata
size metadata
required metadata
\`\`\`

Recovered FK:

\`\`\`text
backup_id
→ backups(backup_id)
ON DELETE CASCADE
\`\`\`

This is a confirmed exception to the global RESTRICT default.

### 16.3 \`wcp_backup.restore_operations\`

Recovered structure includes:

\`\`\`text
restore_id
backup_id
tenant / target references
restore_state
requester
reconciliation_required
reconciliation / outcome / error fields
correlation fields
timestamps
revision
\`\`\`

The exact column names/types for fields beyond those explicitly shown above remain unrecovered and are not invented.

Recovered semantic boundary:

- backup metadata/control-plane state is distinct from backup payload contents;
- restore can require reconciliation;
- hosted database/workload backup content remains distinct from control-plane PostgreSQL authority.

## 17. Sections 13–18 — recovered integrity/transaction/concurrency structure

Recovered final-artifact section grouping:

\`\`\`text
13 — Tenant integrity
14 — Ownership reassignment
15 — RLS / tenant authorization boundary
16 — Transaction boundaries
17 — Concurrency / revisions
18 — Job claiming with leases / fencing
\`\`\`

### 17.1 Tenant integrity

Recovered rule:

\`\`\`text
every tenant-scoped parent exposes UNIQUE (tenant_id, entity_id)
children use composite (tenant_id, parent_id) foreign keys
cross-tenant corruption is rejected by PostgreSQL
\`\`\`

### 17.2 Ownership reassignment

Recovered rule:

\`\`\`text
DIRECT TENANT REASSIGNMENT = FORBIDDEN
\`\`\`

Ownership changes require an explicit approved lifecycle/migration contract rather than direct FK reassignment.

### 17.3 RLS / authorization

Recovered rule:

\`\`\`text
RLS is not the primary Tenant authorization mechanism
\`\`\`

Database tenant integrity supports Security isolation but does not replace application authorization.

### 17.4 Transaction boundaries

Recovered invariants:

\`\`\`text
DB COMMIT != EXTERNAL SUCCESS
RETRY != RECONCILIATION
ROLLBACK != COMPENSATION
\`\`\`

External effects are not modeled as if PostgreSQL could atomically roll them back.

### 17.5 Concurrency / revisions

Recovered rule:

\`\`\`text
revision BIGINT NOT NULL
stale expected revision → fail
\`\`\`

No lost-update overwrite is permitted for revision-protected mutable state.

### 17.6 Job claiming

Recovered model uses leases/fencing for job/executor ownership.

Recovered lease rules are preserved in section 10.3 of this evidence file.

## 18. Sections 21–24 — durability/reconciliation/delete/index structure

Recovered final-artifact headings/topics:

\`\`\`text
21 — Audit durability dependencies
22 — Reconciliation state
23 — Delete semantics
24 — Approved correctness indexes
\`\`\`

### 18.1 Audit durability

Recovered rule:

- mandatory audit/outbox persistence required by a pure control-plane DB mutation participates in that durable transaction;
- mandatory durability failure causes rollback of the DB mutation;
- external effect already dispatched is handled via outcome/reconciliation, not database rollback fiction.

### 18.2 Reconciliation

Recovered model requires persistent reconciliation state for uncertain external effects.

\`\`\`text
UNKNOWN_OUTCOME
→ observe
→ reconcile
→ durable final outcome
\`\`\`

The exact table/column representation of reconciliation state is still incomplete in recovery.

### 18.3 Delete semantics

Recovered global rule:

\`\`\`text
ON DELETE RESTRICT = default
\`\`\`

Recovered exceptions are relationship-specific only.

Confirmed recovered exception:

\`\`\`text
wcp_backup.backup_objects.backup_id
→ backups
ON DELETE CASCADE
\`\`\`

Recovered invariant:

\`\`\`text
audit evidence is never cascade-deleted
\`\`\`

### 18.4 Approved correctness indexes

Recovered index classes:

- every PK / UNIQUE required by the physical contract;
- indexes supporting FK and tenant-parent lookups;
- scoped idempotency uniqueness;
- single-active-lease enforcement/indexing;
- single-active-reseller-delegation enforcement/indexing.

Exact index names and every column ordering are **not** recoverable enough to republish and must be obtained from the owner source.


## 19. What is still missing from the recovered final artifact

The following are known to have existed in the complete publication, but are not sufficiently recoverable here to republish canonically:

- unrecovered details/indexes/delete exceptions within CORE;
- complete IAM schema;
- complete INFRA schema;
- unrecovered details/indexes/delete exceptions within HOSTING;
- unrecovered details/FKs/checks/indexes within COMMERCIAL;
- unrecovered details/FKs/retention/indexes within AUDIT;
- unrecovered exact field names/types/checks within BACKUP;
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

## 20. Owner-assisted republication rule

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

## 21. Gate state

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
