# 580 --- Bare-metal / Redfish / IPMI

## Positionnement

**P2 / Datacenter edition**, hors cœur hosting initial.

## Scope

Hardware inventory, sensors, BMC reachability, power state/actions.
Redfish privilégié comme interface moderne lorsque disponible ; IPMI
adapter legacy/optionnel.

## Sécurité

BMC credentials isolés, réseau management séparé recommandé,
power-off/reset à confirmation renforcée et permissions dédiées.

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
