# Operations

**Status: documented module boundary; application source is not implemented by this directory.**

Durable jobs, idempotency, outbox/event delivery, scoped webhook orchestration, retries, leases/fencing, cancellation, approvals and uncertain-outcome reconciliation.

Start here when changing: **Shared job scheduling, progress, retry and recovery mechanics.**

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Domain-specific decisions remain in the domain use case; an operation identifier is not an authorization grant.

- [Core navigation index](../../../NAVIGATION.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)

Scheduling and event/webhook delivery reuse the durable operation lifecycle. Inbound authenticity/replay and outbound signing/retry behavior follow accepted contracts; token issuance/scope stays in IdentityAccess, while payload validation and resulting resource actions stay in the owning domain. Shared HTTP/security plumbing does not create another endpoint or permission registry. See the [capability map](../../../CAPABILITY-MAP.md).
