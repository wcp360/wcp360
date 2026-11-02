# Observability Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Distinguish local measurements, external probes, inferred objectives and stale/absent samples; record time, source, freshness and vantage point without calling missing telemetry healthy.
2. Qualify HTTP/HTTPS methods/redirects/TLS/body bounds, TCP connect behavior, DNS query/resolver limits and ICMP capability/privilege handling independently; unsupported protocols remain unavailable.
3. Bind every probe to an authorized asset or explicitly admitted target. Bound DNS resolution, redirects, destination ranges, rate, concurrency, packet/body size, duration and retries; block metadata/control-plane or unintended internal targets without creating a network scanner.
4. Enforce tenant scope on series, traces, labels, probes and dashboard queries before rendering/export; redact credentials, tokens, cookies and response secrets.
5. Bound sample cardinality, retention, aggregation cost, outbound buffers and campaign resource use; protect other tenants and Core local observations from probe exhaustion.
6. Validate objective calculations against synthetic gaps, resets, clock changes and delayed samples without claiming unmeasured availability or immediate event propagation.
7. Emit scoped events through Core-owned notification policy for separately admitted Telegram, SMS, email or other transports; basic alerts remain free, and queued/sent/delivered/acknowledged states require distinct evidence.
8. Prove local Core monitoring, ordinary alert routing and diagnostic fallback remain usable during sink, channel, module or provider outages.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
