# WCP360 — Database Model v1.0 — Canonical Implementation Contract

## Publication metadata

OWNER = WCP360 — DATABASE / DATA MODEL  
ARTIFACT = specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md  
VERSION = v1.0  
STATUS = FINAL APPROVED CONTENT  
PUBLICATION TYPE = OWNER RECONSTRUCTION / AMENDMENT  
CONTENT CHANGE = REAL DELTA — DB-MV01 / DB-MV02 TARGETED CONFORMANCE CORRECTION  
VERSION IMPACT = NONE — v1.0 RETAINED; this pre-implementation correction aligns the physical contract with already-approved API v1.0 and Security v1.0 semantics and does not change the product architecture or public API/IPC contract.  
HISTORICAL CONTENT INCLUDED = NO  
READY FOR SOURCE CODE CONSUMPTION = YES, only if MASTER separately authorizes SC-M01.  
CODE-BASELINE = NONE  
SC-M01 RESUME AUTHORIZATION = NOT GRANTED BY THIS DOCUMENT  

### Provenance

1. Final owner metadata and a complete owner publication were previously confirmed in the DATABASE / DATA MODEL workstream.
2. Historical GitHub recovery evidence preserves substantial verbatim fragments but is explicitly incomplete:
   - governance/recovery/SC-M01-RECOVERED-DB-FINAL-ARTIFACT-EVIDENCE.md
   - governance/evidence/SC-M01-DB-RECOVERED-TABLE-FRAGMENTS-2026-10-27.md
   - source recovery blob 7e06ce38c2dd742cfec9669884d7f13b4b611f39
3. The non-canonical review worksheet governance/proposals/SC-M01-DB-36-TABLE-PHYSICAL-OWNER-MATERIALIZATION-CANDIDATE-v0.1.md was used only to identify unresolved DB-R01..DB-R15 cells. It is not authority.
4. This publication is therefore not claimed as byte-identical historical republication. DATABASE explicitly owns and approves the reconstruction/amendment choices below.

No executable DDL or migration is contained here.

## Hard invariants

- account_id != organization_id != tenant_id
- Tenant = security/data isolation boundary.
- ownership != membership != delegation.
- Reseller delegation != Tenant ownership.
- CONTROL-PLANE POSTGRESQL != HOSTED DB ENGINES != TENANT / APPLICATION DB CONTENT.
- PostgreSQL = authoritative logical control-plane state.
- /var/lib/wcp360 = materialized / node-local / reconstructable.
- /run/wcp360 = ephemeral runtime.
- /var/cache/wcp360 = reconstructable cache.
- Linux / services / filesystem = observed physical state.
- audit_event_id = semantic audit event TYPE.
- audit_record_id = immutable audit OCCURRENCE.
- correlation_id is correlation only, never authorization.
- retry != reconciliation; rollback != compensation; DB commit != external verified success; restore != replay.

## DB-R01..DB-R15 owner decision matrix

| ID | Topic | Decision | Final owner disposition |
|---|---|---|---|
| DB-R01 | CORE completeness | ACCEPT | All CORE tables, ownership/membership/delegation constraints, composite owner keys and delete behavior are explicitly materialized. |
| DB-R02 | IAM completeness | AMEND | Full IAM table set, scope CHECKs, delegation binding, nullability and retention classes are now explicit. |
| DB-R03 | INFRA completeness | ACCEPT | Servers, capabilities, managed resources, desired/observed state and quotas are explicit with scope/tenant constraints. |
| DB-R04 | HOSTING completeness | ACCEPT | Ten hosted-resource tables are explicit with tenant-aware composite FKs and ownership invariants. |
| DB-R05 | COMMERCIAL completeness | AMEND | Plan/subscription/entitlement persistence and delete/retention semantics are explicit; DB rows do not override Security authorization ceilings. |
| DB-R06 | OPS completeness | AMEND | job_attempts, NULL-safe idempotency uniqueness, leases/fencing, outbox, event and reconciliation contracts are completed. |
| DB-R07 | AUDIT completeness | AMEND | Immutable occurrence model, no-cascade evidence, exact correctness/query indexes and retention class are explicit. |
| DB-R08 | BACKUP completeness | AMEND | backup_objects and restore_operations now have exact physical definitions and restore/reconciliation rules. |
| DB-R09 | Global constraints | ACCEPT | UUIDv7, typed IDs, TIMESTAMPTZ, revision, JSONB boundaries, tenant composites, NOT NULL/CHECK and delete defaults are explicit. |
| DB-R10 | Approved index catalogue | AMEND | Deterministic constraint/FK naming and exact additional correctness/worker/query indexes are now canonical. |
| DB-R11 | Transaction boundaries | AMEND | Mutation/audit/outbox, idempotency claim/result, job enqueue, revision and reconciliation transactions are enumerated. |
| DB-R12 | Concurrency / locking | AMEND | Optimistic revision, SKIP LOCKED claim, one-row lease target, monotonic fencing and stale-result rejection are explicit. |
| DB-R13 | Retention / partitioning | AMEND | Per-class retention policy is explicit; v1.0 has no fixed numeric durations and no required partitioning. |
| DB-R14 | Backup / restore | AMEND | Three backup classes, restore boundary, re-observation and no-blind-replay rules are explicit. |
| DB-R15 | Migration boundary | AMEND | Model vs migration authority, compatibility classification, pre-upgrade backup, rollback/forward-fix and hosted-DB exclusion are explicit. |

All fifteen items are resolved by this owner publication. No unresolved DB-R item remains.

## PostgreSQL schema namespaces

- wcp_core — Account / Organization / Tenant / Hosting Account and structural relations.
- wcp_iam — identities, memberships, roles, permissions, delegations, sessions and API credential metadata.
- wcp_infra — servers, managed resources, desired/observed state and quotas.
- wcp_hosting — domains, websites, DNS, mail, certificates and hosted-database management metadata.
- wcp_commercial — plans, subscriptions and entitlements.
- wcp_ops — jobs, attempts, idempotency, leases, outbox, events, reconciliation and notifications.
- wcp_audit — immutable audit occurrences.
- wcp_backup — backup and restore metadata.

The public schema must not contain WCP360 business/control-plane tables.

## Global physical rules

### Identifiers
Durable entity and occurrence identifiers use PostgreSQL UUID with UUIDv7 generation. IDs are opaque to clients.

Typed namespaces use namespace-specific lexical contracts; there is no universal lowercase grammar:

- `permission_id`, `action_id`, `job_type`, `event_id`, `audit_event_id`, `ipc_operation_id` and `entitlement_id`: `TEXT`, maximum 128 characters, grammar `^[a-z][a-z0-9_.-]{1,127}$`.
- `error_code`: `TEXT`, maximum 128 characters, grammar `^[A-Z][A-Z0-9_]{1,127}$`, and membership in the canonical API v1.0 Error Registry persisted by `wcp_core.error_definitions`.
- `audit_record_id`: UUID occurrence identity, not a typed text identifier.
- `contract_id` is owner-defined contract metadata and is not `ipc_operation_id`; API contract identifiers such as `API-WCP-0101` retain their API-owned grammar and must never be normalized into a different typed namespace.

Exact canonical error codes are preserved byte-for-byte. Database/application code must not lowercase, slugify or otherwise rename them.

Distinct typed namespaces remain distinct: permission_id, action_id, job_type, event_id, audit_event_id, audit_record_id, ipc_operation_id, error_code and entitlement_id.

### Time, revision and state
All authoritative instants use TIMESTAMPTZ. Mutable aggregate roots that require conflict detection carry revision BIGINT NOT NULL CHECK revision >= 1. Canonical lifecycle/state fields use TEXT constrained to their owner registry; Source Code may not invent states.

### JSONB
JSONB is allowed only for schema-versioned typed documents such as desired/observed state, event payloads, entitlement values and safe metadata. It must not replace relational ownership, tenancy, membership, delegation, permissions, role assignments or secret references.

### Delete default
ON DELETE RESTRICT is the default for ownership/resource relations. Cascade is allowed only for inseparable dependent/profile/history rows explicitly identified below. No universal deleted_at exists.

### Tenant-aware FK rule
Every Tenant-scoped parent exposes UNIQUE(tenant_id, entity_id). A Tenant-scoped child references both child.tenant_id and child.parent_id to the parent composite key. Repository filtering alone is not sufficient for structural Tenant integrity.

### Ownership immutability
Direct updates that reparent tenants.account_id or a Tenant-scoped entity's tenant_id are unsupported in v1.0. A future transfer workflow requires a new explicit contract, audit, authorization, integrity checks and recovery semantics.

