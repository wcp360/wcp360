# WCP360 — Database physical model OWNER MATERIALIZATION WORKSHEET (36 known fragments)

**Date:** 2026-10-27  
**Status:** NEW PROPOSED PHYSICAL OWNER DECISIONS — **NON-CANONICAL / NOT IMPLEMENTATION-READY**  
**Owner:** DATABASE / DATA MODEL, with mandatory Security/API/DevOps/QA cross-review  
**Baseline:** WCP360-BM-0001 FINAL; DOC v4.0.21.23; DESIGN V2.5.3.36.4  
**Issue #6:** missing exact original complete final DB v1.0 body.  
**Source recovery:** `governance/evidence/SC-M01-DB-RECOVERED-TABLE-FRAGMENTS-2026-10-27.json` (blob `cca9c5a9dc0f61cb57025cf0482681d78c3e35ee`).  
**Scoping:** 36 named partial recovered fragments in 8 schemas, **not proof that original final v1.0 had only 36 tables**.  
**Forbidden:** DDL, migrations, `specifications/database/` changes, CLAIMED APPROVAL, `CODE-BASELINE`.

## 1 — Genuine owner decision path (choose before implementation)

**Path A: exact complete retained v1.0 source found.** Republish *that entire body* with provenance and `CONTENT CHANGE = NONE`. This worksheet remains historical review aid only.

**Path B: no complete retained source available.** The Database owner must **explicitly accept/amend/reject newly proposed physical definitions** for all missing cells below; construct a complete normative model, declare `CONTENT CHANGE = YES` and an explicit version impact (candidate `DB Model v1.1` *only if owner and MASTER approve*). `v1.0` historical metadata cannot be used to falsely claim that newly designed structures were previously approved.

At either path, source must be complete, tenant-aware, transactionally coherent, auditable, reversible where meaningful and independently cross-reviewed. **The output of this worksheet is not a final contract.**

## 2 — Source fragment census, unchanged

| Schema | Named recovered fragments | First-block typed physical declarations |
|---|---:|---:|
| `wcp_core` | 9 | 9 |
| `wcp_iam` | 2 | 2 |
| `wcp_infra` | 1 | 1 |
| `wcp_hosting` | 10 | 10 |
| `wcp_ops` | 6 | 5 |
| `wcp_commercial` | 4 | 4 |
| `wcp_audit` | 1 | 1 |
| `wcp_backup` | 3 | 1 |
| **TOTAL** | **36** | **33** |

The two conceptual-only tables are `wcp_backup.backup_objects` and `wcp_backup.restore_operations`; `wcp_ops.job_attempts` is recovered by name only.

## 3 — Per-table owner materialization sheets

For each table, the first block below is reproduced from the **historical recovery file**, not a final physical model. Further recovered FK/unique/ownership semantics must be consulted verbatim in the associated JSON/source section. The owner must decide every missing physical property and resolve conflicts before final publication. No source column may be silently discarded or changed.

### Schema `wcp_core`

#### DB-TABLE-01 — `wcp_core.tenants`

