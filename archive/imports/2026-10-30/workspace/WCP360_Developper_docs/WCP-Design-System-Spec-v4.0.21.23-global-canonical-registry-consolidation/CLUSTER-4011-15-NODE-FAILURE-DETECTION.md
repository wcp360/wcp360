# V4.0.11 --- Node Failure Detection

## Statut

**NORMATIVE --- V4.0.11 MULTI-NODE, CLUSTER, HA & DISTRIBUTED CONTROL
PLANE DEEP SPECIFICATION --- NO CODE**

Cette passe formalise l'exécution WCP au-delà d'un serveur unique :
identité et enrollment des nœuds, confiance inter-nœuds, placement,
ownership, coordination, verrous, jobs distribués, drain, failover,
quorum, réplication, stockage, réseau, rolling upgrades et scénarios de
partition.

## Invariants

-   `Node`, `Cluster`, `Placement`, `ResourceOwner`, `Lease`,
    `DistributedLock`, `FailoverPolicy` et `ReplicationGroup` sont des
    ressources distinctes.
-   L'état `node online` ne signifie pas que ses services sont sains ou
    qu'il peut recevoir de nouvelles charges.
-   Aucun nœud ne rejoint un cluster sur simple possession d'une IP ou
    d'un secret statique partagé.
-   Les communications privilégiées inter-nœuds sont authentifiées,
    autorisées et chiffrées.
-   Un failover ne doit pas créer deux propriétaires actifs d'une
    ressource non conçue pour le multi-writer.
-   Quorum, consensus et réplication ne sont pas déclarés « HA » sans
    modèle de panne explicite.
-   Les mécanismes distribués restent optionnels : WCP doit conserver un
    mode single-node cohérent.

## Signals

Heartbeat/lease, control-plane connectivity, health probes, service
checks and external observations.

## States

suspected → unreachable → failure-confirmed according policy; avoid
instant failover on one missed heartbeat.

## Threshold

Timeouts tuned to environment and service criticality.

## False positive

Network loss vs host death handled conservatively for stateful
resources.

## Recovery

Returning node is quarantined/reconciling until ownership conflicts are
resolved.
