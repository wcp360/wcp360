# 184 --- Fail2ban Manager

## Statut et origine

\[SOURCE\] Fail2ban.

## Objectif

Gérer jails et bannissements.

## Pages / sous-vues

Jails; Banned IPs; Filters; Actions; Logs.

## Ressources et données

jail, enabled, failures, bans, IP, expiry.

## Actions opérateur

Enable/disable jail; unban; edit supported policy.

## Monitoring, santé et logs

Ban counts and recent events.

## Sécurité, permissions et audit

Audit manual unban/policy changes.

## Contrat backend attendu

Fail2ban adapter.

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

Les bans temporaires indiquent leur expiration lorsqu'elle est connue.
