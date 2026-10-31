# WCP360 — Canonical Web / Session Security Evidence

**Status:** CURRENT CANONICAL EVIDENCE — NON-NORMATIVE SUMMARY
**Reference:** Security Baseline v1.0 in main
**Related findings:** AUDIT-WCP-0117, AUDIT-WCP-0118
**Related decisions:** S02, S03, H07

This file summarizes what is already current canonical Security content so owners do not redesign settled behavior.

It does not choose production hostnames, ports, reverse-proxy topology or WSS routing.

## 1. Surface/session separation already final

Security v1.0 requires:

    WCP-USER session != WCP-ADMIN session

and separate:

    origin
    cookie name
    server-side session namespace
    surface claim
    runtime Unix identity

Canonical cookie names:

    USER  = __Host-wcp_user_session
    ADMIN = __Host-wcp_admin_session

Canonical cookie attributes:

    Secure
    HttpOnly
    SameSite=Strict
    Path=/
    host-only
    no Domain attribute

Canonical session lifetime/auth-strength distinctions are separately defined for USER and ADMIN.

Session identifiers are opaque, >=128-bit unpredictable and rotate after login, MFA/auth-strength changes, privilege elevation, impersonation transitions and recovery/reset flows.

Normal session authentication is never stored in URL/query/fragment/localStorage/sessionStorage.

## 2. CSRF / Origin already final

For a state-changing browser request:

    valid session
    + synchronizer CSRF token
    + exact expected Origin

AJAX header:

    X-WCP-CSRF

Invalid/missing CSRF or Origin:

    403
    no mutation
    security evidence

GET/HEAD/OPTIONS must not produce business mutation.

These are not proposal choices.

## 3. CORS already final at baseline

Session APIs are same-origin by default.

Forbidden:

    credentialed wildcard CORS

Any future cross-origin API requires:

    separate API-auth contract
    exact origin allowlist

Therefore H07/S03 do not need to decide whether cookie-session APIs are broadly cross-origin. They are not.

## 4. S02 corrected classification

S02 asks about cross-surface cookie handling.

Already final:

- USER and ADMIN are different sessions;
- cookie names are different;
- server-side session namespaces are different;
- surface claims are different;
- origins are different;
- runtime identities are different;
- no cookie Domain attribute;
- host-only cookies;
- wrong surface cannot legitimately become the other surface by changing UI/routes;
- Security final AUTHZ still checks surface.

Still requiring explicit owner/implementation rule:

- exact behavior when only the wrong-surface cookie arrives at an endpoint;
- exact behavior when both cookie names arrive;
- whether the wrong-surface cookie is ignored, rejected, or recorded as security evidence;
- safe external error projection;
- tests at browser → ingress → proxy → backend → auth acceptance layers.

Therefore:

    S02 = OPEN / STRONGLY CONSTRAINED BY CANONICAL SECURITY

No implementation may create heuristic fallback or cross-surface credential aliasing.

## 5. S03 corrected classification

S03 asks about cross-surface CSRF/Origin/WSS behavior.

Already final:

- mutation requires valid surface session + synchronizer token + exact expected Origin;
- invalid/missing CSRF or Origin → 403/no mutation/security evidence;
- same-origin session API baseline;
- wildcard credentialed CORS forbidden;
- CSP connect-src only permits explicit current-surface WSS endpoints.

Still requiring topology/protocol clarification:

- exact production expected Origin values;
- reverse-proxy handling of Origin/Forwarded headers;
- whether/how WSS re-authenticates or revalidates after session rotation/revocation;
- cross-surface WSS endpoint mapping;
- safe handling where proxy and app disagree on scheme/host;
- test topology proving no trust is created by proxy rewriting.

Therefore:

    S03 = OPEN / STRONGLY CONSTRAINED BY CANONICAL SECURITY

Owners must not reopen the baseline requirement for exact Origin + synchronizer CSRF.

## 6. H07 impact

HTTP transport/auth work must consume the already-final browser rules.

For cookie-session authentication:

    exact surface session
    exact surface Origin
    CSRF on mutation
    same-origin baseline

For non-cookie token authentication, the API owner must still define the transport and multi-auth conflict rule.

Open H07 work is therefore narrower:

- token header/type;
- session + token simultaneous presentation;
- deterministic selection or rejection rule;
- CORS for separately authorized cross-origin token APIs.

H07 must not weaken browser-session CSRF/Origin requirements.

## 7. AUDIT-WCP-0117 impact

The audit finding remains open because:

- distinct origins do not by themselves prove cookie non-exposure;
- ports alone are not a cookie boundary;
- production hostnames/routing/proxy behavior are not fixed;
- QA must observe the full browser/ingress/backend path.

But the owner does **not** need to redesign:

- cookie names;
- cookie attributes;
- session namespace separation;
- surface claims;
- exact Origin requirement;
- synchronizer CSRF;
- same-origin baseline.

## 8. Required owner work

Security + DevOps:

    topology =
    exact USER origin =
    exact ADMIN origin =
    proxy trust / forwarded-header policy =
    wrong-cookie behavior =
    simultaneous-cookie behavior =
    WSS surface/revalidation policy =
    non-exposure criterion =

QA:

- browser selected cookies;
- ingress observed cookies;
- proxy forwarded cookies;
- backend received cookies;
- backend accepted credential;
- exact Origin/CSRF negative tests;
- session rotation/revocation/WSS behavior when implemented.

## 9. Current state

    CANONICAL SECURITY MODIFIED = NO
    S02 = OPEN / STRONGLY CONSTRAINED
    S03 = OPEN / STRONGLY CONSTRAINED
    H07 = OPEN / PARTIALLY DEFINED
    PRODUCTION HOSTNAMES = NOT CHOSEN
    APPLICATION TEST EXECUTION = 0
    SC-M01 = PAUSED
