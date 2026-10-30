# V4.0.12 --- Lifecycle End-to-End & Failure Matrix

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

## Scénarios

1.  Stable release discovery and signed artifact verification.
2.  Tampered artifact rejected.
3.  Withdrawn release blocked.
4.  Single-node update success.
5.  Preflight insufficient disk.
6.  DB migration fails transactionally.
7.  Irreversible migration blocks rollback claim.
8.  Crash after package switch before Job ack.
9.  Resume/reconcile update.
10. Kernel update requiring reboot.
11. Node fails to return after reboot.
12. Rolling cluster upgrade.
13. Version skew exceeds matrix.
14. Quorum risk stops rollout.
15. Canary health regression.
16. Emergency security update.
17. Admin pins EOL version.
18. Feature flag kill switch.
19. Config generation rollback.
20. Plan entitlement migration with overage.
21. Billing maps to wrong/unknown plan version.
22. Licensing endpoint unavailable.
23. License expiry never deletes hosted data.
24. Air-gapped signed bundle update.
25. Signing-key compromise/withdrawal response.
