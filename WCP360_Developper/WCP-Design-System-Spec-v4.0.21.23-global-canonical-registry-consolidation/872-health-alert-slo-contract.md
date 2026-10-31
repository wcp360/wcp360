# 872 --- Health, Alert & SLO Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Health agrège signals explicables avec cause/evidence. Alert lifecycle :
pending → firing → acknowledged/silenced si supporté → resolved. SLO :
indicator, objective, window, burn/error budget si applicable. Les
règles ne doivent pas dépendre d'une valeur telemetry inexistante sans
état `unknown`.

## V4.0.10 normative deepening

- `OPS-4010-09-ALERT-RULE-MODEL.md`
- `OPS-4010-10-ALERT-DEDUP-SUPPRESSION.md`
- `OPS-4010-02-HEALTH-STATE-MODEL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
