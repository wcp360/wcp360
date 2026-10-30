# 492 --- VLAN / Bonding / Linux Bridge

## Pages

VLANs; Bonds/Teams where supported; Bridges; Members; State;
Dependencies.

## VLAN

Parent interface, VLAN ID, addresses, MTU.

## Bonding

Members, mode, monitoring parameters selon backend ; LACP nécessite une
configuration réseau amont cohérente.

## Bridge

Members, STP/settings si backend supporte, usage VM/container/host.

## Sécurité

Dependency graph avant suppression ; empêcher la suppression d'une
interface utilisée par IP, route, VM, bridge ou service critique.

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
