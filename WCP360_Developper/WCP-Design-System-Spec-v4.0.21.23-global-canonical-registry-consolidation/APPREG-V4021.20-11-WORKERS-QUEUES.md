# V4.0.21.20 --- Workers / Application Queues

Worker resources define application, command/entrypoint, replica count,
restart/backoff, graceful shutdown, health, env/secret bindings and
resource limits.

Operations: `WORKER-CREATE`, `WORKER-SCALE`, `WORKER-START`,
`WORKER-STOP`, `WORKER-RESTART`, `WORKER-DELETE`, `WORKER-LOGS-READ`.

Queue visibility/actions are application-scoped. Retry/purge/dead-letter
actions are destructive/sensitive and capability/permission gated.
Worker process supervision is adapter-backed; tenant cannot create
arbitrary privileged service units.
