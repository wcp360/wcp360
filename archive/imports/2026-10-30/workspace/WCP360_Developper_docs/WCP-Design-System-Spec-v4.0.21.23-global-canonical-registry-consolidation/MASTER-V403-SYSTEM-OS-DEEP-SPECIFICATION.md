# MASTER V4.0.3 --- System & OS Deep Specification

## Position

V4.0.3 exécute la Passe B du plan V4.0.1/V4.0.2 sans écrire de code.

## Principes structurants désormais normatifs

1.  Distribution detected/support/certified séparés.
2.  Toutes mutations système utilisent intentions fermées.
3.  Aucun shell/path/unit arbitraire ne traverse la frontière
    privilégiée.
4.  PID seul n'identifie pas un processus.
5.  `active` systemd ne signifie pas `healthy`.
6.  Disk bytes et inodes sont deux ressources différentes.
7.  cgroups v2 constitue la base native de resource control quand
    disponible.
8.  SELinux/AppArmor ne sont pas désactivés pour faire fonctionner WCP.
9.  Firewall/network mutations possèdent anti-lockout/recovery.
10. Package transaction, reboot et recovery sont des lifecycles.
11. Observation stale/unknown n'est jamais transformée en healthy.
12. Après reboot/crash/timeout, WCP reconcile avant de rejouer un effet
    ambigu.

## Prochaine passe

**V4.0.4 --- Hosting Core Deep Specification --- tranche 1 :
Accounts/Plans → Domains → Web stack/PHP**, puis DNS/TLS, DB/Cache,
Mail, Files/Backup/Security.
