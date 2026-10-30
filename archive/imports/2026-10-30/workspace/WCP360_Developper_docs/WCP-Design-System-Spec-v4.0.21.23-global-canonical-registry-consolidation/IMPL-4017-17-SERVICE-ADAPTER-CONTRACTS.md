# V4.0.17 — Managed Service Adapter Contract

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

## Interface conceptuelle
`discover`, `validate_desired`, `plan`, `apply`, `verify`, `observe`, `reconcile`, `backup_metadata`, `health` selon capacité.

## CORE adapters
Nginx, PHP-FPM, PowerDNS, MariaDB, Postfix, Dovecot, Rspamd, nftables, systemd, filesystem/quota, ACME/certificate, S3-compatible backup.

## Résultats
Typed evidence, changed/no-change, warnings, retry class, observed version/state.

## Version support
Chaque adapter consulte la compatibility matrix. Une version inconnue ne devient pas automatiquement supportée.
