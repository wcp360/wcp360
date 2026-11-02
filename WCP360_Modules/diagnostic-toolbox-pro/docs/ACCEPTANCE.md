# Diagnostic Toolbox Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Catalogue each named check with fixed typed inputs, permissions, resource/tenant scope, budgets and explicit unavailable states.
2. For HTTP/HTTPS, TCP, DNS or ICMP check bundles, require admitted protocols/targets, bounded resolution/redirects, time/rate/size/concurrency and correct capability/privilege handling; no unrestricted scanner or raw shell is exposed.
3. Treat paths, logs and returned diagnostics as untrusted data; redact secrets and prevent output from becoming executable markup or instructions.
4. Keep observation and repair separate; a repair invokes the owning module's qualified operation with current authority, resource revision and recovery evidence.
5. Test malicious arguments, wrong-tenant/internal-control targets, exhaustion, failed checks and source outages without fabricating healthy states.
6. Preserve free Core diagnostics, ordinary local/remote checks where admitted, essential alerts and independent repair/recovery when Toolbox is removed.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
