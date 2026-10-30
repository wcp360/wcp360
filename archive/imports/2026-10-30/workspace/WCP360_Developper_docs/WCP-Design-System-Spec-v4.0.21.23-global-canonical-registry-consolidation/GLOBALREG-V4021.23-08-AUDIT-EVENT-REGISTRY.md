# V4.0.21.23 --- Global Audit / Event Registry

Canonical audit envelope: event/action ID, timestamp, initiator actor,
effective actor, tenant/scope, Operation ID, resource refs, policy
decision/reason, correlation ID, Job ID, safe before/after metadata,
result, stable error, evidence refs.

Secrets, credentials, private keys, message bodies and unrestricted
command output are forbidden in normal audit payloads.

Every R1--R3 mutation emits decision + completion/failure audit
evidence. Impersonation, step-up, privileged action, recovery and
destructive operations are explicitly visible.
