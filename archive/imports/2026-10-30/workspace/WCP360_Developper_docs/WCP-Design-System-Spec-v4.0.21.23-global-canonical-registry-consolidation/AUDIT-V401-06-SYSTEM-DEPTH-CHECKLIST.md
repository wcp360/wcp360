# V4.0.1 --- System Depth Checklist

Chaque sous-système système doit couvrir discovery/inventory, desired
state, apply, verify, reconcile, rollback/recovery, package/config
ownership, reload/restart, locks, concurrency, permissions Linux,
SELinux/AppArmor, logs, metrics, backup, upgrade, failure modes et tests
multi-OS.

À vérifier : kernel/sysctl, hostname/time/NTP, users/groups, PAM/sudo,
filesystem/mounts/inodes, quotas, cgroups CPU/RAM/PIDs/IO, systemd,
packages/repos, network/routes/IPv4/IPv6, firewall, resolver,
storage/RAID/SMART, swap/OOM, cron/timers, logrotate/journald, temp
dirs, sockets, process supervision et reboot lifecycle.
