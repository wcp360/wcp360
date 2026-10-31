# V4.0.21.4.1 — Isolation Acceptance & Threat Matrix

## P0
Tester : cross-tenant filesystem/socket/DB/cache/mail/backup/secret denial ; symlink/hardlink/archive escape ; fork bomb bornée ; memory/OOM attribuable ; CPU saturation bornée ; reboot persistence ; controller absent => `UNAVAILABLE` ; disk quota enforcement ; downgrade non destructif ; enfant/orphelin reste dans cgroup ; temp/session/socket non cross-tenant ; restore cross-tenant refusé ; SSH/SFTP respecte entitlement/scope/profile ; DEMO ne crée aucune ressource réelle ; reseller ne dépasse pas ceiling ; UI/API/CLI donnent la même décision effective.

Les adapters AppArmor/SELinux/seccomp/chroot/CageFS/LVE ont une certification propre ; package installé ≠ protection active.
