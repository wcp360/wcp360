# 741 --- Web/PHP API & RBAC

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## API

`/api/v1/websites`, `/api/v1/web-servers`, `/api/v1/php/runtimes`,
`/api/v1/php/pools`, `/api/v1/php/extensions`,
`/api/v1/php/ini-profiles`.

Install/remove runtime, apply vhost, reload/restart et extension changes
sont des Jobs lorsque l'opération n'est pas instantanée.

## Permissions

Admin : `web.read/manage`, `php.read/manage`, `web.service.control`.
User : permissions scoped au Website/Domain attribué ; aucun contrôle
global du service.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-28-HOSTING-CORE-RBAC-AUDIT.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
