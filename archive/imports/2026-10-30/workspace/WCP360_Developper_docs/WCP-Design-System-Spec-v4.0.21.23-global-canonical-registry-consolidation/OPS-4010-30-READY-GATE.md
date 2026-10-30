# V4.0.10 --- V4.0.10 READY Gate

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

## SPECIFIED-DEEP

Health, metrics, Prometheus-compatible adapter, OpenTelemetry tracing,
structured logs, retention/redaction, alerts/dedup/silences, SLI/SLO,
capacity, incidents/timeline/runbooks/postmortems, maintenance/status,
notifications/escalation, diagnostic bundles, support sessions,
dashboards, control-plane SLO, Job/daemon/external dependency telemetry.

## Critères

Unknown≠healthy, active≠healthy, tenant isolation, secret-safe
telemetry, bounded cardinality, alert no-data semantics, maintenance
non-destructive, incident evidence correlation, diagnostic scrub and
observability outage tolerance.

## Statut

**V4.0.10 = SPECIFIED-DEEP --- NO CODE --- NOT GLOBAL READY.**

Les familles multi-node/cluster/HA, upgrades/lifecycle,
licensing/entitlements si applicable, UX/design-system global et audit
transversal final restent à fermer.
