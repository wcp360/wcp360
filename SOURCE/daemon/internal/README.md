# Pure foundation components

These packages are implementation candidates requested on 2026-10-29. They
extend the inert foundation with `wcpd --contracts` for offline inventory.
The reported counts are contract metadata, not implemented endpoint counts.
No listener or system effect is enabled; `--check` still reports disabled transport.
They require Engineering and Security review before integration. PR #108's
previous approvals do not apply to this new code. No production CODE-BASELINE
or independent E3 review is declared.

## contracts

Immutable, typed, source-derived catalog: 86 API records, 82 permission records,
47 IPC operation records and 23 error codes. All table cells are preserved,
including action/operation names that differ. There are no aliases or inferred
API-to-privileged-operation bindings. Records are metadata, not authorization.
No request payload validator or endpoint handler is implied by storing schemas.

Regenerate from repository root:

    python3 SOURCE/daemon/tools/contracts/generate.py
    python3 SOURCE/daemon/tools/contracts/generate.py --check

Generator uses Python standard library only. This is a development generator,
not a new product runtime dependency. Input Git blob hashes are pinned in the
generator and generated JSON. Source drift requires owner-reviewed pin changes.
Go tests verify hashes and every generated row against the actual source files.

Read retry decisions implement only RR with a maximum of two transport retries,
429/503 handling, no retry for authentication/authorization/validation errors,
no retry after exposing a response, and no automatic mutation/UNKNOWN_OUTCOME
redispatch. Durable RK/RN handling remains unimplemented.

## sessionpolicy

Pure time/surface predicates from Security v1.0 section 13: distinct cookie
names; USER/ADMIN idle and absolute lifetimes; mandatory ADMIN and policy-driven
USER MFA; high-risk MFA freshness; ADMIN privileged-child idle/absolute expiry.
No cookie is issued and no session is authenticated by these functions. Inputs
must be server-loaded, authenticated records bound to the correct session and
surface. MFAAt must be an event already verified and bound by authentication.
The adapter must verify login MFA, identity, CSRF, exact Origin, revocation and
resource authorization. Do not deserialize client claims into these structs.

Clock anomalies and exact expiration boundaries reject. Fresh MFA follows the
canonical inclusive <= age boundary. Validation never updates activity or
extends a session. Read clocks from a trusted server source.

## Not implemented by these packages

Backend transport; session issuance/storage; full RBAC/entitlement evaluation;
HTTP/API handlers; PostgreSQL migrations; durable jobs/idempotency/outbox/audit;
control sockets; privileged dispatcher; service adapters; UI; installer/release.
The 12 parent/provisioning bindings remain unresolved. Neither package bypasses
them. Session checks do not replace execution-time authorization.

## Validation

Go tests cover source provenance, registry field equality, namespace confusion,
unknown lookup rejection, immutable concurrent reads, corrupted metadata,
surface crossing, revocation, future timestamps, lifetime/MFA boundaries and
privileged-child restrictions. Tests execute with the existing pinned daemon
CI. These are component unit tests, not the canonical 199-case product suite.

## consistency

Pure predicates from Database Model v1.0, blob
`2660e057bad1d00592c57f354b1f3745ccccbe7d`:

- TX-REV-01: positive revisions, exactly one increment, stale expected revision
  -> `CONCURRENCY_CONFLICT`, exhausted BIGINT counters reject without wrapping.
- TX-IDEMP-01: GLOBAL/ACCOUNT/TENANT composite identity, exact opaque keys and
  namespaces, semantic fingerprint equality/conflict without overwriting input.
- Lease contract/concurrency: exact lease/target/worker/fencing identity,
  authoritative expiration, expired-lease token increment, stale result denial.

These are transactional adapter building blocks, not persistence. A positive
comparison does not acquire an idempotency row or authorize replay. In particular
RUNNING/UNKNOWN_OUTCOME and unresolved reconciliation cannot be made retryable
by expiry. Request canonicalization/fingerprint algorithm is not defined here.
IDs are already-decoded UUID values; boundary validation and foreign-key
existence remain adapter responsibilities.

Revision updates and lease reclaim/result writes MUST perform their comparisons
atomically against the current DB row in the same transaction as the write.
Checking a snapshot in Go and later writing unconditionally is forbidden.
Tests simulate competing snapshots; no PostgreSQL locking, SKIP LOCKED,
transactional race, migration or durability PASS is claimed.

## Session consolidation and browser mutation prerequisites

