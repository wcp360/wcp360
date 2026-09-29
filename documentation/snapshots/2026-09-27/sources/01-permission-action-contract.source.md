# Permission & Action Contract

## Objective

Define one canonical vocabulary for every user-visible or automated action.

Each action must map to:
- actor class;
- scope;
- permission identifier;
- optional policy gate;
- optional MFA/re-auth gate;
- optional approval gate;
- job type;
- audit event;
- target resource type;
- expected state transition.

## Action contract shape

Each action specification SHALL contain:

| Field | Requirement |
|---|---|
| `action_id` | Stable canonical identifier |
| `resource_type` | Target resource family |
| `scope` | Account / project / app / server / cluster / global |
| `permission_id` | Required base permission |
| `privilege_level` | normal / elevated / privileged / emergency |
| `reauth` | none / password / MFA / policy |
| `approval` | none / optional / mandatory by policy |
| `job_type` | Background execution contract if state-changing |
| `audit_event` | Required audit event identifier |
| `idempotency` | none / key-required / replay-safe |
| `mobile_policy` | allow / read-only / blocked by default |
| `demo_policy` | allow / read-only / blocked |
| `failure_model` | Expected canonical error classes |

## Examples

### `domains.record.update`
- Resource: DNS zone
- Scope: application/domain
- Permission: `domains.write`
- Privilege: normal
- Approval: optional policy
- Job: `dns.change.apply`
- Audit: `dns.record.updated`
- Idempotency: key-required for API
- Demo: blocked
- Mobile: allowed with review/confirm
- Preconditions:
  - zone active;
  - no conflicting record;
  - actor authorized for domain;
  - record validates syntactically.
- Workflow:
  `Validate → Diff → Confirm → Execute → Propagate → Audit`

### `servers.reboot`
- Resource: server
- Scope: server
- Permission: `servers.reboot`
- Privilege: privileged
- Re-auth: MFA/policy
- Approval: optional/mandatory depending deployment policy
- Job: `server.reboot`
- Audit: `server.reboot.requested`, `server.reboot.completed`
- Preconditions:
  - no blocking incident policy;
  - HA/capacity impact evaluated;
  - active privileged session valid.
- Workflow:
  `Impact review → Re-auth → Confirm → Queue job → Observe → Health verify → Audit`

### `processes.kill`
- Resource: process
- Scope: server
- Permission: `processes.kill`
- Privilege: privileged
- Re-auth: policy
- Job: `process.kill`
- Preferred alternative: graceful TERM first
- Audit mandatory.
- Direct arbitrary signal dispatch is NOT permitted through generic user input.

## Invariants

1. UI visibility never grants authorization.
2. Context switching never changes permission.
3. Approval never replaces execution-time authorization.
4. A reseller cannot grant a permission outside its own delegated envelope.
5. Explicit deny overrides inherited allow.
6. DEMO mode is an additional restriction layer.
7. Automation uses service identities, not an interactive user's session.
8. Break-glass is separate from persistent role assignment.
