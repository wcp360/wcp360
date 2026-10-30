# 811 --- Entitlement & Quota Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Avant toute création de domaine/mailbox/DB/backup/runtime, le service
vérifie `Entitlement + ResourceLimit + current usage`. Hard limits
bloquent ; soft limits alertent. Les compteurs sont réconciliables
depuis l'état observé. CPU/RAM/I/O ne sont exposés que si l'isolation
backend réellement installée peut les garantir.

## V4.0.12 normative deepening

- `LIFE-4012-21-ENTITLEMENT-CANONICAL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
