# V4.0.3 --- Network Interfaces, IPv4/IPv6 & Routing

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

## Ressources

Interface, address, route, gateway, link, DNS association et IP
ownership.

## Observation

Stable interface identity lorsque possible, MAC redacted selon
permissions, link state/speed, addresses/prefixes, default routes, MTU.

## Mutations

Le V1 privilégie inventory/diagnostics ; modifications
d'interface/routing sont WCP-ADMIN uniquement et nécessitent adapter
réseau OS, preflight et anti-lockout.

## IPv6

Fonction de première classe : AAAA, listening sockets, firewall family,
source addresses et diagnostics. Ne pas supposer IPv4.

## Conflicts

Détecter duplicate address, overlapping subnet, invalid gateway, route
loss et address déjà allouée.

## Remote safety

Toute modification de l'interface portant la session active est critique
et doit disposer d'un rollback timer/out-of-band recovery documenté.

## V4.0.14 normative deepening

- `BOOT-4014-07-NETWORK-IPAM-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
