# 552 --- SFTP / Shell Restrictions

## Fonctions

Shell allowed/disabled, SFTP-only, chroot/jail adapter, allowed commands
only if architecture explicitly supports it.

## Scope

Policy global/plan/account ; valeur effective visible.

## Sécurité

Ne pas simuler une isolation forte avec une simple restriction UI.

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

## V4.0.7 normative deepening

- `SECOPS-407-09-SFTP-FTP-ACCOUNT-DEEP.md`
- `SECOPS-407-10-SSH-USER-KEYS-SHELL.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
