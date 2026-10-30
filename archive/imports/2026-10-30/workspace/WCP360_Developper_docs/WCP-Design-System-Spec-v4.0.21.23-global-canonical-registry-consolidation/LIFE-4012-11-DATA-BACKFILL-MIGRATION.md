# V4.0.12 --- Data Backfill & Long-running Migration

## Statut

**NORMATIVE --- V4.0.12 LIFECYCLE, UPDATES, COMPATIBILITY & PLATFORM
GOVERNANCE DEEP SPECIFICATION --- NO CODE**

Cette passe ferme la gouvernance du cycle de vie de WCP et de sa
plateforme : releases, artefacts, provenance, canaux, mises à jour du
control-plane et des nœuds, OS/packages, migrations, configuration
generations, feature flags, compatibilité, dépréciations, EOL,
maintenance, rollback et frontière d'entitlements/licensing.

## Invariants

-   Une mise à jour n'est jamais équivalente à un simple `apt upgrade`,
    remplacement de binaire ou `git pull`.
-   `Release`, `Artifact`, `UpdatePlan`, `Migration`,
    `CompatibilityRule`, `FeatureFlag`, `ConfigurationGeneration`,
    `Entitlement` et `LicenseState` sont distincts.
-   Un artefact non vérifié n'est jamais promu vers une étape
    privilégiée.
-   Une migration irréversible doit être explicitement déclarée et
    bloquer les promesses de rollback incompatibles.
-   Les upgrades single-node et rolling cluster utilisent le même modèle
    de release mais des orchestrations différentes.
-   Une version EOL peut être détectée/tolérée temporairement selon
    policy, mais n'est jamais présentée comme saine/supportée.
-   Les feature flags ne remplacent ni RBAC ni entitlement.
-   Une indisponibilité d'un service de licence externe ne doit pas
    provoquer une destruction ou suspension irréversible des données
    hébergées.

## Problem

Large tables/state transformations cannot always run inside one
deployment transaction.

## Model

Migration creates resumable backfill Job with cursor/checkpoint, rate
limits and progress evidence.

## Compatibility

Old/new application versions coexist only if read/write semantics remain
safe.

## Load

Throttle based on DB/system health.

## Failure

Resume from checkpoint; duplicate work must be safe/idempotent.

## Completion

Contract phase/removal only after backfill verification.

## V4.0.14 normative deepening

- `BOOT-4014-27-TRANSFER-CENTER.md`
- `BOOT-4014-28-MIGRATION-COMPATIBILITY.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
