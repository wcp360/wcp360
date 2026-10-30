# V4.0.21.21 --- Operational Notification Channels

Notification routes are adapter-backed (email and future providers) with
recipient/tenant scope, severity filters, verification, retry/backoff,
deduplication and delivery status.

Critical platform alerting should support an out-of-band route
independent of the locally hosted mail stack where configured,
preventing local mail failure from hiding its own alert.

Notification templates never include secrets or unrestricted log
payloads.
