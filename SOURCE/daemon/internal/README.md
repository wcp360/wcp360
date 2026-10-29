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
No HTTP server, endpoint, cookie issuance, CSRF token generation/storage, rate
limiter, database session adapter or API response mapping is implemented here.

Regression tests cover shortened expiry, cross-session child reuse, missing
session references, duplicate headers, CSRF rotation, cross-origin/lookalike
hosts, malformed trusted configuration and safe-method mutation attempts.
