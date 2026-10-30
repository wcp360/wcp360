# Permission & Security Closure Audit

## Model retained
- persistent RBAC;
- plans / feature lists;
- explicit allow / deny / inherit;
- account overrides;
- reseller delegation envelope;
- DEMO restriction profile;
- temporary privileged sessions;
- read-only impersonation by default;
- break-glass emergency path;
- service identities for automation.

## Closure requirements
1. Final backend permission identifiers must be frozen before implementation.
2. Every action-bearing page must map to one or more permission actions.
3. Explicit deny must remain stronger than inherited allow.
4. Context switching must never change privilege.
5. Approval does not replace execution-time authorization.
6. Automation and webhooks must not inherit the interactive user's session.
7. Support impersonation must preserve the real actor identity in all audit events.
8. Break-glass must have hard expiry and post-incident review.
9. DEMO must block execution even if a page is visible.
10. Mobile restrictions must be enforced by capability policy, not only by hidden UI.

## Audit status
Architecture is strong; final permission identifiers and complete page/action mapping remain implementation-contract dependencies.
