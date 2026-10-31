# 638 --- Offline / Degraded Node Lifecycle

## Statut de provenance

**\[V3.5 --- architecture proposée à partir des lacunes de la V3.4\]**
Ce contrat n'était pas spécifié en profondeur dans les documents
précédents. Il doit être validé par l'architecture technique avant
implémentation.

## États proposés

Enrolling, online, degraded, maintenance, unreachable, quarantined,
decommissioning, decommissioned.

## Règles

Heartbeat stale ≠ node failed. Les mutations sont bloquées ou mises en
attente selon type lorsque l'état n'est pas fiable.

## Reconnexion

Reconcile desired/observed state ; ne pas rejouer aveuglément une
mutation non idempotente après une longue coupure.

## V4.0.11 normative deepening

- `CLUSTER-4011-01-CLUSTER-NODE-MODEL.md`
- `CLUSTER-4011-02-NODE-ENROLLMENT-TRUST.md`
- `CLUSTER-4011-05-NODE-CORDON-DRAIN.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.12 normative deepening

- `LIFE-4012-15-DEPRECATION-EOL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
