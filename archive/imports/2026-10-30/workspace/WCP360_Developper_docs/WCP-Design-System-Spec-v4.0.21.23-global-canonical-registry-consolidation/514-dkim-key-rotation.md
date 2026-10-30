# 514 --- DKIM Key Rotation

## Workflow

Generate new selector/key → publish public record → wait/validate DNS →
activate signing selector → grace period → retire old key.

## Sécurité

Private key non exportée par défaut ; rotation auditée ; rollback
possible tant que l'ancienne clé reste valide.

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

## V4.0.6 normative deepening

- `MAIL-406-11-DKIM-KEY-LIFECYCLE.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.
