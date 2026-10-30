# 572 --- SDK / Infrastructure-as-Code Contract

## Objectif

Préparer SDKs et intégrations IaC à partir d'OpenAPI.

## Cibles

SDKs générés/maintenus selon roadmap ; Terraform/OpenTofu provider et
Ansible collection comme projets/adapters P2.

## Règle

L'IaC utilise Service Accounts, idempotency et resource state/versioning
; aucune API privée UI.

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
