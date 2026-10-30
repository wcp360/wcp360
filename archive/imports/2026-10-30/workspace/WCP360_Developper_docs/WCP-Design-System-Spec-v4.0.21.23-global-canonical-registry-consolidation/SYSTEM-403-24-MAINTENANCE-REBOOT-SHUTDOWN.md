# V4.0.3 --- Maintenance, Reboot & Shutdown Lifecycle

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

## Maintenance

State du node : `normal`, `entering_maintenance`, `maintenance`,
`rebooting`, `shutting_down`, `recovering`, `degraded`.

## Enter maintenance

Bloquer nouvelles mutations incompatibles, laisser
finir/cancel/reconcile les Jobs selon policy, vérifier backups critiques
et afficher impacts.

## Reboot

Step-up, reason, optional schedule, preflight package/fs/storage health,
flush state, mark expected reboot, issue closed action, detect new boot
ID, run post-boot reconciliation et health checks.

## Shutdown

Plus strict qu'un reboot ; confirmation typée et avertissement sur perte
d'accès/out-of-band requirement.

## Recovery

Si node ne revient pas, WCP externe/cluster futur le marque unavailable.
Sur single-node local panel, la dernière page avant reboot ne doit pas
promettre un retour réussi.

## V4.0.10 normative deepening

- `OPS-4010-17-MAINTENANCE-WINDOWS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
