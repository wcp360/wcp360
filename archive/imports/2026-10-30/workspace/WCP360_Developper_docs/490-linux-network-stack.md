# 490 --- Linux Network Stack

## Objectif

Spécifier le réseau hôte Linux au-delà de l'IPAM : interfaces,
addresses, routes, MTU, DNS resolver, hostname/FQDN et backend de
configuration.

## Abstraction

`Network Model → OS Network Adapter`. Adaptateurs possibles :
systemd-networkd, NetworkManager, Netplan comme couche de configuration
selon distribution. WCP ne modifie jamais plusieurs backends concurrents
sans détection explicite.

## Pages

Interfaces; Addresses; Routes; Resolver; Hostname; Backend; Pending
Changes; Connectivity Validation.

## Workflow critique

Snapshot/config evidence → validate → stage → apply → connectivity check
→ commit ou recovery/rollback lorsque le backend le permet.

## Anti-lockout

Une modification susceptible de couper la connexion d'administration
doit afficher l'impact et utiliser un mécanisme de
confirmation/recovery.

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
