# V4.0.11 --- V4.0.11 READY Gate

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

## SPECIFIED-DEEP

Cluster/node model, secure enrollment, machine PKI/mTLS, capability
compatibility, cordon/drain, placement/reservations,
ownership/leases/fencing, distributed locks/jobs/events, quorum/leader
boundary, partitions/failure detection/failover, replication,
DB/storage/web/DNS/mail/control-plane HA, secrets, rolling upgrades,
decommission, DR, observability and threat model.

## Critères

No shared permanent enrollment secret, no failover without
ownership/fencing model, no fake HA, no split-brain writes under
no-quorum policy, no unauthorized secret spread, no rolling upgrade
outside compatibility window, single-node remains valid.

## Statut

**V4.0.11 = SPECIFIED-DEEP --- NO CODE --- NOT GLOBAL READY.**

Il reste à fermer au minimum lifecycle/update orchestration global,
entitlements/licensing si retenus, UX/design-system fonctionnel global,
puis l'audit transversal final de dépendances, contradictions, PARTIAL
et DECISION REQUIRED.
