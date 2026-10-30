# 231 --- Nodes, Heartbeat & Fencing

## Statut et origine

\[SOURCE\] Node Manager + Heartbeat & Fencing.

## Objectif

Gérer cycle de vie nœud et protections split-brain selon orchestrateur.

## Pages / sous-vues

Nodes; node detail; heartbeat; fencing config/status.

## Ressources et données

node, state, health, capacity, heartbeat freshness, fencing capability.

## Actions opérateur

Maintenance; drain if supported; fence only dedicated workflow.

## Monitoring, santé et logs

Heartbeat, workload/capacity.

## Sécurité, permissions et audit

Fencing extremely high-risk; step-up/approval.

## Contrat backend attendu

Orchestrator-specific.

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

Action fence expose clairement la cible et l'impact.

## V4.0.11 normative deepening

- `CLUSTER-4011-01-CLUSTER-NODE-MODEL.md`
- `CLUSTER-4011-02-NODE-ENROLLMENT-TRUST.md`
- `CLUSTER-4011-05-NODE-CORDON-DRAIN.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
