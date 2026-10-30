# 780 --- Backup / Storage Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Ressources

`BackupPolicy`, `BackupRun`, `RecoveryPoint`, `BackupEngine`,
`BackupDestination`, `RestoreJob`, `VerificationRun`, `StorageTarget`.

## Baseline V1

Orchestration WCP + Restic premier adapter certifié ; destinations
Local, SFTP, S3-compatible ; NFS lorsqu'il est monté/validé.

## Distinction

Un transfert n'est pas un backup sans manifest, rétention et
restaurabilité vérifiable.

## V4.0.7 normative deepening

- `SECOPS-407-12-BACKUP-RESOURCE-MODEL-DEEP.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
