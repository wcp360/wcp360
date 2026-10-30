# 785 --- Backup / Storage Acceptance Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

-   Restic baseline ;
-   Local/SFTP/S3-compatible destinations ;
-   destination credential secrecy ;
-   failed destination handling ;
-   retention/prune safety ;
-   manifest/integrity ;
-   test restore ;
-   scoped user restore ;
-   overwrite protection ;
-   cancellation semantics ;
-   audit/jobs/events.

## V4.0.7 normative deepening

- `SECOPS-407-29-SECURITY-BACKUP-E2E.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