## Physical table catalogue

### wcp_core.accounts

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| account_id | UUID | NOT NULL | PK; UUIDv7 |
| display_name | TEXT | NOT NULL |  |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| revision | BIGINT | NOT NULL | CHECK revision >= 1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- Account is the commercial/administrative owner of Tenant; Account identity is not Tenant identity.
- ON DELETE RESTRICT while owned tenants or retained dependent business state exists.

### wcp_core.customer_profiles

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| account_id | UUID | NOT NULL | PK; FK -> wcp_core.accounts(account_id) ON DELETE CASCADE |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- Customer profile is classification only; it never grants Tenant authority.

### wcp_core.reseller_profiles

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| account_id | UUID | NOT NULL | PK; FK -> wcp_core.accounts(account_id) ON DELETE CASCADE |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- Reseller profile is capability/classification only; delegation is separate and never transfers ownership.

### wcp_core.organizations

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| organization_id | UUID | NOT NULL | PK; UUIDv7 |
| name | TEXT | NOT NULL |  |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| revision | BIGINT | NOT NULL | CHECK revision >= 1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- Organization is a grouping/membership construct, not a Tenant security boundary.

### wcp_core.account_organization_memberships

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| account_id | UUID | NOT NULL | PK part; FK -> accounts ON DELETE CASCADE |
| organization_id | UUID | NOT NULL | PK part; FK -> organizations ON DELETE CASCADE |
| membership_state | TEXT | NOT NULL | canonical state registry |
| created_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- PK(account_id, organization_id). Relation type = N:M membership, never ownership.

### wcp_core.tenants

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| tenant_id | UUID | NOT NULL | PK; UUIDv7 |
| account_id | UUID | NOT NULL | FK -> accounts(account_id) ON DELETE RESTRICT |
| name | TEXT | NOT NULL |  |
| tenant_key | TEXT | NOT NULL |  |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| revision | BIGINT | NOT NULL | CHECK revision >= 1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- UNIQUE(account_id, tenant_key).
- UNIQUE(tenant_id, account_id) supports composite ownership FKs.
- Every Tenant has exactly one owning Account. Direct account_id reassignment is forbidden in v1.0.

### wcp_core.organization_tenant_memberships

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| organization_id | UUID | NOT NULL | PK part; FK -> organizations ON DELETE CASCADE |
| tenant_id | UUID | NOT NULL | PK part; FK -> tenants ON DELETE CASCADE |
| membership_state | TEXT | NOT NULL | canonical state registry |
| created_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- PK(organization_id, tenant_id). Relation = optional grouping/membership, never ownership or authorization.

### wcp_core.hosting_accounts

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| hosting_account_id | UUID | NOT NULL | PK; UUIDv7 |
| tenant_id | UUID | NOT NULL | FK -> tenants(tenant_id) ON DELETE RESTRICT |
| system_key | TEXT | NOT NULL |  |
| display_name | TEXT | NOT NULL |  |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| revision | BIGINT | NOT NULL | CHECK revision >= 1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- UNIQUE(tenant_id, system_key).
- UNIQUE(tenant_id, hosting_account_id) supports tenant-aware child FKs.
- Every Hosting Account belongs to exactly one Tenant; direct tenant_id reassignment is forbidden.

### wcp_core.reseller_tenant_delegations

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| delegation_id | UUID | NOT NULL | PK; UUIDv7 |
| reseller_account_id | UUID | NOT NULL | FK -> reseller_profiles(account_id) ON DELETE RESTRICT |
| tenant_id | UUID | NOT NULL | FK -> tenants(tenant_id) ON DELETE RESTRICT |
| granted_by_account_id | UUID | NOT NULL | composite FK with tenant_id -> tenants(tenant_id, account_id) |
| delegation_state | TEXT | NOT NULL | canonical state registry |
| valid_from | TIMESTAMPTZ | NOT NULL |  |
| valid_until | TIMESTAMPTZ | NULL | CHECK NULL OR > valid_from |
| revision | BIGINT | NOT NULL | CHECK revision >= 1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- UNIQUE(reseller_account_id, tenant_id): one logical delegation row per reseller/Tenant pair; lifecycle changes are versioned on that row.
- Delegation never mutates tenants.account_id and never becomes ownership.

### wcp_core.error_definitions

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| error_code | TEXT | NOT NULL | PK; CHECK exact API v1.0 Error Registry membership |
| api_contract_version | TEXT | NOT NULL | CHECK = '1.0' |

Canonical allowed `error_code` values for DB Model v1.0:

```text
VALIDATION_FAILED
AUTHENTICATION_REQUIRED
AUTHORIZATION_DENIED
ENTITLEMENT_REQUIRED
POLICY_DENIED
MFA_REQUIRED
REAUTHENTICATION_REQUIRED
APPROVAL_REQUIRED
RESOURCE_NOT_FOUND
RESOURCE_CONFLICT
CONCURRENCY_CONFLICT
IDEMPOTENCY_CONFLICT
CANCELLATION_NOT_ALLOWED
CONTRACT_VERSION_UNSUPPORTED
RATE_LIMITED
OPERATION_TIMEOUT
DEPENDENCY_FAILURE
SERVICE_UNAVAILABLE
UNKNOWN_OUTCOME
INTERNAL_ERROR
IPC_OPERATION_NOT_ALLOWED
IPC_CALLER_UNTRUSTED
IPC_SCHEMA_INVALID
```

Owner rules:
- This table is a physical mirror of the approved API v1.0 Error Registry, not a Database-owned replacement registry.
- An unknown uppercase token is invalid even if it matches the lexical grammar.
- Child `error_code` / `last_error_code` columns reference this primary key with `ON DELETE RESTRICT`.
- API/Security contract identifiers are never inserted here unless they are canonical API `error_code` values.

### wcp_iam.users

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| user_id | UUID | NOT NULL | PK; UUIDv7 |
| primary_email_normalized | TEXT | NOT NULL | UNIQUE |
| display_name | TEXT | NOT NULL |  |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| revision | BIGINT | NOT NULL | CHECK revision >= 1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- No plaintext authentication secret.

### wcp_iam.identities

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| identity_id | UUID | NOT NULL | PK; UUIDv7 |
| user_id | UUID | NOT NULL | FK -> users ON DELETE RESTRICT |
| provider_id | TEXT | NOT NULL |  |
| provider_subject | TEXT | NOT NULL |  |
| identity_state | TEXT | NOT NULL | canonical state registry |
| verified_at | TIMESTAMPTZ | NULL |  |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- UNIQUE(provider_id, provider_subject).

### wcp_iam.tenant_memberships

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| tenant_id | UUID | NOT NULL | PK part; FK -> tenants ON DELETE CASCADE |
| user_id | UUID | NOT NULL | PK part; FK -> users ON DELETE RESTRICT |
| membership_state | TEXT | NOT NULL | canonical state registry |
| created_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- PK(tenant_id,user_id). Membership != permission != ownership.

### wcp_iam.permission_definitions

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| permission_id | TEXT | NOT NULL | PK; typed identifier <=128 chars |
| description | TEXT | NOT NULL |  |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| registry_version | INTEGER | NOT NULL | CHECK >= 1 |

Owner rules:
- permission_id namespace is distinct from action_id/job_type/event_id/audit_event_id/ipc_operation_id/error_code.

### wcp_iam.roles

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| role_id | UUID | NOT NULL | PK; UUIDv7 |
| role_key | TEXT | NOT NULL | UNIQUE |
| display_name | TEXT | NOT NULL |  |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| revision | BIGINT | NOT NULL | CHECK revision >= 1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

### wcp_iam.role_permission_bindings

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| role_id | UUID | NOT NULL | PK part; FK -> roles ON DELETE CASCADE |
| permission_id | TEXT | NOT NULL | PK part; FK -> permission_definitions ON DELETE RESTRICT |
| effect | TEXT | NOT NULL | CHECK IN ('ALLOW','DENY') |
| created_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- PK(role_id,permission_id).

