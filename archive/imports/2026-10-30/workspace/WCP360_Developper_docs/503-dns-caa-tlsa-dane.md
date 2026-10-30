# 503 --- CAA / TLSA / DANE

## Scope

Record builders/validation pour CAA et TLSA. DANE est présenté
uniquement lorsque DNSSEC et le contexte applicatif permettent une
utilisation cohérente.

## UX

Formulaires structurés + représentation record ; validation syntaxique ;
explication des dépendances.

## Règle

WCP ne génère pas un TLSA supposé correct sans connaître le
certificat/service cible.

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
