# V4.0.3 --- Process Inventory, Identity & Safe Signalling

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

## ProcessIdentity

PID + start time/boot ID + executable/cgroup/unit lorsque disponibles.
Le PID seul est insuffisant à cause du reuse.

## Lecture

PID/PPID, UID/GID, state, CPU/RSS, start, unit/cgroup, command redacted,
open port summary si capability.

## Signaux

Actions fermées : graceful terminate puis force-kill séparé. Cible
revalidée juste avant signal.

## Protection

PID 1, kernel threads, WCP critical services, storage/network critical
processes et autres protected processes nécessitent policy renforcée ou
sont non actionnables.

## Race

Si l'identité du processus change entre lecture et action, refuser
`target_changed`.

## Privacy

Arguments de commande peuvent contenir secrets ; redaction et permission
dédiée pour vue détaillée.
