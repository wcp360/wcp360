# V4.0.21.21 --- Backup Policy / Scheduling / Retention

Backup policy defines scope, included resource classes,
schedule/timezone, retention tiers, target, encryption policy,
consistency mode, bandwidth/ concurrency ceilings and verification
requirements.

Operations: `BACKUP-POLICY-CREATE`, `BACKUP-POLICY-UPDATE`,
`BACKUP-POLICY-PAUSE`, `BACKUP-RUN-NOW`, `BACKUP-DELETE`.

Retention supports daily/weekly/monthly style tiers without assuming one
storage provider. A successful upload is not a VERIFIED backup.
Quota/capacity preflight and target health are checked before large runs
where possible.
