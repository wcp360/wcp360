# 105 --- Service Control / systemd

## Statut et origine

\[SOURCE\] Service Control Panel : start/stop/restart et dépendances.

## Objectif

Normaliser l'administration des services sans supposer systemd pour tous
les futurs adaptateurs.

## Pages / sous-vues

Services; service detail; dependencies; recent logs; configuration link.

## Ressources et données

name, display name, state, health, enabled-at-boot, pid,
version/capability.

## Actions opérateur

Start; stop; restart; reload; enable/disable; inspect dependencies.

## Monitoring, santé et logs

Uptime, restart count if available, resource usage, recent errors.

## Sécurité, permissions et audit

`service.read/control`; stop/restart critique peut exiger confirmation;
audit/job.

## Contrat backend attendu

Service adapter exposes supported actions; action result; journal link.

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

Reload n'est affiché que si réellement supporté.
