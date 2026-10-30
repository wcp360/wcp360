# 521 --- Prometheus Metrics Adapter

## Scope

Scrape/discovery/export ou remote integration selon architecture retenue
; Prometheus reste optionnel.

## Fonctions

Targets, scrape health, metric availability, labels/scope mapping,
cardinality safeguards.

## Sécurité

Endpoints metrics protégés ; labels ne doivent pas exposer de secrets ou
données cross-tenant.

## Contrat transversal

-   **WCP-ADMIN** porte l'administration système/infrastructure et les
    politiques globales.
-   **WCP-USER** ne voit que les capacités explicitement déléguées à son
    scope client/revendeur.
-   Les technologies concrètes sont des **adapters/capabilities** ; le
    cœur WCP dépend d'un contrat stable, pas d'un fournisseur unique.
-   Toute mutation sensible exige autorisation backend, audit, preflight
    lorsque pertinent et résultat confirmé.
-   Toute opération longue est un job observable, annulable/retryable
    lorsque techniquement sûr.
-   Secrets et données sensibles sont redacted dans logs, audit,
    notifications, traces et sorties IA.

## V4.0.10 normative deepening

- `OPS-4010-03-METRICS-MODEL.md`
- `OPS-4010-04-PROMETHEUS-ADAPTER.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
