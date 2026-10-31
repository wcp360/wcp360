# V4.0.3 --- Cron & systemd Timers Governance

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

## Sources

System cron, user crontabs, `/etc/cron.d`, anacron si présent, systemd
timers. Ownership explicite.

## Scheduler WCP

Les tâches WCP internes ne sont pas nécessairement des crons Linux ;
elles suivent le Job model V4.0.2.

## User cron

Command/working directory/env doivent provenir d'un modèle borné par
hosting account. Aucun cron WCP-USER exécuté root.

## Time

Timezone, DST, missed-run policy, overlap policy, max runtime et
concurrency sont documentés.

## Security

Env secrets via secret references/mécanismes adaptés, jamais en clair
dans une ligne visible. PATH explicite et environnement minimal.

## Recovery

Missed timer après reboot : run-now, skip ou catch-up selon type de
tâche.
