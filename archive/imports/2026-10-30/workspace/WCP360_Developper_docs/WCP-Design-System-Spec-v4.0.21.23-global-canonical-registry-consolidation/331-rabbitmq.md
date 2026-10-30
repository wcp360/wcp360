# 331 --- RabbitMQ Manager

## Origine

**\[V3.1\]** RabbitMQ comme broker optionnel.

## Pages

Overview; VHosts; Users; Queues; Exchanges; Connections; Policies.

## Scope

WCP-ADMIN gère le service et allocations; WCP-USER peut gérer uniquement
les vhosts/credentials/queues explicitement attribués si cette fonction
est activée.

## Sécurité

Credentials write-only, permissions par vhost, aucune administration
globale exposée au tenant.

## Monitoring

Queue depth, consumers, connections, unacked messages si API de
management disponible.

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
