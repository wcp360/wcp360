# 782 --- Backup Engine & Destination Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Engine

Plan/run/progress/cancel-if-safe/manifest/restore/verify/incremental/encryption
capabilities.

## Destination

Connectivity test, capacity if observable, read/write/delete,
TLS/transport, credential reference, retention/immutability capability.

## Restic

Premier adapter V1 ; ses capacités réelles sont exposées sans prétendre
qu'elles sont universelles aux futurs engines.

## V4.0.7 normative deepening

- `SECOPS-407-14-BACKUP-DESTINATIONS.md`
- `SECOPS-407-15-BACKUP-ENCRYPTION-SECRETS.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
