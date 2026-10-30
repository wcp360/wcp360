# 144 --- Database Remote Access

## Statut et origine

\[V3\] Extension cohérente de la gestion DB/réseau ; non détaillée
explicitement dans la source.

## Objectif

Encadrer exposition réseau des moteurs lorsque le backend choisit de la
supporter.

## Pages / sous-vues

Listeners; allowed networks; account-level remote grants; diagnostics.

## Ressources et données

bind/listen state, port, ACL references.

## Actions opérateur

Enable via policy; add/remove allowed source; test.

## Monitoring, santé et logs

Connections and denied attempts only if telemetry exists.

## Sécurité, permissions et audit

High-risk; firewall coordination; explicit warning.

## Contrat backend attendu

Requires explicit capability; absent by default.

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

Le module disparaît si la capability n'est pas exposée.
