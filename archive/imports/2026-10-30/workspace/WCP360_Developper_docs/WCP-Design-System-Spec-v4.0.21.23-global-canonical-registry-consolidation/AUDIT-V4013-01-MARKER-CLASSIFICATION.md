# V4.0.13 --- Marker Classification

## Comptage brut

-   **PARTIAL** : 599 occurrences dans 204 fichiers
-   **DECISION REQUIRED** : 21 occurrences dans 14 fichiers
-   **TODO** : 10 occurrences dans 8 fichiers
-   **TBD** : 12 occurrences dans 8 fichiers

## Interprétation

`PARTIAL` apparaît surtout dans les états UI, scénarios de panne et
descriptions de résultats partiels. Les documents d'audit historiques
contiennent aussi de longues listes de statuts antérieurs. Ces
occurrences ne doivent pas être modifiées mécaniquement.

## Candidats structurels hors historiques

-   `940-ha-cluster-readiness-gate.md:11` ---
    multi-control-plane/cluster reste **PARTIAL / FUTURE**.
-   `CLUSTER-4011-30-READY-GATE.md:55` --- et DECISION REQUIRED.
-   `MASTER-VERSION-SUPPORT-POLICY.md:5` --- **DECISION REQUIRED.** Les
    sources V3.5 ne fixent pas encore les
-   `931-plugin-marketplace-security-gate.md:10` --- Le
    marketplace/plugin tiers reste **PARTIAL / NOT V1-READY** tant que
    ne
-   `LIFE-4012-30-READY-GATE.md:57` --- des PARTIAL/DECISION
    REQUIRED/TODO, contradictions, doublons, références
-   `MASTER-ADR-GOVERNANCE.md:5` --- Fermer les `DECISION REQUIRED` sans
    transformer des hypothèses en
-   `930-licensing-resource-contract.md:18` --- commercial avant
    activation. **DECISION REQUIRED** :
-   `MASTER-PACKAGE-REPOSITORY-STRATEGY.md:5` --- **DECISION REQUIRED.**

## Fermeture

Le fichier `940-ha-cluster-readiness-gate.md` conservait le statut
historique `PARTIAL / FUTURE`. Il reçoit une note de supersession vers
les contrats `CLUSTER-4011-*`. Cela préserve l'historique sans mentir
sur l'état actuel du corpus.

Les marqueurs contenus dans les audits antérieurs restent des **preuves
historiques** ; leur présence textuelle n'empêche pas la baseline
actuelle d'être spécifiée.
