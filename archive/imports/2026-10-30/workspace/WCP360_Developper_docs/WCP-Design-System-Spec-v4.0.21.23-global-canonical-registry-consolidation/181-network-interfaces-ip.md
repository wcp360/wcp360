# 181 --- Interfaces & IP Address Manager

## Statut et origine

\[SOURCE\] Network Configuration + IP Address Manager.

## Objectif

Gérer interfaces et IP avec conscience du risque de perte de
connectivité.

## Pages / sous-vues

Interfaces; addresses; assignments; IP pools if backend supports.

## Ressources et données

name, state, MAC, MTU, addresses, ownership/assignment.

## Actions opérateur

Add/remove/assign according to capability; enable/disable only via
guarded workflow.

## Monitoring, santé et logs

RX/TX/errors.

## Sécurité, permissions et audit

High risk; preflight; audit; confirmation.

## Contrat backend attendu

Transactional/network job if supported.

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

UI identifie l'interface utilisée par la session/panneau lorsque backend
sait le déterminer.

## V4.0.14 normative deepening

- `BOOT-4014-07-NETWORK-IPAM-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
