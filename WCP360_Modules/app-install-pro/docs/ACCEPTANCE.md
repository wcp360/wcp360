# AppInstall Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Qualify each offered recipe on an explicit application/version/OS/architecture/runtime/database/provider matrix; unsupported combinations must remain unavailable.
2. Pin and verify upstream sources, image digests and recipe artifacts; record upstream licenses and redistribution/source obligations, including Nextcloud or other application products, and refuse unreviewed or incompatible combinations.
3. Exercise fresh install, interrupted install, update, supported rollback, backup/restore and removal with owned writable paths, secret references, data retention and per-instance results.
4. Admit only explicitly qualified Compose versions and features into a canonical typed workload/resource plan. Reject arbitrary raw YAML passthrough, host sockets, sensitive mounts, privileged/host namespaces, unbounded resource requests and hidden competing managers.
5. Treat application and build payloads as guest/workload code executed under admitted scoped identities with isolation and budgets; no package or recipe hook becomes a host shell or obtains Core credentials.
6. Distinguish native application recipes, Docker/Compose workload definitions and externally managed Portainer/Coolify/Dokku resources; select one effect owner instead of two reconcilers silently changing the same deployment.
7. Demonstrate that removing or disabling AppInstall Pro preserves installed applications, canonical resource inventory, local recovery and access through the free Core.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
