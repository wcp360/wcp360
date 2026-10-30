# V4.0.3 --- cgroups v2 CPU/RAM/PIDs/I/O Resource Control

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

Base native d'isolation/limitation, indépendante d'options commerciales
telles que CloudLinux/LVE.

## Capabilities

Détecter cgroup v2 unified hierarchy, controllers disponibles et
délégation effective. Ne jamais afficher une limite non enforceable.

## Dimensions

CPU weight/quota, memory high/max, swap policy si disponible, pids max
et I/O weight/limits si device mapping fiable.

## Hiérarchie

WCP service slice, hosting-account scope et application/process scopes
clairement séparés. Une limite enfant ne peut contourner la limite
parent.

## OOM

Observer memory events/OOM kills. Un OOM n'est pas seulement « process
exited » ; corréler avec resource scope.

## Policy

Valeurs bornées par plan/entitlement et sécurité host. Le revendeur ne
peut allouer plus que son parent.

## Tests

Fork bomb/PID limit, memory pressure, CPU saturation, service restart,
reboot persistence et controller absent.
