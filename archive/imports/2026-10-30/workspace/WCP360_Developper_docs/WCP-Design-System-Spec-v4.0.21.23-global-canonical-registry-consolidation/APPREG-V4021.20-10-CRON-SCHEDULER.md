# V4.0.21.20 --- Cron / Scheduled Tasks Registry

A scheduled task contains owner, command/application entrypoint, working
directory, schedule/timezone, environment references, concurrency
policy, timeout and resource limits.

Operations: `CRON-CREATE`, `CRON-UPDATE`, `CRON-PAUSE`, `CRON-RESUME`,
`CRON-DELETE`, `CRON-RUN-NOW`, `CRON-HISTORY-READ`.

Tasks run as tenant identity. Schedules are validated and bounded by
platform/ plan frequency floor. No root cron injection. Overlap policies
include `ALLOW`, `SKIP_IF_RUNNING`, `SERIALIZE` according to
entitlement.
