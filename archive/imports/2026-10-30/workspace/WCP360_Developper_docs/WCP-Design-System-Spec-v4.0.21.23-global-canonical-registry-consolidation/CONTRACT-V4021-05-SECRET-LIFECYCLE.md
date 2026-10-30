# V4.0.21 --- Secret Material, Rotation & Rollout Contract

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

## Objects

`secret_ref` stores metadata only. Each secret has immutable material
versions: `PENDING → ACTIVE → RETIRING → REVOKED`, with `FAILED`
terminal for an unusable pending version.

## Create

1.  Authorize owning resource action.
2.  Generate material with CSPRNG or receive provider-generated material
    over protected channel.
3.  Encrypt before durable persistence.
4.  Persist `PENDING` version and reference.
5.  Deploy to owning service.
6.  Verify using a non-destructive authentication/probe.
7.  Atomically mark new version `ACTIVE`.
8.  Return plaintext only if the product contract explicitly requires
    show-once delivery.
9.  Audit metadata only.

## Rotate

1.  Lock `secret:{id}` plus owning resource.
2.  Create new `PENDING` version.
3.  Deploy new material without revoking old where dual-validity is
    supported.
4.  Verify new material.
5.  Switch owner reference to new version.
6.  Mark old `RETIRING`.
7.  Invalidate old material at provider/service.
8.  Verify old material is rejected when testable.
9.  Mark old `REVOKED`.

If dual-validity is impossible, the owning-resource Job must define a
bounded maintenance/cutover step. Failure before step 5 leaves old
`ACTIVE`; failure after step 5 triggers owning-resource reconciliation
and never silently returns both as ACTIVE.

## Delete

Deleting metadata does not erase required audit/cryptographic recovery
metadata. Secret material purge follows retention and backup-key policy.

## Forbidden

Plaintext in Job payloads, logs, audit safe-diffs, process argv, URLs,
metrics labels or error messages.
