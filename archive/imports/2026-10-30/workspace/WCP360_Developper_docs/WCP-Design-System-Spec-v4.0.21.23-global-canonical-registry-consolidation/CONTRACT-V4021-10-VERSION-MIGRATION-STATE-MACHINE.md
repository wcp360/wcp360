# V4.0.21 --- Version & Migration State Machine

## Statut

**NORMATIVE --- V4.0.21 DETERMINISTIC CONTRACT CLOSURE --- NO CODE**

## Mandat

Cette passe ferme exclusivement les 12 blockers P0 de
`AUDIT-V4020-02-P0-BLOCKERS.md`. Aucune capability produit n'est
ajoutée. Les identifiants ci-dessous sont des contrats de conception non
exécutables.

## Règle

Aucun `CRUD`, wildcard `*`, `when needed`, `policy based`,
`according to adapter`, `if large` ou `N/A` non justifié n'est autorisé
dans un registry normatif final.

## Version axes

`release_version`, `db_schema_version`, `resource_spec_version`,
`rpc_protocol_version`, `config_template_version`,
`backup_manifest_version`.

## Release compatibility states

`DISCOVERED → COMPATIBILITY_CHECKED → STAGED → MIGRATION_PREPARED → APPLYING → VERIFYING → COMMITTED`.
Failure transitions: `BLOCKED`, `ROLLBACK_REQUIRED`,
`RECOVERY_REQUIRED`.

## DB migration

Only
`expand → migrate/backfill → verify → switch readers/writers → contract`
is allowed for destructive evolution. `contract` cannot occur in the
same compatibility window if the previous supported release still
requires old fields.

## RPC

Web/worker and `wcpd` advertise protocol min/max. Upgrade order must
preserve an overlap. No Job is dispatched if its required RPC
method/schema version is outside negotiated overlap.

## Config templates

Generated resources store template version + generation fingerprint.
Release upgrade does not rewrite all configs blindly; reconciliation
plans only resources whose template/spec migration requires it.

## Resource specs

Every spec-changing migration is idempotent and records source/target
version. Failed conversion leaves original desired spec recoverable.

## Backup manifests

Readers declare supported manifest ranges. A release cannot purge the
last reader capable of restoring backups still inside mandatory
retention without an explicit migration/retention decision.

## Downgrade

Allowed only if DB schema, resource specs, RPC, templates and backup
reader remain compatible. Otherwise downgrade is `BLOCKED`; package
rollback alone is forbidden.

## Failed migration recovery

Before irreversible step: restore previous application release and
expanded schema. After irreversible external mutation: enter
`RECOVERY_REQUIRED`, run explicit reconciliation, never silently
downgrade.
