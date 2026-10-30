# 431 --- Bandwidth Accounting

## Objectif

Mesurer trafic par compte/domaine/ressource lorsque la télémétrie permet
une attribution fiable.

## Pages

Current period; History; Top consumers; Limits; Data source.

## Règle

Toujours exposer période, unité, méthode/source et fraîcheur.
`Bandwidth quota` et `network rate limit` sont deux concepts distincts.

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
