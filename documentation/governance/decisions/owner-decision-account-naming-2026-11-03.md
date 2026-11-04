# Owner decision: path naming and account identifiers

Recorded: November 3, 2026. Source: the project owner's naming instruction in the working session. Scope: new project-owned names and new human panel-login allocations. This records the owner's target, with technical application boundaries; it does not amend frozen contracts or change existing accounts.

## Confirmed target

- Use English ASCII lowercase kebab-case for freely named new project paths, without spaces or accents. Use underscores only for explicit prefix/version separation where the language or consumer does not require another format. Preserve meaningful extensions.
- New panel login usernames have 6 through 32 ASCII characters: `^[a-z][a-z0-9_]{5,31}$`, interpreted as a strict full-string match. Begin with a lowercase letter; permit only lowercase letters, digits and underscores thereafter.
- Reject at least `root`, `admin`, `system`, `www-data`, `support` and `test` for new human login allocation. Reserve service/operator identities and enforce atomic uniqueness within the authoritative context.
- `usr_`, `adm_` and a potential future `rev_` are optional organizational examples. Prefixes confer no permissions. Reseller remains excluded from the Community target.

## Technical application boundaries

The project already uses PSR-4 class paths, framework and standard filenames, Go `_test.go` files, fixed executable entrypoints, external names and existing public identifiers. These retain their required spelling and case. No global renaming is authorized by this document.

The generated hosting UNIX identity remains distinct: the frozen design uses `wcp_<base32-id-fragment>`, maximum 31 characters, immutable after allocation. Internal service users and Linux root are not human login allocations and must not be renamed or disabled by this rule. The 32-character panel limit is a WCP360 policy, not a universal kernel constraint. A regex and a prefix alone guarantee neither uniqueness nor security.

## Integration still required

The executable candidate currently accepts 3–32-character usernames and reserved examples including `root`/`admin`; it is not changed by this decision. New allocation checks, persistence constraints and legacy-compatible lookup/recovery/restore need a bounded implementation and exact-source tests. Existing accounts must not be silently renamed or locked out. No role, permission, UID/GID, account or credential is modified here.

The instruction does not define a new password policy. The current candidate policy remains separate; do not apply the username regex or six-character minimum to passwords. MFA remains optional per administrator and enforced once enabled. Free remains unlimited in sites without premium packages, and no reseller functionality is restored to Community.

The [implementation guide](../../development/naming-and-account-identifiers.md) owns the detailed examples, migration boundaries, dependencies and future acceptance cases. This decision does not qualify a production path or change its maturity.
