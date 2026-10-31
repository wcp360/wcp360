# 852 --- Migration Security & Cutover

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Source credentials temporaires, least privilege et révocation
recommandée après migration. Cutover DNS/mail/web est un plan explicite
avec TTL/prechecks/recovery. La migration ne supprime pas
automatiquement la source.

## V4.0.12 normative deepening

- `LIFE-4012-10-DATABASE-SCHEMA-MIGRATIONS.md`
- `LIFE-4012-11-DATA-BACKFILL-MIGRATION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.

## V4.0.14 normative deepening

- `BOOT-4014-27-TRANSFER-CENTER.md`
- `BOOT-4014-28-MIGRATION-COMPATIBILITY.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
