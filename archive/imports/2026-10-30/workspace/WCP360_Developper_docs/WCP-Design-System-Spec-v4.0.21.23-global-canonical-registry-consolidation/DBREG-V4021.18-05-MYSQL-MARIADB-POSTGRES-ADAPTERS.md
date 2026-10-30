# V4.0.21.18 --- MySQL / MariaDB / PostgreSQL Adapter Contract

Canonical provider operations cover create/drop database,
create/alter/lock/drop principal, grants, import/export, health and
capability discovery.

Provider-specific features are capability gated: charset/collation,
extensions, schemas, ownership semantics, connection limits,
repair/optimize equivalents, logical backup formats and rename support.

WCP never pretends semantic equivalence where providers differ.
Unsupported features return `CAPABILITY_UNAVAILABLE`.