### wcp_iam.role_assignments

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| assignment_id | UUID | NOT NULL | PK; UUIDv7 |
| user_id | UUID | NOT NULL | FK -> users ON DELETE RESTRICT |
| role_id | UUID | NOT NULL | FK -> roles ON DELETE RESTRICT |
| scope_kind | TEXT | NOT NULL | CHECK GLOBAL/ACCOUNT/TENANT |
| account_id | UUID | NULL | FK -> accounts ON DELETE RESTRICT when ACCOUNT |
| tenant_id | UUID | NULL | FK -> tenants ON DELETE RESTRICT when TENANT |
| source_kind | TEXT | NOT NULL | CHECK DIRECT/DELEGATED/SYSTEM |
| delegation_id | UUID | NULL | FK -> reseller_tenant_delegations when DELEGATED |
| assignment_state | TEXT | NOT NULL | canonical state registry |
| valid_from | TIMESTAMPTZ | NOT NULL |  |
| valid_until | TIMESTAMPTZ | NULL | CHECK NULL OR > valid_from |
| revision | BIGINT | NOT NULL | CHECK revision >= 1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- GLOBAL => account_id/tenant_id NULL; ACCOUNT => account_id NOT NULL and tenant_id NULL; TENANT => tenant_id NOT NULL and account_id NULL.
- DELEGATED => delegation_id NOT NULL and scope_kind='TENANT'; delegated assignment tenant must equal delegation tenant.

### wcp_iam.delegation_permissions

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| delegation_id | UUID | NOT NULL | PK part; FK -> reseller_tenant_delegations ON DELETE CASCADE |
| permission_id | TEXT | NOT NULL | PK part; FK -> permission_definitions ON DELETE RESTRICT |
| created_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- PK(delegation_id,permission_id). This limits delegation capability and does not create ownership.

### wcp_iam.entitlement_definitions

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| entitlement_id | TEXT | NOT NULL | PK; typed identifier |
| description | TEXT | NOT NULL |  |
| value_schema_id | TEXT | NOT NULL |  |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| registry_version | INTEGER | NOT NULL | CHECK >= 1 |

Owner rules:
- entitlement_id != permission_id. Database persistence does not grant authorization by itself.

### wcp_iam.user_sessions

This relation is the canonical server-side session namespace for the WCP-USER surface.

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| session_id | UUID | NOT NULL | PK; UUIDv7; opaque bearer lookup identifier |
| user_id | UUID | NOT NULL | FK -> users ON DELETE RESTRICT |
| surface_claim | TEXT | NOT NULL | CHECK = 'WCP-USER' |
| secret_verifier | BYTEA | NOT NULL | hashed/verifier material only |
| session_state | TEXT | NOT NULL | canonical state registry |
| auth_strength | INTEGER | NOT NULL | CHECK >= 0 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| last_seen_at | TIMESTAMPTZ | NULL |  |
| expires_at | TIMESTAMPTZ | NOT NULL | CHECK > created_at |
| revoked_at | TIMESTAMPTZ | NULL |  |

Owner rules:
- Server-side namespace = `wcp_iam.user_sessions`.
- Security cookie binding = `__Host-wcp_user_session`.
- Security surface claim = `WCP-USER`.
- Runtime Unix identity = `wcp360-user:wcp360-user`.
- The PostgreSQL credential used by the WCP-USER runtime MUST have session CRUD access to `wcp_iam.user_sessions` and MUST have no session CRUD/read access to `wcp_iam.admin_sessions`.
- Raw session tokens are forbidden; only verifier/hash material is persisted.

### wcp_iam.admin_sessions

This relation is the canonical server-side session namespace for the WCP-ADMIN surface.

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| session_id | UUID | NOT NULL | PK; UUIDv7; opaque bearer lookup identifier |
| user_id | UUID | NOT NULL | FK -> users ON DELETE RESTRICT |
| surface_claim | TEXT | NOT NULL | CHECK = 'WCP-ADMIN' |
| secret_verifier | BYTEA | NOT NULL | hashed/verifier material only |
| session_state | TEXT | NOT NULL | canonical state registry |
| auth_strength | INTEGER | NOT NULL | CHECK >= 0 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| last_seen_at | TIMESTAMPTZ | NULL |  |
| expires_at | TIMESTAMPTZ | NOT NULL | CHECK > created_at |
| revoked_at | TIMESTAMPTZ | NULL |  |

Owner rules:
- Server-side namespace = `wcp_iam.admin_sessions`.
- Security cookie binding = `__Host-wcp_admin_session`.
- Security surface claim = `WCP-ADMIN`.
- Runtime Unix identity = `wcp360-admin:wcp360-admin`.
- The PostgreSQL credential used by the WCP-ADMIN runtime MUST have session CRUD access to `wcp_iam.admin_sessions` and MUST have no session CRUD/read access to `wcp_iam.user_sessions`.
- Raw session tokens are forbidden; only verifier/hash material is persisted.

### Session-surface enforcement

The two relations are deliberately separate physical namespaces. Authentication lookup is surface-bound:

```text
WCP-USER request
→ __Host-wcp_user_session
→ WCP-USER origin validation
→ wcp360-user runtime
→ wcp_iam.user_sessions
→ surface_claim = WCP-USER

WCP-ADMIN request
→ __Host-wcp_admin_session
→ WCP-ADMIN origin validation
→ wcp360-admin runtime
→ wcp_iam.admin_sessions
→ surface_claim = WCP-ADMIN
```

A session identifier from one table is never looked up in the other surface's relation. Cross-surface DB privileges are denied for normal web runtimes. A migration/maintenance principal may administer both relations but must not be usable as either web runtime credential.

Therefore a valid USER session cannot authenticate an ADMIN request, and a valid ADMIN session cannot authenticate a USER request. Origin, cookie, timeout, MFA, rotation and CSRF enforcement remain Security-owned and are not redefined by Database.

### wcp_iam.api_credentials

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| credential_id | UUID | NOT NULL | PK; UUIDv7 |
| scope_kind | TEXT | NOT NULL | CHECK USER/ACCOUNT/TENANT |
| user_id | UUID | NULL | FK -> users ON DELETE RESTRICT |
| account_id | UUID | NULL | FK -> accounts ON DELETE RESTRICT |
| tenant_id | UUID | NULL | FK -> tenants ON DELETE RESTRICT |
| public_identifier | TEXT | NOT NULL | UNIQUE |
| secret_ref | TEXT | NOT NULL | reference only |
| credential_state | TEXT | NOT NULL | canonical state registry |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| expires_at | TIMESTAMPTZ | NULL |  |
| revoked_at | TIMESTAMPTZ | NULL |  |
| last_used_at | TIMESTAMPTZ | NULL |  |

Owner rules:
- Exactly one scope target according to scope_kind. Raw API token prohibited.

### wcp_infra.servers

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| server_id | UUID | NOT NULL | PK; UUIDv7 |
| server_key | TEXT | NOT NULL | UNIQUE |
| display_name | TEXT | NOT NULL |  |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| revision | BIGINT | NOT NULL | CHECK revision >= 1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

### wcp_infra.server_capabilities

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| server_id | UUID | NOT NULL | PK part; FK -> servers ON DELETE CASCADE |
| capability_id | TEXT | NOT NULL | PK part |
| enabled | BOOLEAN | NOT NULL |  |
| observed_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- PK(server_id,capability_id). Capability observation is not authorization.

### wcp_infra.managed_resources

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| resource_id | UUID | NOT NULL | PK; UUIDv7 |
| resource_type | TEXT | NOT NULL | bounded canonical type |
| scope_kind | TEXT | NOT NULL | GLOBAL/SERVER/TENANT/HOSTING_ACCOUNT/TENANT_SERVER |
| tenant_id | UUID | NULL | FK -> tenants |
| hosting_account_id | UUID | NULL | tenant-aware composite FK -> hosting_accounts |
| server_id | UUID | NULL | FK -> servers |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| revision | BIGINT | NOT NULL | CHECK revision >= 1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- UNIQUE(tenant_id,resource_id) for tenant-aware children.
- Scope CHECK: GLOBAL has no tenant/hosting/server; SERVER requires server only; TENANT requires tenant; HOSTING_ACCOUNT requires tenant+hosting account; TENANT_SERVER requires tenant+server.
- This is not a generic arbitrary JSON or privileged command store.

### wcp_infra.resource_desired_states

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| resource_id | UUID | NOT NULL | PK; FK -> managed_resources ON DELETE CASCADE |
| tenant_id | UUID | NULL | tenant-aware FK when resource is tenant-scoped |
| schema_id | TEXT | NOT NULL |  |
| schema_version | INTEGER | NOT NULL | CHECK >=1 |
| desired_revision | BIGINT | NOT NULL | CHECK >=1 |
| desired_payload | JSONB | NOT NULL | schema-versioned |
| payload_hash | BYTEA | NOT NULL |  |
| updated_by_user_id | UUID | NULL | FK -> users ON DELETE SET NULL |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- PostgreSQL desired state is authoritative logical state; ownership/security facts are relational, not JSON-only.

