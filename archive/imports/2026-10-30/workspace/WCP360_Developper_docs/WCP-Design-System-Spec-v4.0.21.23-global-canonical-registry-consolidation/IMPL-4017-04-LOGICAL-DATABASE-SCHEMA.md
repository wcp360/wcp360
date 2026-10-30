# V4.0.17 — Logical Control-plane Database Schema

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

## Domaines logiques
Identity: users, credentials metadata, MFA factors metadata, sessions, recovery material metadata, API tokens, roles/bindings.
Tenancy: tenants, resellers, hosting accounts, plans, entitlements, quota policies.
Resources: canonical resources, relationships, desired specs, observed summaries, versions.
Execution: jobs, attempts, leases, idempotency records, outbox, events.
Security: secret references, security findings, audit records/checkpoints.
Operations: integrations, notification routes, backup metadata, release/update state, health/readiness summaries.

## Contraintes
Tenant FK/scope obligatoire pour ressources tenant-owned ; uniqueness contextuelle ; optimistic version ; timestamps UTC ; soft-delete seulement quand le lifecycle le requiert.

## JSON
JSONB n’est admis que pour specs/versioned payloads réellement extensibles ; les clés critiques pour intégrité/RBAC/requêtes restent colonnes structurées.

## Interdit
Stocker fichiers clients, dumps DB, contenu mailbox ou secrets plaintext dans PostgreSQL WCP.
