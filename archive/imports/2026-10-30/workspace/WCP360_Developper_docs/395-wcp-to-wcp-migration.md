# 395 --- WCP → WCP Migration

## Objectif

Migration native entre deux installations WCP avec contrat versionné.

## Fonctions

Capability negotiation, encrypted source connection, preflight,
full/incremental sync si supporté, resource IDs mapping, cutover,
verification, resumable jobs.

## Compatibilité

Source et destination publient versions API/schema et capacités ; les
incompatibilités sont bloquées ou explicitement downgradées.

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
