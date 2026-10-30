# V4.0.11 --- Multi-node, Cluster & HA Closure Audit

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

## Méthode

La passe conserve intégralement V4.0.10 et ajoute une couche normative
distribuée. Les documents historiques ont été reliés uniquement lorsque
leurs noms réellement présents indiquaient directement cluster, node,
multi-node, HA, failover, replication, quorum, distributed, placement,
load-balancing ou rolling upgrade.

## Résultat

30 contrats normatifs V4.0.11 ajoutés. **15 documents historiques
existants** ont reçu un lien de deepening.

## Décisions structurantes

-   Le mode single-node reste une configuration de première classe.
-   WCP n'implémente ni consensus, ni réplication DB/filesystem maison.
-   Enrollment one-time + identité machine distincte + mTLS/équivalent
    certifié.
-   Failover stateful exige ownership autoritatif, fencing et données
    réellement disponibles.
-   Quorum perdu bloque les écritures qui exigent autorité distribuée.
-   Replication ≠ backup ; HA ≠ DR.
-   Un nœud compromis est quarantiné, révoqué et reconstruit plutôt que
    simplement marqué offline.

## Décisions encore à ADR

Backend de coordination concret, topologies HA certifiées par moteur DB,
stratégie de secret-store distribué, load-balancer/VIP providers,
stockage distribué éventuel et matrices exactes de version skew.
