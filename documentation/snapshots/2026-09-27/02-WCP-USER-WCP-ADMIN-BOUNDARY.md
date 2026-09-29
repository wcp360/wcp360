# WCP360 — WCP-USER / WCP-ADMIN Boundary

## WCP-USER
Client/reseller hosting administration surface.

Typical domains:
- applications;
- domains/DNS;
- databases;
- email;
- files;
- backups;
- account-scoped security;
- scoped monitoring.

## WCP-ADMIN
Server/infrastructure/services/orchestration administration surface.

Typical domains:
- servers;
- service control;
- network/firewall/routes;
- updates;
- processes;
- storage;
- clusters/HA;
- IAM administration;
- platform policies.

## Security rule

A shared backend, design component or repository does not imply shared authorization context.

The following must remain distinguishable:
- session/cookie/security context;
- routes/endpoints;
- authorization policies;
- audit context;
- runtime identity where deployment requires it;
- privileged action envelope.

Cross-surface privilege crossing must fail closed.
