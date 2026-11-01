# Shared HTTP presentation and transport primitives

**Status: executable development request-reference and error-presentation candidate. Complete production HTTP contracts and tracing remain unfinished.**

| Source | Responsibility |
| :--- | :--- |
| [BoundarySubscriber](BoundarySubscriber.php) | Existing runtime host check, CSP, Origin-adjacent browser headers and no-store responses. |
| [RequestReference](RequestReference.php) | Fresh 128-bit random reference stored on each main Request, with no singleton request state or caller-header trust. |
| [HttpErrorSubscriber](HttpErrorSubscriber.php) | Reference lifecycle and safe main-request exceptions; API authentication redirects become JSON refusals before standard security headers are applied. |
| [HttpFailure](HttpFailure.php) | Fixed status/code/message mapping; no exception-message, path, query or payload reflection. |
| [HttpErrorResponder](HttpErrorResponder.php) | JSON/browser presentation, narrowed exception headers, optional safe diagnostic metadata and nonrecursive rendering fallback. |
| [CandidateAuthenticationEntryPoint](CandidateAuthenticationEntryPoint.php) | Existing cookie login entry point: API 401 JSON or browser login redirect. No bearer authentication is introduced. |
| [CandidateApiRequest](CandidateApiRequest.php) | Existing exact `/workbench/v1` namespace selection; Accept, credentials and similar path prefixes cannot select a different transport or surface. |
| [IdempotencyConflict](IdempotencyConflict.php) | Typed 409 distinction for the existing plan submission, without text-based exception classification. |

## Request references

Every admitted main HTTP request receives a new server-generated `X-WCP360-Request-Id` response header containing 32 lowercase hexadecimal characters. Client values in that header, `X-Request-Id`, `traceparent`, query/body data and preexisting request attributes never establish the new reference. Request attributes hold the reference; no service property retains it between FrankenPHP worker requests. Subrequests do not replace the main reference.

This reference is diagnostic metadata, never an identity, permission, tenant selector, idempotency key, job ID or audit-record ID. It does not link database audit occurrences, queues, IPC or a distributed trace. Errors optionally log only the reference, status and fixed code through an available PSR logger; no new log backend or durable audit storage is installed. Existing framework/domain logging and secret-redaction obligations remain separate.

## Existing workbench API

Successful `/workbench/v1/*` payloads and application authorization remain unchanged. Handled exception responses use this candidate envelope:

```json
{
  "error": {
    "code": "AUTHORIZATION_DENIED",
    "message": "The request is not permitted."
  },
  "request_id": "32 lowercase hexadecimal characters"
}
```

The body reference matches the response header. Status mappings reuse a bounded subset of the accepted [API Error Registry](https://github.com/yyogas/wcp360/blob/742844f16bcc25b300e67ead645c23498a7eb72b/documentation/specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md#5-error-registry): validation, authentication, authorization, missing resource, resource/idempotency conflict, rate limit, unavailable service, timeout and internal error. A typed idempotency conflict is distinct from an arbitrary 409 message. This candidate envelope is not a new canonical API schema or implementation of the full registry.

Anonymous API calls receive 401 JSON instead of the browser login page. Existing session-revocation and pending-factor redirects in this namespace are rendered as 401 or 403/MFA_REQUIRED after security has already refused access. This namespace has no resource redirects; it continues to use cookie authentication, Origin/CSRF and the existing authorization gates. It does not accept bearer tokens or grant roles during a pending challenge.

## Browser and failure behavior

Browser login/MFA redirects remain intact. Other main-request exceptions use the [official auth layout](../../../templates/errors/http.html.twig), French fixed messages and a reference the owner can quote to the instance operator. Raw exception text, SQL, stack traces, file paths, cookies, submitted credentials and another tenant's details are never copied into this presentation. The existing credential-route listener delegates to this responder before debug rendering.

Only a bounded numeric Retry-After and recognized Allow methods survive exception headers; arbitrary debugging, cookie, location and content headers do not. Existing CSP, no-referrer and no-store controls remain applied. A broken template or asset path falls back to fixed plain text and the reference. Optional diagnostic logger failure does not recursively invoke an error page or replace mandatory application audit.

An HTTP error does not prove that a mutation failed or authorize retry. Unknown external outcomes still require their domain's reconciliation. Existing explicitly returned form-validation pages, health payloads and domain responses remain owned by their controllers. Boot failures before request admission, fatal process errors, complete production API contracts, full log routing, audit/job/IPC propagation and independent acceptance remain open.

## Verification sources

- [Mapping and typed-conflict cases](../../../tests/Unit/HttpFailureTest.php).
- [Reference, namespace, header/log privacy and fallback cases](../../../tests/Unit/HttpErrorBoundaryTest.php).
- [Actual HTTP/API journeys](../../../tests/Integration/HttpErrorJourneyTest.php): authentication, idempotency, tenant/Origin/CSRF refusal, routing/host errors, branded pages, revoked sessions and audit rollback.
- [Pending administrator challenge](../../../tests/Integration/MfaJourneyTest.php) and existing credential journeys protect retained authentication behavior.

These are test definitions. Exact-source CI results are recorded in [draft PR #116](https://github.com/yyogas/wcp360/pull/116); local executable tests remain stopped by user instruction.

Domain controllers and form mappings stay in `src/Module/<Owner>/UI`; business validation, authorization and durable effects stay in their application use cases. This folder is not a second API registry, universal controller, plugin SDK or alternative authorization engine.

- [Shared foundations](../README.md)
- [Shared security](../Security/README.md)
- [Core roadmap](../../../ROADMAP.md)
