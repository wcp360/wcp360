# V4.0.21.21 --- Operations Privileged Actions

Closed actions include `BACKUP-SNAPSHOT-CREATE`, `RESTORE-APPLY`,
`FIREWALL-POLICY-APPLY`, `WAF-CONFIG-APPLY`, `MALWARE-QUARANTINE-APPLY`,
`PACKAGE-UPDATE-APPLY`, `WCP-RELEASE-SWITCH`, `SERVICE-RECOVERY-ACTION`,
`HOST-REBOOT`.

Each action declares allowed callers, typed inputs, preconditions,
timeout, idempotency, verification and compensation/recovery semantics.
No arbitrary package/root command/config path enters through
tenant-facing payloads.
