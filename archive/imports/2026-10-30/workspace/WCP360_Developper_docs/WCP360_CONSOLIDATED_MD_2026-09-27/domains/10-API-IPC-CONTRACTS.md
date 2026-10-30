# WCP360 — API / IPC / Contracts

## Contract-before-implementation rule

Every contract should define:
- ID / name / version;
- producer / consumer;
- authentication;
- authorization;
- request schema;
- response schema;
- errors;
- timeout;
- retry policy;
- idempotency;
- audit;
- security;
- backward compatibility;
- deprecation;
- tests;
- status.

## IPC trust boundaries

At minimum:
1. backend → `wcpd`;
2. `wcpd` → `wcp-privd`.

Direct backend → `wcp-privd` is forbidden.

## Privileged operation rule

`action_id != ipc_operation_id != executable != command line`

The caller requests semantic bounded operations. The privileged component selects the safe primitive.

## Unknown outcome

After dispatch, timeout does not prove failure. Contracts must support `UNKNOWN_OUTCOME`, observation and reconciliation.

## Open items
- `H3CM-X002` process identifier canonicalization;
- `H3CM-X003` daemon operation canonical status/count;
- `H3CM-X004` privileged IPC-B physical contract;
- complete API/Job/Event/Error registries;
- complete Action→IPC→Audit→Test mappings.
