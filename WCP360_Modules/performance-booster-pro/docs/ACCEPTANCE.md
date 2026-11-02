# Performance Booster Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Declare the exact application/version/runtime/provider/cache/compression matrix, workload and resource budget; refuse unsupported profiles and avoid generic Worker-compatibility claims.
2. Qualify cache keys for Host/tenant/resource, path/query, content/language variants and Vary including Accept-Encoding; enforce expiry, invalidation/purge and bypass for authentication cookies, sessions, carts, admin/private APIs, authorization headers, private responses and state-changing requests.
3. Validate native gzip/Brotli or other admitted compression against content types, Vary/ETag/content-length behavior, already-compressed data, double compression and effective CPU/memory budgets. Compression never implies permission for public caching.
4. Keep edge direct-file and response-cache paths off by default in the current Nginx -> Apache -> FPM target. Exercise protected static files such as CSS, nested .htaccess/authorization rules and authenticated/private responses through Apache; an optimizer cannot serve them from an unauthorized edge path.
5. Require explicit Core-owner admission and versioned authority handoff for any accelerated profile, including file/rule/request semantics, tenant/Host variant separation and revocation; qualified performance evidence alone does not approve a cache or direct-file bypass.
6. Review dynamic compression of responses containing secrets or attacker-controlled reflections per profile; apply the required prohibition/bypass when risks or correctness are unqualified, rather than enabling it globally.
7. Check cache namespace/credential separation, effective memory/CPU/storage budgets, eviction and outage behavior without introducing cross-tenant cache leakage.
8. Bind proposed runtime/database/edge changes to current resource revisions, qualified owning operations, diffs, recovery references and post-change checks.
9. Benchmark before/after with stated hardware, versions, dataset, concurrency, warm/cold caches, latency/error/resource metrics and reproducible evidence; no unmeasured universal speed claims.
10. Exercise load, cache/provider loss, stale content, compression negotiation and failed tuning; preserve correctness and a supported recovery or reconciliation route.
11. Removing or expiring the module must retain Core baseline optimization controls and keep existing sites serving safely without the premium coordinator.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
