# V4.0.21.11 --- Installer Preflight Contract

## Preflight avant installation

-   distribution/version/architecture certifiée ;
-   kernel/cgroups/filesystem capabilities ;
-   CPU/RAM/disk/inodes minimums ;
-   hostname/FQDN ;
-   DNS resolution ;
-   clock/NTP ;
-   ports conflicts ;
-   package manager state/locks ;
-   existing web/mail/DB services and collision policy ;
-   network egress/repos ;
-   IPv4/IPv6 capability ;
-   firewall state ;
-   SELinux/AppArmor context where applicable ;
-   filesystem mount/options and quota capability ;
-   virtualization/container restrictions ;
-   backup/rollback feasibility.

## Verdict

`PASS`, `WARN_ACK_REQUIRED`, `BLOCKED`. Les blockers doivent être
résolus avant mutation système.
