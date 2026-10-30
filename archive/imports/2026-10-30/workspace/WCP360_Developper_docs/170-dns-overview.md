# 170 --- DNS Overview

## Statut et origine

\[SOURCE\] DNS Zone Manager, cluster/sync, PTR, DNSSEC, authentication.

## Objectif

Point d'entrée DNS WCP-ADMIN.

## Pages / sous-vues

Zones; Templates; DNSSEC; Cluster; PTR; Health.

## Ressources et données

authoritative service, zone count, sync state, failures.

## Actions opérateur

Create zone; open BIND/service; sync actions.

## Monitoring, santé et logs

Query/zone health if measured.

## Sécurité, permissions et audit

DNS permissions by scope; audit.

## Contrat backend attendu

DNS adapter independent from BIND where possible.

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

Une zone en erreur affiche la cause et la dernière validation.
