# AppInstall Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Qualify each offered recipe on an explicit application/version/OS/architecture/runtime/database/provider matrix; unsupported combinations must remain unavailable.
2. Pin and verify upstream sources and recipe artifacts, record the applicable upstream licenses and redistribution/source obligations, and refuse unreviewed artifacts or incompatible combinations.
3. Exercise fresh install, interrupted install, update, supported rollback, backup/restore and removal with owned writable paths, secret references, data retention and per-instance results.
4. Run dependency installers as the application identity through admitted operations; deny arbitrary executable hooks, shell text, tenant escapes and access to Core credentials.
5. Demonstrate that removing or disabling AppInstall Pro preserves installed applications, canonical resource inventory, local recovery and access through the free Core.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
