# WCP360 — DevOps / Build / Release

## Principles

Installation, upgrade, rollback and release must be:
- versioned;
- auditable;
- reproducible;
- testable;
- reversible where practical.

## Candidate filesystem roots

Stable categories:
- product assets;
- `/etc/wcp360`;
- `/var/lib/wcp360`;
- `/var/log/wcp360`;
- `/run/wcp360`;
- cache/temp;
- managed hosting data roots.

Historical documentation uses `/usr/local/wcp360`.

Later specialized work recommends `/usr/lib/wcp360` for package-managed product assets.

`H3CM-X006` remains open; MASTER must not silently choose.

## Physical runtime
- `wcpd` non-root;
- `wcp-privd` root/minimal TCB;
- exact users/groups/socket modes remain to be finalized;
- systemd hardening must be service-specific and compatible with legitimate operation.

## Release gates
No release baseline exists while `CODE-BASELINE = NONE`.
