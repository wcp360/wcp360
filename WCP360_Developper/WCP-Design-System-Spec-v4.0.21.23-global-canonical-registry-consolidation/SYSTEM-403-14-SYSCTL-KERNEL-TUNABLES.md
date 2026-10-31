# V4.0.3 --- Kernel sysctl & Tunables Governance

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

## Principe

Pas d'éditeur `sysctl` libre pour WCP-USER ni d'entrée clé/valeur root
non bornée.

## Registry

Chaque tunable géré possède key fixe, type, range, OS/kernel
applicability, default/recommended profile, security impact, persistence
file ownership et reload semantics.

## Apply

Preflight capability → validate → write WCP-owned sysctl drop-in →
sysctl validation/apply ciblé → readback → audit.

## Profiles

`baseline`, `web`, `mail`, `database`, `custom-operator` peuvent
exister, mais les valeurs exactes doivent être versionnées et
justifiées.

## Safety

Les tunables réseau/sécurité susceptibles de couper l'accès exigent
maintenance/recovery path.
