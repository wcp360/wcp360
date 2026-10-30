# V4.0.21.23 --- Global Security Acceptance Ledger

Security acceptance IDs use `SEC-<family>-NNN`.

Mandatory categories: tenant boundary, path/root containment, daemon
input validation, secret leakage, RBAC/scope bypass,
entitlement/capability bypass, lifecycle race, idempotency, SSRF,
archive traversal/bomb, open relay, DB cross-grant, cache exposure,
firewall lockout, WAF floor bypass, SSO replay, update/recovery bypass
and installer duplication.

Each case maps preconditions → Operation ID → malicious/negative input →
expected policy/error → expected no-side-effect/compensation → audit
evidence.
