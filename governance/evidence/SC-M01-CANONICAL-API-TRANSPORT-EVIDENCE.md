# WCP360 — Canonical API Transport Evidence

**Status:** CURRENT CANONICAL EVIDENCE — NON-NORMATIVE ANALYSIS
**Reference main:** fa5b2e448baf9f5d493e8d8381d11805a87a2a7c
**Sources:** API Contract 1.0 + Security Baseline v1.0

Purpose: narrow AUDIT-WCP-0103 and decisions H01–H10 to the transport details that are genuinely unresolved.

No API route, HTTP method or DTO is created by this file.

## 1. Source identities

API:
- path: specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
- Git blob: 4893c5191b4928ecfa5a70f4dc14b39a8939fcea
- version: 1.0
- status: FINAL APPROVED CONTENT

Security:
- path: specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
- Git blob: 6d74f2f363524511424f839ca7262143c2af95ab
- version: v1.0
- status: FINAL APPROVED — canonical implementation authority

## 2. Already canonical: API semantic versioning

API 1.0 fixes:

    API major version = 1.0
    additive optional response field/query filter = compatible
    remove/rename required field = breaking
    semantic change = breaking
    permission/action change = breaking
    current deprecation status = D0

Therefore H01 does not need to decide whether semantic API versioning exists or what constitutes a breaking change.

Still open:
- how version 1.0 is represented at the HTTP transport layer: path, media type/header or another explicit transport mechanism.

## 3. Already canonical: authentication profiles

API 1.0 defines:

- AUTH-U = WCP-USER session or scoped token; CSRF required for browser mutations;
- AUTH-A = WCP-ADMIN session or scoped token; CSRF required for browser mutations;
- AUTH-B = authenticated surface-specific identity; USER/ADMIN surface preserved;
- AUTH-P = authenticated principal/service plus policy eligibility;
- AUTH-PRIV = AUTH-A + privileged-session authorization + required MFA/reauth;
- AUTH-E = emergency principal + MFA + break-glass entitlement + reason/reference + hard expiry.

Authorization is fail-closed and includes surface/permission/entitlement/tenant/ownership/policy/resource state/MFA/approval as applicable.

Security further fixes:
- browser mutation = valid session + synchronizer CSRF + exact expected Origin;
- invalid/missing CSRF or Origin = 403, no mutation;
- session APIs same-origin by default;
- credentialed wildcard CORS forbidden.

Therefore H07 does not need to decide the application-level authentication semantics from scratch.

Still open:
- concrete HTTP header/cookie/token transport form for scoped tokens;
- precedence/refusal rule when more than one auth mechanism is presented;
- exact CORS allowlist for any future cross-origin API;
- route-specific mapping of AUTH-* profiles.

## 4. Already canonical: retry/idempotency semantics

API 1.0 fixes:

RR:
- read-only;
- at most two transport retries;
- only before response or on 429/503;
- never authorization/validation retry.

RK:
- Idempotency-Key required for mutations;
- same key + same semantic request = same logical result;
- same key + different request = IDEMPOTENCY_CONFLICT.

RN:
- submission protected by idempotency key;
- execution never blindly redispatched after UNKNOWN_OUTCOME;
- reconciliation precedes retry.

Therefore H04 does not need to choose semantic idempotency behavior.

Still open:
- exact HTTP transport source/location of Idempotency-Key;
- whether logical idempotency_key in the registry is a wire-body property or only a registry notation;
- conflict/precedence if duplicate representations are accepted;
- HTTP status mapping.

Physical persistence remains Database #6.

## 5. Already canonical: logical error registry

API 1.0 fixes the logical error_code set, including:

    VALIDATION_FAILED
    AUTHENTICATION_REQUIRED
    AUTHORIZATION_DENIED
    ENTITLEMENT_REQUIRED
    POLICY_DENIED
    MFA_REQUIRED
    REAUTHENTICATION_REQUIRED
    APPROVAL_REQUIRED
    RESOURCE_NOT_FOUND
    RESOURCE_CONFLICT
    CONCURRENCY_CONFLICT
    IDEMPOTENCY_CONFLICT
    CANCELLATION_NOT_ALLOWED
    CONTRACT_VERSION_UNSUPPORTED
    RATE_LIMITED
    OPERATION_TIMEOUT
    DEPENDENCY_FAILURE
    SERVICE_UNAVAILABLE
    UNKNOWN_OUTCOME
    INTERNAL_ERROR
    IPC_OPERATION_NOT_ALLOWED
    IPC_CALLER_UNTRUSTED
    IPC_SCHEMA_INVALID

API also groups them into E-R / E-M / E-P / E-J / E-AUD profiles.

Security v1.0 fixes the safe public error content:

    stable safe error_code
    safe message
    correlation_id
    safe field details

and forbids:
- stack trace;
- SQL;
- filesystem path;
- command line;
- environment;
- secret/token/session ID;
- private key;
- raw upstream credential response.

