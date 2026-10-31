# V4.0.3 --- SELinux / AppArmor Mandatory Access Control

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

## Objectif

WCP s'adapte au MAC de la distribution au lieu de le désactiver.

## SELinux

Observer enforcing/permissive/disabled, policy version, denials
pertinents. Les besoins WCP passent par policy/module/labels dédiés
certifiés ; pas de `setenforce 0` comme remédiation normale.

## AppArmor

Observer enabled/profiles/enforce/complain. Profils WCP-owned versionnés
et validés.

## Contexts

Les fichiers générés doivent recevoir context/label correct après
création/restauration. Backup/restore conserve ou reconstruit les labels
selon policy.

## Diagnostic

Denial MAC doit être distingué d'une permission Unix classique. Proposer
evidence sans exposer contenu sensible.
