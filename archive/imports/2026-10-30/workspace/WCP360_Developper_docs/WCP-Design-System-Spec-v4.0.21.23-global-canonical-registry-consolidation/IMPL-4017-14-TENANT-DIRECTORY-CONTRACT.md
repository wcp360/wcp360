# V4.0.17 — Tenant Directory & Path Safety Contract

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

## Identité de chemin
Le répertoire utilise un identifiant système stable, pas le domaine ni le display name brut.

## Sous-arbres
`sites/<site-id>/public`, `sites/<site-id>/private`, `logs`, `tmp`, `backups-staging` uniquement si policy, et données applicatives explicitement classées.

## Path safety
Canonicalization avant authorization ; interdiction `..`, NUL, device paths, traversal symlink hors root autorisé. Extraction archive avec zip-slip/tar traversal protection.

## Permissions
Tenant UID/GID ; services auxiliaires accèdent par groupe/ACL minimal. File Manager agit sous identité/contrat tenant et ne devient pas root file browser.
