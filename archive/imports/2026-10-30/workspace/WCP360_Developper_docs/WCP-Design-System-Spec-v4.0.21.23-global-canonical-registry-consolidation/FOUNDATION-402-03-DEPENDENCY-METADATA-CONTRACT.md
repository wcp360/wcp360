# V4.0.2 --- Dependency Metadata Contract

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Métadonnées obligatoires par module

`Depends on`, `Provides`, `Optional capabilities`, `Consumes events`,
`Emits events`, `Privileged actions`, `Secrets`, `Ports/sockets`,
`Persistent data`, `Configuration ownership`, `Backup scope`,
`Observability`, `Failure dependencies`, `OS constraints`.

## Types

**hard-runtime**, **capability**, **adapter**, **policy**,
**optional-integration**, **future**.

## Propagation

Une dépendance `degraded` ne rend pas automatiquement tous ses
consommateurs `failed`. Chaque consumer définit sa politique :
unavailable, degraded, queued, read-only ou unaffected.

## Cycles

Les cycles de dépendance runtime sont interdits sauf justification
architecture explicite et mécanisme de démarrage/recovery documenté.
