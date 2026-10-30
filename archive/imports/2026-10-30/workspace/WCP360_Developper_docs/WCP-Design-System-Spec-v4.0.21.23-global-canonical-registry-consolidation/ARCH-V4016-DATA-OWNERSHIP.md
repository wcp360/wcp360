# V4.0.16 --- Canonical Data Ownership

## PostgreSQL WCP owns

Identity/RBAC metadata, tenants/accounts/resellers, plans/entitlements,
resource desired state, Jobs, idempotency, events/outbox metadata, audit
indexes/records, integration metadata, secret references and observed
summaries.

## Filesystem owns

Tenant files/mail datasets where local, generated service
configurations, immutable WCP packages, runtime sockets and logs
according canonical paths.

## Managed services own

Operational runtime data in their native stores: MariaDB client DB data,
PowerDNS authoritative backend data, mail indexes/queues, etc., with WCP
holding identifiers/desired state rather than duplicating entire
datasets.

## Rule

WCP metadata is not a substitute for backup of service-native data;
service-native data is not a substitute for control-plane recovery.
