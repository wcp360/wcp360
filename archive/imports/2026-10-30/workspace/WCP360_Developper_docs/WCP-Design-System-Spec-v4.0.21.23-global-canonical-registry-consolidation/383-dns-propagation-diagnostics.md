# 383 --- DNS Propagation & Delegation Diagnostics

## Objectif

Diagnostiquer délégation et propagation sans promettre un délai
universel.

## Diagnostics

Authoritative NS, SOA serial, records observés, DNSSEC chain, resolver
observations si backend fournit plusieurs points de vue, TTL et
timestamps.

## UI

Expected vs observed ; local authoritative vs recursive observation ;
stale/unknown explicites.

## Actions

Recheck, copy diagnostic report, ouvrir zone/registrar/DNSSEC concerné.

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

## V4.0.10 normative deepening

- `OPS-4010-22-DIAGNOSTIC-BUNDLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