### wcp_infra.resource_observed_states

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| resource_id | UUID | NOT NULL | PK; FK -> managed_resources ON DELETE CASCADE |
| tenant_id | UUID | NULL | tenant-aware FK when resource is tenant-scoped |
| schema_id | TEXT | NOT NULL |  |
| schema_version | INTEGER | NOT NULL | CHECK >=1 |
| observation_seq | BIGINT | NOT NULL | CHECK >=1 |
| observed_payload | JSONB | NOT NULL | schema-versioned |
| payload_hash | BYTEA | NOT NULL |  |
| observed_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- Persisted observation is not physical authority; Linux/services/filesystem remain observed reality.

### wcp_infra.resource_quotas

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| quota_id | UUID | NOT NULL | PK; UUIDv7 |
| tenant_id | UUID | NOT NULL | FK -> tenants ON DELETE RESTRICT |
| resource_id | UUID | NULL | tenant-aware FK -> managed_resources |
| quota_key | TEXT | NOT NULL |  |
| soft_limit | BIGINT | NULL | CHECK NULL OR >=0 |
| hard_limit | BIGINT | NULL | CHECK NULL OR >=0 |
| unit | TEXT | NOT NULL |  |
| revision | BIGINT | NOT NULL | CHECK >=1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- CHECK soft_limit/hard_limit ordering when both present.

### wcp_hosting.domains

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| domain_id | UUID | NOT NULL | PK; UUIDv7 |
| resource_id | UUID | NOT NULL | UNIQUE; tenant-aware FK -> managed_resources |
| tenant_id | UUID | NOT NULL |  |
| canonical_name | TEXT | NOT NULL | UNIQUE normalized IDNA/ASCII |

Owner rules:
- UNIQUE(tenant_id,domain_id). Direct tenant reassignment forbidden.

### wcp_hosting.websites

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| website_id | UUID | NOT NULL | PK; UUIDv7 |
| resource_id | UUID | NOT NULL | UNIQUE; tenant-aware FK -> managed_resources |
| tenant_id | UUID | NOT NULL |  |
| hosting_account_id | UUID | NOT NULL | tenant-aware FK -> hosting_accounts |
| primary_domain_id | UUID | NULL | tenant-aware FK -> domains |
| display_name | TEXT | NOT NULL |  |
| runtime_profile_id | TEXT | NOT NULL | bounded typed profile |

Owner rules:
- No arbitrary filesystem path is authoritative in this table.

### wcp_hosting.dns_zones

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| zone_id | UUID | NOT NULL | PK; UUIDv7 |
| resource_id | UUID | NOT NULL | UNIQUE; tenant-aware FK -> managed_resources |
| tenant_id | UUID | NOT NULL |  |
| domain_id | UUID | NOT NULL | tenant-aware FK -> domains |
| canonical_name | TEXT | NOT NULL | UNIQUE |

Owner rules:
- UNIQUE(tenant_id,zone_id).

### wcp_hosting.dns_records

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| record_id | UUID | NOT NULL | PK; UUIDv7 |
| tenant_id | UUID | NOT NULL |  |
| zone_id | UUID | NOT NULL | tenant-aware FK -> dns_zones |
| record_type | TEXT | NOT NULL | canonical supported DNS type |
| owner_name | TEXT | NOT NULL |  |
| record_value | TEXT | NOT NULL |  |
| ttl_seconds | INTEGER | NOT NULL | CHECK >=0 |
| priority | INTEGER | NULL | CHECK NULL OR >=0 |
| revision | BIGINT | NOT NULL | CHECK >=1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

### wcp_hosting.mail_domains

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| mail_domain_id | UUID | NOT NULL | PK; UUIDv7 |
| resource_id | UUID | NOT NULL | UNIQUE; tenant-aware FK -> managed_resources |
| tenant_id | UUID | NOT NULL |  |
| domain_id | UUID | NOT NULL | tenant-aware FK -> domains |
| canonical_name | TEXT | NOT NULL | UNIQUE |

Owner rules:
- UNIQUE(tenant_id,mail_domain_id).

### wcp_hosting.mailboxes

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| mailbox_id | UUID | NOT NULL | PK; UUIDv7 |
| resource_id | UUID | NOT NULL | UNIQUE; tenant-aware FK -> managed_resources |
| tenant_id | UUID | NOT NULL |  |
| mail_domain_id | UUID | NOT NULL | tenant-aware FK -> mail_domains |
| local_part | TEXT | NOT NULL |  |
| auth_kind | TEXT | NOT NULL | bounded canonical value |
| credential_ref | TEXT | NULL | required when auth_kind=LOCAL |
| revision | BIGINT | NOT NULL | CHECK >=1 |

Owner rules:
- UNIQUE(tenant_id,mail_domain_id,local_part). Raw mailbox password prohibited.

### wcp_hosting.certificates

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| certificate_id | UUID | NOT NULL | PK; UUIDv7 |
| resource_id | UUID | NOT NULL | UNIQUE; FK -> managed_resources |
| tenant_id | UUID | NULL |  |
| server_id | UUID | NULL | FK -> servers |
| common_name | TEXT | NOT NULL |  |
| serial_number | TEXT | NOT NULL |  |
| issuer_name | TEXT | NOT NULL |  |
| not_before | TIMESTAMPTZ | NOT NULL |  |
| not_after | TIMESTAMPTZ | NOT NULL | CHECK > not_before |
| public_certificate | TEXT | NOT NULL |  |
| private_key_ref | TEXT | NOT NULL | reference only |
| revision | BIGINT | NOT NULL | CHECK >=1 |

Owner rules:
- Raw private key prohibited.

### wcp_hosting.hosted_db_engine_instances

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| engine_instance_id | UUID | NOT NULL | PK; UUIDv7 |
| resource_id | UUID | NOT NULL | UNIQUE; FK -> managed_resources |
| server_id | UUID | NOT NULL | FK -> servers ON DELETE RESTRICT |
| engine_type | TEXT | NOT NULL | CHECK IN ('POSTGRESQL','MARIADB') |
| engine_version | TEXT | NOT NULL |  |
| service_id | TEXT | NOT NULL | typed privileged-service reference |
| revision | BIGINT | NOT NULL | CHECK >=1 |

Owner rules:
- Hosted DB engine metadata != Control-plane PostgreSQL.

### wcp_hosting.hosted_databases

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| hosted_database_id | UUID | NOT NULL | PK; UUIDv7 |
| resource_id | UUID | NOT NULL | UNIQUE; tenant-aware FK -> managed_resources |
| tenant_id | UUID | NOT NULL |  |
| engine_instance_id | UUID | NOT NULL | FK -> hosted_db_engine_instances ON DELETE RESTRICT |
| database_name | TEXT | NOT NULL |  |
| charset_name | TEXT | NULL |  |
| collation_name | TEXT | NULL |  |
| revision | BIGINT | NOT NULL | CHECK >=1 |

Owner rules:
- UNIQUE(engine_instance_id,database_name); UNIQUE(tenant_id,hosted_database_id). Tenant/application DB content is never mirrored here.

### wcp_hosting.hosted_db_principals

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| principal_id | UUID | NOT NULL | PK; UUIDv7 |
| tenant_id | UUID | NOT NULL |  |
| hosted_database_id | UUID | NOT NULL | tenant-aware FK -> hosted_databases |
| principal_name | TEXT | NOT NULL |  |
| privilege_profile | TEXT | NOT NULL | bounded typed profile |
| credential_ref | TEXT | NOT NULL | reference only |
| revision | BIGINT | NOT NULL | CHECK >=1 |

Owner rules:
- UNIQUE(hosted_database_id,principal_name). Raw DB password and arbitrary SQL privilege strings prohibited.

### wcp_commercial.plans

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| plan_id | UUID | NOT NULL | PK; UUIDv7 |
| plan_key | TEXT | NOT NULL | UNIQUE |
| display_name | TEXT | NOT NULL |  |
| lifecycle_state | TEXT | NOT NULL | canonical state registry |
| revision | BIGINT | NOT NULL | CHECK >=1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

### wcp_commercial.plan_entitlements

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| plan_id | UUID | NOT NULL | PK part; FK -> plans ON DELETE CASCADE |
| entitlement_id | TEXT | NOT NULL | PK part; FK -> entitlement_definitions ON DELETE RESTRICT |
| value_payload | JSONB | NOT NULL | must validate entitlement value schema |
| created_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- PK(plan_id,entitlement_id). Database rows never elevate authorization beyond Security policy.

