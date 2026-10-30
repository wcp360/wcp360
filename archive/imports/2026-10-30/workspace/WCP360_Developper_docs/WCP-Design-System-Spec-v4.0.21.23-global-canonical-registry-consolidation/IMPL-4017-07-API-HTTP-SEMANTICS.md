# V4.0.17 — API HTTP Semantics, Concurrency & Pagination

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

## Codes
200/201 pour lecture/création synchrone bornée ; 202 pour Job ; 204 pour action sans body ; 400 validation ; 401 auth ; 403 authorization ; 404 non-visible/non-existent selon anti-enumeration policy ; 409 conflict/state ; 412 precondition ; 422 domain validation ; 429 throttling ; 5xx safe.

## Concurrence
ETag/version sur ressources mutables critiques ; `If-Match` requis pour modifications sensibles afin d’éviter lost update.

## Idempotency
Mutations à effet durable acceptent/peuvent exiger `Idempotency-Key`; même clé + même fingerprint retourne résultat/Job antérieur ; même clé + autre fingerprint = conflict.

## Pagination
Cursor opaque, `limit` borné, sort/filter allowlistés. Pas d’offset massif comme contrat principal.
