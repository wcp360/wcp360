# 173 --- DNS Templates

## Statut et origine

\[SOURCE\] DNS templates implied by zone management patterns; \[V3\]
formalized.

## Objectif

Réutiliser configurations de zone sans copier des erreurs.

## Pages / sous-vues

Templates; preview; usage; versions.

## Ressources et données

template records/variables/version.

## Actions opérateur

Create/edit/version; instantiate.

## Monitoring, santé et logs

Usage count.

## Sécurité, permissions et audit

Permissions; audit.

## Contrat backend attendu

Template rendering backend-side with validation.

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

Prévisualisation montre la zone rendue avant création.
