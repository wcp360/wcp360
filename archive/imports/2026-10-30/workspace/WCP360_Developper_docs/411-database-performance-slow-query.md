# 411 --- Database Performance & Slow Queries

## Objectif

Diagnostic moteur sans afficher de métriques non collectées.

## Pages

Connections; Locks; Slow queries; Top queries; Engine metrics;
Recommendations.

## Données

Source/timestamp obligatoires ; requêtes et paramètres potentiellement
sensibles sont redacted selon policy.

## IA

Une recommandation IA reste distincte d'un finding déterministe et ne
modifie pas automatiquement la configuration.

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