Session validation now requires a nonzero persisted session reference and the
stored `expires_at`, in addition to the canonical idle/absolute caps. A shorter
stored expiry wins; a later stored expiry cannot extend those caps. Privileged
contexts carry an explicit parent session reference and cannot be transferred
to another ADMIN session, even with identical timestamps. These are internal
DTO fields, not a new DB schema or a caller-supplied authority.

The future session adapter must load these values from the correct surface
store, bind MFA proof to the authenticated session, map a NULL last_seen_at to
the applicable creation/initial-activity rule, and enforce rotation/revocation.
Raw bearer tokens must not be used as the internal persisted row reference.
Session errors now share the canonical `contracts.ErrorCode` type.

`browserpolicy.Check` composes session validation with exact trusted-origin
matching and constant-time synchronizer-token comparison, from Security v1.0
section 13. Expected Origin comes from surface configuration, never incoming
Host/forwarded headers. The adapter must pass all Origin/X-WCP-CSRF header values;
missing, repeated or ambiguous values are rejected. No automatic trimming,
origin normalization, wildcard or same-domain fallback is allowed.

Supported mutation verbs are explicitly POST/PUT/PATCH/DELETE in this candidate;
GET/HEAD/OPTIONS and unknown verbs cannot enter the mutation path. HTTPS-only
origin configuration and this restricted method list require normal Security
review with the rest of this new implementation. A successful check is only a
browser prerequisite, not action/resource authorization or a durable audit.
No HTTP server, endpoint, session creation, CSRF token generation/storage, rate
limiter, database session adapter or API response mapping is implemented here.

Regression tests cover shortened expiry, cross-session child reuse, missing
session references, duplicate headers, CSRF rotation, cross-origin/lookalike
hosts, malformed trusted configuration and safe-method mutation attempts.

## Session cookie serialization

`browserpolicy.SessionCookie` produces a single Set-Cookie value with the
canonical surface-specific name, Secure, HttpOnly, SameSite=Strict and Path=/.
There is no Domain, Expires or Max-Age option for normal session cookies.
The concrete candidate token encoding is 32 cryptographically random bytes
encoded as unpadded base64url (256 bits, above the canonical 128-bit minimum).
This encoding is an implementation choice subject to Security review, not a
change to the canonical contract. An all-zero uninitialized secret is rejected;
nonzero input does not establish unpredictability. The session adapter MUST
obtain cryptographic randomness, persist only a verifier, commit the correct
surface/session binding, and handle rotation before delivering the header.
Returned headers contain credentials and must never enter logs or diagnostics.

`ClearSessionCookie` uses the same name/path/security flags and explicitly
expires the browser cookie. Logout must ALSO revoke the stored session, CSRF
state and session-bound channels; ADMIN logout revokes privileged, impersonation
and break-glass contexts. Clearing a cookie alone does not implement logout.

Tests parse the actual headers with Go's HTTP cookie parser, check both surfaces,
nonpersistent issuance, exact deletion scope, secret round trips, every byte
value against header injection, unknown surfaces and empty-secret rejection.
Production imports remain pure: encoding/base64 is added, net/http is test-only.

## Authoritative session lookup orchestration

`browserpolicy.AuthenticateSession` decodes exactly one canonical 43-character
unpadded base64url cookie into the candidate 32-byte secret, calls SessionStore
for the trusted surface, then applies sessionpolicy.Validate. Duplicate,
malformed, noncanonical and zero credentials never reach the store. Each call
reloads server state; no session cache or LastSeen write is introduced. Failed
lookups/policy checks return a zero Session. Storage errors and cancellations
map to SERVICE_UNAVAILABLE without exposing database errors or credentials.

SessionStore is an internal adapter interface, not a DB schema or public API.
Its implementation MUST verify the secret using a persisted verifier, isolate
USER/ADMIN namespaces, honor cancellation, and return current state. No concrete
verifier algorithm, PostgreSQL implementation or HTTP handler is supplied.
The HTTP adapter must preserve all occurrences of the configured cookie name
and reject malformed Cookie headers before this call. Authentication success
still requires current authorization and mutation-specific Origin/CSRF checks.
Writes must recheck revocation and permissions at their transactional boundary;
a valid read cannot authorize a later write after revocation.

Tests use a fake store to verify round trips from the real cookie renderer,
reloading after revocation, duplicate rejection before I/O, surface mismatch,
expiry, MFA, missing records, backend failures and cancellation. These are
component orchestration tests, not PostgreSQL or browser end-to-end evidence.
