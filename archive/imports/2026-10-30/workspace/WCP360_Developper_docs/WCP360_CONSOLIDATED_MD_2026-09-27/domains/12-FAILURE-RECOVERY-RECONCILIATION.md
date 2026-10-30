# WCP360 — Failure / Recovery / Reconciliation

## Mutation model

`request → validate → authorize → declare intent → execute steps → verify → commit desired result → observe/reconcile`

## Distinctions

- requested != desired != observed;
- execution success != verified success;
- timeout != failure;
- rollback != compensation;
- retry != reconciliation.

## Outcomes

- `SUCCEEDED`
- `FAILED`
- `PARTIALLY_SUCCEEDED`
- `UNKNOWN_OUTCOME`
- `CANCELLED`
- `TIMED_OUT`
- `COMPENSATION_REQUIRED`
- `RECONCILIATION_REQUIRED`

## Idempotency classes
- IDEMPOTENT;
- CONDITIONALLY_IDEMPOTENT;
- NON_IDEMPOTENT;
- UNKNOWN/BLOCKED.

## Concurrency scopes
- RESOURCE;
- HOSTING_ACCOUNT;
- TENANT;
- SERVICE;
- SERVER;
- GLOBAL.

## Transactional outbox

Current architecture direction: outbox is accepted as the durable coordination pattern where DB intent and asynchronous delivery must be coupled. Consumers still require idempotency.

## Open issue

`H3CM-X009 — Reconciliation ownership`

Must define who:
- schedules reconciliation;
- owns desired state;
- observes real state;
- authorizes repair;
- performs repair;
- audits repair.
