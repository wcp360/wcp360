# WCP360 — Typed Identifier Namespaces

## Required namespaces

| Namespace | Meaning |
|---|---|
| `permission_id` | capability granted/denied by authorization |
| `action_id` | semantic user/system intention |
| `job_type` | durable asynchronous work type |
| `event_id` | domain/operational event type |
| `audit_event_id` | audit event type identifier |
| `ipc_operation_id` | bounded process-to-process operation |
| `error_code` | stable contract error code |

## Invariants

- Permission != action.
- Action != job.
- Job != IPC.
- IPC != shell command.
- Event != audit event.
- Error code != human-readable message.
- Lexical equality does not merge namespaces.

## Known unresolved contamination

The source Permission Registry contains identifiers used elsewhere as non-permission types, including:
- `domains.record.update`
- `dns.change.apply`
- `dns.record.updated`
- process/server-related identifiers.

These are tracked by `H3CM-X001` and `H3CM-X002`.

Do not implement the Permission Registry as a literal authorization table until typed cleanup is approved.
