# 430 --- IPAM IPv4 / IPv6

## Objectif

Gérer pools, allocations et ownership des adresses.

## Pages

Pools; IPv4; IPv6; Assignments; Reservations; Utilization; Conflicts.

## Données

Address/prefix, pool, node/interface, account/resource assignment,
state, reverse DNS capability.

## Actions

Allocate, reserve, release, assign, move via guarded workflow.

## IPv6

Traité nativement avec préfixes/subnets ; pas comme simple chaîne IPv4
étendue.

## Contrat transversal WCP

-   **WCP-ADMIN** configure les moteurs, politiques, capacités globales
    et opérations d'infrastructure.
-   **WCP-USER** n'expose que les fonctions autorisées dans le scope
    client/revendeur.
-   Les autorisations sont contrôlées côté backend : rôle + scope +
    ownership + capability.
-   Toute mutation sensible est auditée ; toute opération longue devient
    un job Task Center.
-   Les secrets sont redacted dans logs, audit, notifications et IA.
-   Les intégrations tierces sont des adaptateurs/capabilities, jamais
    des dépendances implicites.
-   États UI : loading, empty, denied, unavailable, partial, stale,
    pending, failed, success confirmé.
