# V4.0.21.6 --- Identity/RBAC Acceptance & Security Matrix

## P0 acceptance

-   unknown user and wrong password have enumeration-safe public
    behavior;
-   locked/suspended/disabled principal cannot establish a usable
    session;
-   session fixation is prevented;
-   revoked session/token is rejected server-side;
-   role binding cannot cross tenant scope;
-   reseller cannot grant above ceiling;
-   account admin cannot grant platform permission;
-   delegation cannot exceed delegator authority;
-   expired delegation is denied;
-   step-up expires and is operation-class aware;
-   removing MFA cannot violate privileged security floor;
-   recovery challenge is single-use and expires;
-   token raw secret is not retrievable after creation;
-   service account cannot use interactive login unless explicitly
    permitted;
-   impersonation preserves initiator in audit;
-   impersonation cannot export secrets or weaken MFA/security floor by
    default;
-   API/CLI/UI produce the same authorization decision for the same
    actor/scope/operation;
-   authorization denial does not leak cross-tenant resource existence;
-   disabling principal invalidates access according to policy;
-   concurrent role/permission edits use version/conflict handling.
