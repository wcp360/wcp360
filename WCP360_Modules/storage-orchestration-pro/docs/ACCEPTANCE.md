# Storage Orchestration Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Qualify each engine/version/topology and storage tier using observed latency, resilience and durability characteristics; a tier name never proves performance or redundancy.
2. Resolve authoritative pool/volume identities, tenants, capacity, inode/IO limits and mounted workload dependencies before allocating, moving, expanding or retiring storage.
3. Review encryption-at-rest support, key references, rotation, escrow/recovery and failure behavior without exporting plaintext keys into package data or logs.
4. Coordinate snapshots with application/database consistency and retention; a local snapshot is not evidence of an independent backup or a tested restore.
5. Qualify replication direction, lag, snapshot compatibility, fencing and recovery-point selection before an orchestrated promotion or migration.
6. Exercise full pools, read-only filesystems, interrupted copies, failed mounts, concurrent changes and lost connectivity with explicit uncertain outcomes and reconciliation.
7. Refuse destructive pool/volume/snapshot actions without current authority, resource revisions, dependency review and an independently verified recovery reference.
8. Removing or expiring orchestration must preserve mounted workloads, Core storage control, retained snapshots/backups, key recovery and a supported service-specific exit route.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
