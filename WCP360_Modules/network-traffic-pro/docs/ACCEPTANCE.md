# Network Traffic Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Map public/private listeners, domain/tenant routing authority, certificate ownership and allowed upstreams before accepting a traffic plan.
2. Qualify each HAProxy, Traefik or Nginx Proxy Manager API/version/profile, protocols, source-address handling, TLS termination, timeout and health semantics; their capabilities are not interchangeable or already admitted.
3. Choose one authoritative ingress/reverse-proxy controller for each resource; reconcile external-manager changes through declared contracts rather than running competing reconcilers or exposing raw config/root sockets.
4. Keep current Nginx -> Apache -> FPM all-request Apache authorization and protected-file semantics; an ingress provider does not authorize edge direct-file or response-cache bypass.
5. Test management lockout prevention, failed configuration checks, stale routes, lost connectivity and supported rollback with current authority and resource revisions.
6. Constrain probe, tunnel and upstream destinations, budgets, credentials and egress; reject unauthorized metadata/control-plane reachability and arbitrary network scans.
7. Disable new orchestration without removing established routing or the free Core's independent DNS/TLS/network administration and recovery route.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
