# Multi-server Hosting Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Admit nodes through reviewed enrollment, peer identity, trust roots, key rotation/revocation and OS/version/capability evidence; reject unknown, replaced or replayed identities.
2. Declare Web, database, mail, DNS or mixed roles with actual admitted service capabilities, dependencies, capacity and tenancy constraints; no role name proves readiness, high availability or interchangeability.
3. Resolve each tenant/resource to authoritative placement, role-specific owner and current route/revision before dispatch; never choose a destination solely from a client-supplied node ID or copy the resource registry.
4. Qualify scheduling/capacity policy against effective CPU/RAM/storage/network limits, locality and isolation; distinguish recommendations from actual reservations or enforcement.
5. Exercise leases/fencing, stale workers, concurrent placement, partitions, delayed acknowledgements and unavailable control paths without cross-tenant effects or competing active owners.
6. Track drain, maintenance, certificate/runtime upgrades and fleet-care waves with bounded batches, per-node receipts and explicit pending/failed/unknown states; convergence is measured, not instantaneous.
7. Qualify Web routing/TLS, database consistency/quorum, mail queue/mailbox/authentication and DNS zone/delegation/serial behavior through each owner before a role migration or cutover.
8. Document which workloads continue during controller/provider outages; reconcile uncertain operations rather than blindly replaying them on another node.
9. Qualify resource migration and return-to-service with Hosting/Data/Mail/Files/Transfers/BackupRestore contracts, independent recovery references and explicit cutover authority.
10. Disable premium coordination without interrupting established workloads or free Core node/provider foundations, ownership inventory, ordinary service control and recovery.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
