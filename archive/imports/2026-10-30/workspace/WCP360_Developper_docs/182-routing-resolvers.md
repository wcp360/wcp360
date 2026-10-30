# 182 --- Routing & Resolvers

## Statut et origine

\[SOURCE\] Network Configuration; DNS resolvers logically part of host
networking.

## Objectif

Gérer routes et résolveurs via schémas structurés.

## Pages / sous-vues

Routes; default gateway; resolver config; effective state.

## Ressources et données

destination, gateway, interface, metric; resolver addresses/search
domains.

## Actions opérateur

Add/edit/remove route; change resolvers if capability.

## Monitoring, santé et logs

Reachability tests if supported.

## Sécurité, permissions et audit

Risk warnings; audit.

## Contrat backend attendu

Network adapter and validation.

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

Default route modification exige workflow renforcé.
