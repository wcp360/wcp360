# 136 --- PHP per Domain / Account

## Statut et origine

\[SOURCE\] Multi-version PHP + isolation par compte.

## Objectif

Associer site/domaine à runtime et pool.

## Pages / sous-vues

Mappings; bulk migration; impact preview.

## Ressources et données

domain/site, account, PHP version, pool, inheritance.

## Actions opérateur

Change runtime; bulk migrate; dry-run compatibility if backend supports.

## Monitoring, santé et logs

Migration failures, affected sites.

## Sécurité, permissions et audit

Scope permissions; audit; job for bulk.

## Contrat backend attendu

Mapping endpoint + preflight.

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

Migration partielle fournit résultats par domaine.