**Historical evidence:** source lines 244–274; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
tenant_id UUID NOT NULL PK
account_id UUID NOT NULL
name TEXT NOT NULL
tenant_key TEXT NOT NULL
lifecycle_state TEXT NOT NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
```

**Recovered:** tenant_id PK, account_id FK RESTRICT, UNIQUE(account_id,tenant_key), UNIQUE(tenant_id,account_id), revision. Owner should confirm that composite parent UNIQUE is used to enforce cross-tenant FK consistency; account_id != tenant_id; ownership changes are explicit/audited, not silent.

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-02 — `wcp_core.accounts`

**Historical evidence:** source lines 275–294; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
account_id UUID NOT NULL PK
display_name TEXT NOT NULL
lifecycle_state TEXT NOT NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
CHECK (revision >= 1)
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-03 — `wcp_core.customer_profiles`

**Historical evidence:** source lines 295–318; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
account_id UUID NOT NULL PK
lifecycle_state TEXT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-04 — `wcp_core.reseller_profiles`

**Historical evidence:** source lines 319–342; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
account_id UUID NOT NULL PK
lifecycle_state TEXT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-05 — `wcp_core.organizations`

**Historical evidence:** source lines 343–363; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
organization_id UUID NOT NULL PK
name TEXT NOT NULL
lifecycle_state TEXT NOT NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
CHECK (revision >= 1)
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-06 — `wcp_core.account_organization_memberships`

**Historical evidence:** source lines 364–391; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
account_id UUID NOT NULL
organization_id UUID NOT NULL
membership_state TEXT NOT NULL
created_at TIMESTAMPTZ NOT NULL
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-07 — `wcp_core.organization_tenant_memberships`

**Historical evidence:** source lines 392–425; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
organization_id UUID NOT NULL
tenant_id UUID NOT NULL
membership_state TEXT NOT NULL
created_at TIMESTAMPTZ NOT NULL
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-08 — `wcp_core.hosting_accounts`

**Historical evidence:** source lines 426–460; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
hosting_account_id UUID NOT NULL PK
tenant_id UUID NOT NULL
system_key TEXT NOT NULL
display_name TEXT NOT NULL
lifecycle_state TEXT NOT NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
```

**Recovered:** hosting_account_id PK, tenant_id FK RESTRICT, UNIQUE(tenant_id,system_key), UNIQUE(tenant_id,hosting_account_id), immutable tenant ownership. Owner must cross-check tenant-aware composite FK usage in every dependent hosted resource.

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-09 — `wcp_core.reseller_tenant_delegations`

**Historical evidence:** source lines 461–509; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
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
```

**Recovered:** (tenant_id,granted_by_account_id)→tenants(tenant_id,account_id), valid_until later than valid_from, no transfer of tenant ownership; one active logical delegation per (reseller_account_id,tenant_id) under current scope. Owner must decide exact active exclusivity + revocation/audit/role assignment FK relationship.

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

### Schema `wcp_iam`

#### DB-TABLE-10 — `wcp_iam.tenant_memberships`

**Historical evidence:** source lines 512–537; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
tenant_id UUID NOT NULL
user_id UUID NOT NULL
membership_state TEXT NOT NULL
created_at TIMESTAMPTZ NOT NULL
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-11 — `wcp_iam.role_assignments`

**Historical evidence:** source lines 538–584; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
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
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

### Schema `wcp_infra`

#### DB-TABLE-12 — `wcp_infra.managed_resources`

**Historical evidence:** source lines 587–633; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
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
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

### Schema `wcp_hosting`

#### DB-TABLE-13 — `wcp_hosting.domains`

**Historical evidence:** source lines 675–698; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
domain_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NOT NULL
canonical_name TEXT NOT NULL UNIQUE
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-14 — `wcp_hosting.websites`

**Historical evidence:** source lines 699–718; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
website_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NOT NULL
hosting_account_id UUID NOT NULL
primary_domain_id UUID NULL
display_name TEXT NOT NULL
runtime_profile_id TEXT NOT NULL
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-15 — `wcp_hosting.dns_zones`

**Historical evidence:** source lines 719–746; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
zone_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NOT NULL
domain_id UUID NOT NULL
canonical_name TEXT NOT NULL UNIQUE
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-16 — `wcp_hosting.dns_records`

**Historical evidence:** source lines 747–778; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
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
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-17 — `wcp_hosting.mail_domains`

**Historical evidence:** source lines 779–803; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
mail_domain_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NOT NULL
domain_id UUID NOT NULL
canonical_name TEXT NOT NULL UNIQUE
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-18 — `wcp_hosting.mailboxes`

**Historical evidence:** source lines 804–835; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
mailbox_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NOT NULL
mail_domain_id UUID NOT NULL
local_part TEXT NOT NULL
auth_kind TEXT NOT NULL
credential_ref TEXT NULL
revision BIGINT NOT NULL
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-19 — `wcp_hosting.certificates`

**Historical evidence:** source lines 836–864; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
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
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-20 — `wcp_hosting.hosted_db_engine_instances`

**Historical evidence:** source lines 865–895; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
engine_instance_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
server_id UUID NOT NULL
engine_type TEXT NOT NULL
engine_version TEXT NOT NULL
service_id TEXT NOT NULL
revision BIGINT NOT NULL
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-21 — `wcp_hosting.hosted_databases`

**Historical evidence:** source lines 896–928; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
hosted_database_id UUID NOT NULL PK
resource_id UUID NOT NULL UNIQUE
tenant_id UUID NOT NULL
engine_instance_id UUID NOT NULL
database_name TEXT NOT NULL
charset_name TEXT NULL
collation_name TEXT NULL
revision BIGINT NOT NULL
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-22 — `wcp_hosting.hosted_db_principals`

**Historical evidence:** source lines 929–964; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
principal_id UUID NOT NULL PK
tenant_id UUID NOT NULL
hosted_database_id UUID NOT NULL
principal_name TEXT NOT NULL
privilege_profile TEXT NOT NULL
credential_ref TEXT NOT NULL
revision BIGINT NOT NULL
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

### Schema `wcp_ops`

#### DB-TABLE-23 — `wcp_ops.jobs`

**Historical evidence:** source lines 978–1024; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
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
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-24 — `wcp_ops.idempotency_records`

**Historical evidence:** source lines 1025–1069; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
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
```

