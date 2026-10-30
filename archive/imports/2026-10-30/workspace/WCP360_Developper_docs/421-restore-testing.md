# 421 --- Restore Testing

## Objectif

Tester périodiquement la restaurabilité dans un environnement isolé
lorsque l'infrastructure le permet.

## Workflow

Select recovery point → isolated target → restore → health/integrity
checks → destroy test target → report.

## Sécurité

Aucune collision avec production ; secrets et données restaurées soumis
aux mêmes contrôles ; destruction de l'environnement test auditée.

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
