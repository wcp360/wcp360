# V4.0.21.3.1 --- Corrected P0 Blocker Model

## Root blockers

### B1 --- Complete CORE Resource Dictionary

Every CORE resource in the canonical module/resource catalog must have a
field dictionary with type, required/nullability, mutability,
reference/cardinality, sensitivity, writer authority and invariants.

### B2 --- Complete CORE Operation Registry

Every supported UI/API action must have a stable Operation ID,
method/path or internal action identifier, capability, scope, step-up,
entitlement, precondition/idempotency, execution class and audit action.

### B3 --- Derived execution/authorization coverage

For every mutation from B2: exact Job or explicit `NO_JOB`; exact
Decision IDs; exact privileged RPC/adapter boundary where applicable;
exact RBAC/scope/entitlement and audit payload.

### B4 --- Exact acceptance coverage

Every Operation ID must be bound to acceptance/security tests and an
observable oracle. Mutation tests must bind expected
Job/Decision/RPC/Adapter sequence or explicitly prove that no privileged
execution occurs.

### B5 --- Delete/retention completeness

Every deletable CORE resource needs explicit child disposition,
retention/legal-hold behavior, ordering, irreversible boundary and
absence/retention oracle.

## Dependency

`B1 → B2 → B3 → B4/B5 → cross-corpus re-audit`.

Closing downstream rows before B1/B2 are exhaustive is not sufficient.
