# ADR-011 --- Backup Engine Baseline

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

WCP définit un moteur natif d'orchestration/manifest et certifie
**Restic** comme premier adapter de repository externe. Destinations V1
: local, SFTP et S3-compatible. NFS est supporté comme filesystem
destination lorsqu'il est monté et validé. Borg/Rclone/Rsync restent
adapters secondaires.

## Règle

Un transfert n'est déclaré `backup` que s'il possède manifest, recovery
point, policy de rétention et restore verification.

## V4.0.7 normative deepening

- `SECOPS-407-12-BACKUP-RESOURCE-MODEL-DEEP.md`
- `SECOPS-407-14-BACKUP-DESTINATIONS.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
