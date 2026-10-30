# MASTER V4.0.12 --- Lifecycle, Updates, Compatibility & Platform Governance

## Pipeline de release

Build/Publish → signed manifest/provenance → channel promotion →
discovery → preflight → recovery readiness → stage → migrations →
switch/apply → restart/reboot as required → functional health →
commit/reconcile.

## Règles structurantes

-   Update success exige état fonctionnel vérifié.
-   Artifact digest/signature mismatch bloque l'installation.
-   Schema migration irreversible rend le rollback binaire
    potentiellement faux.
-   Rolling cluster upgrade n'est autorisé que dans la version-skew
    matrix.
-   Feature flag ≠ permission ≠ entitlement.
-   Replication/HA ≠ backup avant upgrade.
-   EOL/unsupported est visible et gouverné.
-   Licensing éventuel ne peut jamais servir de mécanisme destructif
    contre les données client.
-   Air-gap/mirror conserve les mêmes exigences cryptographiques.

## Suite obligatoire

**V4.0.13 --- Final Cross-Corpus Completeness & Contradiction Audit**
doit maintenant analyser les 900+ fichiers comme un seul système :
familles, dépendances, ADR, statuts, références, duplications,
contradictions, PARTIAL, DECISION REQUIRED, TODO/TBD, sécurité
transversale, scénarios E2E et readiness matrix. Aucun repository
skeleton ni code avant cette fermeture.
