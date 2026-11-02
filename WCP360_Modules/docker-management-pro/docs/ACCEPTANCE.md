# Docker Management Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Qualify Docker/OCI versions and rootful/rootless assumptions, peer identity, cgroup delegation and actual CPU/RAM/PID/storage/log enforcement before declaring support.
2. Pin images to reviewed immutable digests with upstream license/provenance checks; isolate builds and refuse unreviewed image/build privileges.
3. Admit Compose versions/features through a canonical typed plan; validate service identities, dependencies, mounts, networks, ports, health and effective budgets rather than sending arbitrary raw YAML or shell text to a daemon.
4. Map tenant UID/GID, allowed mounts, ports and network/egress ownership; refuse host Docker sockets, sensitive bind mounts, host namespaces and unrestricted privileged modes.
5. Select one authoritative effect/reconciliation owner per deployment when using Portainer, Coolify or Dokku. Connector capabilities, manager-owned metadata and Core resource mappings must be version-qualified; no two managers silently mutate the same workload.
6. Manage volumes, database consistency, encryption/secret references and retention explicitly; container recreation is not data recovery.
7. Bound logs, named diagnostic capabilities, health checks and observations; this product cannot become arbitrary container/host exec or an unrestricted daemon API.
8. Exercise start/stop/update/drain and supported rollback with current authority, resource revisions, measured health and per-workload outcomes.
9. Rehearse isolated backup/restore of application data and required configuration/secret references; test failed pulls, full storage, daemon/manager loss and interrupted updates.
10. On expiry/removal preserve workloads, data, ordinary free Core provider control and independent recovery while stopping new premium orchestration safely.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
