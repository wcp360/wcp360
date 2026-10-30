# 186 --- Network Diagnostics

## Statut et origine

\[SOURCE\] Network Diagnostic Tools.

## Objectif

Regrouper diagnostics non destructifs.

## Pages / sous-vues

Ping/reachability; DNS lookup; route trace if permitted;
ports/connections diagnostics.

## Ressources et données

target, tool, timestamp, result.

## Actions opérateur

Run diagnostic; copy/export sanitized result.

## Monitoring, santé et logs

Duration/result.

## Sécurité, permissions et audit

Validate targets; prevent SSRF/abuse; rate limit backend.

## Contrat backend attendu

Allowlisted diagnostic operations, no arbitrary shell.

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

Aucune saisie n'est concaténée dans une commande shell.
