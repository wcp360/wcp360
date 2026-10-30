# V4.0.3 --- Updates, Security Patching & Reboot Policy

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

## Classes

Security, bugfix, enhancement, WCP-owned dependencies, kernel/boot
stack.

## Modes

Manual, scheduled maintenance window, security-only policy et
notification-only. Aucun unattended update global n'est activé
implicitement par WCP.

## Preflight

Pending jobs, disk/inodes, package health, backup/snapshot policy,
cluster role futur, service impact.

## Reboot required

Signal provider + packages concernés + kernel running vs installed.
`reboot_required` est un état observé, pas une supposition.

## Rollback

Package rollback n'est pas garanti. Le module doit annoncer ce qui est
réellement réversible et recommander snapshot/backup lorsque nécessaire.

## EOL

Une distribution EOL génère état de risque et bloque l'affirmation «
fully patched ».

## V4.0.12 normative deepening

- `LIFE-4012-04-UPDATE-CHANNELS.md`
- `LIFE-4012-05-UPDATE-POLICY-SCHEDULING.md`
- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
