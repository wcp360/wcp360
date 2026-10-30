# 432 --- Resource Metering

## Objectif

Créer une couche commune d'historique de consommation.

## Dimensions

Disk, inodes, bandwidth, CPU time/usage, RAM, I/O, DB/storage, mail
usage et compute selon capacités.

## Modèle

metric, resource, tenant, interval, value, unit, source,
quality/freshness.

## Règle

**Limit ≠ Usage ≠ Metering ≠ Billing usage.** Cette distinction est
obligatoire dans l'UI et l'API.

## Contrat transversal WCP

-   **WCP-ADMIN** configure les moteurs, politiques, capacités globales
    et opérations d'infrastructure.
-   **WCP-USER** n'expose que les fonctions autorisées dans le scope
    client/revendeur.
-   Les autorisations sont contrôlées côté backend : rôle + scope +
    ownership + capability.
-   Toute mutation sensible est auditée ; toute opération longue devient
    un job Task Center.
-   Les secrets sont redacted dans logs, audit, notifications et IA.
-   Les intégrations tierces sont des adaptateurs/capabilities, jamais
    des dépendances implicites.
-   États UI : loading, empty, denied, unavailable, partial, stale,
    pending, failed, success confirmé.
