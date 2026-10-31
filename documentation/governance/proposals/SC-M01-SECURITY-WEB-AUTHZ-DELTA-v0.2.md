# WCP360 — Security Web/Authz Delta v0.2 — narrowed open items

**Status:** PROPOSAL / OWNER HARMONIZATION — NON-NORMATIVE  
**Reference evidence:** governance/evidence/SC-M01-CANONICAL-SECURITY-COMPOSITION-EVIDENCE.md  
**Supersedes for review:** SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md

This version narrows S01–S09. It does not ask Security to redesign semantics already FINAL in Security Baseline v1.0 / Permission Registry v1.0.

## S01 — admissible USER/ADMIN topology

Already final:
- separate origins;
- separate cookie names;
- separate server-side session namespaces;
- separate surface claims;
- distinct runtime identities.

Still open:
- exact deployment topology;
- whether hostnames are distinct;
- ingress/reverse-proxy routing;
- objective proof that cookies are not exposed across surfaces.

Owner output:

    S01 = ACCEPT TOPOLOGY / AMEND
    USER ORIGIN =
    ADMIN ORIGIN =
    INGRESS RULE =
    COOKIE NON-EXPOSURE MECHANISM =
    TEST ENVIRONMENT =

No hostname is invented by this proposal.

## S02 — cross-surface cookie handling

Already final:
- USER session != ADMIN session;
- distinct cookie names/namespaces/surface claims;
- no cross-surface credential authority is permitted by the canonical model.

Still open:
- deterministic behavior if both cookies are physically received;
- wrong-surface cookie response without leaking session existence;
- whether ingress must strip the unrelated surface cookie in addition to backend ignoring it.

Suggested owner invariant:

    each surface accepts only its own session namespace/claim
    no fallback from missing own credential to other-surface credential

This is an explicit harmonization of existing rules, not a new privilege model.

## S03 — CSRF / Origin / WSS

Already final:
- state-changing browser request requires valid session + synchronizer CSRF + exact expected Origin;
- invalid/missing CSRF or Origin = 403/no mutation/security evidence;
- GET/HEAD/OPTIONS no business mutation;
- session APIs same-origin by default;
- credentialed wildcard CORS forbidden;
- logout revokes session-bound long-lived channels.

Still open:
- explicit cross-surface CSRF token test behavior;
- reverse-proxy preservation/validation of Origin;
- WSS authentication/revalidation/reconnect detail for active foundation endpoints.

No new CSRF model is required.

## S04 — measurable non-exposure

Still genuinely open.

Security/DevOps must define which level is required:

    browser did not select cookie
    ingress did not receive cookie
    proxy did not forward cookie
    backend ignored/rejected cookie

AUDIT-WCP-0117 asks for non-exposure, so a backend-only rejection test is insufficient if the owner intends network non-exposure.

## S05 — permission resolution versus final AUTHZ

Already final:
- Permission Registry resolution order;
- Security final AUTHZ is an intersection containing permission, entitlement, tenant, ownership/delegation, auth strength, state and approval;
- approval does not replace execution-time authorization;
- identifiers/UI visibility do not grant authority.

Therefore:

    permission-resolution ALLOW != final authorization success

Still open:
- exact internal representation/algorithm;
- exact positive account-override relation to plan/feature/entitlement;
- whether NOT_GRANTED is represented separately from DENY.

No first-match-ALLOW implementation is permitted if it skips mandatory Security operands.

## S06 — account override ceiling

Already final:
- explicit DENY outranks account override.

Still genuinely open:
- whether a positive override may exceed plan/feature envelope;
- whether it may create/modify entitlement versus only permission grant state;
- exact representation/audit/revision semantics.

Owner must publish a positive-override ceiling.

## S07 — reseller delegation

Already final:
- delegation != ownership;
- reseller delegation cannot exceed reseller effective envelope;
- revocation is enforced from current authoritative state, not session expiry.

Still open:
- exact computation and persisted representation of effective envelope;
- exact revalidation/cache behavior;
- DB relationships/indexes/constraints from #6;
- safe public error projection.

The semantic ceiling itself is not a design choice anymore.

## S08 — temporary privilege / approval

Already final:
- temporary privilege does not mutate persistent roles;
- approval never replaces execution-time authorization;
- privileged ADMIN context requires privilege.elevate, fresh MFA, scope, audit and expiry;
- privilege never bypasses action permission.

Still open:
- physical/session-context state representation;
- exact lifecycle persistence;
- specific transport/error projections not already defined.

No superuser bypass model is open for debate.

## S09 — absent/unknown/DENY representation

Already final:
- unknown permission/action/IPC binding = DENY, no side effect, audit evidence.

Still open:
- whether internal NOT_GRANTED is distinct from explicit DENY;
- safe external HTTP/error mapping;
- resource-existence masking behavior where cross-tenant enumeration risk applies.

## QA status

The sixteen SQ cases remain local proposal labels and are not canonical TEST IDs.

Owner/QA may move individual cases from blocked-on-semantics to ready-for-implementation only after the remaining transport/topology/representation decisions are ratified.

    SECURITY REDESIGN = NOT REQUIRED
    OWNER HARMONIZATION = REQUIRED
    CANONICAL FILE CHANGE = NONE IN THIS PROPOSAL
    APPLICATION EXECUTIONS = 0
