# 481 --- Commercial Integration Licenses

## Objectif

Formaliser les adaptateurs de licence pour LiteSpeed, CloudLinux,
Imunify360 et futurs produits commerciaux.

## Règle

Chaque intégration déclare son propre modèle de licence/capabilities.
Une licence expirée dégrade uniquement la fonctionnalité concernée selon
le comportement réel du produit tiers.

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

## V4.0.12 normative deepening

- `LIFE-4012-22-LICENSING-BOUNDARY.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
