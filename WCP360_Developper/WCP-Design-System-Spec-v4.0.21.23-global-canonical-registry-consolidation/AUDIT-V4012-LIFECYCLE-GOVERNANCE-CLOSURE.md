# V4.0.12 --- Lifecycle, Updates & Governance Closure Audit

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

## Base réellement étudiée

La V4.0.11 fournie contient **880 fichiers Markdown**. Cette passe
conserve le corpus et ajoute la couche normative lifecycle/governance.

Des documents déjà présents confirment que les sujets existaient
partiellement, notamment `08-versioning-feature-flags-compatibility.md`,
`103-system-packages-updates.md` et plusieurs fichiers de
migrations/repository/architecture. V4.0.12 approfondit ces surfaces au
lieu de les remplacer.

## Résultat

30 contrats normatifs V4.0.12 ajoutés. **56 documents historiques
réellement présents** ont reçu un lien de deepening par correspondance
de nom.

## Pré-scan du futur audit transversal

Ce scan est volontairement informatif et **ne ferme aucun marqueur** : -
PARTIAL : 597 occurrences - DECISION REQUIRED : 19 occurrences - TODO :
8 occurrences - TBD : 10 occurrences - NOT READY : 0 occurrences

Ces occurrences peuvent inclure des mentions historiques, exemples ou
documents d'audit ; elles devront être classifiées sémantiquement lors
de V4.0.13, pas supprimées mécaniquement.

## Fermetures V4.0.12

Release manifests immuables, artefacts signés/vérifiés,
channels/promotion, preflight, single-node/rolling upgrade,
OS/package/kernel lifecycle, schema/backfill migrations, config
generations, feature flags, compatibility matrix, EOL/deprecation,
rollback vs recovery, update reconciliation, canary, security emergency
updates, entitlements/licensing boundary et offline/mirror update.

## Décisions encore à arbitrer

Format/signature/provenance concret des releases, repository/package
distribution, cadence/support policy, backend de feature flags/config
generation, politique commerciale éventuelle de licensing et matrices
exactes OS/composants restent des ADR/configurations produit avant
implémentation.
