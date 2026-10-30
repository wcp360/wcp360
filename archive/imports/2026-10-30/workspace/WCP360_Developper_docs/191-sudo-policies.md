# 191 --- Sudo / Privileged Policies

## Statut et origine

\[SOURCE\] Root/Sudo Policies.

## Objectif

Représenter les privilèges sans éditeur libre par défaut.

## Pages / sous-vues

Policies; principals; commands/scopes; validation.

## Ressources et données

principal, policy, target, source.

## Actions opérateur

Create/edit via constrained model if supported.

## Monitoring, santé et logs

Policy validation failures.

## Sécurité, permissions et audit

Very high privilege; step-up; audit.

## Contrat backend attendu

Backend validates sudo policy before apply.

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

Une policy invalide ne remplace pas la configuration effective.
