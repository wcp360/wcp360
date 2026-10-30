# 109 --- Reboot, Shutdown & Maintenance

## Statut et origine

\[SOURCE\] Reboot / Shutdown + service administration. \[V3\]
Maintenance workflow structuré.

## Objectif

Encadrer les actions hôte à impact majeur.

## Pages / sous-vues

Maintenance status; reboot; shutdown; pending operations; blockers.

## Ressources et données

host, affected services/resources, active jobs/sessions, reason.

## Actions opérateur

Enter maintenance; reboot; shutdown; cancel if backend allows.

## Monitoring, santé et logs

Readiness/blockers and post-action health.

## Sécurité, permissions et audit

Step-up; typed target confirmation; permission dedicated; audit
mandatory.

## Contrat backend attendu

Preflight endpoint; job; reconnect/health verification.

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

L'UI ne déclare pas le reboot terminé avant retour vérifié du host.
