# Multi-server Hosting Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Admit nodes through reviewed enrollment, peer identity, trust roots, key rotation/revocation and version/capability evidence; reject unknown, replaced or replayed identities.
2. Resolve each tenant/resource to authoritative node placement and current route/revision before dispatch; never select a destination solely from a client-supplied node ID.
3. Qualify scheduling and capacity policy against effective CPU/RAM/storage/network limits, locality and isolation; distinguish recommendations from reserved or enforced resources.
4. Exercise leases/fencing, stale workers, concurrent placement, partitions, delayed acknowledgements and unavailable control paths without cross-tenant effects or competing active owners.
5. Track drain, maintenance, certificate/runtime upgrades and fleet-care waves with bounded batches, per-node results and service-specific recovery.
6. Document which existing workloads continue during controller/provider outage; keep uncertain operations reconcilable instead of blindly replaying them on another node.
7. Qualify resource migration and return-to-service checks with the owning Hosting/Data/Mail/Files/Transfers/BackupRestore contracts, retained recovery points and explicit cutover authority.
8. Disable the premium coordinator without interrupting established node workloads or independent Core control, ownership inventory and recovery on each admitted server.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
