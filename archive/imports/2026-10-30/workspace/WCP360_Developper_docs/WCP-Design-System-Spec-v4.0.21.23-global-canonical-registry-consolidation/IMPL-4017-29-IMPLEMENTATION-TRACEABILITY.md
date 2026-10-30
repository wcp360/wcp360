# V4.0.17 — Requirement-to-Implementation Traceability Contract

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

## Matrice obligatoire avant code
Requirement/spec → ADR → resource type → API/service action → Job/RPC si nécessaire → config/service adapter → audit/event → acceptance tests.

## But
Éviter qu’une exigence de sécurité ou lifecycle existe uniquement dans un document sans point d’implémentation/test.

## Gate
Toute capability CORE V1 sans chaîne de traçabilité complète reste `NOT READY FOR IMPLEMENTATION`.
