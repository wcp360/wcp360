# Operations

**Status: a durable hosting-plan development candidate is implemented. A general operations engine and production qualification remain unfinished.**

Durable jobs, idempotency, outbox/event delivery, scoped webhook orchestration, retries, leases/fencing, cancellation, approvals and uncertain-outcome reconciliation.

Start here when changing: **Shared job scheduling, progress, retry and recovery mechanics.**

| Implemented candidate source | Responsibility |
| :--- | :--- |
| [Application/RunHostingPlan.php](Application/RunHostingPlan.php) | Claim a queued plan, recheck authority, render its bounded result and persist the terminal state with audit. PostgreSQL claims use row locking. |
| [Infrastructure/WorkbenchStore.php](Infrastructure/WorkbenchStore.php) | Candidate persistence, job reads and audit writes; its existing tenant/admin authorization and visibility facade delegates to IdentityAccess. |
| [Infrastructure/WorkbenchSchema.php](Infrastructure/WorkbenchSchema.php) | Candidate account, tenant, site, job and audit schema. |
| [UI/Console/WorkCommand.php](UI/Console/WorkCommand.php) | Worker-context command for the implemented candidate operations. |

Hosting owns [plan submission](../Hosting/Application/SubmitHostingPlan.php), including its validated payload, transactional persistence and idempotency checks. Interrupted plan jobs become `UNKNOWN_OUTCOME` for explicit reconciliation; they are not blindly replayed. This slice does not implement generic retries, leases/fencing, cancellation, compensation, outbox delivery, webhooks or scheduling. Those mechanisms still require accepted contracts, implementation and failure-path qualification.

## Current authority at execution

[CandidateAuthorization](../IdentityAccess/Application/CandidateAuthorization.php) owns the common current-database decision for tenant visibility and the single `candidate.hosting.plan` permission. `WorkbenchStore` preserves its existing constructor and methods while delegating those checks. Submission, HTTP/API reads and worker execution therefore require an active USER account, the exact tenant membership and no explicit access block. An administrator role does not provide tenant authority, and a queued job does not retain authority revoked after submission.

[Administrator access blocks](../IdentityAccess/README.md#candidate-authorization-and-access-blocks) are idempotent, transactional and audited by IdentityAccess. Blocks survive membership deletion/regrant; removing a block cannot create membership. The worker rechecks the shared rule before processing a queued hosting plan. This is the bounded candidate execution check, not a claim of general production revocation fencing or resolution of every concurrent revocation/effect race.

Older candidate databases must apply [Version20261101000200](../../../migrations/Version20261101000200.php) before these checks run. [Authorization regressions](../../../tests/Integration/CandidateAuthorizationTest.php) cover blocked queued work and transaction/audit behavior; [HTTP journeys](../../../tests/Integration/PermissionAdministrationTest.php) cover existing user sessions, API reads and queued work after a block. Current-source executed results belong to [draft PR #116](https://github.com/yyogas/wcp360/pull/116), separately from historical checkpoints.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Domain-specific decisions remain in the domain use case; an operation identifier is not an authorization grant.

- [Core roadmap and completion estimate](../../../ROADMAP.md)
- [Core navigation index](../../../NAVIGATION.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)

Scheduling and event/webhook delivery reuse the durable operation lifecycle. Inbound authenticity/replay and outbound signing/retry behavior follow accepted contracts; token issuance/scope stays in IdentityAccess, while payload validation and resulting resource actions stay in the owning domain. Shared HTTP/security plumbing does not create another endpoint or permission registry. See the [capability map](../../../CAPABILITY-MAP.md).
