# V4.0.21.16 --- Core API / CLI / UI Parity

All three surfaces invoke the same Operation ID.

REST resources expose stable IDs and optimistic concurrency/version
where mutating stale state matters. CLI returns stable exit/error codes
and supports dry-run for impact-bearing operations. UI shows
denied/disabled reasons from policy reason codes, not duplicated
client-side authorization.

No WCP-ADMIN-only hidden endpoint may bypass the canonical policy
engine. Bootstrap/emergency paths remain separately scoped, time-bounded
and audited.
