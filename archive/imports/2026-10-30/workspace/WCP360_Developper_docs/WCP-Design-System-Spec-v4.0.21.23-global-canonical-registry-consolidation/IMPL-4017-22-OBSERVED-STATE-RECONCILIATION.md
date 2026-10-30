# V4.0.17 — Observed State & Reconciliation Contract

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

## Observation
Chaque adapter définit preuves : service active, config digest, listener, authoritative DNS answer, DB grant, mailbox existence, quota, certificate validity, etc.

## Drift classes
`IN_SYNC`, `MISSING`, `DRIFTED`, `CONFLICTING`, `STALE`, `UNREACHABLE`, `EXTERNAL_CHANGE`, `UNKNOWN`.

## Reconciliation
Observe → classify → safe plan → policy/approval si mutation → Job → verify. `UNKNOWN` n’autorise pas une réécriture aveugle.

## Freshness
Observed state porte timestamp/source/version. L’UI indique les observations périmées.
