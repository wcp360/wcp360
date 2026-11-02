# Standalone capacity and future node topology

**Status: design reservation. No cluster, HA control plane or remote agent protocol is implemented here.**

WCP360 Standalone is initially operated and recovered as one installed panel. Scale in measured stages while preserving resource ownership, authorization and bounded effects. The [Core structure](../../../../../STRUCTURE.md) maps collaborators; [Operations](../../../Operations/design/distributed-execution/README.md) owns execution mechanics.

## Growth stages

| Stage | Required work | Evidence before advertising support |
| :--- | :--- | :--- |
| Reliable single node | Provider limits, bounded workers/queues, capacity reserve, restart/recovery and application metrics. | Contention, overload and failure leave management/recovery usable. |
| More worker capacity | Shared durable intent, leases/heartbeats/fencing, fairness and idempotency. | Old workers cannot publish or apply after losing authority/ownership. |
| More application nodes | Explicit node inventory/placement, identity, capabilities and secure remote provider transport. | Node failure, partitions, delayed/replayed messages and tenant isolation are exercised. |
| Redundant control plane | Session/grant/secret/key distribution, database/storage consistency, leader/ownership rules and compatible deploys. | Independent restore/failover rehearsal and declared recovery objectives. |
| Fleet upgrades | Compatibility windows, provider/artifact admission, drain, migration, rollout limits and reconciliation. | Partial rollout and failed migration recover according to the actual data contract. |

These are future scopes, not four new deployment modes or installer flags. Do not add Kubernetes, a distributed database or a second scheduler merely to prepare folders.

## Contracts before additional nodes

Platform owns node identity and observed provider capabilities; Hosting/Data/Mail retain resources. A placement plan binds the resource, node, policy/template/provider versions, ports/storage and expected revision. Capacity observation is not a reservation or permission.

Operations supplies durable accepted intent, admission limits, leases/fencing and bounded recovery. The recipient of an effect must reject an obsolete fencing token; a database lock alone does not fence a disconnected process.

IdentityAccess must establish independent transport identities, peer trust, scopes and revocation. Existing local Unix-socket peer rules are not a remote authentication protocol. No generic remote root channel, arbitrary SSH command or Docker socket tunnel is introduced.

Before more than one web instance, replace/qualify the candidate file-backed session assumptions, context isolation, shared credentials and bootstrap/service-discovery model. Copying cookie/session files or sharing vendor directories does not prove a valid deployment.

Observability measures per-node and per-tenant CPU/RAM/I/O/FD/network/queue/service health with retention and privacy. Reserve capacity for installation/builds, migrations, backups and recovery instead of assigning all available resources to sites.

## Acceptance

Exercise duplicate execution, revoked actor, stale resource/node revision, old worker, network partition, unknown effect, backend failure, noisy tenant, saturated queue and node reboot. Show actual resource limits/fairness, bounded logs and operational recovery. A successful single-node candidate or five-OS userspace matrix does not establish these properties.
