# V4.0.3 --- Swap, OOM & Memory Pressure

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

## Observation

RAM total/available, swap total/used, PSI si capability, OOM events, top
consumers et cgroup attribution.

## Distinction

Cache mémoire Linux n'est pas « RAM perdue ». UI privilégie
available/pressure plutôt que simple used%.

## Swap

Inventory des swap devices/files et policy. Création/redimensionnement
futur nécessite espace, permissions et persistence explicitement gérés.

## OOM

Corréler kernel OOM event → killed process → cgroup/tenant/service →
Job/incident. Une répétition peut marquer ressource degraded.

## Protection

Les services WCP critiques ont des politiques de ressources documentées
mais ne doivent pas être rendus invulnérables au détriment total de
l'hôte.
