# WCP360 — Observability / Audit / Secrets

## Taxonomy

Keep distinct:
- domain_event;
- operational_event;
- job_event;
- security_event;
- audit_event;
- metric;
- trace/span;
- alert;
- incident.

## Correlation identifiers

Keep distinct:
- request_id;
- correlation_id;
- trace_id;
- span_id;
- job_id;
- workflow_id;
- audit event type ID;
- audit occurrence identity;
- incident_id.

Correlation metadata never grants authorization.

## Audit requirements

Audit records should carry, where applicable:
- actor/effective actor;
- tenant/scope;
- action;
- resource;
- authorization decision/context reference;
- outcome;
- correlation.

## Open conflicts
- `H3CM-X010`: audit event type identifier vs audit occurrence identifier.
- `H3CM-X011`: audit durability physical contract.
- `H3CM-X012`: tenant identifier metric-cardinality policy.

## Cardinality
High-cardinality identifiers such as tenant/resource/request/job/correlation IDs are not allowed as unrestricted metric labels by default. Final tenant metric policy remains under owner review.

## Redaction
Never expose registered secrets in logs/traces/metrics/audit/errors.