### wcp_commercial.subscriptions

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| subscription_id | UUID | NOT NULL | PK; UUIDv7 |
| account_id | UUID | NOT NULL | FK -> accounts ON DELETE RESTRICT |
| tenant_id | UUID | NULL | composite with account_id -> tenants(tenant_id,account_id) when present |
| plan_id | UUID | NOT NULL | FK -> plans ON DELETE RESTRICT |
| subscription_state | TEXT | NOT NULL | canonical state registry |
| starts_at | TIMESTAMPTZ | NOT NULL |  |
| ends_at | TIMESTAMPTZ | NULL | CHECK NULL OR > starts_at |
| revision | BIGINT | NOT NULL | CHECK >=1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

### wcp_commercial.subscription_entitlements

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| subscription_id | UUID | NOT NULL | PK part; FK -> subscriptions ON DELETE CASCADE |
| entitlement_id | TEXT | NOT NULL | PK part; FK -> entitlement_definitions ON DELETE RESTRICT |
| override_payload | JSONB | NULL | typed by entitlement schema |
| state | TEXT | NOT NULL | canonical state registry |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- PK(subscription_id,entitlement_id). Override persistence is not an authorization grant; Security composes final ceilings/denies.

### wcp_ops.jobs

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| job_id | UUID | NOT NULL | PK; UUIDv7 |
| tenant_id | UUID | NULL | FK -> tenants ON DELETE RESTRICT |
| job_type | TEXT | NOT NULL | typed job registry ID |
| action_id | TEXT | NOT NULL | typed action registry ID |
| resource_id | UUID | NULL | tenant-aware FK when tenant_id present |
| requested_by_user_id | UUID | NULL | FK -> users ON DELETE SET NULL |
| job_state | TEXT | NOT NULL | canonical job state registry |
| outcome_class | TEXT | NULL | bounded outcome class |
| error_code | TEXT | NULL | FK -> wcp_core.error_definitions(error_code) ON DELETE RESTRICT |
| attempt_count | INTEGER | NOT NULL | CHECK >=0 |
| max_attempts | INTEGER | NOT NULL | CHECK >=1 and attempt_count<=max_attempts |
| available_at | TIMESTAMPTZ | NOT NULL |  |
| started_at | TIMESTAMPTZ | NULL |  |
| completed_at | TIMESTAMPTZ | NULL |  |
| timeout_at | TIMESTAMPTZ | NULL |  |
| correlation_id | UUID | NOT NULL | not authorization |
| revision | BIGINT | NOT NULL | CHECK >=1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| updated_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- Generic command, executable, arbitrary path, signal, sudo/systemctl proxy payload is forbidden.

### wcp_ops.job_attempts

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| job_attempt_id | UUID | NOT NULL | PK; UUIDv7 |
| job_id | UUID | NOT NULL | FK -> jobs ON DELETE CASCADE only when job retention purge is authorized |
| attempt_no | INTEGER | NOT NULL | CHECK >=1 |
| worker_id | TEXT | NOT NULL | bounded worker identity |
| attempt_state | TEXT | NOT NULL | canonical attempt state |
| started_at | TIMESTAMPTZ | NOT NULL |  |
| finished_at | TIMESTAMPTZ | NULL |  |
| outcome_class | TEXT | NULL | bounded outcome class |
| error_code | TEXT | NULL | FK -> wcp_core.error_definitions(error_code) ON DELETE RESTRICT |
| correlation_id | UUID | NOT NULL |  |
| created_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- UNIQUE(job_id,attempt_no). Attempt rows are append-oriented history; completion fields may transition once from NULL to terminal values.

### wcp_ops.idempotency_records

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| idempotency_id | UUID | NOT NULL | PK; UUIDv7 |
| scope_kind | TEXT | NOT NULL | GLOBAL/ACCOUNT/TENANT |
| account_id | UUID | NULL | FK -> accounts ON DELETE RESTRICT |
| tenant_id | UUID | NULL | FK -> tenants ON DELETE RESTRICT |
| operation_namespace | TEXT | NOT NULL |  |
| idempotency_key | TEXT | NOT NULL | opaque caller key |
| request_fingerprint | BYTEA | NOT NULL | semantic request hash |
| idempotency_state | TEXT | NOT NULL | canonical state |
| result_reference | UUID | NULL | logical result reference |
| created_at | TIMESTAMPTZ | NOT NULL |  |
| expires_at | TIMESTAMPTZ | NOT NULL | CHECK > created_at |
| revision | BIGINT | NOT NULL | CHECK >=1 |

Owner rules:
- Scope CHECK: GLOBAL => account_id/tenant_id NULL; ACCOUNT => account_id NOT NULL and tenant_id NULL; TENANT => tenant_id NOT NULL and account_id NULL.
- Same scope+namespace+key + different fingerprint => IDEMPOTENCY_CONFLICT.
- Partial UNIQUE indexes define exact NULL-safe uniqueness per scope.

### wcp_ops.leases

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| lease_id | UUID | NOT NULL | PK; UUIDv7 |
| target_kind | TEXT | NOT NULL | JOB/RESOURCE |
| job_id | UUID | NULL | FK -> jobs ON DELETE CASCADE when target_kind=JOB |
| resource_id | UUID | NULL | FK -> managed_resources ON DELETE CASCADE when target_kind=RESOURCE |
| owner_worker_id | TEXT | NOT NULL |  |
| fencing_token | BIGINT | NOT NULL | CHECK >=1 |
| acquired_at | TIMESTAMPTZ | NOT NULL |  |
| expires_at | TIMESTAMPTZ | NOT NULL | CHECK > acquired_at |

Owner rules:
- Exactly one of job_id/resource_id is non-NULL and must match target_kind.
- One row per target; expired leases are reclaimed by atomic update with strictly increased fencing_token.
- Lease expiry alone never authorizes stale writes; result/state writes must validate current fencing token.

### wcp_ops.outbox_records

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| outbox_id | UUID | NOT NULL | PK; UUIDv7 |
| event_id | TEXT | NOT NULL | semantic event type |
| tenant_id | UUID | NULL | FK -> tenants ON DELETE RESTRICT |
| aggregate_type | TEXT | NOT NULL | bounded canonical type |
| aggregate_id | UUID | NOT NULL |  |
| schema_version | INTEGER | NOT NULL | CHECK >=1 |
| payload | JSONB | NOT NULL | schema-versioned, secret-safe |
| delivery_state | TEXT | NOT NULL | canonical state |
| attempt_count | INTEGER | NOT NULL | CHECK >=0 |
| available_at | TIMESTAMPTZ | NOT NULL |  |
| published_at | TIMESTAMPTZ | NULL |  |
| last_error_code | TEXT | NULL | FK -> wcp_core.error_definitions(error_code) ON DELETE RESTRICT |
| correlation_id | UUID | NOT NULL |  |
| created_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- Source mutation + required outbox intent commit atomically. Delivery is at-least-once; consumers are duplicate-safe.

### wcp_ops.event_records

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| event_record_id | UUID | NOT NULL | PK; UUIDv7 |
| event_id | TEXT | NOT NULL | semantic event type |
| event_version | INTEGER | NOT NULL | CHECK >=1 |
| tenant_id | UUID | NULL | FK -> tenants ON DELETE RESTRICT |
| resource_id | UUID | NULL | tenant-aware FK when tenant_id present |
| payload | JSONB | NOT NULL | schema-versioned, secret-safe |
| occurred_at | TIMESTAMPTZ | NOT NULL |  |
| correlation_id | UUID | NOT NULL |  |
| source_outbox_id | UUID | NULL | FK -> outbox_records ON DELETE SET NULL; UNIQUE when non-NULL |

Owner rules:
- event_id is type; event_record_id is occurrence.

### wcp_ops.reconciliation_runs

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| reconciliation_id | UUID | NOT NULL | PK; UUIDv7 |
| resource_id | UUID | NOT NULL | FK -> managed_resources ON DELETE RESTRICT |
| tenant_id | UUID | NULL | must match resource tenant when scoped |
| desired_revision | BIGINT | NOT NULL | CHECK >=1 |
| observed_sequence | BIGINT | NULL | CHECK NULL OR >=1 |
| drift_class | TEXT | NOT NULL | bounded canonical class |
| reconciliation_state | TEXT | NOT NULL | canonical state |
| repair_job_id | UUID | NULL | FK -> jobs ON DELETE SET NULL |
| manual_intervention | BOOLEAN | NOT NULL |  |
| correlation_id | UUID | NOT NULL |  |
| started_at | TIMESTAMPTZ | NOT NULL |  |
| completed_at | TIMESTAMPTZ | NULL |  |
| revision | BIGINT | NOT NULL | CHECK >=1 |

Owner rules:
- Reconciliation != blind replay; security-sensitive unresolved drift sets manual_intervention=true and blocks automatic destructive repair.

