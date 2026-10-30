# V4.0.10 --- OpenTelemetry Tracing & Context Propagation

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

## Position

OpenTelemetry est l'adapter/protocole privilégié si tracing distribué
activé.

## Chaîne

UI/API request → API handler → Job → daemon/adapters → external
dependency, avec trace/span IDs corrélables.

## Trust

Trace headers entrants sont contexte, jamais identité/auth.

## Sampling

Head/tail/configurable selon backend ; erreurs/latence peuvent être
privilégiées sans promettre capture totale.

## Attributes

Pas de secrets, auth headers, SQL sensible ou corps de requête par
défaut.
