# WCP360 — Database / Data Model

## Boundary

`control-plane PostgreSQL != hosted MariaDB/PostgreSQL engines != tenant/application DB content`

## Core identities

`Account != Organization != Tenant`

Also distinguish:
- User;
- Customer;
- Reseller;
- Hosting Account;
- Tenant;
- Organization.

Roles/capabilities are not tenants.

## Ownership

Ownership, management/delegation and operational authority are distinct concepts.

Tenant-bound resources require stable internal resource IDs and explicit scope.

## Transaction reality

There is no global ACID transaction across:
- PostgreSQL;
- jobs;
- wcpd/wcp-privd;
- filesystem;
- Linux identities;
- services;
- external systems.

## State authority

Candidate direction:
- PostgreSQL: logical control-plane state;
- filesystem/runtime: bounded materialized/local state;
- Linux/services: observed external/system state.

However `H3CM-X007` remains open because final PostgreSQL vs `/var/lib/wcp360` authority has not been MASTER-closed.

## Open items
- `H3CM-X007` state authority;
- `H3CM-X008` Account/Organization/Tenant cardinalities;
- complete entity graph;
- constraints and lifecycle;
- backup/restore consistency;
- outbox/reconciliation persistence details.
