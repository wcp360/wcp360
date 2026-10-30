# 531 --- Backup Destinations

## Types

Local filesystem, SFTP/SSH, S3/S3-compatible, NFS et futurs providers.

## Contrat

Connectivity test, capacity/usage if available, write/read/delete
capability, encryption transport, credential reference,
retention/immutability capability.

## Sécurité

SSRF/network egress policy pour destinations distantes ; credentials
write-only.

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

- `SECOPS-407-14-BACKUP-DESTINATIONS.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
