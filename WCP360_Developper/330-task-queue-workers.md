# 330 --- Task Queue / Workers

## Origine

**\[V3.1\]** Supervisor, systemd user-services, RabbitMQ et workers
d'applications.

## Objectif

Distinguer les **jobs internes WCP** des **workers applicatifs du
client**.

## Pages

Worker Groups; Processes; Queues; Schedules; Failures; Logs; Metrics.

## Backends

Supervisor, systemd user-services et brokers tels que RabbitMQ sont des
adaptateurs distincts.

## Actions

Create worker definition; scale process count; start/stop/restart; retry
failed job lorsque l'application/backend le permet.

## Monitoring

Running workers, restart count, queue depth, processing/failure rate si
mesurable.

## Sécurité

Commande/entrypoint validé dans le scope de l'application; quotas et
isolation appliqués.

## États UI obligatoires

Loading, skeleton, empty, no-results, permission denied, partial data,
stale data, unavailable, maintenance, pending, failed et success
confirmé par le backend.

## Règles transversales

-   WCP-ADMIN administre la capacité globale, les services, politiques
    et limites.
-   WCP-USER n'accède qu'aux ressources et fonctions explicitement
    attribuées à son compte/revendeur.
-   Toute mutation sensible est autorisée côté backend, auditée et
    reliée à un correlation/job ID.
-   Toute opération longue ou massive passe par Task Center.
-   Les secrets ne sont jamais écrits dans les logs, notifications,
    audit ou sorties IA.
-   Les modules sont pilotés par capabilities : une technologie non
    installée n'est pas présentée comme disponible.
