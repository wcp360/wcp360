# 940 --- HA / Cluster Readiness Gate

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

Le profil V1 Standalone reste la cible implémentable. HA
multi-control-plane/cluster reste **PARTIAL / FUTURE**.

Pour passer READY il faudra fermer : topology, leader/election ou
coordination, PostgreSQL HA ownership, queue/outbox HA, secret-store HA,
distributed locks, split-brain policy, upgrade ordering, backup/restore
cluster et tested RTO/RPO.

Aucun module V1 ne doit supposer que HA existe.

## V4.0.11 normative deepening

- `CLUSTER-4011-01-CLUSTER-NODE-MODEL.md`
- `CLUSTER-4011-10-COORDINATION-QUORUM.md`
- `CLUSTER-4011-27-CLUSTER-OBSERVABILITY.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.


## V4.0.13 closure note

Le statut historique `PARTIAL / FUTURE` ci-dessus est **superseded** par la famille normative `CLUSTER-4011-*` de V4.0.11. Le profil standalone reste supporté, mais la spécification multi-node/cluster/HA n'est plus considérée absente. La readiness d'implémentation reste conditionnée aux ADR concrets (coordination backend, topologies DB/storage/LB, secret-store, version-skew) et aux tests définis par `CLUSTER-4011-29-CLUSTER-E2E-FAILURE-MATRIX.md` et `CLUSTER-4011-30-READY-GATE.md`.
