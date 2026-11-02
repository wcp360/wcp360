# Git Delivery Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Bind repository identity, permitted revisions, verified webhook provenance and destination resources explicitly; refuse arbitrary URL schemes, paths and changed trust.
2. Treat builds and dependency resolution as untrusted non-root work with isolated workspaces, bounded CPU/RAM/PID/disk/time/network and no panel/agent secrets.
3. Verify artifact identity, provenance and upstream license obligations before passing an immutable reference to an admitted deployment operation.
4. Parse only qualified Compose versions/features into the same typed workload plan used by admitted deployment owners; validate services, mounts, networks, ports, dependencies, health checks and effective quotas rather than forwarding raw YAML or shell text.
5. Choose the authoritative deployment manager for each resource when integrating Portainer, Coolify or Dokku; use declared API capabilities and prohibit concurrent independent reconciliation by this module and that manager.
6. Test trigger replay, concurrent pushes, revoked actors, stale resource revisions, provider interruption and missing acknowledgements without duplicate deployment effects.
7. Keep the last deployed release serving after connector or module failure; retain history and supported recovery references without claiming every schema migration is reversible.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
