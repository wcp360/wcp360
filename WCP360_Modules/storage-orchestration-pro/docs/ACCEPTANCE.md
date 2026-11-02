# Storage Orchestration Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Qualify each engine/version/topology and storage tier using measured latency, durability, consistency and resilience; distinguish Ceph RBD block, CephFS file, RGW object and GlusterFS file capabilities rather than claiming equivalence.
2. Resolve authoritative pool/volume/bucket/share identities, tenants, capacity, inode/IO limits and mounted workload dependencies before allocating, moving, expanding or retiring storage.
3. Admit NFS/SMB/object gateways only under qualified identity, protocol, mount and network models; S3 API compatibility does not establish POSIX semantics, every S3 feature or durability guarantees.
4. Treat Nextcloud as an application with its own database, data directory/object-store compatibility, locking/background jobs, upstream license and backup/restore evidence; do not advertise it as an S3 server or bypass its application data lifecycle.
5. Review encryption-at-rest support, key references, rotation, escrow/recovery and failure behavior without exporting plaintext keys into package data or logs.
6. Coordinate snapshots with application/database consistency and retention; a local snapshot is not evidence of an independent backup or a tested restore.
7. Qualify replication direction, lag, consistency, fencing and recovery-point selection before promotion or migration; distinguish a resilient storage pool from application-level service availability.
8. Exercise full pools, failed disks, read-only filesystems, interrupted copies, failed mounts, concurrent changes and partitions with explicit uncertainty and reconciliation.
9. Refuse destructive pool/volume/bucket/snapshot actions without current authority, resource revisions, dependency review and an independently verified recovery reference.
10. Removing orchestration must preserve mounted workloads, ordinary Core storage control, retained recovery material, key access and a supported service-specific exit route.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
