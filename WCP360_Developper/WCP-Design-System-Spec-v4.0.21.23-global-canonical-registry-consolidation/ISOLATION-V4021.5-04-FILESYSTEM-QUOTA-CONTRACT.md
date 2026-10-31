# V4.0.21.5 — Filesystem, Quota & Path Isolation Contract

## Statut
**NORMATIVE — V1 STANDALONE — NO CODE — REPOSITORY BLOCKED**

Cette famille transforme les options d’isolation et de commandes de V4.0.21.4.1 en contrats B1/B2 explicites.

## Invariants
- racine hébergée distincte par compte ;
- UID/GID ownership déterministe ;
- aucune écriture utilisateur dans WCP releases/config/secrets/broker sockets ;
- résolution canonique sous racine autorisée ;
- refus traversal, symlink/hardlink escape, device nodes et archive traversal ;
- temp/session/upload privés lorsque le runtime l’exige.

## Quotas
WCP distingue `commercial allowance` et `kernel/filesystem enforcement`. Bytes et inodes sont séparés. Une limite commerciale sans backend dur est affichée `SOFT`, pas `HARD`.

## Options backend
Quota filesystem natif certifié par OS/filesystem ; project quota peut être supporté par adapter certifié. Aucun backend n’est supposé présent sans capability detection.
