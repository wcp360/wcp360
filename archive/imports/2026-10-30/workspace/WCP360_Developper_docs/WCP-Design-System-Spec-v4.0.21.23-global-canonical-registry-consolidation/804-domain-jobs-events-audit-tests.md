# 804 --- Domain Jobs, Events, Audit & Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Jobs : `domain.provision`, `domain.suspend`, `domain.delete`,
`registrar.renew`, `registrar.transfer`. Events :
`domain.created/activated/suspended/deleted`,
`registrar.status_changed`. Tests : workflow partiellement
échoué/reprise, quota, cross-tenant, registrar capability mismatch,
suppression non destructive du registrar par défaut, audit complet.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-27-ACCOUNT-DOMAIN-WEB-PHP-JOBS-EVENTS.md`
- `HOSTING-404-29-HOSTING-CORE-E2E-SCENARIOS.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
