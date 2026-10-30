# 793 --- Security Scanner Adapter Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Malware

ClamAV/Maldet/Imunify360 restent adapters selon installation/licence.
Contrat : scan target by resource ID, progress, finding, quarantine
capability if supported, signatures/version, health.

## Intrusion

Fail2ban/CrowdSec : policy/status/findings/actions selon capability.

## Règle

Une détection n'est jamais transformée automatiquement en suppression
destructive sans policy explicite.

## V4.0.7 normative deepening

- `SECOPS-407-22-MALWARE-SCANNER-ADAPTERS.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