Therefore H06 does not need to invent the error registry or safe-error fields.

Still open:
- exact HTTP status code for each logical error;
- response media type/envelope naming;
- optional field-error representation details;
- resource-existence masking rule for cross-tenant enumeration cases.

## 6. Already canonical: unknown-field behavior

API 1.0 fixes:

    unknown request field = VALIDATION_FAILED

Therefore H03 must preserve strict field handling.

Still open:
- which logical fields are path/query/body/header;
- whether a value may appear in more than one location;
- exact conflict/refusal behavior if transport duplicates a logical field;
- serialization types/format constraints not already explicit.

## 7. Already canonical: timeouts

API 1.0 fixes:

    T-READ = 10s
    T-WRITE = 15s
    T-JOB = 10s to durably accept/persist a job

Each contract row binds to a timeout profile/value.

Therefore transport implementation cannot arbitrarily select timeouts per route.

Still open:
- HTTP server/client timeout composition;
- relation to proxy timeout;
- relation to IPC operation deadline for downstream privileged jobs;
- response behavior at transport timeout where job acceptance/result state differs.

## 8. Already canonical: response names

The API registry already names logical response projections such as:

    JobAccepted
    Mutation<T>
    *View
    Collection
    SecretCreated

This is stronger than having no response contract at all.

Still open:
- exact fields/types for shared DTOs;
- optional/required field rules;
- serialization details;
- HTTP success status;
- link/cursor representation.

## 9. Already canonical: sensitive cache behavior

Security v1.0 fixes:

- SHOW_ONCE response = Cache-Control: no-store;
- authenticated HTML and sensitive APIs = Cache-Control: no-store;
- NEVER_REVEAL has no retrieval endpoint.

Therefore H10 is partially defined.

Still open:
- which non-sensitive/read-only APIs, if any, may be cacheable;
- validators/ETag policy if introduced;
- shared-cache behavior;
- Vary rules where applicable.

Defaulting all authenticated/sensitive traffic to no-store is consistent with Security; broader caching needs explicit authorization.

## 10. Already canonical: browser mutation transport constraints

Security requires:
- state-changing browser requests: valid session + synchronizer CSRF + exact expected Origin;
- AJAX CSRF header name: X-WCP-CSRF;
- invalid/missing CSRF or Origin: 403/no mutation/security evidence;
- GET/HEAD/OPTIONS do not perform business mutation.

Thus HTTP method choices cannot violate safe-method non-mutation.

Still open:
- actual method/route per API-WCP contract;
- which non-browser token flows are exempt from CSRF because they do not use cookie auth;
- exact route/auth transport binding.

## 11. Decision narrowing

| ID | Canonical today | Genuine open remainder |
|---|---|---|
| H01 | API semantic version 1.0 + breaking/additive rules | HTTP representation of version |
| H02 | action semantics and surface/auth profiles exist per contract | HTTP method + route template |
| H03 | logical request schema + unknown field rejection | path/query/header/body locations + serialization |
| H04 | RK/RN idempotency semantics final | header/body transport representation + HTTP conflict mapping |
| H05 | logical DTO names exist | exact DTO fields/types/status |
| H06 | error registry/profiles + safe public fields final | HTTP status/media envelope/masking details |
| H07 | AUTH-* semantics + browser CSRF/Origin/CORS baseline final | exact cookie/token headers, multi-auth rule, route profile mapping |
| H08 | logical operation field appears in contracts | per-operation discriminated required/forbidden fields |
| H09 | cursor/limit logical fields exist in read contracts | cursor encoding/order/filter binding/limits |
| H10 | sensitive/authenticated no-store rule final | explicit cacheability policy for any allowed non-sensitive reads |

## 12. QA implications

HQ cases should be narrowed:

- HQ02/HQ03 can be anchored to strict schema/VALIDATION_FAILED.
- HQ04/HQ05 are already anchored to RK idempotency semantics.
- HQ08/HQ09 are anchored to canonical CSRF/Origin behavior.
- HQ13 must distinguish durable job acceptance from external effect success, consistent with T-JOB.
- HQ14 safe error redaction is anchored by Security.
- HQ15 no-store for SecretCreated is anchored by Security.

Still blocked on owner transport decisions:
- method/route tests;
- concrete HTTP status map;
- dual-auth precedence;
- DTO fields;
- pagination/cursor;
- non-sensitive cache rules.

No PASS/FAIL is created here.

## 13. Result

    API SEMANTIC CONTRACT = FINAL
    HTTP TRANSPORT MAPPING = INCOMPLETE
    H01–H10 ALL FULLY CLOSED = NO
    REDESIGN OF IDP/AUTH/ERROR SEMANTICS = NOT REQUIRED
    TARGETED HTTP PROJECTION DECISIONS = REQUIRED
    APPLICATION TESTS EXECUTED = 0

AUDIT-WCP-0103 remains OPEN until the activated SC-M01 endpoints have deterministic transport bindings and QA evidence.
