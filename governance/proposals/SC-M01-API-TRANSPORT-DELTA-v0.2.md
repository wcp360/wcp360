# WCP360 — API Transport Delta v0.2 — narrowed open items

**Status:** PROPOSAL / OWNER HARMONIZATION — NON-NORMATIVE
**Evidence:** governance/evidence/SC-M01-CANONICAL-API-TRANSPORT-EVIDENCE.md
**Supersedes for review:** SC-M01-API-TRANSPORT-DELTA-v0.1.md

This version stops treating already-FINAL API/Security semantics as open design questions.

## H01 — HTTP version representation

Already final:
- API version = 1.0;
- additive optional fields/filters compatible;
- required-field removal/rename, semantic change and permission/action change are breaking;
- breaking change requires new major.

Still open:
- transport representation: URL path, media type/header or another explicit mechanism.

Owner must publish one deterministic mechanism for activated endpoints.

## H02 — method and route

Still genuinely open.

For each activated API-WCP contract publish:

    METHOD =
    ROUTE TEMPLATE =
    SAFE/IDEMPOTENT HTTP SEMANTICS =
    AUTH PROFILE =
    SURFACE =

GET/HEAD/OPTIONS may not perform business mutation.

## H03 — parameter location / serialization

Already final:
- logical request schema exists;
- unknown field = VALIDATION_FAILED.

Still open:
- path/query/header/body location;
- serialization type/format;
- duplicate logical value behavior across locations.

No transport may silently accept contradictory duplicate values.

## H04 — Idempotency-Key transport

Already final:
- RK requires Idempotency-Key;
- same key + same semantic request = same logical result;
- same key + different request = IDEMPOTENCY_CONFLICT;
- RN forbids blind redispatch after UNKNOWN_OUTCOME.

Still open:
- header-only versus any body representation;
- duplicate representation equality/refusal rule;
- HTTP status for IDEMPOTENCY_CONFLICT.

Database owns durable persistence.

## H05 — shared DTO definitions

Logical names already exist.

Still open exact schemas for:
- JobAccepted;
- Mutation<T>;
- Collection<T>;
- SecretCreated;
- other shared projections actually used by SC-M01.

Each must define required/optional fields, types and success HTTP status.

JobAccepted must not imply external effect success merely because T-JOB durable acceptance succeeded.

## H06 — HTTP error projection

Already final:
- logical error_code registry;
- E-* profiles;
- public error content limited to safe error_code/message/correlation_id/safe field details;
- sensitive internals forbidden.

Still open:
- HTTP status mapping;
- media/envelope serialization;
- field-error shape;
- resource-existence masking policy.

Owner should publish one versioned error map rather than per-module ad-hoc mapping.

## H07 — HTTP authentication transport

Already final:
- AUTH-U/A/B/P/PRIV/E semantics;
- fail-closed authorization;
- browser mutation CSRF;
- exact Origin;
- session APIs same-origin by default;
- credentialed wildcard CORS forbidden.

Still open:
- exact cookie/token header representation for scoped-token flows;
- behavior if session and token are both presented;
- future cross-origin allowlist profile;
- per-route auth transport binding.

No implicit strongest-credential-wins rule should be invented.

## H08 — operation discriminators

Still open for contracts using an operation field.

For each allowed operation publish:
- enum value;
- required fields;
- forbidden fields;
- response;
- idempotency profile;
- errors;
- audit/action relationship.

Fields belonging to another operation must not be silently ignored.

## H09 — pagination

Still open:
- cursor format/opacity;
- stable ordering;
- page-size bounds;
- filter/tenant/surface binding;
- expired/invalid cursor behavior.

A cursor grants no authority.

## H10 — caching

Already final:
- authenticated HTML and sensitive APIs = no-store;
- SHOW_ONCE = no-store;
- NEVER_REVEAL = no retrieval endpoint.

Still open:
- whether any non-sensitive read API may be cached;
- validators/ETag;
- shared/private cache distinction;
- Vary rules.

No cacheable exception should be assumed.

## Minimal endpoint transport record

For each SC-M01 endpoint:

    API CONTRACT =
    METHOD =
    ROUTE =
    VERSION TRANSPORT =
    PATH PARAMS =
    QUERY =
    HEADERS =
    BODY =
    AUTH TRANSPORT =
    CSRF/ORIGIN =
    IDEMPOTENCY =
    SUCCESS STATUS =
    SUCCESS DTO =
    ERROR MAP =
    CACHE =
    PAGINATION =
    TESTS =

Fields already fixed by the canonical API/Security artifacts should reference them, not duplicate/redefine them.

## Status

    SEMANTIC API RULES = FINAL
    HTTP PROJECTION = OWNER DECISION REQUIRED
    CANONICAL API FILE CHANGED = NO
    APPLICATION TESTS EXECUTED = 0
    SC-M01 = PAUSED