**Recovered:** separate scope_kind GLOBAL/ACCOUNT/TENANT; nullable account_id/tenant_id; namespace/key/fingerprint; exact scope-specific UNIQUE SQL is UNKNOWN. **NEW OWNER CHOICE:** (A) strict scope XOR CHECKs plus separate partial unique indexes for `GLOBAL(operation_namespace,idempotency_key)`, `ACCOUNT(account_id,operation_namespace,idempotency_key)`, `TENANT(tenant_id,operation_namespace,idempotency_key)`; (B) immutable non-null normalized scope reference with one composite UNIQUE. Do not use nullable composite UNIQUE without proving NULL semantics. Decide expiration/reuse, result reference FK, transactional contention handling; same key differing fingerprint → IDEMPOTENCY_CONFLICT. **NO DDL proposed as executable.**

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-25 — `wcp_ops.leases`

**Historical evidence:** source lines 1070–1101; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
lease_id UUID NOT NULL PK
target_kind TEXT NOT NULL
job_id UUID NULL
resource_id UUID NULL
owner_worker_id TEXT NOT NULL
fencing_token BIGINT NOT NULL
acquired_at TIMESTAMPTZ NOT NULL
expires_at TIMESTAMPTZ NOT NULL
```

**Recovered:** exactly-one job_id/resource_id, monotonic fencing tokens, only one active owner per target, reclaims after expiry. **NEW OWNER CHOICE:** durable target identity+fencing sequence with transactionally serialized claim, CAS revision/fencing check on each update; define exact row lock/constraint strategy and clock semantics. Never claim a unique partial index with `NOW()` in a predicate can enforce active expiry or that an expired lease prevents a stale actor from writing. No transaction design approved yet.

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-26 — `wcp_ops.outbox_records`

**Historical evidence:** source lines 1102–1137; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
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
```

**Recovered:** required source mutation+outbox insertion atomic; at-least-once delivery and duplicate-safe consumers. Owner must specify exact aggregate/event identity uniqueness, claiming/retries/lease concurrency, retention and recipient acknowledgement, schema version, and whether audit/outbox persistence failures roll back logical mutation. External OS effects are never PostgreSQL-atomic.

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-27 — `wcp_ops.event_records`

