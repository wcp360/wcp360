# V4.0.21.11 --- Alerts / Notifications / Escalation Contract

## Ressources

`alert_rule`, `alert_instance`, `notification_channel`,
`notification_delivery`, `maintenance_window`, `silence`.

## Conditions

disk/inode pressure, memory/OOM, service down, queue backlog, TLS
expiry, backup stale/failure, malware finding, DNSSEC failure, mail
reputation/security, job failure, excessive auth failures.

## Anti-noise

deduplication, grouping, hysteresis, cooldown, resolve notifications,
maintenance windows and escalation policy.

## Channels

Email, webhook and certified adapters such as Slack/Discord. Webhook
secrets sont SecretStore-backed. Notification payloads respectent data
minimization.
