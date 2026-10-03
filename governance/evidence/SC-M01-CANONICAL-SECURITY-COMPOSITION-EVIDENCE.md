# WCP360 — Canonical Security Composition Evidence

**Status:** CURRENT CANONICAL EVIDENCE — NON-NORMATIVE ANALYSIS  
**Reference main:** dd221a625dc087a548e478822f1a8bf223561be9  
**Sources:** Security Baseline v1.0 + Permission Registry v1.0

Purpose: narrow AUDIT-WCP-0117/0118 and the S01–S09 decision set to the parts that are genuinely unresolved.

This file does not create a new Security rule.

## 1. Source identities

Security:
- path: specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
- Git blob: 6d74f2f363524511424f839ca7262143c2af95ab
- status: FINAL APPROVED — canonical implementation authority

Permission Registry:
- path: specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
- Git blob: 81f3d425f36470167aba4bfb7ba069268626342f
- status: FINAL APPROVED — canonical implementation authority

## 2. Already canonical: final authorization is an intersection

Security v1.0 defines authorization as the intersection of:

    authenticated_identity
    surface
    action
    permission
    role_constraints
    entitlement
    resource
    tenant_boundary
    ownership/delegation
    policy/context
    authentication_strength
    resource_state
    approval_when_required

It also states:

- authorization occurs before mutation, effectful enqueue or privileged dispatch;
- sensitive async execution revalidates mutable security invariants before effect;
- approval never replaces execution-time authorization;
- job/event/audit/IPC identifiers never grant authority;
- UI visibility never grants authority.

Therefore a role grant or account override cannot, by itself, be treated as final authorization when another mandatory Security operand fails.

What remains open is not whether the final Security decision is an intersection. It is the precise semantics of the Permission Registry resolution order inside the permission operand, especially the positive account-override relationship to plan/feature/entitlement.

## 3. Already canonical: Permission Registry resolution and hard restrictions

Permission Registry v1.0 fixes:

    explicit DENY
    > account override
    > role grants
    > plan / feature envelope
    > default policy

It also fixes:

- DEMO is an additional hard restriction;
- reseller delegation cannot exceed the reseller's effective envelope;
- temporary privilege does not mutate persistent roles;
- approval never replaces execution-time authorization;
- UI visibility never grants permission;
- context switching never changes permission;
- every state-changing action references one base permission_id plus optional policy gates.

Open question: exact positive account-override ceiling and its interaction with plan/feature/entitlement still need owner clarification because the ordering alone does not specify whether an override may create capability beyond a commercial/entitlement envelope.

## 4. Already canonical: USER / ADMIN surface and session separation

Security v1.0 states:

    WCP-USER session != WCP-ADMIN session

and requires distinct:

- origin;
- cookie name;
- server-side session namespace;
- surface claim;
- runtime Unix identity.

Cookie identities are separately named:
- __Host-wcp_user_session
- __Host-wcp_admin_session

Cookie policy is Secure, HttpOnly, SameSite=Strict, Path=/, host-only, no Domain attribute.

Therefore cross-surface authentication fallback is not an acceptable implementation model.

Open questions remain:
- admissible deployment topology proving cookie non-exposure;
- exact behavior when both surface cookies arrive in one request;
- exact externally visible response for a wrong-surface credential without leaking the other session;
- proxy/ingress proof that a cookie was not merely ignored after being forwarded.

## 5. Already canonical: CSRF and Origin

For state-changing browser requests, Security v1.0 requires:

    valid session
    + synchronizer CSRF token
    + exact expected Origin

AJAX uses X-WCP-CSRF.

Invalid or missing CSRF or Origin:
- HTTP 403;
- no mutation;
- security evidence.

GET/HEAD/OPTIONS must not create business mutation.

Session APIs are same-origin by default and credentialed wildcard CORS is forbidden.

Therefore the core browser mutation rule is already final.

Open details:
- explicit cross-surface CSRF-token behavior as a named test;
- reverse-proxy handling of Origin;
- WSS reconnect/session-revocation details for long-lived channels;
- exact topology-dependent expected Origin values.

## 6. Already canonical: logout and current-state revocation

Security v1.0 states:

