# CDN Management Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Bind each zone/distribution/profile to the authorized tenant, domain, origin, selected provider account and current resource revision before requesting an edge change.
2. Qualify origin TLS, certificate/hostname validation, SNI, origin authentication, direct-origin exposure policy and management-recovery access without disabling mandatory checks.
3. Review cache keys for Host/tenant/resource, query and negotiated variants, including Vary/Accept-Encoding; require bypass for logged-in users, authorization headers, sessions, carts, admin/private APIs, cookies, private responses and state-changing methods to prevent poisoning or cross-account disclosure.
4. Preserve all-request Apache authorization in the current Nginx -> Apache -> FPM target: default edge-cache/direct-file remains disabled, including for CSS/assets protected by nested .htaccess or directory access rules.
5. Require explicit Core-owner admission/versioning and negative protected-file/auth/cookie/private-content tests before any CDN cache policy or authority handoff; compression and CDN connectivity do not authorize public caching.
6. Qualify compression negotiation, variant isolation, double-compression prevention, effective resource budgets and supported rollback. Secret-bearing dynamic compression requires its own risk/correctness decision and must remain bypassed when unqualified.
7. Implement bounded scoped purge/invalidation with provider receipts, replay handling and explicit propagation uncertainty; acceptance is not global purge completion.
8. Constrain rewrites, redirects, upstream destinations, compression and edge-worker features to admitted capabilities and separately reviewed code where applicable.
9. Measure cache correctness, origin/offload behavior, stale responses, error rates and request costs for each provider/profile; avoid treating CDN activation as proof of faster or safer delivery.
10. Exercise provider outage, credential rotation/revocation, DNS/certificate changes and failed deployments with a documented service-specific exit/recovery path.
11. Disabling premium orchestration must preserve current delivery policy and independent Core origin/DNS/TLS control; provider removal requires dependency review rather than silently breaking sites.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
