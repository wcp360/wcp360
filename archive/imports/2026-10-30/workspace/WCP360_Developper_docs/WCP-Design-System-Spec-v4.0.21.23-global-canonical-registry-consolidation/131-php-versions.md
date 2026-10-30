# 131 --- PHP Versions

## Statut et origine

\[SOURCE\] Multi-version PHP.

## Objectif

Gérer plusieurs versions sans supposer une liste codée en dur.

## Pages / sous-vues

Installed; available from configured repos; usage by domain.

## Ressources et données

version, source repo, installed, default, domains/pools count.

## Actions opérateur

Install; set default; deprecate internally; remove after dependency
check.

## Monitoring, santé et logs

Install job and runtime health.

## Sécurité, permissions et audit

Package permissions; audit; repo trust handled backend.

## Contrat backend attendu

Available versions originate from backend/repositories.

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

Aucune version PHP fictive n'est affichée.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-19-PHP-RUNTIME-VERSION-LIFECYCLE.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.

## V4.0.12 normative deepening

- `LIFE-4012-01-PLATFORM-RELEASE-MODEL.md`
- `LIFE-4012-14-COMPATIBILITY-MATRIX.md`
- `LIFE-4012-15-DEPRECATION-EOL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
