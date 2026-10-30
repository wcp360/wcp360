# 123 --- Apache Modules & Security

## Statut et origine

\[SOURCE\] Apache modules implied by web server management;
WAF/ModSecurity under Security.

## Objectif

Gérer modules Apache et liens vers contrôles de sécurité.

## Pages / sous-vues

Installed/enabled modules; module detail; ModSecurity linkage.

## Ressources et données

name, version if available, enabled, dependencies.

## Actions opérateur

Enable/disable where supported; dependency preflight.

## Monitoring, santé et logs

Module errors/restart requirement.

## Sécurité, permissions et audit

Privileged; dependency checks; audit.

## Contrat backend attendu

Adapter capability-driven.

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

Impossible d'activer une action non supportée par la distribution.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-12-APACHE-DEEP-CONTRACT.md`
- `HOSTING-404-18-WEB-WAF-MODSECURITY.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
