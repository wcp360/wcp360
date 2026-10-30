# 832 --- Application Installer Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Template manifeste versionné : requirements runtime/extensions/DB,
download source/checksum/signature lorsque disponible, install steps
déclaratives, health probe, update/rollback capabilities. Les
installateurs ne reçoivent pas un shell root arbitraire. Toute opération
privilégiée passe par Action Registry/adapters.

## V4.0.14 normative deepening

- `BOOT-4014-01-INSTALLER-ARCHITECTURE.md`
- `BOOT-4014-02-SYSTEM-PREFLIGHT.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
