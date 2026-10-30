# 822 --- File Editor & Archive Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Éditeur : texte uniquement selon taille/encoding autorisés, optimistic
concurrency/ETag pour éviter overwrite silencieux. Archive : zip/tar.gz
selon capability, extraction vers staging, validation de tous les paths
avant commit. Preview images/documents ne doit pas exécuter contenu
actif côté serveur.

## V4.0.7 normative deepening

- `SECOPS-407-04-FILE-EDITOR-MONACO.md`
- `SECOPS-407-06-ARCHIVE-CREATE-EXTRACT.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
