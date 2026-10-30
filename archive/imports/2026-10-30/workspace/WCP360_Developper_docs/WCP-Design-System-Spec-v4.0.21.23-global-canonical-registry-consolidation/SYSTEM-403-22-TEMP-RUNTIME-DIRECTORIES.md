# V4.0.3 --- Temporary Files, /run, /tmp & Runtime Directories

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

## Zones

Persistent state `/var/lib/wcp`; config `/etc/wcp`; runtime ephemeral
`/run/wcp`; logs selon policy ; temporary workspace privé hors web
roots.

## /run

Recréé au boot par mécanisme OS approprié avec owner/group/mode stricts.
Sockets root broker non accessibles aux comptes hosting.

## Temp

mktemp-like secure semantics, random names, O_EXCL conceptuel,
no-follow, size limits et cleanup. Ne jamais utiliser un nom dérivé
directement d'un domaine comme unique protection.

## Upload/build

Staging directories séparés par tenant/job avec quotas et cleanup après
crash.

## Reboot

Aucun état durable nécessaire à la reprise d'un Job ne doit exister
uniquement dans `/run` ou `/tmp`.
