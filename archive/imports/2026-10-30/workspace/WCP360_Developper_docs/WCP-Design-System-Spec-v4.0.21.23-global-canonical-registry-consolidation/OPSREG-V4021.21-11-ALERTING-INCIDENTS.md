# V4.0.21.21 --- Alerting / Incident Lifecycle

Alert rule defines signal/query, threshold/condition, evaluation window,
severity, dedup/grouping, cooldown, scope and notification routes.

Lifecycle: FIRING → ACKNOWLEDGED → RESOLVED, with incident correlation.
Flapping/noise controls, notification retry and route health are
observable.

Tenant alerts cannot subscribe to cross-tenant data. Automated
remediation, if present, invokes an ordinary canonical Operation ID with
policy checks; alerts never execute arbitrary shell.
