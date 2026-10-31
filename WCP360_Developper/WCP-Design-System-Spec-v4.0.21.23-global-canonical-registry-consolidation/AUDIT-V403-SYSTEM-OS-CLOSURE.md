# V4.0.3 --- System & OS Closure Audit

## Statut

**NORMATIVE --- V4.0.3 SYSTEM & OS DEEP SPECIFICATION --- NO CODE**

Cette spécification instancie les fondations V4.0.2 pour WCP-ADMIN. Elle
décrit le comportement attendu sans imposer de framework ni produire
d'artefact exécutable.

## Invariants communs

-   WCP-USER n'obtient jamais de primitives d'administration hôte.
-   Toute mutation système est autorisée, bornée, auditable et exécutée
    via une action privilégiée fermée.
-   Aucun champ UI/API ne devient une commande shell, un chemin absolu
    arbitraire, un nom d'unité arbitraire ou une liste d'arguments root.
-   Toute mutation suit **preflight → plan/diff → lock → apply → verify
    → reconcile/rollback → audit**.
-   Timeout ou perte de réponse = état ambigu jusqu'à vérification.
-   L'état affiché provient de l'observation effective et porte un
    timestamp/freshness.

## Résultat

La Passe B possède maintenant des contrats approfondis couvrant OS
certification, host/kernel/boot, time, users/groups, PAM/sudo/SSH,
packages/repos/updates, process identity, systemd, cgroups v2,
filesystems/inodes, LVM/RAID/SMART, swap/OOM, sysctl, SELinux/AppArmor,
firewall, IPv4/IPv6/routing, resolver, ports/sockets, cron/timers,
logs/rotation, temp/runtime directories, hardening, maintenance/reboot,
recovery, OS adapter, privileged actions, degraded mode et edge cases.

## Fermeture

Les documents 100--109 ont été reliés aux nouveaux contrats normatifs ;
plusieurs documents historiques adjacents sont également reliés.

## Ce qui reste

Network, Storage, Security et Hosting Core possèdent déjà des documents,
mais devront être audités selon les mêmes critères. La famille System/OS
est **SPECIFIED-DEEP** et non `GLOBAL READY`.

## NO-CODE gate

Maintenu.
