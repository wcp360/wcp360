# Agency Workspace Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Resolve agency/reseller/client/resource relationships through Core ownership, delegation and reseller ceilings rather than copying an authority or quota ledger.
2. Keep hosting package/template creation, versioning, account assignment, quota enforcement and scoped overrides usable through free Core Tenancy; premium views only reference and orchestrate admitted existing operations.
3. Scope cross-account portfolio views and handoffs to currently delegated resources; test nested resellers, staff revocation, wrong-tenant links and effective ceilings.
4. Apply branding only through reviewed declarative assets while retaining security, consent and operator context; white labeling never changes identity or permission.
5. Preserve language/accessibility fallback and usable Core account/hosting-plan navigation when the workspace is disabled.
6. Keep support records, portfolio preferences and permitted customer data exportable under explicit retention and handoff policy without blocking independent Core access.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
