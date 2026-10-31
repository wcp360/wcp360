# V4.0.17 — Schema, API, Config & Resource Versioning Plan

## Statut
**NORMATIVE — V4.0.17 — CANONICAL IMPLEMENTATION CONTRACT — NO CODE**

## Portée
Ce document traduit les décisions gelées en V4.0.16 en contrat d’implémentation non exécutable. Il ne crée ni SQL, ni OpenAPI, ni protobuf, ni unit systemd installable, ni code.

## Invariants transversaux
- IDs internes stables et non dérivés d’un nom affiché.
- `desired_state`, `observed_state` et état d’exécution restent distincts.
- Toute mutation privilégiée passe par Job puis `wcpd`, sauf lecture explicitement non privilégiée.
- RBAC, tenant scope, audit, idempotency, secrets et correlation ID s’appliquent à chaque surface.
- Suppression logique, rétention et destruction physique sont des étapes distinctes lorsque des données client existent.

## Axes distincts
Release WCP, DB schema version, API version, RPC protocol version, config-template version, resource spec version, backup manifest version.

## DB migrations
Forward migrations ordonnées, transactional quand possible, expand/migrate/contract pour changements incompatibles, preflight espace/backup et recovery plan.

## Resource specs
Reader accepte versions supportées ; migration explicite des anciennes specs ; jamais mutation silencieuse non auditée.

## Backup
Restore vérifie compatibilité manifest/release ; migration de restore explicitement supportée ou bloquée.

## Rollback release
Possible uniquement si DB/config/data compatibility le permet ; sinon recovery/forward-fix.
