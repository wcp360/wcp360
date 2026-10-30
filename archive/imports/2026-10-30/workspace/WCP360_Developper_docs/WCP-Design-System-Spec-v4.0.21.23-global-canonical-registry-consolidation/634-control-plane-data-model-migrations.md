# 634 --- WCP Data Model & Schema Migrations

## Statut de provenance

**\[V3.5 --- architecture proposée à partir des lacunes de la V3.4\]**
Ce contrat n'était pas spécifié en profondeur dans les documents
précédents. Il doit être validé par l'architecture technique avant
implémentation.

## Objectif

Gouverner l'évolution du schéma persistant WCP.

## Contrat

Migration versionnée, ordre déterministe, preflight, compatibility
window pendant rolling upgrade si supporté, journal des migrations,
failure handling.

## Règle

Une migration destructive nécessite stratégie explicite de
sauvegarde/restore ou migration progressive. Le rollback applicatif ne
doit pas être promis si le schéma n'est pas backward-compatible.

## V4.0.12 normative deepening

- `LIFE-4012-10-DATABASE-SCHEMA-MIGRATIONS.md`
- `LIFE-4012-11-DATA-BACKFILL-MIGRATION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.

## V4.0.14 normative deepening

- `BOOT-4014-27-TRANSFER-CENTER.md`
- `BOOT-4014-28-MIGRATION-COMPATIBILITY.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
