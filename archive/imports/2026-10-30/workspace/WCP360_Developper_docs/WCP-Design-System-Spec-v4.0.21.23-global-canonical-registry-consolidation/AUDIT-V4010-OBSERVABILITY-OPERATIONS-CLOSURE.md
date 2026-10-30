# V4.0.10 --- Observability, Operations & Incident Closure Audit

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

## Méthode

La passe a conservé le corpus V4.0.9 et ajouté une couche normative
dédiée. Les liens vers documents historiques sont établis uniquement
lorsque leurs noms présents dans le corpus indiquent directement
observability/metrics/traces/logs/alerts/incidents/notifications/health/monitoring/capacity/diagnostics/maintenance/status.

## Résultat

30 contrats normatifs V4.0.10 ajoutés. **57 documents historiques
réellement présents** ont reçu un lien de deepening.

## Chaîne de corrélation cible

Human/ServiceAccount → Session/API Request → Job → Worker/Daemon →
Adapter/System Service → Resource → Event → Audit → Metric/Log/Trace →
Alert → Incident → Notification.

## Fermetures majeures

Unknown≠healthy ; process-active≠healthy ; logs structurés et
secret-safe ; cardinalité bornée ; no-data alerts ; SLO distinct de SLA
; incidents distincts des alertes ; maintenance non destructive ;
diagnostic bundles scrubbed ; external dependency health séparé du
control-plane.

## Limites

Cette passe ne choisit pas encore une stack de stockage unique pour
metrics/logs/traces et n'impose pas Prometheus/OpenTelemetry comme seuls
backends. Les choix de déploiement et rétention précis restent
dépendants de l'architecture finale et de la capacité cible.
