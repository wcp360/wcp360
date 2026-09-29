# Cross-Family Dependency Matrix

| Workflow | Dependency chain | Invariant |
|---|---|---|
| Deployment | Backups → Builds → Releases → Jobs → Health Checks → Monitoring → Audit | Production deploy must confirm rollback target and recovery readiness. |
| DNS change | RBAC → DNS validation → Diff/Review → Job → Propagation → Audit | No blind production mutation. |
| Database restore | RBAC/MFA → Backup artifact → Pre-snapshot → Job dependencies → Health → Audit | Idempotency and retry safety mandatory. |
| Firewall change | Privilege → Validate → Diff → Anti-lockout simulation → Apply job → Connectivity verify → Audit | Current operator path protected. |
| Server reboot | Privilege → Incident check → HA capacity/drain → Backup readiness → Reboot job → Health → Audit | Single-node impact must be explicit. |
| Cluster failover | Privilege → Quorum → Replication → Promotion → Routing → Health → Incident/Audit | Unsafe replica promotion can be blocked. |
| User privilege change | RBAC → Approval policy (optional) → Effective permission recompute → Audit | Cannot grant beyond delegated envelope. |
| Automation execution | Service identity → Scope → Job → Result → Audit | Automation never creates authority. |
| Impersonation | Operator identity → reason → read-only session → optional elevation → audit | Real actor remains visible. |
| Support bundle | Consent → data inventory → secret redaction → generation job → expiry | Not yet visually implemented. |