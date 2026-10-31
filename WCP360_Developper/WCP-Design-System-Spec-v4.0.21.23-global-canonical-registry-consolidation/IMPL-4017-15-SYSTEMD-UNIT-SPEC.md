# V4.0.17 — systemd Unit Specification

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

## Units logiques CORE
`wcp-web`/PHP runtime integration, `wcp-worker`, `wcpd`, scheduler/timer périodique, maintenance/update helper borné si nécessaire.

## `wcpd`
Root, hardening maximal compatible, socket local, restart policy bornée, filesystem access allowlistée selon responsabilités.

## Worker/web
Utilisateur `wcp`, no-new-privileges, private temp, restrictive umask, capabilities Linux nulles sauf décision spécifique.

## Ordering
PostgreSQL availability et runtime dirs avant control-plane ; `wcpd` socket avant Jobs privilégiés ; absence d’un service hosting optionnel ne doit pas bloquer le panel.

## Failure
Restart loops bornées et visibles dans health ; pas de masquage par restart infini.
