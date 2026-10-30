# WCP360 — Structural Decisions

## F-001 — Typed identifier namespaces
Distinct namespaces:
- `permission_id`
- `action_id`
- `job_type`
- `event_id`
- `audit_event_id`
- `ipc_operation_id`
- `error_code`

Identity is **value + namespace**. Lexical equality does not imply semantic identity.

## F-002 — Authorization
Authorization is explicit and fail-closed. Relevant dimensions include:
- authenticated subject;
- trusted identity;
- surface;
- role;
- permission;
- entitlement where applicable;
- tenant scope;
- ownership/delegation;
- resource scope;
- policy/context;
- resource state;
- action constraints.

UI visibility never grants authorization.

## F-003 — Privileged execution
Target architecture:

`backend → wcpd(non-root) → bounded authenticated IPC → wcp-privd(root minimal) → explicit bounded primitive`

Generic privileged execution is forbidden.

## F-004 — Unix identity isolation
Logical identities remain distinct:
- WCP-USER runtime;
- WCP-ADMIN runtime;
- `wcpd`;
- `wcp-privd`;
- hosting/tenant identities.

## F-005 — IPC trust boundary
- Web/PHP direct to `wcp-privd` is forbidden.
- At least backend→wcpd and wcpd→wcp-privd trust relationships exist.
- Exact socket paths/modes/peer authentication belong to API/Security/DevOps.

## F-006 — WCP-USER / WCP-ADMIN boundary
Distinct security/application surfaces even if some code is shared.

`shared code != shared security context`

## F-007 — Database boundary
`control-plane PostgreSQL != hosted database engines != tenant/application database contents`

## F-008 — Canonical authority
`CANONICAL CLAIM != BASELINE MEMBERSHIP`

Specialized owners own domain content; MASTER controls cross-domain membership/readiness.
