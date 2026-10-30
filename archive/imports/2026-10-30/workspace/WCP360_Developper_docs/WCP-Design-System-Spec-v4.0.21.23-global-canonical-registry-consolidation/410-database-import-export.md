# 410 --- Database Import / Export

## Objectif

Fournir dumps/imports sûrs pour MySQL/MariaDB/PostgreSQL selon
adaptateurs.

## Pages

Exports; Imports; Jobs; History.

## Actions

Export DB/schema selection; upload/import artifact; choose target;
preflight; execute; download artifact subject to retention.

## Sécurité

Quotas, archive/file validation, credentials non exposés, imports sous
scope tenant, taille/timeout contrôlés.

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
