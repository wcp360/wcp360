# V4.0.12 --- V4.0.12 READY Gate

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

## SPECIFIED-DEEP

Release/manifest/artifact/provenance, channels, policies,
single-node/cluster upgrades, OS/packages/kernel/reboot, schema/backfill
migrations, config generations, feature flags, compatibility,
deprecation/EOL, rollback/recovery, reconciliation, canary, security
updates, release notes, entitlements/licensing, plan migrations,
approvals, telemetry, air-gap/mirror, backup readiness and threat model.

## Critères

No unsigned/unverified privileged update, no blind retry after ambiguous
side effect, no fake rollback across irreversible migration, no
unsupported mixed-version rollout, no feature-flag security bypass, no
destructive license outage/expiry behavior and explicit EOL/deprecation
governance.

## Statut

**V4.0.12 = SPECIFIED-DEEP --- NO CODE --- NOT GLOBAL READY.**

La prochaine étape ne doit pas être une nouvelle famille arbitraire :
elle doit être l'audit transversal exhaustif du corpus, avec inventaire
des PARTIAL/DECISION REQUIRED/TODO, contradictions, doublons, références
orphelines, dépendances circulaires, décisions ADR manquantes et matrice
finale READY par famille.
