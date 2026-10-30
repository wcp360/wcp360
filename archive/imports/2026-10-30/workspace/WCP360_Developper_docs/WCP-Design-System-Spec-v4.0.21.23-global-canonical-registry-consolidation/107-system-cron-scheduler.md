# 107 --- System Cron & Scheduler

## Statut et origine

\[SOURCE\] Cron & Scheduler.

## Objectif

Gérer tâches système planifiées séparément des crons d'hébergement
WCP-USER.

## Pages / sous-vues

Jobs; create/edit; run history; execution detail.

## Ressources et données

schedule, command/reference, owner, enabled, last/next run, exit state.

## Actions opérateur

Create/edit/disable/delete; run now if policy.

## Monitoring, santé et logs

Success/failure history, duration.

## Sécurité, permissions et audit

`cron.*`; commande sensible; validation et scope backend; secrets
redacted.

## Contrat backend attendu

Structured schedule; execution as job/event; history endpoint.

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

Expression et prochain déclenchement sont prévisualisés.

## V4.0.3 — Deep Specification normative

Ce document de surface doit être interprété avec les contrats approfondis suivants :

- `SYSTEM-403-20-CRON-SYSTEMD-TIMERS.md`

Les règles V4.0.3 prévalent lorsqu'une ancienne formulation est moins précise. Le statut de cette surface reste **à valider contre le READY Gate System/OS**.
