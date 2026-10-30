# V4.0.17 — Filesystem & Package Ownership Tree

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

## `/opt/wcp`
Packages/binaries/assets immuables, versionnés, root-owned, non modifiables par le service web.

## `/etc/wcp`
Configuration locale, policy, keys root-only sous `/etc/wcp/keys`; fichiers générés séparés des overrides opérateur.

## `/var/lib/wcp`
State local non-tenant : release state, generation metadata/cache, recovery metadata bornée. PostgreSQL reste source canonique pour le control-plane.

## `/run/wcp`
Socket `wcpd`, locks/runtime ephemeral ; recréé au boot avec ownership strict.

## `/var/log/wcp`
Logs WCP structurés, rotation/retention.

## `/srv/wcp-data/users/<account-id>`
Tenant data : sites, mail/data selon topology, private/tmp/logs selon conventions détaillées. UID/GID du compte, jamais `wcp`.

## Interdit
Secrets dans `/opt/wcp`; tenant write dans `/etc/wcp`; panel code dans `/srv/wcp-data`.
