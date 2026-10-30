# V4.0.21.4.1 — Filesystem / Process / IPC Boundaries

Aucun compte hébergé ne peut écrire dans releases WCP, configuration système maître, secrets, sockets du broker privilégié ou données d'un autre tenant. Les chemins root sont dérivés d'identifiants gérés.

File Manager/archive doit refuser `..`, symlink/hardlink cross-root, device files et archive traversal. Temp/session/upload privés avec quota/cleanup. Chaque workload doit rester dans son cgroup attendu, y compris enfants/orphelins. Sockets PHP/app/cache ont owner/group/mode explicites.

SSH/SFTP est entitlement-driven. Shell possible : `disabled`, `restricted`, `standard-user-shell`; jamais root/sudo par plan client.
