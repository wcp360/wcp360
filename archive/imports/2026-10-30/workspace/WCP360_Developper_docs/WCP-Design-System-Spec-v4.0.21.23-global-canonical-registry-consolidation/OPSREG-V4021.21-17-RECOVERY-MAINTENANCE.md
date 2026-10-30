# V4.0.21.21 --- Recovery / Maintenance / Safe Mode

Platform states include NORMAL, MAINTENANCE, DEGRADED and
RECOVERY_REQUIRED. Maintenance can gate user mutations while allowing
explicitly safe reads/admin recovery operations.

Recovery mode has minimal privileged surface: inspect state, validate
config, restore last-known-good, resume/abort durable update where
supported, restore backup and run Doctor. It does not expose a generic
root shell through WCP.

Maintenance start/end and recovery actions are strongly audited.
