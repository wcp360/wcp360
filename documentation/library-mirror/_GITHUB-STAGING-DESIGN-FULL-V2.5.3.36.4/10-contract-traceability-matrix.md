# Contract Traceability Matrix

| Design workflow | Permission contract | Job contract | Daemon/config contract | Audit | Error |
|---|---|---|---|---|---|
| DNS mutation | `domains.write` | `dns.change.apply` | DNS provider/zone adapter | `dns.record.updated` | validation/conflict |
| Website deploy | `applications.deploy` | `deploy.release` | app runtime adapter | `deployment.completed` | build/health/unknown |
| DB restore | `databases.restore` + `backups.restore` | `database.restore` | DB adapter | `backup.restore.*` | artifact/capacity |
| Firewall apply | `network.firewall.manage` | `firewall.apply` | `firewall.policy.apply` | `firewall.policy.applied` | lockout/conflict |
| Server reboot | `servers.reboot` | `server.reboot` | allowlisted reboot op | `server.reboot.*` | dependency/unknown |
| Service reload | `servers.services.manage` | `service.reload` | allowlisted service registry | `service.reload.*` | validation/failure |
| Cert deploy | `ssl.manage` | `certificate.deploy` | `certificate.deploy` | `certificate.deployed` | chain/key mismatch |
| Process kill | `processes.kill` | `process.kill` | bounded process operation | `process.killed` | permission/unknown |
| User privilege change | `iam.roles.manage` | optional | none | `iam.role_assignment.updated` | policy/conflict |
| Support bundle | `support.bundle.create` | `support.bundle.generate` | bounded data collectors | `support.bundle.created` | redaction/generation |

## Rule

No executable contract should be created later unless it can point back to:
- a frozen design workflow;
- a permission/action contract;
- an expected state model;
- an audit requirement;
- an error model.