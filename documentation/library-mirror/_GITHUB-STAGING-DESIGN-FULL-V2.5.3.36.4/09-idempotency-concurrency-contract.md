# Idempotency & Concurrency Contract

## Idempotency

Each state-changing operation must declare:
- idempotent;
- idempotency-key protected;
- unsafe to retry.

## Optimistic concurrency

Config/resources that may be edited concurrently should expose a version/revision token.

A stale update must produce `CONFLICT`, not overwrite silently.

## Examples requiring concurrency protection

- DNS zone edits;
- firewall policy;
- service configuration;
- plan/feature lists;
- user role assignments;
- system settings;
- notification routing rules.

## Job concurrency

The scheduler must support:
- per-resource serialization where required;
- per-queue concurrency;
- mutual exclusion for incompatible operations.

Examples:
- do not run two restores on the same database;
- do not reboot a server while a conflicting update/reboot job is active;
- do not apply two firewall policies concurrently;
- do not promote two database replicas simultaneously.

## Lock visibility

If an operation is blocked by another job, UI/API should expose:
- blocking job;
- resource;
- expected next state;
- whether cancellation is possible.