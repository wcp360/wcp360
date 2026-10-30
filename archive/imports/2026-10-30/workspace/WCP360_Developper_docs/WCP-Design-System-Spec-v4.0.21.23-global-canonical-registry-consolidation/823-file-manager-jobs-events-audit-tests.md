# 823 --- File Manager Jobs, Events, Audit & Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Jobs pour opérations volumineuses : copy/move/archive/extract/delete
tree. Audit : mutation metadata, jamais contenu de fichier par défaut.
Tests : traversal, symlink race, zip-slip, quota, huge archive,
concurrent edit, permission denial, tenant isolation, interrupted
upload.

## V4.0.7 normative deepening

- `SECOPS-407-28-CROSS-TENANT-FILESYSTEM-TESTS.md`
- `SECOPS-407-29-SECURITY-BACKUP-E2E.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
