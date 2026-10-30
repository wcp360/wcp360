# V4.0.21.1 --- API Completeness Correction

## Finding

`CONTRACT-V4021-02-EXACT-API-OPERATIONS.md` is exact for the operations
it lists, but it is **not a complete API registry** for every CORE
resource.

Examples absent as individually registered mutation operations include
reseller lifecycle, plan/entitlement mutation, mail-domain/alias/policy
mutation, backup-policy mutation, cron mutation, API-token lifecycle,
integration lifecycle, and notification-route lifecycle.

The sentence "Every resource ... has GET collection and/or GET /{id}" is
also a family rule, not an endpoint registry.

## Corrected status

-   listed high-risk operations: **PARTIAL/PASS at row level subject to
    V4.0.21.1 corrections**;
-   full CORE API operation inventory: **PARTIAL**;
-   P0-002 overall: **REOPENED**.

## Required closure

Before Repository Entry, create one canonical row for every CORE
read/mutation action with stable Operation ID, exact method/path,
capability, scope, step-up, execution class, precondition/idempotency,
response and error contract.
