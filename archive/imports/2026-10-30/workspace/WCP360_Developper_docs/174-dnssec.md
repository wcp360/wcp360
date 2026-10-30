# 174 --- DNSSEC

## Statut et origine

\[SOURCE\] DNSSEC.

## Objectif

Gérer signature et état DNSSEC.

## Pages / sous-vues

Status; keys metadata; DS instructions; rollover status.

## Ressources et données

algorithm, key tags/fingerprints, state, DS data, timestamps.

## Actions opérateur

Enable/disable; rollover only if backend supports workflow.

## Monitoring, santé et logs

Validation state.

## Sécurité, permissions et audit

Private keys never exposed; high-risk disable confirmation.

## Contrat backend attendu

DNSSEC adapter; registrar interaction not assumed.

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

L'UI distingue action locale et action à effectuer chez le registrar.
