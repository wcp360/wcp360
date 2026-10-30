# 451 --- Disk Health & Failure Management

## Objectif

Centraliser alertes disque, SMART, erreurs I/O et capacité.

## Pages

Devices; Health; Alerts; Tests; Replacement notes/workflow if
orchestrator supports.

## États

Healthy, warning, failing, unknown, test running ; `unknown` n'est
jamais healthy.

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
