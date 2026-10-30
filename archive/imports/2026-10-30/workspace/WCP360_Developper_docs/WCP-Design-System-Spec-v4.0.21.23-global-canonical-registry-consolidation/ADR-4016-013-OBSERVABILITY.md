# ADR-013 --- Local Observability Baseline

## Statut

**ACCEPTED FOR V1 --- V4.0.16 --- NO CODE**

## Autorité

Cet ADR ferme un choix P0 identifié par `ADR-V4015-07-REGISTER.md`. Il
concrétise la baseline V4.0.15 sans élargir le scope fonctionnel.

## Règles

-   Toute implémentation incompatible exige un ADR de supersession.
-   Les secrets ne sont jamais placés en clair dans les arguments de
    processus, logs ou artefacts exportés.
-   Toute mutation privilégiée reste typée, auditable,
    idempotente/reconciliable et vérifiée par état observé.
-   Le V1 reste single-node-first tout en conservant des interfaces qui
    n'interdisent pas le cluster futur.

## Décision

WCP fournit nativement un minimum sans dépendance SaaS : health
registry, structured logs, metrics endpoint, Job telemetry, audit/events
et alert evaluation essentielle.

## Format

Logs structurés JSON avec correlation IDs et redaction. Metrics exposées
dans un format scrape-compatible ; traces distribuées optionnelles V1.

## Storage

Les logs système restent intégrables à journald/rotation ; le
control-plane ne devient pas un data lake.

## Alertes CORE

Disk/inodes, memory pressure, service down, certificate expiry, backup
failure, mail queue/abuse, security finding, update/EOL, Job failures.

## External

Prometheus-compatible, Loki/ELK/OpenSearch, OTLP et notification
providers sont adapters/integrations, non prérequis au fonctionnement.
