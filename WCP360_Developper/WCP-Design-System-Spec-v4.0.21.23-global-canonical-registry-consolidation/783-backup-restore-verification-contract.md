# 783 --- Restore & Verification Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Restore

Sélection recovery point → preflight destination/capacity/conflicts →
restore isolé ou ciblé → integrity/application checks → commit.

## Verification

Checksum/manifest + test restore périodique selon policy.
`backup completed` et `backup verified` sont deux états distincts.

## Destructif

Overwrite exige impact explicite, permission dédiée et recovery path.

## V4.0.7 normative deepening

- `SECOPS-407-17-BACKUP-INTEGRITY-VERIFICATION.md`
- `SECOPS-407-18-GRANULAR-RESTORE.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`
- `BOOT-4014-26-CONTROL-PLANE-RESTORE.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
