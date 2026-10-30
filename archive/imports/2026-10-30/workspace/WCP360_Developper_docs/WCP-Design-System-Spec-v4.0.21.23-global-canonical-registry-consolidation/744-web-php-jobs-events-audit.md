# 744 --- Web/PHP Jobs / Events / Audit

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

Jobs : `website.config.apply`, `webserver.reload`,
`php.runtime.install/remove`, `php.pool.apply`, `php.extension.change`.

Events : `website.created/updated`, `webserver.config_applied`,
`php.runtime_changed`, `php.pool_changed`.

Audit : resource ID, node, adapter, generation, diff metadata,
validation/reload/health result.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-27-ACCOUNT-DOMAIN-WEB-PHP-JOBS-EVENTS.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
