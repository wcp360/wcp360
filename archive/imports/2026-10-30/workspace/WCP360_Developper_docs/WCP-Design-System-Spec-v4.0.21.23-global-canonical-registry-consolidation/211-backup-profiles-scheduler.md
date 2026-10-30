# 211 --- Backup Profiles & Scheduler

## Statut et origine

\[SOURCE\] Backup Scheduler & Profiles.

## Objectif

Définir quoi/quand/où conserver.

## Pages / sous-vues

Profiles; schedule; scope; retention; exclusions.

## Ressources et données

resources, schedule, mode, destination, retention.

## Actions opérateur

Create/edit/disable/delete; run now.

## Monitoring, santé et logs

Next run, last result.

## Sécurité, permissions et audit

Scope authorization; destructive retention changes warned.

## Contrat backend attendu

Structured policy schema.

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

Prévisualise ressources incluses/exclues.

## V4.0.7 normative deepening

- `SECOPS-407-13-BACKUP-POLICY-SCHEDULER.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
