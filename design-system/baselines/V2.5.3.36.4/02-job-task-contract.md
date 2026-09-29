# Job / Task Engine Contract

## Objective

All non-trivial state-changing operations must be observable background jobs where appropriate.

## Canonical states

`QUEUED`
→ `WAITING_DEPENDENCY`
→ `RUNNING`
→ `SUCCEEDED`

Alternative terminal states:
- `FAILED`
- `CANCELLED`
- `UNKNOWN_OUTCOME`

Transient states:
- `RETRYING`
- `PAUSED`
- `BLOCKED`

## Required job fields

- `job_id`
- `job_type`
- `actor_id`
- `effective_identity`
- `resource_type`
- `resource_id`
- `scope_id`
- `correlation_id`
- `idempotency_key` where required
- `requested_at`
- `started_at`
- `finished_at`
- `state`
- `progress`
- `attempt`
- `max_attempts`
- `dependency_ids`
- `error_class`
- `error_detail_safe`
- `result_summary`
- `audit_links`

## Idempotency

Operations that may be retried after disconnect/timeout MUST define one of:
- replay-safe;
- idempotency-key protected;
- not safely retryable.

Examples requiring idempotency keys:
- DNS mutation;
- certificate issuance request;
- backup restore;
- deploy;
- firewall apply;
- account creation;
- database creation.

## Unknown outcome

If the web/API layer loses connectivity after dispatch and cannot prove whether a privileged operation executed, the job must become `UNKNOWN_OUTCOME`, not blindly `FAILED`.

The system then performs reconciliation before retry.

## Cancellation

Cancellation must be capability-aware:
- cancellable before execution;
- cooperative cancellation during safe phases;
- non-cancellable during atomic/destructive critical sections.

## Queue isolation

At minimum:
- control-plane;
- deploy;
- backup/restore;
- database;
- mail;
- system privileged;
- monitoring/background;
- support bundle.

Long-running backup/export jobs must not starve urgent control-plane operations.