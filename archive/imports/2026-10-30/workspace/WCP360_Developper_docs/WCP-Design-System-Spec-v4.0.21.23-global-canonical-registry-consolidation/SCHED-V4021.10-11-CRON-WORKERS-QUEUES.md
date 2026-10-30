# V4.0.21.10 --- Cron, Workers & Application Queue Deep Contract

## `scheduled_task`

account/app ownership, typed schedule, timezone, action reference,
concurrency policy, timeout, resource profile, status, last/next run.

## `worker`

application ownership, action/entrypoint reference, desired instances,
restart policy, resource sub-profile, health, status.

## Scheduler

Validation des expressions, timezone/DST policy, missed-run policy,
overlap `ALLOW|SKIP|QUEUE|REPLACE`, jitter optionnel.

## Workers

systemd/user-service or certified adapter. Toujours sous UID/GID/cgroup
du compte. Backoff, crash-loop detection, max restart rate et drain/stop
semantics.

## Queue

RabbitMQ ou autre broker est un module séparé :
credentials/vhosts/namespaces isolés ; l'existence d'un worker n'impose
pas RabbitMQ.
