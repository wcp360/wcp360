# V4.0.21.19 --- Mail Privileged Actions

Closed actions include: `MAIL-CONFIG-STAGE`, `MAIL-CONFIG-VALIDATE`,
`MAIL-CONFIG-PUBLISH`, `MAIL-SERVICE-RELOAD`,
`MAILBOX-STORAGE-PROVISION`, `MAILBOX-STORAGE-LOCK`,
`MAILBOX-STORAGE-DELETE`, `DKIM-KEY-GENERATE`, `DKIM-KEY-DEPLOY`,
`MAIL-QUEUE-ACTION`, `SPAM-CONFIG-APPLY`, `AV-CONFIG-APPLY`.

No arbitrary shell, raw service command, unrestricted filesystem path or
raw root mail configuration is accepted from tenant-facing inputs.
