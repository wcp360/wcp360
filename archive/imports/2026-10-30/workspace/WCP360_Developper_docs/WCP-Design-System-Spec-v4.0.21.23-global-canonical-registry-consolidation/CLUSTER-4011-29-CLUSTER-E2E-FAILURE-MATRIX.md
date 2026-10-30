# V4.0.11 --- Cluster End-to-End & Failure Matrix

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

## Scénarios

1.  Secure node enrollment.
2.  Enrollment token replay rejected.
3.  Machine certificate rotation.
4.  Node cordon/drain.
5.  Non-movable resource blocks drain.
6.  Concurrent placement capacity reservation.
7.  Worker dies after side effect before Job ack.
8.  Distributed lock expires while stale worker runs.
9.  Leader loss and new election.
10. Quorum loss → authoritative writes blocked.
11. Asymmetric network partition.
12. Node falsely suspected dead.
13. Stateful failover with fencing.
14. Replica lag too high for failover.
15. DB primary failover.
16. Shared storage outage.
17. Web target readiness before LB routing.
18. Mail/DNS partial failure.
19. Control-plane API node loss.
20. Secret access from unauthorized node rejected.
21. Rolling upgrade mixed versions.
22. Failed upgrade aborts rollout.
23. Lost node decommission.
24. Compromised node quarantine.
25. Cluster DR reconstruction.
