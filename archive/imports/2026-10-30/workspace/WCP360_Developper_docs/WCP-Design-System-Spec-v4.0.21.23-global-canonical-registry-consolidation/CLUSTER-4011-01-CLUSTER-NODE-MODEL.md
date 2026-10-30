# V4.0.11 --- Cluster & Node Canonical Model

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

## Cluster

ID, mode, control-plane membership, node inventory, placement policy,
health summary, compatibility generation et lifecycle.

## Node

Stable node ID, machine identity, hostname/display name, addresses
observées, roles/capabilities, capacity, labels/constraints, version,
trust state et lifecycle.

## Etats

enrolling, active, cordoned, draining, maintenance, degraded,
unreachable, quarantined, decommissioning, removed.

## Séparation

Reachability, trust, health, schedulability et service capability sont
des axes distincts.

## Single-node

Un cluster d'un nœud peut utiliser le même modèle sans prétendre fournir
de la HA.
