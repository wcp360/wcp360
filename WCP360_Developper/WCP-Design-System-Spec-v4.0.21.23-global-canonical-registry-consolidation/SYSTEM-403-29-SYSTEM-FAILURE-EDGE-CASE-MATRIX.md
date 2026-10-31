# V4.0.3 --- System Failure & Edge-case Matrix

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

## Cas obligatoires

Package manager lock actif ; transaction interrompue ; disque plein ;
inodes pleins ; filesystem read-only ; RAID degraded ; SMART unavailable
; OOM kill ; PID reuse ; service crash-loop ; systemd reload unsupported
; cgroup controller absent ; clock drift ; NTP provider conflict ;
hostname DNS mismatch ; resolver down ; firewall apply coupe accès ;
IPv6-only ; route default perdue ; stale mount ; SELinux denial ;
AppArmor denial ; `/run` recréé après reboot ; log disk pressure ;
reboot pendant Job ; shutdown non confirmé ; machine clonée avec
identity dupliquée.

## Pour chaque cas

Trigger/evidence → impacted resources → allowed reads → blocked
mutations → Job state → retryability → verify/reconcile → notification →
audit → operator remediation → acceptance test.
