# V4.0.21.23 --- Global Version Compatibility Registry

Compatibility dimensions: WCP release, control DB schema, resource
schema, daemon/RPC contract, provider adapter contract, backup manifest
format and Host Profile/provider versions.

Each WCP release must declare min/current/max-readable versions, write
version, migration sequence, reversibility boundary and restore/import
compatibility.

Unknown future schema is fail-closed for writes. Irreversible migrations
require checkpoint/recovery and explicitly terminate rollback
compatibility.
