# CDN Management Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Bind each zone/distribution/profile to the authorized tenant, domain, origin, selected provider account and current resource revision before an edge change.
2. Qualify exact provider/API/version/plan limits and separately admitted features, including Cloudflare proxy/cache rules, certificate modes, origin authentication, purge scope/rate and request/body limits; do not promise all plans or features.
3. Require validated origin TLS, hostname/SNI/certificate checks and any admitted origin authentication; visitor-edge encryption alone or an encryption-only origin mode is not verified origin identity. Review direct-origin exposure and management recovery separately.
4. Review cache keys for Host/tenant/resource, query and negotiated variants, including Vary/Accept-Encoding; require bypass for authenticated users, authorization headers, sessions, carts, admin/private APIs, cookies, private responses and state-changing methods.
5. Preserve all-request Apache authorization in the current Nginx -> Apache -> FPM target: edge-cache/direct-file stays off by default, including CSS/assets protected by nested .htaccess or directory rules.
6. Require explicit Core-owner admission/versioning and negative protected-file/auth/cookie/private-content checks before any CDN cache policy or authority handoff; connectivity or compression is not permission for public caching.
7. Qualify compression negotiation, variant separation, double-compression prevention, budgets and rollback. Secret-bearing dynamic compression needs its own risk/correctness decision and stays bypassed when unqualified.
8. Bound scoped purge/invalidation using provider receipts, replay control and measured propagation uncertainty; provider acceptance is not immediate global completion.
9. Constrain rewrites, redirects, upstream destinations and edge-worker features to admitted capabilities and reviewed code; ingress Traefik/Nginx Proxy Manager workflows remain under Network Traffic rather than creating a competing CDN owner.
10. Measure correctness, origin/offload behavior, stale responses, error rates and costs per profile; activation alone proves neither speed nor protection.
11. Exercise provider outage, credentials revocation/rotation and DNS/certificate changes with an explicit exit route; disabling orchestration preserves delivery and independent free Core DNS/TLS/origin control.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
