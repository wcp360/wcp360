# V4.0.21.4.1 — Isolation Options Matrix

| Option | Position V1 | Fonction |
|---|---|---|
| cgroups v2 | REQUIRED | CPU/RAM/PIDs/I/O |
| systemd slices/scopes | REQUIRED | lifecycle + placement |
| UID/GID par compte | REQUIRED | ownership/process boundary |
| PHP-FPM pool séparé | REQUIRED | isolation PHP |
| temp/session privés | REQUIRED | isolation runtime |
| filesystem quotas bytes/inodes | REQUIRED where supported | stockage |
| POSIX ACL | OPTIONAL/CONTROLLED | permissions ciblées |
| chroot | OPTIONAL | shell/service compatible |
| systemd hardening | RECOMMENDED | services contrôlés |
| seccomp | OPTIONAL ADVANCED | syscall reduction |
| AppArmor | OPTIONAL ADAPTER | MAC |
| SELinux | OPTIONAL ADAPTER | MAC |
| bubblewrap/namespaces sandbox | OPTIONAL ADVANCED | workload sandbox |
| network namespace | FUTURE/OPTIONAL | isolation réseau |
| nftables egress policy | OPTIONAL POLICY | sorties réseau |
| CloudLinux LVE | OPTIONAL COMMERCIAL ADAPTER | resource control |
| CageFS | OPTIONAL COMMERCIAL ADAPTER | filesystem/shell isolation |
| container par compte | NOT DEFAULT V1 | profil futur |
| VM par compte | OUT OF V1 | infrastructure forte |

Les adapters commerciaux/avancés ne deviennent jamais une dépendance du cœur WCP.
