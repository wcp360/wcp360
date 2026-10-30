# 471 --- Knowledge Base

## Objectif

Aide structurée liée aux modules WCP.

## Pages

Categories; Articles; Search; Contextual help; Admin
authoring/versioning.

## Règle

La KB peut documenter les technologies installées mais ne doit pas
afficher des instructions incompatibles avec les capabilities du serveur
lorsqu'une aide contextuelle est fournie.

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
