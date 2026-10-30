# V4.0.21.19 --- Queue / Message Trace / Delivery Diagnostics

Operations: `MAIL-QUEUE-READ`, `MAIL-QUEUE-ITEM-READ`,
`MAIL-QUEUE-RETRY`, `MAIL-QUEUE-FREEZE`, `MAIL-QUEUE-UNFREEZE`,
`MAIL-QUEUE-DELETE`, `MAIL-TRACE`, `MAIL-DELIVERABILITY-DIAGNOSE`.

Trace exposes metadata: sender/recipient as policy permits, queue ID,
timestamps, routing stages, accepted/deferred/bounced/delivered, remote
SMTP response, TLS and auth results when available.

Body is excluded by default. Destructive queue actions require admin
permission, step-up and audit. Tenant users can never enumerate
unrelated queue traffic.
