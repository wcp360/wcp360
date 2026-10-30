# 620 --- Advanced ACME / TLS Manager

## Objectif

Approfondir certificats et automatisation ACME.

## Pages

Certificates; ACME Accounts; CAs; Orders; Challenges; Renewals;
Deployments; Policies; Failures.

## Challenges

HTTP-01 et DNS-01 selon capabilities ; wildcard requiert une méthode
compatible.

## CAs

Let's Encrypt, ZeroSSL ou CA ACME personnalisée sont des
adapters/configurations, aucune CA n'est codée comme unique dépendance.

## Contrat transversal

-   **WCP-ADMIN** porte l'administration système/infrastructure et les
    politiques globales.
-   **WCP-USER** ne voit que les capacités explicitement déléguées à son
    scope client/revendeur.
-   Les technologies concrètes sont des **adapters/capabilities** ; le
    cœur WCP dépend d'un contrat stable, pas d'un fournisseur unique.
-   Toute mutation sensible exige autorisation backend, audit, preflight
    lorsque pertinent et résultat confirmé.
-   Toute opération longue est un job observable, annulable/retryable
    lorsque techniquement sûr.
-   Secrets et données sensibles sont redacted dans logs, audit,
    notifications, traces et sorties IA.
