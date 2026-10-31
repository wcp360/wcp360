# V4.0.17 — Job Retry, Locking, Cancellation & Reconciliation

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

## Locks
Clé logique par ressource/service ; ordre déterministe pour opérations multi-ressources. Pas de mutex global sauf maintenance critique.

## Retry classes
`SAFE_RETRY`, `RECONCILE_BEFORE_RETRY`, `NO_AUTOMATIC_RETRY`, `EXTERNAL_BACKOFF`.

## Cancellation
Coopérative ; refusée après point-of-no-return documenté. Cancel ne signifie pas rollback automatique.

## Timeout
Timeout transport/process ≠ échec certain. L’état devient ambigu lorsque l’effet externe peut avoir eu lieu ; observation obligatoire.

## Backoff
Exponentiel borné + jitter conceptuel ; budget de tentatives par Job type ; circuit-breaker/provider health possible.
