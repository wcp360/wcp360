# V4.0.10 --- Structured Logging Canonical Contract

## Statut

**NORMATIVE --- V4.0.10 OBSERVABILITY, OPERATIONS, NOTIFICATIONS &
INCIDENT MANAGEMENT --- NO CODE**

Cette passe approfondit l'observabilité et l'exploitation du
control-plane WCP et des ressources hébergées. Elle relie santé,
métriques, logs, traces, alertes, SLI/SLO, incidents, maintenance,
notifications, diagnostics et capacity management sans transformer les
signaux en autorisations d'action.

## Invariants

-   `HealthState`, `Metric`, `LogRecord`, `Trace`, `Alert`, `Incident`,
    `Notification`, `MaintenanceWindow` et `AuditEvent` sont distincts.
-   `systemd active` ou `process running` ne signifie jamais
    automatiquement `healthy`.
-   Une métrique, un log ou une trace n'est pas une preuve
    d'autorisation.
-   Les secrets, credentials, corps sensibles, tokens et données
    cross-tenant ne doivent pas fuiter dans la télémétrie.
-   WCP-USER ne peut observer que son scope ; WCP-ADMIN dispose des vues
    infrastructure selon RBAC.
-   Les alertes utilisent des états observés et des règles explicites ;
    l'IA peut résumer/corréler mais ne remplace pas la preuve.
-   Toute remédiation automatique dangereuse reste gouvernée par les
    contrats Automation/Security.

## Record

timestamp, severity, component, event/message, resource refs,
correlation IDs et structured fields allowlistés.

## Sources

WCP API, daemon, workers, adapters, system services,
web/mail/db/application streams.

## Security

Authorization headers, cookies, passwords, tokens, private keys, secret
env values et payloads sensibles sont redacted/excluded.

## Tenant

Tenant log views ne donnent jamais accès aux control-plane/system logs
arbitraires.

## Size

Message/field limits et multiline/stacktrace handling empêchent
ingestion abuse.
