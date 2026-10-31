# 561 --- SCIM Provisioning

## Objectif

Provisioning/deprovisioning Enterprise depuis un IdP.

## Scope

Users, groups, active state, selected attributes ; mapping groups→WCP
roles/scopes via politique explicite.

## Sécurité

Bearer/service credential scoped, audit, conflict handling, no automatic
privilege escalation from an unmapped group.

## Deprovisioning

Désactivation IdP peut révoquer sessions/tokens selon policy ;
ressources hébergées ne sont pas supprimées automatiquement.

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
