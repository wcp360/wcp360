# 108 --- System Logs & Journal

## Statut et origine

\[SOURCE\] System Logs Viewer; Monitoring & Logs.

## Objectif

Fournir un viewer technique robuste, filtrable et sûr.

## Pages / sous-vues

Live logs; sources; saved filters; event detail.

## Ressources et données

timestamp, source, severity, message, structured fields, correlation id.

## Actions opérateur

Search/filter/follow/pause/export according to permission.

## Monitoring, santé et logs

Rate, severity counts, stale/disconnected stream.

## Sécurité, permissions et audit

`logs.read/export`; escape untrusted output; redact secrets.

## Contrat backend attendu

Cursor/stream contract; backpressure; server-side filters.

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

Le viewer reste utilisable sur gros volumes sans charger tout le journal
en mémoire.

## V4.0.3 — Deep Specification normative

Ce document de surface doit être interprété avec les contrats approfondis suivants :

- `SYSTEM-403-21-JOURNALD-LOGROTATE-RETENTION.md`

Les règles V4.0.3 prévalent lorsqu'une ancienne formulation est moins précise. Le statut de cette surface reste **à valider contre le READY Gate System/OS**.

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
