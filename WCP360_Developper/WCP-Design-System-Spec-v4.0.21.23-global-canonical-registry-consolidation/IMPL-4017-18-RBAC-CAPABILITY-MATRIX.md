# V4.0.17 — RBAC Capability Contract

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

## Principes
Permissions par capacité + scope, jamais par simple nom de route. `view`, `create`, `update`, `delete`, `operate`, `security`, `impersonate`, `audit`, `billing/admin` sont séparés lorsque pertinent.

## Rôles de base
WCP root/admin, operator, security operator, support, reseller admin, tenant owner, tenant developer/mail admin selon scope. Les rôles exacts sont bundles de capabilities modifiables par policy, pas des bypass codés.

## Impersonation
Real actor conservé ; effective actor distinct ; step-up ; reason ; durée bornée ; bannière UI ; audit complet ; interdiction d’accéder à certains secrets/recovery material par défaut.

## Deny
Absence de capability = deny. Tenant scope est appliqué au service/data layer, pas seulement à l’UI.
