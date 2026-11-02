# Database Operations & DBaaS Pro acceptance preparation

**Status: design.** These are required future checks, not executed results or production acceptance. The authoritative requirements are the acceptance list in [package.proposal.json](../package.proposal.json), materialized from [CATALOG.json](../../CATALOG.json).

## Package-specific checks

1. Define an explicit engine/version/provider/topology and upstream-license matrix for MySQL, MariaDB, PostgreSQL, customer Redis, MongoDB and Galera; list unsupported functions rather than treating the names as interchangeable or already supported.
2. Define optional DBaaS service profiles using authoritative tenant accounts, quotas, endpoint/TLS policy, effective capacity and measured service objectives; Core-owned basic database provisioning and administration remain available without this package.
3. Distinguish asynchronous replicas, PostgreSQL promotion mechanisms, Redis Sentinel/Cluster, MongoDB replica sets/shards and Galera multi-primary/quorum semantics; qualify each selected topology and recovery action independently.
4. Verify membership, current topology/revisions, quorum, witness roles, fencing, lag and recovery-point requirements before promotion or membership changes; refuse split-brain and do not promise automatic availability during partitions.
5. Qualify consistent engine-native backups, binary/WAL/oplog or persistence requirements, encryption/key references, retention and isolated restore/PITR checks where actually supported; a replica or snapshot is not an independently verified backup.
6. Use narrowly scoped hosted-database identities and separately authorize engine extension changes through the Data owner; never reuse Core database or queue credentials for customer services.
7. Exercise interrupted initialization, quorum loss, stale topology, credential revocation, engine upgrades and failed promotion with bounded jobs and explicit reconciliation, not silent primary reselection.
8. Publish provider-specific endpoint/client behavior, compatibility and recovery evidence before offering a service profile; absence of evidence is planned/unavailable, not DBaaS readiness.
9. Disabling or expiring orchestration must preserve databases, grants, safe replication state, ordinary Core control and documented engine-specific recovery.

## Shared release and exit checks

- Keep all 233 essential capabilities, seven Core engine adapters and resource authority available through the free Core. Verify wrong-tenant, revoked/stale authority and malformed-input refusals at the actual use-case boundary.
- Review the selected package source/license model and every distributed dependency/upstream asset; record provenance and applicable obligations. Core's license does not decide this package's terms or compatibility.
- Verify bounded work, secret redaction, replay/failure recovery and independent Core operation after expiry, provider outage, disablement, update and removal. Optional entitlement never creates a permission or bypasses package trust.
- Bind each result to exact source, versions, target, fixtures and outcome; unavailable or unexecuted checks remain explicit. Do not infer passing runtime from these documents or restart local executables.

Consult [Core licensing](../../../WCP360_Core/LICENSING.md), [editions/premium strategy](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and the [documentation-only template](../../_template/README.md). No installer, arbitrary shell, Core manifest or production provider is supplied here.
