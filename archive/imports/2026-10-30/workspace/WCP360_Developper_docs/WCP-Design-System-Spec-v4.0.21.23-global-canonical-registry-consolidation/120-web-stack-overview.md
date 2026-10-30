# 120 --- Web Stack Overview

## Statut et origine

\[SOURCE\] Web Server Manager + PHP Runtime Manager.

## Objectif

Vue de composition Apache/Nginx/PHP et routage des sites.

## Pages / sous-vues

Topology; Web servers; PHP runtimes; Sites; TLS; health.

## Ressources et données

active web engines, versions, ports, site count, PHP mappings, proxy
topology.

## Actions opérateur

Open Apache/Nginx/PHP; validate configuration; coordinated reload.

## Monitoring, santé et logs

Requests, errors, latency, workers/connections where available.

## Sécurité, permissions et audit

Read + stack control capabilities; coordinated actions audited.

## Contrat backend attendu

Adapters declare topology/capabilities; config-test before apply.

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

Supporte Nginx-only, Apache-only et proxy combinations sans supposition
fixe.
