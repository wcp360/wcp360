# 821 --- File Manager API & Security

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Actions : list/stat/read bounded, upload, create dir/file,
rename/move/copy, delete, chmod selon policy, archive/extract, editor
save. Uploads : taille/quota, filename validation, temporary safe
write + atomic move, malware hook configurable. Archives : protection
Zip Slip/path traversal, expansion ratio/size/file-count limits.
Symlinks : aucune sortie du root autorisé.

## V4.0.7 normative deepening

- `SECOPS-407-01-FILE-ROOT-PATH-RESOLUTION.md`
- `SECOPS-407-28-CROSS-TENANT-FILESYSTEM-TESTS.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