**Historical evidence:** source lines 1138–1178; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
event_record_id UUID NOT NULL PK
event_id TEXT NOT NULL
event_version INTEGER NOT NULL
tenant_id UUID NULL
resource_id UUID NULL
payload JSONB NOT NULL
occurred_at TIMESTAMPTZ NOT NULL
correlation_id UUID NOT NULL
source_outbox_id UUID NULL
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-28 — `wcp_ops.job_attempts`

**Historical evidence:** source lines 1179–1184; classification **NO_TYPED_DECLARATION_BLOCK**. Final table completeness: **NOT VERIFIED**.

Historical source contains no usable physical column declaration block for this named table.

**Highest-priority unrecovered physical table.** Only table NAME is historical. **NEW PROPOSAL FOR DATABASE OWNER:** `job_attempt_id UUID PK`, `job_id UUID NOT NULL`, `attempt_number INTEGER NOT NULL`, `worker_ref TEXT NOT NULL`, `attempt_state TEXT NOT NULL`, `started_at TIMESTAMPTZ NOT NULL`, `finished_at TIMESTAMPTZ NULL`, `outcome_class TEXT NULL`, `error_code TEXT NULL`, `correlation_id UUID NOT NULL`; candidate `UNIQUE(job_id,attempt_number)`, `attempt_number >= 1`, job FK RESTRICT and append-only immutable attempt identity. **NOT RECOVERED / OWNER-UNAPPROVED.** DB must decide whether these columns/types/indices actually belong and how partial/UNKNOWN_OUTCOME attempts persist.

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

### Schema `wcp_commercial`

#### DB-TABLE-29 — `wcp_commercial.plans`

**Historical evidence:** source lines 1259–1272; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
plan_id UUID NOT NULL PK
plan_key TEXT NOT NULL UNIQUE
display_name TEXT NOT NULL
lifecycle_state TEXT NOT NULL
revision BIGINT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-30 — `wcp_commercial.plan_entitlements`

**Historical evidence:** source lines 1273–1289; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
plan_id UUID NOT NULL
entitlement_id TEXT NOT NULL
value_payload JSONB NOT NULL
created_at TIMESTAMPTZ NOT NULL
```

**Open Security S06 warning:** source permission precedence DENY > account override > role grants > plan/feature envelope > default policy does not prove whether a POSITIVE override may exceed a plan/feature ceiling. DB must not encode permissive override behavior until Security ratifies that ceiling.

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-31 — `wcp_commercial.subscriptions`

**Historical evidence:** source lines 1290–1320; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
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
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-32 — `wcp_commercial.subscription_entitlements`

**Historical evidence:** source lines 1321–1347; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
subscription_id UUID NOT NULL
entitlement_id TEXT NOT NULL
override_payload JSONB NULL
state TEXT NOT NULL
created_at TIMESTAMPTZ NOT NULL
updated_at TIMESTAMPTZ NOT NULL
```

**Security S06:** do not assume subscription/positive override lifts current plan maximum. Explicit owner and Security composition decision required before boolean/JSONB semantics, FK scopes and entitlement checks are used.

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

### Schema `wcp_audit`

#### DB-TABLE-33 — `wcp_audit.audit_records`

**Historical evidence:** source lines 1360–1400; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
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
```

**Recovered:** append-only immutable `audit_record_id` occurrence distinct from `audit_event_id` type; durable audit is mandatory, no cascade delete. Owner must decide FK/delete behavior where referenced actor/resource is later deleted, retention/export immutability, redaction schema and approved indexes. Two-stage reboot/shutdown event types may generate distinct durable occurrences; do not substitute event-type strings for occurrence IDs.

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

### Schema `wcp_backup`

#### DB-TABLE-34 — `wcp_backup.backups`

**Historical evidence:** source lines 1417–1452; classification **PARTIAL_TYPED_DECLARATIONS**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
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
```

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-35 — `wcp_backup.backup_objects`

