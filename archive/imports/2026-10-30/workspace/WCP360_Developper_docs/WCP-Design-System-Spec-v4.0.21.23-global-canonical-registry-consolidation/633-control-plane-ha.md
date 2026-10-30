# 633 --- Control Plane High Availability

## Statut de provenance

**\[V3.5 --- architecture proposée à partir des lacunes de la V3.4\]**
Ce contrat n'était pas spécifié en profondeur dans les documents
précédents. Il doit être validé par l'architecture technique avant
implémentation.

## Objectif

Définir ce que signifie HA pour WCP lui-même, distinct du Cluster
Manager des workloads.

## Composants à rendre HA selon édition

API/web, scheduler/job dispatch, event processing, database/state store,
cache/locks si utilisé, secret/key access, artifact/config store.

## Règles

Les composants stateless peuvent être répliqués ; les composants
stateful nécessitent un mécanisme de cohérence/failover explicitement
choisi.

## À décider

Technologies exactes, quorum, topology, RTO/RPO et stratégie de leader
election. La V3.4 ne permet pas de les déduire.
