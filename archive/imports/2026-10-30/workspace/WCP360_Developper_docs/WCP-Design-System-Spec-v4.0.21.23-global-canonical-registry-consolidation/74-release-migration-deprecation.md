# 74 --- Release, migration et dépréciation

## Versioning

Le Design System suit SemVer. Les documents portent une version de pack
; les composants peuvent porter leur propre statut.

## Release notes

Ajouts, changements, corrections, breaking changes, migrations et
dépréciations.

## Migration UI

Une évolution de composant documente ancien usage, nouvel usage,
échéance et stratégie de migration.

## Feature rollout

Les fonctionnalités risquées peuvent être activées par
capability/feature flag. L'UI ne doit pas afficher des routes mortes.

## Rollback produit

Différent du rollback de configuration serveur : il concerne la version
du produit et suit la stratégie de déploiement retenue par l'équipe.

## V4.0.12 normative deepening

- `LIFE-4012-10-DATABASE-SCHEMA-MIGRATIONS.md`
- `LIFE-4012-11-DATA-BACKFILL-MIGRATION.md`
- `LIFE-4012-01-PLATFORM-RELEASE-MODEL.md`
- `LIFE-4012-02-RELEASE-MANIFEST-ARTIFACTS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.

## V4.0.14 normative deepening

- `BOOT-4014-27-TRANSFER-CENTER.md`
- `BOOT-4014-28-MIGRATION-COMPATIBILITY.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
