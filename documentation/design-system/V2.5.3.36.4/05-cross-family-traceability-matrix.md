# Cross-Family Traceability Matrix

| Workflow | Page(s) | Permission(s) | State | Error class | Evidence | Safety invariant |
|---|---|---|---|---|---|---|
| DNS record edit | dns-record-edit.html | domains.write | VALIDATING → READY/FAILED | DNS_CONFLICT / validation error | Job + Audit | Production changes reviewed before apply. |
| DB restore | restore-wizard.html / restore-review.html | databases.restore + backups.restore | QUEUED → RUNNING → SUCCEEDED/FAILED | artifact invalid / storage full | Job + Audit + Health | Pre-restore snapshot when feasible. |
| Firewall rule change | firewall-rules.html | network.firewall.manage | VALIDATING → READY/FAILED | FW_LOCKOUT_RISK | Job + Audit | Anti-lockout simulation required. |
| Server reboot | server-actions.html | servers.reboot + privilege.elevate | QUEUED → RUNNING → SUCCEEDED/UNKNOWN_OUTCOME | capacity/HA precheck fail | Job + Audit + Health | Impact review and privileged confirmation. |
| Cluster failover | cluster-failover.html | clusters.failover + privilege.elevate | FAILOVER_READY → FAILOVER_RUNNING → HEALTHY/DEGRADED | quorum/lag blocked | Job + Audit + Incident | Unsafe promotion may be blocked. |
| Production deployment | deployments.html | applications.deploy | QUEUED → RUNNING → SUCCEEDED/FAILED | health check / migration incompatibility | Job + Audit + Monitoring | Known-good rollback target identified. |
| Full restore approval | approval-detail.html | approvals.request / approvals.decide / backups.restore | PENDING → APPROVED → CONSUMED | expired/invalidated/recheck fail | Approval + Job + Audit | Approver separation configurable. |
| Force kill process | process-detail.html | processes.kill + privilege.elevate | RUNNING → SUCCEEDED/FAILED | permission / scope / unknown outcome | Job + Audit | TERM preferred before KILL. |
| Certificate issuance | ssl-tls.html | ssl.manage | ISSUING → VALID/FAILED | ACME challenge / chain error | Job + Audit | Issuance separate from service deployment. |
| Support bundle | support-bundle.html | support.bundle.create | QUEUED → RUNNING → SUCCEEDED/FAILED | redaction/generation error | Job + Audit | Consent + secret redaction + expiry. |