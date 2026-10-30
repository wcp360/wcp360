# V4.0.21.21 --- WAF / ModSecurity-like Adapter Contract

Canonical WAF policy supports mode, ruleset provider/version, exclusions
scoped to site/path/rule and finding/event references.

Platform mandatory rules/security floor cannot be disabled by tenant.
Exclusions are narrow, reasoned, expiring-capable and audited.
Detection-only and blocking states are distinct.

Ruleset update follows stage/validate/canary-capable
apply/health/rollback where provider supports it. Raw arbitrary server
config is not tenant input.
