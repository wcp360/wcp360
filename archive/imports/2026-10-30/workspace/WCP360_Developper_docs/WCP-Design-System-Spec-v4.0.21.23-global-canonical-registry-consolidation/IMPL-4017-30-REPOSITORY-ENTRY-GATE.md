# V4.0.17 — Repository Entry Gate

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

## Conditions minimales
1. Resource catalog et state machines approuvés.
2. Logical DB schema/invariants approuvés.
3. API map/error/idempotency approuvés.
4. Job catalog/retry/locking approuvés.
5. wcpd RPC catalog/envelope approuvés.
6. Filesystem/systemd/config generator contracts approuvés.
7. RBAC/secrets/audit/events approuvés.
8. Security/E2E acceptance matrices approuvées.
9. Migration/versioning et traceability approuvés.

## Interdit avant PASS
Repository skeleton de production, migrations SQL, OpenAPI/protobuf exécutables, systemd units installables, scripts d’installation ou code métier.

## Sortie
Après audit de cette V4.0.17, une phase séparée peut décider si le gate passe. Le présent document ne s’auto-certifie pas.
