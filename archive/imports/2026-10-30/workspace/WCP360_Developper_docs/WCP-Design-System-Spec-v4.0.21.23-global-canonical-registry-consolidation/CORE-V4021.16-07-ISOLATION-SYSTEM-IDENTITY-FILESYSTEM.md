# V4.0.21.16 --- Isolation / System Identity / Filesystem Registry

## Chain

hosting_account → isolation_profile → system_identity → managed_root →
service processes/cgroups/quotas.

## Operations

`SYSTEM-IDENTITY-PROVISION`, `SYSTEM-IDENTITY-LOCK`,
`SYSTEM-IDENTITY-DELETE`, `ISOLATION-PROFILE-ASSIGN`,
`ISOLATION-RECONCILE`, `MANAGED-ROOT-PROVISION`, `MANAGED-ROOT-VERIFY`,
`FILESYSTEM-QUOTA-APPLY`.

All mutations are Job-backed closed privileged actions.

## Invariants

No WCP-USER supplied absolute privileged path. UID/GID ownership cannot
overlap between tenants under a profile requiring OS-user isolation.
Symlink/hardlink escape checks apply at managed-root boundaries. Native
Linux and CloudLinux providers expose the same canonical isolation
contract where capabilities overlap.
