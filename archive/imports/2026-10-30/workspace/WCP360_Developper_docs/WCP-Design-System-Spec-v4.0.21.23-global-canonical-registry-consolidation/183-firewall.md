# 183 --- Firewall Manager

## Statut et origine

\[SOURCE\] Firewall Manager; AI audit_firewall example.

## Objectif

Gérer règles firewall avec vues lisibles, ordre et impact.

## Pages / sous-vues

Rules; Zones/Chains according to backend; Services; Changes; Audit; AI
audit link.

## Ressources et données

rule id, direction, protocol, source/destination, ports, action, order,
owner.

## Actions opérateur

Create/edit/disable/delete/reorder; apply; test/dry-run if backend
supports.

## Monitoring, santé et logs

Blocked/allowed counters only if available.

## Sécurité, permissions et audit

High risk; lockout prevention; session source awareness; audit.

## Contrat backend attendu

Firewall adapter; transactional apply/rollback if implemented.

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

Une règle potentiellement bloquante est signalée avant apply; backend
reste autorité.

## V4.0.14 normative deepening

- `BOOT-4014-08-FIREWALL-PORT-PLAN.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
