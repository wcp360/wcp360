# 504 --- External DNS Provider Connectors

## Objectif

Permettre à WCP de gérer une zone externe via plugin/provider.

## Contrat

List zones/records, create/update/delete, capabilities,
propagation/provider status, credential scopes.

## Providers

Cloudflare, Route 53 ou autres sont de futurs adapters/plugins ; aucun
n'est requis par le cœur.

## Sécurité

Credentials provider write-only, least privilege, tenant mapping
explicite.

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