- logout revokes server session, cookie, CSRF state and session-bound long-lived channels;
- ADMIN logout also revokes privileged, impersonation and break-glass contexts;
- role/permission/Tenant/delegation revocation is enforced from current authoritative state and is not deferred until session expiry.

Therefore stale session lifetime cannot be used as a reason to postpone permission/delegation revocation.

Open detail: exact data representation and cache invalidation mechanism remain implementation/data-model concerns.

## 7. Already canonical: reseller delegation ceiling

Security v1.0:
- reseller delegation never transfers ownership;
- reseller delegation cannot exceed reseller effective envelope.

Permission Registry v1.0 repeats the effective-envelope ceiling.

Thus the semantic ceiling is final.

Open details:
- exact calculation/source representation of effective envelope;
- exact persisted delegation/role assignment relations, dependent on Database #6;
- execution-time freshness/revalidation representation;
- external error mapping.

## 8. Already canonical: temporary privilege and approval

Permission Registry:
- temporary privilege does not mutate persistent roles;
- approval never replaces execution-time authorization.

Security:
- privileged ADMIN context requires privilege.elevate, fresh MFA, explicit scope, audit start and expiry;
- privilege never bypasses action permissions.

Thus the no-bypass semantic rule is final.

Open details:
- storage/lifecycle implementation;
- exact state transition/error codes where not already defined;
- dependency on privileged-session context persistence.

## 9. Already canonical: unknown privileged binding fails closed

Security v1.0 states:

    Unknown permission/action/IPC binding: DENY, no side effect, audit evidence.

Thus fail-closed handling of unknown binding is final.

Open detail:
- HTTP projection/safe error mapping at the public API layer;
- exact internal representation of NOT_GRANTED vs explicit DENY if Security owner wants separate states.

## 10. Decision narrowing

| ID | Previous question | Canonical evidence | Genuinely open remainder |
|---|---|---|---|
| S01 | admissible USER/ADMIN topology | separate origins/sessions/cookies required | actual host/routing topology + non-exposure proof |
| S02 | cross-surface cookie handling | separate session namespace/cookie/surface claim final | simultaneous/wrong-cookie response + ingress non-exposure behavior |
| S03 | CSRF/Origin/WSS | session + CSRF + exact Origin, 403/no mutation final | cross-surface named tests, proxy/WSS lifecycle specifics |
| S04 | non-exposure definition | none beyond surface isolation | measurable browser→ingress→backend non-exposure criterion |
| S05 | permission vs final AUTHZ | final AUTHZ intersection + permission resolution order final | exact positive override/plan/entitlement composition + internal algorithm |
| S06 | account overrides | explicit DENY precedence known | positive override ceiling and representation |
| S07 | reseller delegation | effective-envelope ceiling + no ownership transfer + current-state revocation final | physical calculation/storage/revalidation details |
| S08 | temporary privilege/approval | no role mutation, no permission bypass, fresh MFA/scope final | state representation/lifecycle/error projection |
| S09 | absent/DENY handling | unknown binding DENY/no effect/audit final | internal NOT_GRANTED distinction + external safe mapping |

## 11. QA impact

Existing SQ cases should be read as follows:

- SQ03/SQ04/SQ06/SQ07: expected fail-closed behavior is already anchored in current Security rules; only transport/topology details may remain.
- SQ08/SQ09/SQ10/SQ12/SQ13/SQ15/SQ16: core DENY/non-bypass expectations are already canonical.
- SQ11: negative override precedence is strongly anchored by explicit DENY > account override.
- positive account-override cases remain owner-blocked until S06 is ratified.
- browser non-exposure cases remain topology-blocked until S01/S04 are ratified.

No PASS/FAIL is created here.

## 12. Result

    SECURITY CORE SEMANTICS RECOVERED/FINAL = YES
    S01–S09 ALL FULLY CLOSED = NO
    SEMANTIC REDESIGN REQUIRED = NO
    TARGETED HARMONIZATION / IMPLEMENTATION DETAILS REQUIRED = YES
    CANONICAL SECURITY FILE CHANGED = NO
    APPLICATION TESTS EXECUTED = 0

AUDIT-WCP-0117 and 0118 remain OPEN until owner decisions and executable proof satisfy the closure matrix.
