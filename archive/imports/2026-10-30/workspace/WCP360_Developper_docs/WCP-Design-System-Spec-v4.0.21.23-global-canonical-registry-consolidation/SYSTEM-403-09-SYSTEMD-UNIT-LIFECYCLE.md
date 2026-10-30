# V4.0.3 --- systemd Unit Lifecycle & Service Control

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

## Ressource

Unit canonical name issu d'un registry/capability, load state,
active/sub state, enabled state, fragment ownership, dependencies, main
PID, restart counters.

## Actions

start, stop, restart, reload, enable, disable, mask/unmask uniquement si
autorisés pour cette unit. Les noms arbitraires fournis par client ne
sont jamais transmis au broker root.

## Semantics

Reload ≠ restart. Enable ≠ start. `active` ≠ healthy. Après action,
relire unit state et health applicatif.

## Dependencies

Afficher Requires/Wants/After de manière informative ; ne pas laisser
l'UI créer des dépendances arbitraires.

## Drop-ins

WCP utilise des drop-ins dédiés lorsqu'il doit gérer des overrides et
suit leur ownership. Validation et daemon-reload sont explicitement
planifiés.

## Recovery

Start timeout, crash loop, failed unit, stale PID, dependency failed et
reload unsupported sont des erreurs distinctes.

## V4.0.12 normative deepening

- `LIFE-4012-15-DEPRECATION-EOL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
