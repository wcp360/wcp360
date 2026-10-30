# V4.0.11 --- Secure Node Enrollment & Trust Bootstrap

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

## Enrollment

Admin-authorized enrollment intent → short-lived one-time bootstrap
material → node identity proof → compatibility checks →
certificate/credential issuance → capability discovery →
approval/activation.

## Bootstrap

Token limité dans le temps, à usage unique, scopé au cluster/enrollment.
Jamais secret permanent commun à tous les nœuds.

## Validation

Cluster ID, expected environment, software generation, clock sanity,
OS/platform support et duplicate identity.

## Failure

Token replay, node ID collision, wrong cluster, expired bootstrap et
partial enrollment sont rejetés/reconciliés.

## Removal

Decommission révoque trust material et retire le nœud des futures
décisions.
