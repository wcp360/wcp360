# V4.0.12 --- Update Failure, Resume & Reconciliation

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

## State

planned, downloading, staged, applying, migrating, verifying, committed,
failed_retryable, failed_terminal, recovery_required.

## Resume

Restart after crash reads authoritative update plan/phase and verifies
observed state before continuing.

## Ambiguity

Timeout/restart after side effect triggers inspect/reconcile, not blind
replay.

## Cleanup

Staged artifacts/temp files cleaned only when not required for
resume/forensics.

## Audit

Every phase transition and operator intervention recorded.

## Incident

Critical platform update failure can create V4.0.10 incident
automatically by policy.
