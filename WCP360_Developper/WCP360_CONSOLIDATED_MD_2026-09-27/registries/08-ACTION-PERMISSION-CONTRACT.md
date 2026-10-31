# WCP360 — Action / Permission Contract

Every state-changing action should define at least:
- `action_id`;
- resource type;
- scope;
- base `permission_id`;
- privilege level;
- reauthentication policy;
- approval policy;
- job type if asynchronous;
- audit event type;
- idempotency;
- failure model;
- relevant policy gates.

## Evidence-supported DNS example

`permission_id: domains.write`
→ `action_id: domains.record.update`
→ `job_type: dns.change.apply`
→ `audit_event_id: dns.record.updated`

This exact example demonstrates why those identifiers must not all live in the permission namespace.

## Process example

The source contract uses:
- action: `processes.kill`
- permission: `processes.kill`
- job: `process.kill`

The daemon allowlist includes:
- `process.term`
- `process.kill`

The final typed process graph is still blocked by `H3CM-X002`.

## Invariants
- UI visibility never grants authorization.
- Context switching never changes permission.
- Approval never replaces execution-time authorization.
- Automation uses service identities, not an interactive user's session.
- Break-glass is separate from persistent roles.
