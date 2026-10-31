# 1111 --- Database Migration Execution Rules

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

Migrations versionnées `YYYYMMDDHHMMSS_description.sql` ou mécanisme
équivalent choisi par l'équipe PHP, mais un seul moteur devient
canonique.

Prod : forward-only par défaut. Rollback de données destructif n'est pas
promis. Les changements utilisent expand → dual-compatible code →
contract dans une release ultérieure.

Le service de migration utilise un compte DB dédié différent du compte
runtime. WCP-ADMIN/WCP-USER runtime n'ont pas `CREATE ROLE`,
`CREATE DATABASE` ou ownership du cluster control-plane.

## V4.0.12 normative deepening

- `LIFE-4012-10-DATABASE-SCHEMA-MIGRATIONS.md`
- `LIFE-4012-11-DATA-BACKFILL-MIGRATION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.

## V4.0.14 normative deepening

- `BOOT-4014-27-TRANSFER-CENTER.md`
- `BOOT-4014-28-MIGRATION-COMPATIBILITY.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
