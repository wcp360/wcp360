# V4.0.21.15 --- Negative Security Scenario Gate

P0 négatifs : cross-tenant IDOR, path traversal/symlink escape,
arbitrary root action, command injection, config injection, secret
leakage in logs/argv, CSRF/session theft/replay, MFA bypass, privilege
escalation through plan, reseller ceiling bypass, quota bypass, webhook
replay, archive bomb, malicious Git hook/dependency script, container
escape policy violation, restore into wrong tenant, firewall lockout,
SSRF from health/webhook/import, unsafe URL fetch, race/double-submit
and stale-version overwrite.

Chaque scénario doit mapper à operation/resource/error/audit/acceptance
IDs.
