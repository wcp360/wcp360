# 213 --- Restore Center

## Statut et origine

\[SOURCE\] Granular Restore.

## Objectif

Restaurer compte/site/mail/DB/fichiers selon capacités.

## Pages / sous-vues

Recovery points; browse; scope; preflight; progress; result.

## Ressources et données

backup id, timestamp, resources, integrity/availability state.

## Actions opérateur

Select recovery point; dry/preflight; restore; rollback only if
supported.

## Monitoring, santé et logs

Restore job steps/errors.

## Sécurité, permissions et audit

High-impact; target confirmation; permission; audit.

## Contrat backend attendu

Restore as job with per-resource results.

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

Résultat partiel détaille chaque ressource.
