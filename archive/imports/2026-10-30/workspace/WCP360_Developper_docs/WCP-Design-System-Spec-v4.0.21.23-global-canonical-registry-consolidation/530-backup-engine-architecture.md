# 530 --- Backup Engine Architecture

## Objectif

Séparer **moteur de sauvegarde**, **destination**, **policy**,
**recovery point** et **verification**.

## Contrat moteur

Plan, run, progress, cancel if safe, manifest, restore, verify
capability, incremental capability, encryption capability.

## Adapters

Native, Restic, Borg ou autres futurs moteurs.

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

- `SECOPS-407-12-BACKUP-RESOURCE-MODEL-DEEP.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
