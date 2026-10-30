# 571 --- Official `wcpctl` CLI

## Objectif

CLI officielle consommant la même API REST que l'interface.

## Exemples de domaines de commandes

`wcpctl domains`, `accounts`, `php`, `backup`, `services`, `migration`,
`jobs`, `logs`.

## Contrat

Pas de logique métier parallèle : validation serveur identique, auth via
service/user credentials appropriés, machine-readable output (`json`) +
human output.

## UX CLI

Exit codes stables, `--output`, `--wait` pour jobs, confirmation
destructive et mode non-interactif explicite.

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
