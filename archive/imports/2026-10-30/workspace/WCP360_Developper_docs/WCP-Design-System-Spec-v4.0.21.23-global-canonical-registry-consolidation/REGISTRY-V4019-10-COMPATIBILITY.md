# V4.0.19 — Release / Schema / Spec / RPC / Backup Compatibility Matrix

## Statut
**NORMATIVE — V4.0.19 TRACEABILITY CLOSURE — NO CODE**

## Base
Cette passe ferme exclusivement le backlog P0 de `PLAN-V4018-18-CLOSURE-BACKLOG.md`. Elle n'ajoute aucune capability produit et ne crée aucun artefact exécutable.

## Règle de fermeture
Chaque cellule obligatoire est `PASS` ou `N/A` avec justification. Aucun `PARTIAL` P0 n'est accepté dans les registries V4.0.19.

| Axis | V1 writer | V1 reader | Compatibility rule | Rollback rule |
|---|---|---|---|---|
| WCP release | current stable | current stable | package manifest declares compatible component set | downgrade only when all axes permit |
| DB schema | current release target | same release target | forward ordered; expand/migrate/contract for incompatible change | no binary rollback across incompatible contracted schema |
| API | `/api/v1` | clients supporting v1 | no breaking semantic change inside v1 | old client remains valid within published support window |
| RPC protocol | release-declared V1 protocol | matching wcpd | V1 single-node may require exact compatible pair | app/daemon rollback as compatible pair |
| config template | adapter-declared version | matching adapter | generated config records template/generation version | reactivate last valid compatible generation |
| resource spec | type-specific spec version | current + explicitly supported older readers | explicit migration; no silent rewrite | restore/rollback only if reader supports stored spec |
| backup manifest | manifest V1 | releases declaring V1 restore support | preflight source/target compatibility before restore | unsupported restore is blocked, never guessed |

## Rule
Chaque release publie ses min/max compatibles ; ce document fixe la relation, pas des numéros futurs inventés. **VERSION COMPATIBILITY GAP = CLOSED FOR REPOSITORY ENTRY.**
