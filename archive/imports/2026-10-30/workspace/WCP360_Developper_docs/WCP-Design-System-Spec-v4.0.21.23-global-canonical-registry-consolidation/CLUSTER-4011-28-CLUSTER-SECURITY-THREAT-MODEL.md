# V4.0.11 --- Cluster Security Threat Model

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

## Menaces

Rogue node enrollment, stolen machine credential, compromised node,
MITM, stale leader, replayed Job, forged heartbeat, split-brain,
poisoned capability advertisement, lateral secret access and malicious
tenant workload.

## Controls

One-time enrollment, mTLS, authorization, rotation/revocation,
signed/authenticated state channels where appropriate, fencing, least
privilege, isolation and audit.

## Compromised node

Quarantine → stop scheduling → revoke trust → fence resources → rotate
affected secrets → rebuild/re-enroll → verify.

## Trust

A cluster member is not automatically trusted for all tenant
secrets/resources.
