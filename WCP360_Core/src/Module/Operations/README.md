# Operations

**Status: a durable hosting-plan development candidate is implemented. A general operations engine and production qualification remain unfinished.**

Durable jobs, idempotency, outbox/event delivery, scoped webhook orchestration, retries, leases/fencing, cancellation, approvals and uncertain-outcome reconciliation.

Start here when changing: **Shared job scheduling, progress, retry and recovery mechanics.**

| Implemented candidate source | Responsibility |
| :--- | :--- |
| [Application/RunHostingPlan.php](Application/RunHostingPlan.php) | Claim a queued plan, recheck authority, render its bounded result and persist the terminal state with audit. PostgreSQL claims use row locking. |
| [Infrastructure/WorkbenchStore.php](Infrastructure/WorkbenchStore.php) | Candidate persistence access, tenant/admin authorization, job reads and audit writes. |
| [Infrastructure/WorkbenchSchema.php](Infrastructure/WorkbenchSchema.php) | Candidate account, tenant, site, job and audit schema. |
| [UI/Console/WorkCommand.php](UI/Console/WorkCommand.php) | Worker-context command for the implemented candidate operations. |

Hosting owns [plan submission](../Hosting/Application/SubmitHostingPlan.php), including its validated payload, transactional persistence and idempotency checks. Interrupted plan jobs become `UNKNOWN_OUTCOME` for explicit reconciliation; they are not blindly replayed. This slice does not implement generic retries, leases/fencing, cancellation, compensation, outbox delivery, webhooks or scheduling. Those mechanisms still require accepted contracts, implementation and failure-path qualification.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Domain-specific decisions remain in the domain use case; an operation identifier is not an authorization grant.

- [Core roadmap and completion estimate](../../../ROADMAP.md)
- [Core navigation index](../../../NAVIGATION.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)

Scheduling and event/webhook delivery reuse the durable operation lifecycle. Inbound authenticity/replay and outbound signing/retry behavior follow accepted contracts; token issuance/scope stays in IdentityAccess, while payload validation and resulting resource actions stay in the owning domain. Shared HTTP/security plumbing does not create another endpoint or permission registry. See the [capability map](../../../CAPABILITY-MAP.md).
