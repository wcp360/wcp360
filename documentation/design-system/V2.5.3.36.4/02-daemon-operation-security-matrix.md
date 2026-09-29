# Daemon Operation Security Matrix

| Operation family | Privilege | Re-auth | Approval | Idempotency | Rollback | Audit |
|---|---|---|---|---|---|---|
| Linux user create | elevated | policy | optional | key-required | reconcile/delete | mandatory |
| Linux user delete | privileged | MFA | policy | key-required | usually none/soft staging | mandatory |
| Nginx apply | elevated | policy | optional | revision-based | previous revision | mandatory |
| Nginx reload | elevated | none/policy | none | replay-safe | n/a | mandatory |
| PHP pool apply | elevated | policy | optional | revision-based | previous revision | mandatory |
| DB create | elevated | policy | optional | key-required | drop if empty/controlled | mandatory |
| DB drop | privileged | MFA | policy | not blindly retryable | restore only if backup exists | mandatory |
| Firewall apply | privileged | MFA | policy | revision-based | explicit prior policy | mandatory |
| Package apply plan | privileged | MFA/policy | policy | plan-ID protected | package-specific | mandatory |
| Server reboot | privileged | MFA | policy | not blindly retryable | none | mandatory |
| Process TERM | elevated | policy | none | replay-tolerant | n/a | mandatory |
| Process KILL | privileged | policy | optional | not retry-looped | none | mandatory |
| Volume expand | privileged | MFA/policy | optional | target-size protected | provider-dependent | mandatory |
| Filesystem expand | privileged | policy | optional | target-size protected | often none | mandatory |
| Cluster failover | privileged | MFA | policy | operation-ID protected | failback separate | mandatory |

## Security invariant

A daemon operation cannot become “safer” merely because it originates from automation.

Automation must satisfy the same privilege, scope, approval and re-auth policy through its service identity.