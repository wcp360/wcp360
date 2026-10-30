# 820 --- File Manager Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Ressources : `FileRoot`, `FileEntry`, `FileOperation`,
`ArchiveOperation`, `EditorSession`. Toute opération est relative à un
`FileRoot` résolu par `PathResolver`; l'API n'accepte pas de chemin
Linux absolu. Metadata : relative path, type, size, owner/mode
observables, timestamps, symlink metadata selon policy.

## V4.0.7 normative deepening

- `SECOPS-407-01-FILE-ROOT-PATH-RESOLUTION.md`
- `SECOPS-407-02-FILE-ENTRY-OPERATIONS.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
