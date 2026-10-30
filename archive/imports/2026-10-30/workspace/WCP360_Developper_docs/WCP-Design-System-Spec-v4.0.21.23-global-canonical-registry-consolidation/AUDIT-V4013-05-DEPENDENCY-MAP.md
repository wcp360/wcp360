# V4.0.13 --- Canonical Dependency Map

## Chaîne principale

Foundations/Security Identity → System/OS → Hosting Core →
Mail/DNS/DB/Web → Files/Backup/Security Recovery →
Applications/Developer Platform → API/Automation/Billing/Provisioning →
Observability/Operations → Cluster/HA → Lifecycle/Updates/Governance.

## Dépendances transversales

Toutes les mutations sensibles dépendent de : identity/RBAC →
entitlement/capability → validation → Job/state machine → privileged
adapter/daemon → observed verification → Event/Audit → telemetry.

## Recovery

Backup/restore et reconciliation sont transversaux et ne dépendent
jamais d'une simple réussite de commande.

## Multi-node

Cluster ajoute placement/ownership/leases/fencing/quorum mais ne
remplace pas les contrats single-node.

## Lifecycle

Updates dépendent de compatibility, backup/recovery readiness,
observability et, en cluster, quorum/headroom/drain.
