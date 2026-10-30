# V4.0.10 --- Observability & Incident End-to-End Scenarios

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

## Scénarios

1.  User API request → Job → daemon → resource → trace/log/audit.
2.  Service process active but functional probe fails.
3.  Metrics collector unavailable → health unknown.
4.  Disk bytes healthy but inodes critical.
5.  Alert threshold with for-duration.
6.  Flapping suppression.
7.  Maintenance suppresses notification but keeps evidence.
8.  Critical alert creates incident.
9.  Incident correlates recent deployment.
10. Rollback followed by health recovery.
11. External DNS provider outage isolated.
12. Webhook notification enters DLQ.
13. Alert storm grouping.
14. Tenant attempts cross-tenant telemetry query.
15. High-cardinality label rejected/bounded.
16. Secret value submitted to log field redacted.
17. Diagnostic bundle generated and scrubbed.
18. Support impersonation preserves real actor.
19. SLO burn/error-budget calculation.
20. Capacity forecast warns disk exhaustion.
21. Job queue saturation.
22. Clock skew warning.
23. Public status sanitization.
24. Incident postmortem corrective action.
25. Telemetry backend outage does not block provisioning core path.
