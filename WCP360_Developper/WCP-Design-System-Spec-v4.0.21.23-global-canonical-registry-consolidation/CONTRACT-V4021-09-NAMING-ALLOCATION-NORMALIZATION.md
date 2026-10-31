# V4.0.21 --- Naming, Allocation & Normalization Registry

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

## Hosting system name

Canonical source is opaque account ID plus human-safe prefix, not
customer display name. Format: `wcp_<base32-id-fragment>`; lowercase
ASCII, `[a-z0-9_]`, max 31 chars. Immutable after allocation.

## UID/GID

WCP owns a configurable non-system allocation range. Allocation is
transactional in PostgreSQL under an allocation lock; before commit WCP
verifies `/etc/passwd`/NSS absence through typed observation. UID=GID by
default for a hosting account. IDs are never immediately reused; a
quarantine interval and tombstone prevent restore/cross-ownership
collisions.

## Paths

Tenant root: `/srv/wcp-data/users/<account-id>/`. Site roots use opaque
site ID directories; domains/display names are metadata/symlinks only
where explicitly safe. User input never forms an absolute path.

## Domains

Trim terminal dot for storage; Unicode input normalized through IDNA to
canonical lowercase A-label `fqdn_ascii`; preserve display Unicode
separately. Reject invalid labels, IP literals where domain required,
wildcard except explicit certificate/DNS surfaces, and reserved/internal
names.

## Mail

Domain uses canonical domain normalization. Localpart policy V1 is ASCII
conservative; preserve entered display separately but uniqueness uses
normalized localpart. Address uniqueness is mailbox-domain scoped.

## Database names/users

MariaDB system identifiers are generated from account opaque prefix +
resource opaque suffix, never raw domain/customer name. Length is capped
below engine maximum to reserve migration suffixes. Collision is
resolved by longer opaque suffix, not numeric race-prone probing.

## DNS

Owner names canonicalized relative to zone; `@` is UI notation only, not
stored canonical owner. RR type uppercase. RDATA canonicalization is
type-specific before equality/uniqueness checks.

## Reserved identifiers

`root`, `wcp`, system service users, SQL administrative identities,
RFC-reserved/internal panel hostnames and configured operator-reserved
prefixes cannot be allocated to tenant system identities.
