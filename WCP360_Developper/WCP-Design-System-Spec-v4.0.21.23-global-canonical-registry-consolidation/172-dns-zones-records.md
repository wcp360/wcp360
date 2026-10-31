# 172 --- DNS Zones & Records

## Statut et origine

\[SOURCE\] A/AAAA/MX/TXT/CNAME/SRV + zones.

## Objectif

Gérer zones et enregistrements avec éditeur structuré.

## Pages / sous-vues

Zone list; zone detail; records; SOA; import/export if supported.

## Ressources et données

name, type, TTL, value fields typed per record type.

## Actions opérateur

Create/edit/delete; bulk; validate zone; apply.

## Monitoring, santé et logs

Serial/sync status.

## Sécurité, permissions et audit

Prevent cross-tenant edits; validation; destructive confirmation.

## Contrat backend attendu

Typed DNS record API; zone version/concurrency.

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

Conflits d'édition ne sont pas écrasés silencieusement.
