# WCP360 — H3-CM Conflict Register

These conflicts are MASTER-controlled and must not be silently normalized by specialized owners.

| ID | Finding | Current state | Primary owners |
|---|---|---|---|
| `H3CM-X001` | Permission Registry cleanup membership/count disagreement | CONFLICT | API/IPC, Security, Modules |
| `H3CM-X002` | Process identifier canonicalization | CONFLICT | API/IPC, Security |
| `H3CM-X003` | Historical 43 vs enumerated 47 daemon operations; canonical status/count not proven | CONFLICT | API/IPC, Security |
| `H3CM-X004` | Privileged IPC-B physical contract | OPEN | Security, DevOps, API/IPC |
| `H3CM-X005` | Final Unix identities/groups/ACLs | OPEN | Security, DevOps |
| `H3CM-X006` | `/usr/local/wcp360` vs `/usr/lib/wcp360` | CONFLICT / pending documentation | DevOps, Documentation |
| `H3CM-X007` | PostgreSQL vs `/var/lib/wcp360` state authority | CONFLICT | Database, DevOps, SRE |
| `H3CM-X008` | Account/Organization/Tenant cardinalities | OPEN | Database, Modules, Security |
| `H3CM-X009` | Reconciliation ownership | RECHECK REQUIRED | Database, SRE, API/IPC, Modules |
| `H3CM-X010` | Audit event type ID vs audit occurrence ID | CONFLICT | SRE, Database, API/IPC |
| `H3CM-X011` | Audit durability physical contract | RECHECK REQUIRED | SRE, Database, Security |
| `H3CM-X012` | `tenant_id` metric-cardinality policy | CONFLICT | SRE, Performance, Security |
| `H3CM-X013` | Global traceability completeness | OPEN pending detailed C05 ingestion | Tests/QA, MASTER |

## Rule

Allowed MASTER states:
- OPEN
- CONFLICT
- RECHECK REQUIRED
- CLOSURE PROPOSED
- CLOSED

At snapshot time, no H3V blocker is considered MASTER-closed solely because a specialized package was returned.
