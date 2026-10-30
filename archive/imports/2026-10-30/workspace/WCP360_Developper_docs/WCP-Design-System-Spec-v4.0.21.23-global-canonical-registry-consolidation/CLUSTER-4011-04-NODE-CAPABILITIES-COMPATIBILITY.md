# V4.0.11 --- Node Capabilities & Compatibility Matrix

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

## Capability

Web stacks, DB engines, mail, runtimes, storage, IP/network, backup
adapters and optional features advertised from verified node state.

## Compatibility

Control-plane generation + node agent/daemon generation +
schema/protocol versions.

## Placement

A resource can be placed only where required capabilities and versions
are compatible.

## Mixed versions

Allowed only inside documented rolling-upgrade window.

## Unknown

Unverified capability is unavailable, not assumed.

## V4.0.12 normative deepening

- `LIFE-4012-14-COMPATIBILITY-MATRIX.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
