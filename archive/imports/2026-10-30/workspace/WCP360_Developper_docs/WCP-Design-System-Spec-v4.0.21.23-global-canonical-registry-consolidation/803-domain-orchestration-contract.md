# 803 --- Domain Orchestration Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

## Provisioning

Domain request → entitlement/quota → Domain resource → DNS → Web → TLS →
Mail selon options → verify → active.

## Compensation

Une étape échouée ne déclenche pas automatiquement la suppression des
étapes déjà réussies. Le workflow conserve checkpoints, résultat et
actions de compensation sûres.

## Suppression

Suppression hosting, DNS, mail et registrar sont des intentions
distinctes avec confirmations adaptées.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-07-DOMAIN-LIFECYCLE-ORCHESTRATION.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
