# ADR-020 --- V1 Single-node vs Cluster Boundary

## Statut

**ACCEPTED FOR V1 --- V4.0.16 --- NO CODE**

## Autorité

Cet ADR ferme un choix P0 identifié par `ADR-V4015-07-REGISTER.md`. Il
concrétise la baseline V4.0.15 sans élargir le scope fonctionnel.

## Règles

-   Toute implémentation incompatible exige un ADR de supersession.
-   Les secrets ne sont jamais placés en clair dans les arguments de
    processus, logs ou artefacts exportés.
-   Toute mutation privilégiée reste typée, auditable,
    idempotente/reconciliable et vérifiée par état observé.
-   Le V1 reste single-node-first tout en conservant des interfaces qui
    n'interdisent pas le cluster futur.

## Décision

**WCP V1 production profile est single-node.** Cluster/HA n'est pas
requis pour déclarer V1 production-ready.

## Conservé dès V1

Stable resource IDs, node ID, placement field même trivial, Jobs avec
ownership/lease, adapters sans assumption localhost implicite, external
service modes, backup/restore portable et event model.

## Non implémenté CORE V1

Quorum, distributed scheduler, automatic failover, fencing multi-node,
distributed config consensus et rolling cluster upgrade.

## V1.x/P2

Ces fonctions réutilisent les contrats CLUSTER-4011 sans réécrire le
modèle de ressources.

## UX

Aucune UI V1 ne laisse croire qu'un single-node possède de la HA.
