# 250 --- SDN / Network Virtualization

## Statut et origine

\[SOURCE\] VXLAN, WireGuard, HAProxy/Traefik, VRRP/floating IP, DNS HA.

## Objectif

Point d'entrée réseau distribué.

## Pages / sous-vues

Virtual networks; tunnels; load balancing; floating IP; DNS HA.

## Ressources et données

network/tunnel/LB/VIP resources with topology.

## Actions opérateur

Create/edit according to orchestrator capabilities.

## Monitoring, santé et logs

Tunnel/LB/VIP health.

## Sécurité, permissions et audit

Network/cluster admin; strong guardrails.

## Contrat backend attendu

SDN adapters.

## États UI obligatoires

Loading, skeleton, empty, no-results, partial data, stale telemetry,
permission denied, service unavailable, maintenance, action pending,
action failed, success confirmé.

## Règles UX communes

-   Afficher serveur/nœud, scope et cible avant toute mutation.
-   Distinguer `Save`, `Apply`, `Reload` et `Restart`.
-   Une modification de configuration sensible présente un diff si
    possible.
-   Toute opération longue crée un job dans Task Center.
-   Ne jamais afficher un pourcentage de progression artificiel.
-   Les commandes/configurations proposées par l'UI restent validées et
    autorisées côté backend.
-   Les secrets sont redacted dans logs, audit et notifications.

## Critères d'acceptation

Topologie graphique a une alternative tabulaire accessible.

## V4.0.14 normative deepening

- `BOOT-4014-07-NETWORK-IPAM-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
