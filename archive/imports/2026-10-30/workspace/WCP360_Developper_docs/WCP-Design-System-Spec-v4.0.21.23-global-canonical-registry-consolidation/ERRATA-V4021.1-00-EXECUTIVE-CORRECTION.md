# V4.0.21.1 --- Executive Correction of Deterministic Closure

## Statut

**CORRECTIVE ERRATA --- NO CODE --- REPOSITORY BLOCKED**

La V4.0.21 a amélioré le corpus, mais son affirmation « 12/12 P0 PASS »
était trop forte. Une relecture mécanique des documents normatifs
V4.0.21 retrouve **28 occurrences** de formulations encore
conditionnelles ou génériques dans des zones qui se présentent comme
déterministes.

## Correction de verdict

-   Architecture / périmètre : **PASS**
-   Structure des registries : **PASS**
-   Déterminisme complet action-par-action : **PARTIAL**
-   P0 réellement clos : **non certifié**
-   Repository Entry : **BLOCKED**
-   Code : **NOT AUTHORIZED**

## Cause

Le corpus contient encore des expressions telles que `only if`,
`if generated`, `target adapter`, `managed service adapters`,
`typed dataset reads`, `same as ...`, ou des références à une policy non
résolue. Ces expressions peuvent être légitimes dans une description,
mais elles ne peuvent pas servir de valeur finale dans une colonne
censée donner un RPC, un adapter, une permission ou une décision exacte.

## Décision

V4.0.22 n'est **pas** lancée. Il faut d'abord corriger V4.0.21 par une
passe V4.0.21.1 puis refaire le re-audit.
