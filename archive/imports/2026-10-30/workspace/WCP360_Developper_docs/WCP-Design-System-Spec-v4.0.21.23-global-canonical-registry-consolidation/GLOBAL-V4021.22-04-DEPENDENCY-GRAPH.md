# V4.0.21.22 --- Global Dependency Graph

Identity/Auth/RBAC → Account/Plan/Entitlement/Quota → Host
Capability/Isolation/System Identity/Managed Root → Network/IPAM →
DNS/TLS/Web/PHP → Database/Cache → Mail →
Files/Access/Git/Runtimes/Applications → Cron/Workers →
Backup/Restore/DR → Security/Observability/Alerts →
Updates/Doctor/Recovery

Transversal: Policy Decision, Job, Privileged Action, Audit, Error
taxonomy, SecretStore, lifecycle and desired/observed reconciliation.

Cycles are permitted only as operational dependencies with explicit
bootstrap ordering (for example DNS↔mail diagnostics); ownership/control
dependencies must remain acyclic.
