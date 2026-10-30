# V4.0.21.23 --- Global P0 Acceptance Ledger

P0 IDs use `P0-<family>-NNN`.

A P0 cannot be closed by prose alone. Closure requires: source
requirement, resource, Operation ID, actor/scope, attack/failure,
expected stable error/decision, expected privileged side-effect
behavior, audit event/evidence and executable-test target for the future
implementation.

Initial mandatory P0 groups: AUTH/tenant isolation; filesystem escape;
daemon arbitrary-command/path; open mail relay; DB cross-tenant grant;
secret/private-key exposure; Redis/cache unsafe exposure; deployment
last-known-good; corrupt backup false-positive; wrong-tenant restore;
firewall admin lockout; WAF floor bypass; stale malware signature
false-health; update interruption; installer rerun; Doctor false READY;
recovery root escape; disk-full integrity; duplicate Job after reboot.
