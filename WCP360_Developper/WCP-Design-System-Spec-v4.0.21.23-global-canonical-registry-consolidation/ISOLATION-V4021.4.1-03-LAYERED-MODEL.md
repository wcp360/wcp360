# V4.0.21.4.1 — Layered Multi-tenant Isolation

## Couches obligatoires
1. Identity : UID/GID dédiés et groupes contrôlés.
2. Filesystem : ownership/modes, roots gérées, anti traversal/symlink, temp/session privés.
3. Process : cgroups v2 + systemd slices/scopes.
4. Runtime : PHP-FPM/app processes séparés par compte/app.
5. IPC : sockets/runtime dirs non partagés.
6. DB/cache : credentials, grants et namespaces distincts.
7. Mail : ownership et quotas scopés.
8. Backup : manifest/keys/scope anti cross-tenant restore.
9. Control plane : tenant/scope dans policy + repository/query.
10. Hardening optionnel : MAC/seccomp/chroot/network egress uniquement si enforceable.

## cgroups v2
Hiérarchie : `wcp-hosting.slice → reseller/account scope → application/service scope`.
Contrôleurs : `cpu.weight`, `cpu.max`, `memory.high`, `memory.max`, swap policy si disponible, `pids.max`, `io.weight`, `io.max` si mapping device fiable.

Une limite enfant ne contourne jamais le plafond parent.
