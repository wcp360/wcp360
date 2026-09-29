# WCP360 V2.5.3.24 — Clusters / Failover / HA Functional Depth

## Style lock
V2.5.3.14 remains the active visual baseline. No shell redesign introduced.

## Developed family
- cluster overview
- nodes and roles
- quorum
- replication
- failover
- node maintenance
- storage/database HA
- failure scenarios

## HA principles
- failover is quorum-aware and replication-aware;
- transient health failures do not trigger immediate promotion;
- manual failover follows the same safety checks as automatic failover;
- node maintenance checks capacity and quorum before drain;
- data-plane readiness is independent from compute-node reachability;
- split-brain prevention has priority over availability;
- stale replica promotion may be blocked;
- degraded/read-only mode may be safer than forced failover;
- all promotion/failover actions are privileged and audited.

## Next family
Firewall / Network / Connectivity.