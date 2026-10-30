# 185 --- WAF / ModSecurity

## Statut et origine

\[SOURCE\] WAF/ModSecurity.

## Objectif

Gérer moteur WAF, règles et exceptions.

## Pages / sous-vues

Status; Rulesets; Events; Exceptions; Per-site policy.

## Ressources et données

engine/version, ruleset, mode, event id, site.

## Actions opérateur

Change mode; add scoped exception; update ruleset if supported.

## Monitoring, santé et logs

Blocked events and false-positive workflow.

## Sécurité, permissions et audit

Exceptions are security-sensitive and audited.

## Contrat backend attendu

WAF adapter.

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

Une exception affiche scope, motif et durée si politique temporaire.
