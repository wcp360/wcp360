# 134 --- PHP Extensions

## Statut et origine

\[SOURCE\] PHP extensions.

## Objectif

Gérer extensions par version PHP.

## Pages / sous-vues

Installed/available; per-version; extension detail.

## Ressources et données

name, version, enabled state, source/package, dependencies.

## Actions opérateur

Install/remove/enable/disable if adapter supports distinction.

## Monitoring, santé et logs

Load errors.

## Sécurité, permissions et audit

Privileged package operation; dependency impact.

## Contrat backend attendu

Backend maps extension to distro packages safely.

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

L'UI n'invente pas qu'une extension peut être désactivée dynamiquement
si non supporté.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-22-PHP-EXTENSIONS-MODULES.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
