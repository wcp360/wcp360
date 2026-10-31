# 472 --- Service Status & Announcements

## Objectif

Informer clients/revendeurs des maintenances, incidents et annonces
pertinentes.

## Pages

Status; Active incidents; Maintenance windows; History; Announcements.

## Scope

Global, reseller, node/service ou subset de comptes selon backend. Ne
pas révéler la topologie interne inutilement à WCP-USER.

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
