# V4.0.13 --- Remaining ADR Gates

## Décisions à prendre avant implémentation

Les spécifications ont volontairement laissé certains choix de
technologie/déploiement ouverts. Ils doivent devenir des ADR explicites,
notamment :

-   backend de coordination/quorum et topology control-plane ;
-   state database/queue/outbox HA et stratégies de
    sauvegarde/restauration ;
-   secret-store concret et rotation/root-of-trust ;
-   topologies MariaDB/MySQL/PostgreSQL certifiées ;
-   stockage local/shared/distributed réellement supporté ;
-   load-balancer/VIP/DNS provider strategies ;
-   format/signature/provenance et distribution des releases WCP ;
-   matrices OS/packages/runtimes et version-skew certifiées ;
-   backend metrics/logs/traces et rétentions ;
-   politique produit de licensing/editions si elle est réellement
    retenue ;
-   politique de support/EOL/cadence de release ;
-   providers externes officiellement certifiés (billing, DNS, backup,
    notifications).

## Nature

Ces ADR ne sont pas des trous fonctionnels : ce sont des **choix
d'implémentation/produit** que le corpus interdit de deviner.

## Gate

Aucun skeleton de production ne doit figer ces choix avant validation
des ADR concernés.
