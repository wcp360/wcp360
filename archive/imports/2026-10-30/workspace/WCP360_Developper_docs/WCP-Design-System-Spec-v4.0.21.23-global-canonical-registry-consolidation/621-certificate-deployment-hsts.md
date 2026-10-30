# 621 --- Certificate Deployment / HSTS / Stapling

## Fonctions

Deploy certificate vers web/mail/services supportés, expiry monitoring,
renewal policy, HSTS policy, OCSP stapling lorsque stack compatible.

## Sécurité

HSTS, particulièrement `includeSubDomains`/preload, nécessite
confirmation d'impact ; ne pas l'activer automatiquement.

## CAA

Lien avec le module DNS CAA pour diagnostic, sans écrasement aveugle.

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
