# 104 --- Process Manager

## Statut et origine

\[SOURCE\] Process Manager.

## Objectif

Inspecter processus et agir de manière contrôlée.

## Pages / sous-vues

Process list; process detail; tree; resource consumers.

## Ressources et données

pid, parent, user, command redacted as needed, CPU, RAM, state, start
time.

## Actions opérateur

Filter; inspect; terminate; force-kill only with elevated confirmation.

## Monitoring, santé et logs

CPU/RAM per process, process count, zombies if detectable.

## Sécurité, permissions et audit

`process.read`; `process.signal`; protéger processus critiques selon
policy.

## Contrat backend attendu

Snapshot/list endpoint; signal endpoint with PID identity protection.

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

Le PID seul ne suffit pas si backend peut détecter le PID reuse; cible
vérifiée.
