# Transfers between instances

**Status: Core ownership/source location documented; no transfer engine, importer or system operation is implemented here.**

Own the migration workflow described in the [README capability inventory](../../../../README.md#migration-and-transfers-between-instances): source discovery, compatibility, resource mapping, destination admission, tracked copy/final synchronization, cutover preparation, verification and recovery/cleanup.

This module coordinates resource owners; it does not implement a second file/database/mail/DNS authority or privileged executor.

| Concern | Collaborating owner |
| :--- | :--- |
| Account ownership, plans and destination limits | [Tenancy](../Tenancy/README.md) |
| Sites, names, DNS, certificates and runtime compatibility | [Hosting](../Hosting/README.md) |
| Confined file/archive operations | [Files](../Files/README.md) |
| Database identities, import/export and engine compatibility | [Data](../Data/README.md) |
| Mail domains, mailbox consistency and delivery cutover | [Mail](../Mail/README.md) |
| Restore points and verified recovery options | [BackupRestore](../BackupRestore/README.md) |
| Durable jobs, checkpoints, leases/fencing and reconciliation | [Operations](../Operations/README.md) |

Keep source and destination identities, ownership, consent/scope, mappings and per-resource outcomes explicit. Final synchronization and recovery depend on the selected protocol and observed state; do not assume arbitrary formats are compatible or every cutover is reversible. Specialized external-panel format adapters remain separately qualified packages where selected.

Use the normal Domain/Application/Infrastructure/UI layout when actual source is accepted. **This is not the migrations/ directory:** migrations holds Core database-schema changes; Transfers coordinates hosted-resource movement between instances.

- [Capability-to-owner map](../../../CAPABILITY-MAP.md)
- [Module source layout](../README.md)
- [Integration tests](../../../tests/Integration/README.md)
- [Core schema migrations](../../../migrations/README.md)
