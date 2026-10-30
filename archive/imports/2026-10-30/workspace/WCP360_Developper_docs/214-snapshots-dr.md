# 214 --- Snapshots & Disaster Recovery

## Statut et origine

\[SOURCE\] Snapshots & Disaster Recovery.

## Objectif

Encadrer snapshots et procédures PRA.

## Pages / sous-vues

Snapshots; replication/DR status; recovery workflows.

## Ressources et données

snapshot, source, target, age, consistency state if known.

## Actions opérateur

Create/delete/restore snapshot; DR actions only if backend implements.

## Monitoring, santé et logs

RPO/RTO only if actually configured/measured; replication lag.

## Sécurité, permissions et audit

Very high-risk restore/failover; approvals possible.

## Contrat backend attendu

Storage/DR adapters.

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

Aucun RPO/RTO marketing n'est inventé par l'UI.