### wcp_ops.notifications

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| notification_id | UUID | NOT NULL | PK; UUIDv7 |
| tenant_id | UUID | NULL | FK -> tenants ON DELETE RESTRICT |
| user_id | UUID | NOT NULL | FK -> users ON DELETE RESTRICT |
| channel_id | TEXT | NOT NULL | bounded channel |
| template_id | TEXT | NOT NULL | bounded template |
| payload | JSONB | NOT NULL | secret-safe |
| notification_state | TEXT | NOT NULL | canonical state |
| scheduled_at | TIMESTAMPTZ | NULL |  |
| sent_at | TIMESTAMPTZ | NULL |  |
| correlation_id | UUID | NOT NULL |  |
| created_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- Tenant context does not substitute for authorization.

### wcp_audit.audit_records

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| audit_record_id | UUID | NOT NULL | PK; UUIDv7 immutable occurrence |
| audit_event_id | TEXT | NOT NULL | semantic audit event TYPE |
| event_version | INTEGER | NOT NULL | CHECK >=1 |
| occurred_at | TIMESTAMPTZ | NOT NULL |  |
| actor_type | TEXT | NOT NULL |  |
| actor_user_id | UUID | NULL | retained reference; no cascade |
| tenant_id | UUID | NULL | retained scope reference; no cascade |
| resource_id | UUID | NULL | retained resource reference; no cascade |
| action_id | TEXT | NULL | typed action ID |
| outcome_class | TEXT | NOT NULL | bounded outcome |
| correlation_id | UUID | NOT NULL | not authorization |
| request_id | UUID | NULL |  |
| job_id | UUID | NULL | retained reference; no cascade |
| workflow_id | UUID | NULL |  |
| trace_id | TEXT | NULL |  |
| security_classification | TEXT | NOT NULL |  |
| safe_payload | JSONB | NOT NULL | secret-redacted |
| persisted_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- Audit is append-oriented; ordinary application roles have no UPDATE/DELETE on authoritative occurrences.
- Corrections/annotations are new audit records. Business object deletion never cascades to audit.

### wcp_backup.backups

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| backup_id | UUID | NOT NULL | PK; UUIDv7 |
| backup_class | TEXT | NOT NULL | CONTROL_PLANE/HOSTED_DATABASE/WORKLOAD |
| tenant_id | UUID | NULL | FK -> tenants ON DELETE RESTRICT |
| resource_id | UUID | NULL | tenant-aware FK when scoped |
| backup_state | TEXT | NOT NULL | canonical state |
| storage_ref | TEXT | NOT NULL | opaque storage reference, not arbitrary privileged path |
| encryption_key_ref | TEXT | NOT NULL | secret reference only |
| checksum_algorithm | TEXT | NOT NULL |  |
| checksum_value | TEXT | NULL |  |
| size_bytes | BIGINT | NULL | CHECK NULL OR >=0 |
| started_at | TIMESTAMPTZ | NOT NULL |  |
| completed_at | TIMESTAMPTZ | NULL |  |
| retention_until | TIMESTAMPTZ | NULL |  |
| revision | BIGINT | NOT NULL | CHECK >=1 |

Owner rules:
- Control-plane / hosted DB / workload backup classes stay distinct.

### wcp_backup.backup_objects

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| backup_object_id | UUID | NOT NULL | PK; UUIDv7 |
| backup_id | UUID | NOT NULL | FK -> backups ON DELETE CASCADE |
| object_kind | TEXT | NOT NULL | bounded canonical kind |
| object_ref | TEXT | NOT NULL | opaque object reference |
| checksum_algorithm | TEXT | NOT NULL |  |
| checksum_value | TEXT | NOT NULL |  |
| size_bytes | BIGINT | NOT NULL | CHECK >=0 |
| required_object | BOOLEAN | NOT NULL |  |

Owner rules:
- UNIQUE(backup_id,object_ref). Cascade is allowed only because object rows are inseparable metadata children of the backup record.

### wcp_backup.restore_operations

| Column | Type | Nullability | Physical rule |
|---|---|---|---|
| restore_id | UUID | NOT NULL | PK; UUIDv7 |
| backup_id | UUID | NOT NULL | FK -> backups ON DELETE RESTRICT |
| tenant_id | UUID | NULL | FK -> tenants ON DELETE RESTRICT |
| target_resource_id | UUID | NULL | tenant-aware FK when tenant scoped |
| restore_state | TEXT | NOT NULL | canonical state |
| requested_by_user_id | UUID | NULL | FK -> users ON DELETE SET NULL |
| reconciliation_required | BOOLEAN | NOT NULL |  |
| reconciliation_id | UUID | NULL | FK -> reconciliation_runs ON DELETE SET NULL |
| outcome_class | TEXT | NULL | bounded outcome |
| error_code | TEXT | NULL | FK -> wcp_core.error_definitions(error_code) ON DELETE RESTRICT |
| correlation_id | UUID | NOT NULL |  |
| started_at | TIMESTAMPTZ | NULL |  |
| completed_at | TIMESTAMPTZ | NULL |  |
| revision | BIGINT | NOT NULL | CHECK >=1 |
| created_at | TIMESTAMPTZ | NOT NULL |  |

Owner rules:
- CONTROL_PLANE restore requires reconciliation_required=true until re-observation and verification complete. RESTORE != REPLAY.

## Canonical constraint baseline

### Primary keys
- Durable entities/occurrences: UUIDv7 UUID.
- Typed static registries: typed TEXT primary key.
- Pure relationship tables without independent lifecycle: composite primary key.

### Foreign keys
- Every durable structural relation has an FK.
- Tenant-scoped child relations use composite Tenant-aware FKs.
- Audit evidence does not cascade with mutable business objects.
- Hosted tenant/application database contents never participate in Control-plane PostgreSQL FKs.

### UNIQUE / CHECK / NULL
- NULL is allowed only when the contract says unknown/not-applicable/optional; it never means omitted required scope.
- Global/system objects use NULL tenant_id; no fake system Tenant is created.
- State, scope, attempt, revision, size, quota and time-order invariants are CHECK-constrained.
- Correctness uniqueness is enforced at the DB level, not by application pre-checks.

## Approved index catalogue

Constraint-backed PK/UNIQUE indexes are canonical. All foreign keys must have a supporting B-tree index using the FK column order; the canonical name is ix_<table>__<column1>[_<columnN>]. This deterministic rule is part of v1.0 and removes implementation discretion.

Additional approved indexes:

| Index | Table | Columns / predicate | Purpose |
|---|---|---|---|
| ix_tenants__account_id | wcp_core.tenants | (account_id) | FK/ownership lookup |
| ix_account_org_memberships__organization_id | wcp_core.account_organization_memberships | (organization_id, account_id) | reverse membership lookup |
| ix_org_tenant_memberships__tenant_id | wcp_core.organization_tenant_memberships | (tenant_id, organization_id) | reverse grouping lookup |
| ix_hosting_accounts__tenant_id | wcp_core.hosting_accounts | (tenant_id, hosting_account_id) | tenant child lookup |
| ix_role_assignments__user_scope | wcp_iam.role_assignments | (user_id, scope_kind, account_id, tenant_id) | authorization lookup |
| ix_role_assignments__tenant | wcp_iam.role_assignments | (tenant_id, assignment_state) WHERE tenant_id IS NOT NULL | tenant authorization lookup |
| ix_managed_resources__tenant_type | wcp_infra.managed_resources | (tenant_id, resource_type, lifecycle_state) WHERE tenant_id IS NOT NULL | tenant resource lookup |
| ix_managed_resources__server_type | wcp_infra.managed_resources | (server_id, resource_type, lifecycle_state) WHERE server_id IS NOT NULL | server resource lookup |
| ix_dns_records__zone | wcp_hosting.dns_records | (tenant_id, zone_id, record_type, owner_name) | zone materialization lookup |
| ix_mailboxes__mail_domain | wcp_hosting.mailboxes | (tenant_id, mail_domain_id, local_part) | mailbox lookup; also backed by UNIQUE |
| ix_hosted_databases__engine | wcp_hosting.hosted_databases | (engine_instance_id, database_name) | engine inventory; also backed by UNIQUE |
| ix_jobs__claimable | wcp_ops.jobs | (job_state, available_at, created_at, job_id) WHERE completed_at IS NULL | worker claim order |
| ix_jobs__tenant_state | wcp_ops.jobs | (tenant_id, job_state, created_at) WHERE tenant_id IS NOT NULL | tenant operations |
| ix_job_attempts__job | wcp_ops.job_attempts | (job_id, attempt_no) | attempt history; backed by UNIQUE |
| uq_idem__global | wcp_ops.idempotency_records | (operation_namespace, idempotency_key) WHERE scope_kind='GLOBAL' | NULL-safe global idempotency |
| uq_idem__account | wcp_ops.idempotency_records | (account_id, operation_namespace, idempotency_key) WHERE scope_kind='ACCOUNT' | account idempotency |
| uq_idem__tenant | wcp_ops.idempotency_records | (tenant_id, operation_namespace, idempotency_key) WHERE scope_kind='TENANT' | tenant idempotency |
| uq_leases__job | wcp_ops.leases | (job_id) WHERE job_id IS NOT NULL | one lease row per job target |
| uq_leases__resource | wcp_ops.leases | (resource_id) WHERE resource_id IS NOT NULL | one lease row per resource target |
| ix_outbox__dispatch | wcp_ops.outbox_records | (delivery_state, available_at, created_at, outbox_id) WHERE published_at IS NULL | outbox dispatcher |
| uq_events__source_outbox | wcp_ops.event_records | (source_outbox_id) WHERE source_outbox_id IS NOT NULL | deduplicate outbox->event persistence |
| ix_reconciliation__resource_state | wcp_ops.reconciliation_runs | (resource_id, reconciliation_state, started_at) | drift/reconciliation lookup |
| ix_audit__occurred | wcp_audit.audit_records | (occurred_at, audit_record_id) | retention/export ordering |
| ix_audit__tenant_occurred | wcp_audit.audit_records | (tenant_id, occurred_at, audit_record_id) WHERE tenant_id IS NOT NULL | authorized tenant diagnosis |
| ix_audit__correlation | wcp_audit.audit_records | (correlation_id, occurred_at) | cross-component correlation |
| ix_audit__event_occurred | wcp_audit.audit_records | (audit_event_id, occurred_at) | audit-type queries |
| ix_backups__tenant_state | wcp_backup.backups | (tenant_id, backup_state, started_at) WHERE tenant_id IS NOT NULL | tenant backup inventory |
| ix_restore__state_created | wcp_backup.restore_operations | (restore_state, created_at, restore_id) | restore workflow lookup |