**Historical evidence:** source lines 1453–1475; classification **PARTIAL_UNTYPED_INVENTORY**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
backup_object_id
backup_id
object metadata
checksum metadata
size metadata
required metadata
```

**Partial conceptual attributes recovered, not physical type declarations.** Confirmed backup_id→backups(backup_id) ON DELETE CASCADE (specific exception to global RESTRICT). **NEW PROPOSAL FOR DATABASE OWNER:** `backup_object_id UUID PK`, `backup_id UUID NOT NULL FK`, `object_ref TEXT NOT NULL`, `object_kind TEXT NOT NULL`, `size_bytes BIGINT NOT NULL CHECK>=0`, `checksum_algorithm TEXT NOT NULL`, `checksum_digest BYTEA NOT NULL`, `metadata JSONB`, `created_at TIMESTAMPTZ NOT NULL`; candidate uniqueness `UNIQUE(backup_id,object_ref)` but only if object references canonicalized. No privileged arbitrary filesystem target or hosted payload is stored in control-plane DB. **ALL proposed fields/constraints except recorded conceptual identity and cascade require owner acceptance.**

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

#### DB-TABLE-36 — `wcp_backup.restore_operations`

**Historical evidence:** source lines 1476–1500; classification **PARTIAL_UNTYPED_INVENTORY**. Final table completeness: **NOT VERIFIED**.

Recovered first fenced field/attribute block (verbatim **content**, not a guarantee of completeness):

```text
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
```

**Only partial conceptual field list recovered.** **NEW PROPOSAL FOR DATABASE OWNER:** `restore_id UUID PK`, `backup_id UUID NOT NULL`, `tenant_id UUID NULL`, `target_resource_id UUID NULL`, `requested_by_user_id UUID NULL`, `restore_state TEXT NOT NULL`, `reconciliation_required BOOLEAN NOT NULL`, `outcome_class TEXT NULL`, `error_code TEXT NULL`, `job_id UUID NULL`, `correlation_id UUID NOT NULL`, `revision BIGINT NOT NULL CHECK>=1`, `created_at TIMESTAMPTZ NOT NULL`, `updated_at TIMESTAMPTZ NOT NULL`. Owner must decide FKs, scope checks, expected revision/reconciliation restart, backup class and no false success, and precise existence of proposed columns. **NOT a recovered v1.0 definition.**

**DATABASE owner must explicitly decide:**
- [ ] Confirm all column names, physical types, nullability, defaults and UUIDv7 generation for this table.
- [ ] Confirm exact PRIMARY KEY, UNIQUE/CHECK, FK targets, tenant-aware composite constraints and ON DELETE exceptions.
- [ ] Ratify ownership, reassignment, lifecycle revision/state, durability/audit dependencies, approved correctness indexes and query paths.
- [ ] Confirm delete/retention/restore/upgrade/concurrency rules; do not import historical fragments as final.
- [ ] Confirm this table is still required in the complete final model; identify approved new/removed table version impacts.
- [ ] Record DB-R01..DB-R15 tags, owner approval, API/Security/QA/DevOps handoffs, exact tests, and status.

## 4 — Cross-table decisions required before any DDL/migrations

**DB-X01 — Ownership and composite tenant-aware FKs.** Ensure `account_id`, `organization_id`, `tenant_id` and `hosting_account_id` remain separate. Inventory every dependent resource and exact composite FK to parent tenancy/hosting ownership. Reject cross-tenant attachment and unauthorized reparenting; document reversible vs irreversible ownership transitions and audit.

**DB-X02 — Idempotency scope SQL semantics.** Decide exact `scope_kind` nullability/normalization and indexes, fingerprint comparison under concurrent insert, replay of same logical result, expiry and reuse, cross-tenant collision handling, result reference and durable transaction order. Preserve `IDEMPOTENCY_CONFLICT`.

**DB-X03 — Lease/fencing implementation.** Define one active owner per target with monotonic fencing tokens and DB-level stale worker protection; specify locks, token allocations, reclamation and crash/concurrent recovery. Do not rely on wall-clock NOW() index predicate for uniqueness or on lease expiry alone for fencing.

**DB-X04 — Mandatory audit/outbox atomicity.** For pure PostgreSQL logical mutation, require atomic audit/outbox persistence and rollback on mandatory evidence failure. For external OS effects persist prior intent and observed/correlated outcome; loss after effect yields `UNKNOWN_OUTCOME` and reconciliation, not fake rollback/automatic redispatch.

**DB-X05 — Job/attempts/reconciliation.** Specify all final columns, lifecycle states, per-attempt history, retry eligibility, `job_id` and `correlation_id`, unknown-outcome durable state, late/reordered reply handling and replay-resistant event processing.

**DB-X06 — Immutable audit and deletion.** Audit occurrence IDs distinct from semantic event types; no cascade deletion of audit, lifecycle event pair requested/completed for reboot/shutdown remains independently durable where applicable; redact payload, authorize retention/exceptions and preserve evidence across account/resource deletion.

**DB-X07 — Backup classes and restores.** Keep control-plane PostgreSQL backups distinct from hosted DB engines/customer workload. Confirm backup_objects documented cascade exception, exact backup/restore consistency, foreign ownership, storage refs and post-restore reconciliation. Normal uninstall must not delete hosted customer data.

**DB-X08 — Entitlement/override ceiling.** Do not infer whether positive account overrides can exceed plan/feature envelope; Security S06 owner resolution required. Data schema alone cannot create an authorization grant.

**DB-X09 — Indexes and retention.** Publish approved PK/FK/unique/correctness indexes, predicates, partitioning classification, retention durations/locks/cleanup policies. Avoid speculative performance optimization without benchmark and no dropped audit/outbox under saturation.

**DB-X10 — Migration compatibility.** Formalize physical model version, seed/bootstrap authority, online migration steps, backup before upgrade, rollback/downgrade classification, tenant data safety and exact expected QA/DevOps test cases. **Do not create DDL/migrations until owner-final approval.**

## 5 — Recovered evidence vs *NEW proposed decisions* must remain separate

The three untyped/missing physical fragments are deliberately highlighted above with **NEW PROPOSAL** fields. These are not older v1.0 recovered columns and may be wrong; DATABASE must review each. Every proposed index/constraint/owner decision has the same status until accepted.

Source file inventory has evidence line ranges and full verbatim fragments. The source itself is incomplete. This worksheet includes the first-block excerpts to accelerate targeted owner decisions without rescanning the full >1000 Markdown archive.

## 6 — Mandatory owner approval/materialization result

```text
DB OWNER = WCP360 — DATABASE / DATA MODEL
PUBLICATION PATH = specifications/database/<complete-owner-file>
PUBLICATION TYPE = EXACT RETAINED / OWNER-AMENDED RECONSTRUCTION
DB MODEL VERSION = v1.0 original ONLY if exact source; otherwise owner/version decision
CONTENT CHANGE = NONE only if exact retained prior FINAL source; otherwise YES
36 KNOWN RECOVERED TABLES = checked, plus any originally unrecovered required tables
DB-R01..DB-R15 = decisions with exact physical details and tests
DB-X01..DB-X10 = owner-approved cross-table integrity decisions
SOURCE PROVENANCE AND FULL SELF-CONTAINED FILE = required
API / SECURITY / QA / DEVOPS REVIEW = required on affected constraints
ACTUAL GITHUB MAIN SHA = required
STATUS = DRAFT / REVIEW / APPROVED
```

**No table in this worksheet is considered complete or implementation-authorized.**

## 7 — Global gate

```text
DATABASE #6 = OPEN / exact final physical owner model MISSING
SC-M01 OWNER ARTIFACTS = 5 / 8
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```
