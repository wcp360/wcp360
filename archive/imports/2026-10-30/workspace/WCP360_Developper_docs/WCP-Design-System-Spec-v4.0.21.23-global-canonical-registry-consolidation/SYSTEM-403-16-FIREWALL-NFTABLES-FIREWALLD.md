# V4.0.3 --- Host Firewall: nftables / firewalld Contract

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

## Modèle

`FirewallPolicy`, `Rule`, `ServiceExposure`, `SourceSet`. WCP raisonne
en services/ports/protocoles/scopes, pas en lignes nft arbitraires.

## Backends

nftables natif et firewalld sont adapters ; ne pas gérer simultanément
deux sources d'autorité contradictoires.

## Safety

Avant toute mutation distante : conserver accès de management selon
policy, valider syntaxe, stage, apply atomiquement si backend le permet,
verify connectivity locale et rollback watchdog pour changements
critiques.

## Rules

Direction, family IPv4/IPv6, protocol, port/range borné, source
CIDR/set, action et owner. Commentaires non utilisés comme autorisation.

## Anti-lockout

Une règle qui couperait le canal d'administration déclenche step-up +
confirmation + mécanisme automatique de rollback si non-confirmation de
connectivité.

## V4.0.14 normative deepening

- `BOOT-4014-08-FIREWALL-PORT-PLAN.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
