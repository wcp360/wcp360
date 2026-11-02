# Incident Operations Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Keep incident membership, evidence visibility and public/private status content explicitly scoped.
2. Resolve channel ownership, recipient identity and current notification permission through Core policy for Telegram, SMS, email and other admitted transports; external contacts and connector credentials remain scoped.
3. Distinguish queued, provider-accepted, sent, delivered where evidenced, acknowledged and failed notifications with receipts, deduplication and bounded retries; do not claim instantaneous delivery.
4. Require authorized review for external publication and prevent sensitive host/customer data from entering public drafts.
5. Exercise connector outages, duplicate events, shift changes, channel revocation and concurrent incident updates with current-authority checks and Core local fallback.
6. Disabling the module must preserve local incident history, ordinary free Core alerts and independent response access.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
