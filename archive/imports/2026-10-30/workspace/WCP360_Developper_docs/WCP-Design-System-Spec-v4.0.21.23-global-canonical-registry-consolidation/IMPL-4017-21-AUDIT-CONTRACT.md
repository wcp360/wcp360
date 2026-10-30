# V4.0.17 — Audit Record & Tamper-evidence Contract

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

## Record
UTC timestamp, audit ID, real/effective actor, tenant, capability/action, resource, request/job/correlation IDs, source context, outcome, safe before/after summary ou diff borné.

## Chaînage
Batch/checkpoint hash conceptuel + export scellable. Le mécanisme détecte des altérations dans sa frontière mais ne prétend pas résister à un root totalement compromis sans export externe.

## Retention
Policy par classe ; purge auditable ; security/admin audit plus long que logs techniques selon politique opérateur.

## Consultation
Filtres bornés, export autorisé, RBAC strict et séparation tenant/admin.
