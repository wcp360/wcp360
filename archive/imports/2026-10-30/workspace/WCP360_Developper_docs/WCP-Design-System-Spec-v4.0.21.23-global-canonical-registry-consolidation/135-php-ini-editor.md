# 135 --- PHP INI Editor

## Statut et origine

\[SOURCE\] INI editor.

## Objectif

Éditer paramètres PHP via schéma contrôlé avec mode avancé facultatif.

## Pages / sous-vues

Basic settings; advanced settings; effective values; diff.

## Ressources et données

directive, effective value, origin, allowed range/type, restart/reload
requirement.

## Actions opérateur

Edit/reset to inherited; validate; apply.

## Monitoring, santé et logs

Config errors after apply.

## Sécurité, permissions et audit

Sensitive directives permission; validation server-side.

## Contrat backend attendu

Schema endpoint exposes directives/types/capabilities.

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

Valeur effective relue après application.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-21-PHP-INI-HIERARCHY.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
