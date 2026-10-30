# 171 --- BIND Manager

## Statut et origine

\[SOURCE\] BIND service.

## Objectif

Administrer BIND lorsqu'il est le backend DNS.

## Pages / sous-vues

Overview; Config; Zones link; Logs; Service.

## Ressources et données

version, state, config validation, zone load errors.

## Actions opérateur

Check config; reload; restart.

## Monitoring, santé et logs

Query stats if enabled; load errors.

## Sécurité, permissions et audit

Privileged; audit.

## Contrat backend attendu

BIND adapter with config/zone checks.

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

Reload bloqué ou explicitement forcé si validation échoue selon policy.
