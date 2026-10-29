# Pure foundation components

These packages are implementation candidates requested on 2026-10-29. They
extend the inert foundation without changing the CLI or enabling any effect.
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
