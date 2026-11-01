# Operations

**Status: a durable hosting-plan development candidate is implemented. A general operations engine and production qualification remain unfinished.**

Durable jobs, idempotency, outbox/event delivery, scoped webhook orchestration, retries, leases/fencing, cancellation, approvals and uncertain-outcome reconciliation.

Start here when changing: **Shared job scheduling, progress, retry and recovery mechanics.**

| Implemented candidate source | Responsibility |
| :--- | :--- |
| [Application/ListHostingJobs.php](Application/ListHostingJobs.php) | Current-tenant read scope, bounded projection and stable bidirectional job cursors. |
| [Domain/JobListQuery.php](Domain/JobListQuery.php) | Strict query bounds, state/search filters and canonical UUID cursors. |
| [UI/Http/JobListController.php](UI/Http/JobListController.php) | Filtered job history in the WCP360 layout and existing workbench API. |
| [Application/RunHostingPlan.php](Application/RunHostingPlan.php) | Claim a queued plan, recheck authority, render its bounded result and persist the terminal state with audit. PostgreSQL claims use row locking. |
| [Infrastructure/WorkbenchStore.php](Infrastructure/WorkbenchStore.php) | Candidate persistence, job reads and audit writes; its existing tenant/admin authorization and visibility facade delegates to IdentityAccess. |
| [Infrastructure/WorkbenchSchema.php](Infrastructure/WorkbenchSchema.php) | Candidate account, tenant, site, job and audit schema. |
| [UI/Console/WorkCommand.php](UI/Console/WorkCommand.php) | Worker-context command for the implemented candidate operations. |

## Durable hosting-plan request correlation

[PlanCorrelationSchema](Infrastructure/PlanCorrelationSchema.php) and [Version20261102000100](../../../migrations/Version20261102000100.php) add nullable `request_id` columns to the existing candidate jobs and audit tables, constrained to 32 lowercase hexadecimal characters when present, plus an audit lookup index. Apply the migration with the existing setup maintenance procedure before running this source. Existing rows remain NULL; historical audit is neither backfilled nor rewritten, and append-only protection remains applied. No database-role grant changes.

[Shared RequestId](../../Shared/Correlation/RequestId.php) owns generation and validation. HTTP plan submission passes its already server-generated main-request reference as a separate internal argument; caller body fields cannot set it and caller tracing headers remain ignored. Direct non-HTTP use generates an independent reference when omitted. Correlation metadata is excluded from the intent fingerprint and success payload: an idempotent retry returns the same job, keeps its first origin and adds no duplicate queued occurrence. Its HTTP response header still describes the current request.

The job and queued audit reference commit atomically. A fresh worker reads the stored origin for started, succeeded, failed and interrupted occurrences; audit failure rolls back the associated transaction. Legacy tasks retain NULL through subsequent transitions. Existing authorization and outcome rules remain authoritative. This does not resolve in-flight revocation races or add retries, cancellation, reconciliation or leases.

The authorized task detail shows the creation reference separately from the current response header, with an explicit unavailable state for historical rows. List/API summary projections stay unchanged; references never serve as an access key. The application does not gain audit-table reads. Operators with existing setup authority can correlate candidate job/audit metadata using the fixed resource and reference fields; there is no new general audit-query UI or privileged SQL endpoint.

[Unit definitions](../../../tests/Unit/RequestIdTest.php), [service definitions](../../../tests/Integration/PlanCorrelationTest.php), [HTTP journeys](../../../tests/Integration/PlanCorrelationJourneyTest.php), [SQLite/PostgreSQL upgrade definitions](../../../tests/Integration/PlanCorrelationSchemaTest.php) and [actual restricted-role handoff](../../../tests/Integration/CandidateDatabaseRolesTest.php) cover format/refusal, restart, replay, legacy data, audit rollback/immutability and tenant access. Executed outcomes remain bound to exact source/CI in draft PR #116. IPC propagation, global audit/trace conventions, all resource lifecycles and canonical production correlation remain unfinished.

## Filtered hosting-job inventory

[JobListQuery](Domain/JobListQuery.php) validates the existing tenant scope, five supported job states, an ASCII hostname fragment and page sizes of 25, 50 or 100. Unknown fields, arrays, oversized input, SQL wildcard characters, invalid UUIDs and simultaneous before/after cursors fail before querying job data.

[ListHostingJobs](Application/ListHostingJobs.php) provides a bounded read projection and bidirectional cursor navigation ordered by creation time and UUID. Each pivot, page and boundary query joins the existing IdentityAccess tenant-scope rule: active USER identity, exact membership and no explicit block. The service rechecks current authority before returning. Cursors select an authorized result position; they never grant access. A missing, foreign or no-longer-matching pivot has the same safe refusal. Filter changes restart at the first page. Reads do not change jobs or append business audit records.

[JobListController](UI/Http/JobListController.php) exposes `GET /workbench/jobs` and `GET /workbench/v1/jobs`. The browser defaults to the first accessible workspace, supports an empty-access view, and links from the existing sidebar/dashboard. The API requires an explicit `tenant`. Both require the existing USER session; ADMIN roles and pending authentication do not supply USER tenant access. Existing host/session and HTTP error boundaries remain applied.

API query fields are `tenant`, optional `state`, `q`, `limit`, and either `after` or `before` (a job UUID). Responses contain `items` and `pagination: {limit,next,previous}`. Items expose only ID, state, creation/start/completion times, hostname and application-server choice. Raw plan results, fingerprints, idempotency keys and actor IDs are excluded. Existing dashboard/plugin calls reuse this query and retain their original five-field/100-row projection.

This is live keyset navigation, not a frozen snapshot. Concurrent inserts do not shift an OFFSET or duplicate already traversed rows; state changes/deletion can invalidate a filtered pivot, requiring a fresh first page. No total count, arbitrary sort, global search, new schema/index/grant, general scheduler, cancellation or automatic retry is introduced. Large-dataset performance and canonical production API/permission acceptance remain open.

[Query cases](../../../tests/Unit/JobListQueryTest.php), [service cases](../../../tests/Integration/ListHostingJobsTest.php), [HTTP journeys](../../../tests/Integration/JobListJourneyTest.php) and [restricted PostgreSQL-role coverage](../../../tests/Integration/CandidateDatabaseRolesTest.php) define validation, pagination, privacy, revocation, escaping and retained-grant checks. Exact-source outcomes are recorded in draft PR #116; local executable tests remain stopped by user instruction. M1/M2 and production qualification remain open.

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
