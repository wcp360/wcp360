# V4.0.1 --- Observability Jobs Notifications Checklist

Metrics/logs/traces/health/audit restent distincts. Définir cardinality,
retention, export, stale/unknown, alerting, silences,
dedup/escalation/delivery. Scheduler/Workers : at-least-once,
idempotency, lease, heartbeat, retry/backoff/jitter, DLQ, cancellation,
timeout, locks, concurrency, crash recovery, DST. Job corrélable
API→système→audit.

## V4.0.10 normative deepening

- `OPS-4010-01-OBSERVABILITY-CANONICAL-MODEL.md`
- `OPS-4010-03-METRICS-MODEL.md`
- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-19-NOTIFICATION-ROUTING.md`
- `OPS-4010-20-NOTIFICATION-TEMPLATES.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
