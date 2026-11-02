# VPS / VM Management Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Qualify every hypervisor/provider/version/architecture/guest combination separately, distinguishing full VMs from system containers and refusing unsupported migrations.
2. Verify template/image provenance, immutable references, upstream licenses and the admitted cloud-init profile. Guest code/configuration and authorized guest-root customization require their own scoped qualification; protect guest credentials and refuse host hooks, host-path escapes, management-plane commands or host secrets.
3. Map tenant/guest identity, vCPU/RAM/disk/IO limits, placement and effective enforcement without claiming guest isolation from configuration text alone.
4. Authorize IP assignments, vNICs, bridges/overlays, firewall boundaries, public exposure and guest-network ownership through the relevant Core/provider contracts.
5. Qualify create/start/stop/rebuild/resize and console/agent access with exact scope, current authority, resource revisions and bounded diagnostics.
6. Keep snapshot semantics distinct from independent backups; test consistent guest/data restore and recovery-key references before destructive rebuild or deletion.
7. Exercise provider loss, full storage, concurrent actions, interrupted operations and qualified cold/live migrations with fencing and honest downtime/recovery evidence.
8. Disable premium workflows while guests and retained data keep running under independent Core/provider ownership, export and recovery.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
