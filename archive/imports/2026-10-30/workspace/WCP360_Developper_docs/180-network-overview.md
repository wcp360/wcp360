# 180 --- Network Overview

## Statut et origine

\[SOURCE\] Network Configuration + IP management + routing.

## Objectif

Vue réseau hôte et entrée vers modules.

## Pages / sous-vues

Interfaces; IPs; Routes; Resolvers; Connections; Firewall; Diagnostics.

## Ressources et données

interfaces, addresses, gateways, routes, DNS resolvers, health.

## Actions opérateur

Open modules; safe diagnostics.

## Monitoring, santé et logs

Traffic/error counters if available.

## Sécurité, permissions et audit

Network mutations privileged; preflight and rollback strategy required.

## Contrat backend attendu

Network adapter; out-of-band recovery considerations documented by
implementation.

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

Les changements pouvant couper WCP sont explicitement signalés.
