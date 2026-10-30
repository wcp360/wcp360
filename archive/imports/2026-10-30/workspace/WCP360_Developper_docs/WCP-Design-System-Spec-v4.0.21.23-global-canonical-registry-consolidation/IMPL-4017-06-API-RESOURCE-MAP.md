# V4.0.17 — REST API V1 Resource Map

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

## Préfixe
`/api/v1`.

## Groupes
`/auth`, `/me`, `/tenants`, `/resellers`, `/accounts`, `/plans`, `/domains`, `/sites`, `/certificates`, `/dns-zones`, `/databases`, `/mail`, `/backups`, `/cron-jobs`, `/security`, `/integrations`, `/jobs`, `/events`, `/audit`, `/health`, `/readiness`, `/system`, `/updates`.

## Actions non-CRUD
Les actions de domaine sont explicites : `suspend`, `resume`, `rotate`, `renew`, `restore`, `verify`, `reconcile`, `retry`, `cancel`, `impersonate/start`, `impersonate/stop`. Pas d’endpoint générique `/execute`.

## Async
Provisioning, deletion, restore, certificate issuance, service/config mutation, update et repair renvoient une Job resource.

## Scope
WCP-USER voit son tenant ; reseller voit ses descendants autorisés ; WCP-ADMIN suit permissions explicites, pas un bypass implicite.
