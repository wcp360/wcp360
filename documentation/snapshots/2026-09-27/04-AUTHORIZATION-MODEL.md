# WCP360 — Authorization Model

Candidate authorization equation:

`authenticated_subject + trusted_identity + surface + role + permission + entitlement_if_applicable + tenant_scope + ownership_or_delegation + resource_scope + policy/context + resource_state + action_constraints → ALLOW / DENY`

## Principles

- fail closed;
- role != permission;
- permission != entitlement;
- permission != ownership;
- surface is part of authorization context;
- resource-state precondition is not a permission;
- approval never replaces execution-time authorization;
- UI visibility never grants permission;
- origin/correlation context never grants permission;
- reseller delegation cannot exceed delegated envelope;
- explicit deny overrides inherited allow.

## Async jobs

For queued state-changing work, authorization timing must be explicit:
- request time;
- execution time;
- or both.

Execution must not rely on stale interactive session assumptions without an explicit contract.