No other performance index is approved by v1.0. Additional tuning requires measured workload evidence and PERFORMANCE / SCALABILITY review.

## Transaction boundaries

### TX-DB-01 — Pure PostgreSQL mutation
Where a business/control-plane mutation requires mandatory audit and/or outbox persistence, business mutation + required audit/outbox rows commit in one PostgreSQL transaction. Mandatory evidence failure rolls back the business mutation.

### TX-IDEMP-01 — Idempotency acquisition
A request first resolves its scope, then atomically inserts or locks the scope-specific idempotency row. Concurrent identical requests observe one logical owner/result. Same key with a different semantic fingerprint returns IDEMPOTENCY_CONFLICT and never overwrites the original fingerprint.

### TX-IDEMP-02 — Idempotency result
Terminal logical result linkage and idempotency state transition commit atomically. UNKNOWN_OUTCOME is a durable state and cannot be silently converted to retryable failure.

### TX-JOB-01 — Durable enqueue
Desired-state/operation intent + Job + required Outbox/Audit prerequisite are committed atomically before any external/privileged dispatch. If the transaction fails: no committed job and no external dispatch.

### TX-REV-01 — Revision mutation
Mutations use expected_revision = current revision. Update succeeds only if the expected revision matches; success increments revision exactly once. Zero affected rows due to a stale/mismatched revision means the canonical API error_code `CONCURRENCY_CONFLICT`.

### TX-RECON-01 — Reconciliation checkpoint
Observed evidence, drift classification and reconciliation state changes commit atomically. A repair job is linked only after authorization prerequisites are durable. Repair completion is not terminal until re-observation verifies the target state.

### External effects
External filesystem/service/daemon effects are never placed inside fictional cross-system ACID. Required pattern: durable intent -> dispatch -> observe -> durable result. If result cannot be proven, persist UNKNOWN_OUTCOME and reconcile before any potentially destructive retry.

## Concurrency and locking

1. Integrity constraints are the first concurrency layer.
2. Mutable aggregates use optimistic revision control.
3. Worker claim uses a short PostgreSQL transaction selecting claimable jobs in ix_jobs__claimable order with row locking and SKIP LOCKED semantics, then atomically transitions job state/attempt_count/revision and creates the corresponding job_attempt.
4. Job execution ownership that outlives the claim transaction uses wcp_ops.leases.
5. A lease has one row per target. Reclaim atomically changes owner_worker_id/acquired_at/expires_at and increments fencing_token. A stale fencing token must be rejected by any subsequent result/state write.
6. Deadlocks and serialization/lock conflicts may be retried only under the same logical idempotency context; retry is never treated as reconciliation.
7. Late results from stale workers are recorded/audited as stale evidence where needed but cannot overwrite state produced under a newer fencing token.

## Jobs, attempts, outbox and events

- Job existence never grants authorization.
- Job payloads may contain typed resource/operation parameters only; generic command, arbitrary executable/path/signal/systemctl/sudo primitives are forbidden.
- Every execution attempt has a unique (job_id, attempt_no).
- Outbox delivery is at-least-once.
- Event persistence from an outbox row is deduplicated by source_outbox_id.
- Consumers must be idempotent.
- A timeout may produce UNKNOWN_OUTCOME; it must not be normalized to definite failure without evidence.

## Idempotency

Canonical scope uniqueness:
- GLOBAL: unique(operation_namespace, idempotency_key) where scope_kind='GLOBAL'.
- ACCOUNT: unique(account_id, operation_namespace, idempotency_key) where scope_kind='ACCOUNT'.
- TENANT: unique(tenant_id, operation_namespace, idempotency_key) where scope_kind='TENANT'.

The associated CHECK guarantees the matching scope identifier is present and all non-applicable scope identifiers are NULL. Expiry never authorizes replay while an operation is still RUNNING/UNKNOWN_OUTCOME or has unresolved reconciliation.

## Audit durability

### Pure PostgreSQL mutations
If a mandatory audit occurrence cannot be persisted in the required transaction, fail closed and rollback.

### Queued / external / privileged effects
Durable operation intent, resource identity, authorization reference and correlation must exist before effect. After effect, result, audit occurrence, observed state and reconciliation requirement must become durable.

If authoritative audit persistence is temporarily unavailable after an effect, the Security/DevOps-approved Durable Audit Recovery Buffer preserves the occurrence/result. That buffer is not a second business-state authority.

If both authoritative audit persistence and the recovery buffer fail after a possible effect, classify AUDIT DURABILITY CRITICAL FAILURE, preserve unresolved operation state where possible, alert/escalate and require evidence reconstruction/reconciliation. Silent audit loss is forbidden.

## Reconciliation

- Common scheduler decides when reconciliation runs.
- Domain module owns desired-state semantics.
- PostgreSQL owns authoritative logical desired state.
- Reconciler reads desired state, observes physical state through typed adapters, detects drift and classifies it.
- Security + Module policy authorize any repair.
- Typed worker / wcpd / wcp-privd executes bounded repair.
- Reconciler re-observes and verifies.
- Audit owns durable repair evidence; Observability owns health/drift/backlog visibility.
- Caller-provided tenant_id, filesystem ownership, cache, correlation_id or job payload alone are never proof of Tenant authority.
- Restore and reconciliation never blindly replay historical privileged effects.

## Retention and partitioning

v1.0 intentionally defines policy classes rather than hard-coded numeric durations. Numeric duration values belong to deployment/security/operational policy and can change without schema redesign.

### RET-BUSINESS
Accounts, organizations, tenants, hosting accounts, managed resources, IAM assignments and commercial state are never time-purged merely because they are old. Purge requires explicit lifecycle completion and referential-safety checks.

### RET-SESSION
Expired/revoked rows from both `wcp_iam.user_sessions` and `wcp_iam.admin_sessions`, plus API credential metadata, are purge-eligible only after Security policy allows it and no active incident/legal/audit hold requires them. A retention process must preserve the USER/ADMIN namespace boundary and must never move a session row across surfaces.

### RET-IDEMPOTENCY
Idempotency rows are not purge-eligible before expires_at and never while RUNNING, UNKNOWN_OUTCOME or reconciliation remains unresolved. Reuse after purge is governed by API compatibility/security policy.

### RET-JOB
Jobs/attempts are retained while non-terminal, referenced by audit/reconciliation, or subject to operational/security hold. Terminal cleanup uses configured operational retention; child attempts purge only with their parent job under the same approved cleanup.

### RET-OUTBOX
Unpublished/retryable outbox rows are never purged. Published rows become purge-eligible only after corresponding durable event/audit requirements are satisfied.

