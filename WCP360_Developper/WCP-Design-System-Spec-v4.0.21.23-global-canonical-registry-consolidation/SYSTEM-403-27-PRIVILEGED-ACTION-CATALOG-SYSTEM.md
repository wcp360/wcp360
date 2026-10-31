# V4.0.3 --- System Privileged Action Catalog --- Normative Boundaries

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

Le catalogue décrit des **intentions fermées**. Exemples de familles :
set hostname/timezone, manage WCP-approved user, apply package
transaction, control registered service, signal verified process, apply
registered sysctl, apply firewall policy, reboot/shutdown.

## Paramètres

IDs de ressources, enums, nombres bornés, booléens, CIDR/ports validés
et références de registry. Aucun shell fragment.

## Validation double

Le control-plane valide ; le broker privilégié revalide indépendamment
authorization context technique, target identity, path/unit registry,
ranges et preconditions.

## Evidence

Chaque action retourne action ID, target, started/finished, result,
observed evidence et error class. Stdout/stderr brut n'est pas l'API
publique.

## Timeouts

Timeout de transport et timeout de l'action sont distincts. Après
timeout, verify/reconcile obligatoire si side effect possible.

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
