# DDoS Defense Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Declare which L3/L4 traffic can be handled only by a qualified upstream provider versus which L7 requests an admitted local/edge control can process; state actual provider and capacity limits.
2. Map protected resources, origin exposure, provider accounts, allowed policies and current permissions explicitly; prevent one tenant from changing another tenant's or management traffic.
3. Validate source-attributed signals, baselines, deduplication, rate windows and false-positive criteria before proposing rate, challenge, routing or filtering actions.
4. Bind any approved response to exact scope/resource revisions and supported reversible policies; preserve management access, legitimate sessions and an independent emergency recovery route.
5. Keep packet payloads, personal identifiers and credentials minimized/redacted; restrict destinations and any diagnostics to separately authorized scope.
6. Test provider outage, signal loss, replay, stale rules, normal traffic spikes, exemptions and failed recovery using bounded synthetic fixtures or explicitly authorized isolated labs only.
7. Record observed service availability, filtering consequences, errors and recovery under stated conditions; do not claim guaranteed DDoS protection or generate public/third-party attack traffic.
8. Disable new premium response automation safely while baseline Core protections, existing provider policy, local incidents and independent recovery remain available.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
