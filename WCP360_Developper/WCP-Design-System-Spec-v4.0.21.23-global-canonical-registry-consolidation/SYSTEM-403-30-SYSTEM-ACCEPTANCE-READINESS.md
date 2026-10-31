# V4.0.3 --- System & OS Acceptance / READY Gate

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

## Conditions

La famille System/OS ne devient READY que lorsque chaque sous-module
concret référence les fondations V4.0.2 et les contrats V4.0.3
applicables.

## Tests minimum

Discovery sur chaque OS certifié ; permissions négatives ; target
substitution ; concurrent mutation ; timeout ambigu ; worker crash ;
reboot recovery ; disk/inode pressure ; package failure ; service
failure ; firewall anti-lockout ; clock drift ; MAC denial ; cgroup
limits ; PID reuse ; log redaction.

## Multi-OS

Même intention, résultats fonctionnels équivalents, différences exposées
via capabilities. Aucun test ne doit masquer une fonction non supportée.

## Documentation opérateur

Prerequisites, impact, recovery, evidence et limitations doivent exister
avant READY.

## Gate actuel

**SYSTEM/OS = SPECIFIED-DEEP, pas encore GLOBAL READY.** Les documents
hérités et les familles Network/Storage/Security doivent être alignés et
audités avant fermeture globale.
