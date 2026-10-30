# 404 --- Mail Rate Limits & Sending Policies

## Objectif

Limiter envois par compte/domaine/IP selon backend.

## Pages

Policies; Effective limits; Violations; Overrides.

## Modèle

Messages/time window, recipients/time window, concurrency si moteur
supporte, inherited/local values.

## Actions

Assign policy, temporary override, reset to inherited.

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
