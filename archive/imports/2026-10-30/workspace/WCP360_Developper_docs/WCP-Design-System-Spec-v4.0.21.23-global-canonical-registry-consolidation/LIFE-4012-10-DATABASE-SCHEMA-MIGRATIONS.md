# V4.0.12 --- Control-plane Database Schema Migrations

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

## Migration

ID, from/to schema generation, dependencies, reversible flag,
online/offline characteristics, estimated impact and verification.

## Rules

Ordered, idempotent where feasible, recorded exactly once with
checksum/version.

## Expand/contract

Preferred for rolling/mixed-version compatibility: add compatible
structures → deploy readers/writers → backfill → later remove deprecated
structures.

## Irreversible

Requires explicit gate, backup/recovery plan and no false one-click
rollback claim.

## Failure

Transaction rollback where engine permits; otherwise migration recovery
state with operator procedure.

## Drift

Unexpected schema/checksum mismatch blocks automatic continuation.

## V4.0.14 normative deepening

- `BOOT-4014-27-TRANSFER-CENTER.md`
- `BOOT-4014-28-MIGRATION-COMPATIBILITY.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