### RET-EVENT
Event records use configured operational retention and must not be purged while required for unresolved reconciliation, audit, incident or compatibility investigation.

### RET-RECON
Open/required/manual-intervention reconciliation records are never purged. Terminal history uses configured operational retention after associated audit evidence is durable.

### RET-AUDIT
Audit is append-oriented, longest-retained evidence. Application lifecycle never deletes it. Retention/purge requires explicit Security/Audit policy and must preserve legal/incident holds and exported/archive integrity.

### RET-BACKUP
Backup metadata remains until retention_until when defined and while no active restore, recovery hold or policy requirement depends on it. Backup object rows purge only with an authorized parent backup purge.

### Partitioning
Partitioning is NOT REQUIRED BY DB MODEL v1.0. Audit/events/jobs may become candidates only after workload evidence and PERFORMANCE / SCALABILITY review. No partitioning assumption is required for SC-M01.

## Backup / restore boundary

Backup classes are distinct:
- CONTROL_PLANE
- HOSTED_DATABASE
- WORKLOAD

Control-plane restore never restores hosted tenant/application DB content by implication. After Control-plane restore: invalidate stale runtime ownership/leases, re-observe physical systems, detect drift, reconcile safely, then resume verified operation. No blind privileged replay.

## Migration boundary

DB Model v1.0 is schema-contract authority, not executable migration content.

Future migration workflow:
1. approved DB model/version;
2. migration design and MIG-WCP identifier assignment;
3. QA migration/rollback/recovery cases READY;
4. pre-upgrade Control-plane state backup;
5. compatibility classification;
6. migration implementation;
7. verification/reconciliation.

Compatibility classes:
- BACKWARD_COMPATIBLE — old and new application versions can safely coexist for the declared window.
- FORWARD_ONLY — rollback to old binaries/schema is unsafe; recovery uses forward-fix.
- REVERSIBLE — explicit down-path is proven safe.
- DATA_TRANSFORMING — backup + validation mandatory; downgrade allowed only if reverse transform is explicitly proven.

No migration may modify hosted tenant/application database contents unless a separate hosted-database operation contract explicitly targets them. This artifact creates no migration and no executable DDL.

## Correctness / QA requirements

Implementation-dependent tests remain PLANNED/READY, never PASS by publication alone.

Required families:
- TEST-DB-SCHEMA: schemas/tables/columns/types/nullability/PK/FK/UNIQUE/CHECK and no forbidden plaintext-secret fields.
- TEST-DB-TENANT: orphan tenant rejection; cross-tenant child/parent rejection; hosting account requires tenant; membership/delegation never changes ownership; direct tenant reparenting rejected.
- TEST-DB-CONSTRAINT: delete restrictions, allowed dependent cascades, revision/state/scope checks.
- TEST-DB-CONCURRENCY: lost update, duplicate claim, SKIP LOCKED behavior, lease reclaim, stale fencing token, uniqueness race, deadlock/lock timeout.
- TEST-DB-IDEMPOTENCY: duplicate same semantic request, different fingerprint conflict, account/tenant isolation, expiry with unresolved outcome prohibited.
- TEST-DB-OUTBOX: source rollback/no outbox, publisher crash/pending outbox, duplicate delivery, duplicate-safe event persistence.
- TEST-DB-AUDIT: audit_event_id/audit_record_id distinction, immutability, no cascade loss, secret redaction, fail-closed atomic audit.
- TEST-DB-RECON: UNKNOWN_OUTCOME -> observe/reconcile; stale worker late result cannot overwrite verified state.
- TEST-DB-BACKUP/RESTORE: class separation, restore re-observation, no blind replay, tenant binding preserved.
- TEST-DB-MIGRATION: clean install, forward migration, compatibility refusal, backup prerequisite, rollback when REVERSIBLE, forward-fix when FORWARD_ONLY.

## DB-MV01 / DB-MV02 targeted correction validation

### DB-MV01 — Typed identifier grammar

Status: **CORRECTED / CROSS-CONTRACT CONFORMANT**

API owner source used for validation:
- `specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md`
- canonical blob: `4893c5191b4928ecfa5a70f4dc14b39a8939fcea`
- API Contract Version: `1.0`

The Database contract now preserves the API Error Registry exactly, including uppercase codes such as `IDEMPOTENCY_CONFLICT`, `UNKNOWN_OUTCOME` and `IPC_SCHEMA_INVALID`. No API identifier is renamed or lowercased. `contract_id` remains distinct from `ipc_operation_id`.

Required QA assertions — implementation-dependent, status **READY/PLANNED**, never PASS by this publication:
1. Insert/reference of canonical `IDEMPOTENCY_CONFLICT` succeeds.
2. Insert/reference of canonical `UNKNOWN_OUTCOME` succeeds.
3. Insert/reference of canonical `IPC_SCHEMA_INVALID` succeeds.
4. Lowercased `idempotency_conflict` is rejected.
5. Unknown uppercase `NOT_A_CANONICAL_ERROR` is rejected by canonical registry membership.
6. API contract ID `API-WCP-0101` is not accepted as an `ipc_operation_id` or `error_code`.
7. Error codes round-trip without case transformation.
8. A stale or mismatched `expected_revision` in TX-REV-01 is surfaced as canonical `CONCURRENCY_CONFLICT`; `CONCURRENT_MODIFICATION` is rejected as a non-canonical error code.

### DB-MV02 — WCP-USER / WCP-ADMIN session persistence separation

Status: **CORRECTED / CROSS-CONTRACT CONFORMANT**

Security owner source used for validation:
- `specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md`
- canonical blob: `6d74f2f363524511424f839ca7262143c2af95ab`
- Security Baseline Version: `v1.0`

The Database representation now implements Security's required separate server-side session namespace and surface claim as two physical relations:
- `wcp_iam.user_sessions` for WCP-USER;
- `wcp_iam.admin_sessions` for WCP-ADMIN.

It also records the canonical cookie/runtime mapping from Security and requires surface-specific PostgreSQL runtime credentials to have no cross-surface session-table access.

Required QA assertions — implementation-dependent, status **READY/PLANNED**, never PASS by this publication:
1. USER session creation persists only in `wcp_iam.user_sessions` with `surface_claim='WCP-USER'`.
2. ADMIN session creation persists only in `wcp_iam.admin_sessions` with `surface_claim='WCP-ADMIN'`.
3. Mismatched surface claims are rejected by DB CHECK constraints.
4. USER runtime DB credential cannot SELECT/INSERT/UPDATE/DELETE `wcp_iam.admin_sessions`.
5. ADMIN runtime DB credential cannot SELECT/INSERT/UPDATE/DELETE `wcp_iam.user_sessions`.
6. A valid USER cookie/session identifier presented on ADMIN lookup resolves no ADMIN session and authentication fails closed.
7. A valid ADMIN cookie/session identifier presented on USER lookup resolves no USER session and authentication fails closed.
8. Session rotation/revocation remains within its original surface namespace.
9. Security cookie names remain exactly `__Host-wcp_user_session` and `__Host-wcp_admin_session`; Database does not define an alternative.
10. No web runtime uses a migration/maintenance DB principal capable of reading both session namespaces.

### Cross-owner validation result

```text
API / IPC CANONICAL CONTRACT CHECK = CONFORMANT
SECURITY CANONICAL BASELINE CHECK   = CONFORMANT
API CONTENT CHANGE                 = NONE
SECURITY CONTENT CHANGE            = NONE
DATABASE CONTENT CHANGE            = TARGETED REAL DELTA
```

This is a compatibility validation against already-final owner artifacts. It does not modify or supersede API/Security authority.

## DB-R closure statement

DB-R01 through DB-R15 are owner-approved by this publication. The recovered historical fragments remain evidence only and are not imported as independent authority.

No unresolved Database architectural decision remains for SC-M01 consumption.

## Final publication status

OWNER = DATABASE / DATA MODEL  
ARTIFACT = specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md  
VERSION = v1.0  
FINAL APPROVED CONTENT = YES  
PUBLICATION TYPE = OWNER RECONSTRUCTION / AMENDMENT  
CONTENT CHANGE = REAL DELTA — DB-MV01 / DB-MV02 TARGETED CONFORMANCE CORRECTION  
VERSION IMPACT = NONE — v1.0 retained; correction is pre-implementation and aligns Database with existing API/Security v1.0 contracts without changing their semantics  
HISTORICAL CONTENT INCLUDED = NO  
READY FOR SOURCE CODE CONSUMPTION = YES, subject only to MASTER's separate SC-M01 authorization  
BLOCKER = NONE  
