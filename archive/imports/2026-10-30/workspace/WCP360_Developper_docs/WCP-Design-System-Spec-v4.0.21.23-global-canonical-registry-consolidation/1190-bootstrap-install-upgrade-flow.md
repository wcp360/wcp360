# 1190 --- Install / Bootstrap / Upgrade Flow

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

Install package → create system users/dirs → install immutable release →
write default non-secret config → initialize DB/migrate with dedicated
migration identity → enable/start broker/daemon/app → local one-time
owner bootstrap → health check.

Upgrade : preflight compatibility/disk/DB → backup control-plane
metadata selon policy → expand migration → install new release → switch
`current` → restart/reload in dependency order → health/IPC/API smoke →
retain `previous` → contract migrations later.

Rollback applicatif n'implique pas rollback DB destructif ;
compatibilité expand/contract protège le retour à la release précédente
autorisée.

## V4.0.12 normative deepening

- `LIFE-4012-06-SINGLE-NODE-UPGRADE.md`
- `LIFE-4012-07-CLUSTER-ROLLING-UPGRADE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.

## V4.0.14 normative deepening

- `BOOT-4014-01-INSTALLER-ARCHITECTURE.md`
- `BOOT-4014-02-SYSTEM-PREFLIGHT.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
