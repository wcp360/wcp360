# 524 --- SNMP & External Monitoring

## Positionnement

Adapter optionnel pour supervision existante.

## Scope

SNMP health/configuration selon version/policy, external monitoring
webhooks/exporters, health endpoint.

## Sécurité

SNMPv3 préféré lorsque utilisé ; credentials write-only ; SNMP n'est pas
requis par WCP.

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

- `OPS-4010-01-OBSERVABILITY-CANONICAL-MODEL.md`
- `OPS-4010-24-OPERATIONS-DASHBOARDS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
