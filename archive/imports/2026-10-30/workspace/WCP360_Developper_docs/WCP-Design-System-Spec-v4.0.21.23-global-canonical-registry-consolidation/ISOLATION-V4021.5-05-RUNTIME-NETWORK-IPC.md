# V4.0.21.5 — Runtime, IPC & Network Isolation Contract

## Statut
**NORMATIVE — V1 STANDALONE — NO CODE — REPOSITORY BLOCKED**

Cette famille transforme les options d’isolation et de commandes de V4.0.21.4.1 en contrats B1/B2 explicites.

## Runtime
PHP-FPM pools, Node/Python/Ruby/Go apps, cron et workers sont rattachés au compte et au cgroup attendu. Secrets/env sont injectés par références contrôlées, jamais exposés dans les listes/process logs.

## IPC
Runtime dirs et sockets ont owner/group/mode explicites. Les sockets cross-tenant sont refusés par défaut.

## Réseau
V1 distingue :
- métrique/bandwidth accounting ;
- rate limiting applicatif/API ;
- egress filtering optionnel ;
- network namespace : extension avancée, pas prérequis V1.

`nftables`/egress rules ne sont appliqués que via adapter fermé avec rollback et anti-lockout.

## MAC / sandbox
AppArmor, SELinux, seccomp, chroot, bubblewrap/CageFS sont des backends optionnels. Leur simple installation ne signifie jamais `ACTIVE`.
