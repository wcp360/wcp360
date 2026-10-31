# MASTER V4.0.10 --- Observability, Operations, Notifications & Incidents

## Architecture

Signals (health/metrics/logs/traces) → evaluation/correlation →
AlertInstance → Incident → Notification/Runbook → remediation via typed
WCP actions.

## Corrélation

`real_actor/effective_actor + request_id + job_id + resource_id + event_id + audit_id + trace_id`
permet de suivre une action sans utiliser la télémétrie comme source
d'autorisation.

## Principes

-   Observability must fail open for non-critical telemetry paths while
    exposing degraded visibility.
-   No-data is explicit.
-   High-cardinality and secrets are treated as operational/security
    risks.
-   Maintenance suppresses noise, not evidence.
-   Incidents preserve timeline and operator actions.
-   Public status is a sanitized publication layer.
-   SLOs are engineering targets, not automatically contractual SLAs.
-   Capacity forecasts are advisory and include uncertainty.

## Prochaine passe

V4.0.11 : **Multi-node, Cluster, HA & Distributed Control Plane Deep
Specification** --- node enrollment/trust, leader/coordination,
placement, distributed locks, state ownership, failover, replication
boundaries, split-brain, quorum, service VIP/load balancing,
storage/database HA, maintenance/drain, rolling upgrades and disaster
scenarios.
