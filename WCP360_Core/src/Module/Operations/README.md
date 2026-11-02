# Operations

**Status: a durable hosting-plan development candidate is implemented. A general operations engine and production qualification remain unfinished.**

Durable jobs, idempotency, outbox/event delivery, scoped webhook orchestration, retries, leases/fencing, cancellation, approvals and uncertain-outcome reconciliation.

Start here when changing: **Shared job scheduling, progress, retry and recovery mechanics.**

| Implemented candidate source | Responsibility |
| :--- | :--- |
| [Application/ListHostingJobs.php](Application/ListHostingJobs.php) | Current-tenant read scope, bounded projection and stable bidirectional job cursors. |
| [Domain/JobListQuery.php](Domain/JobListQuery.php) | Strict query bounds, state/search filters and canonical UUID cursors. |
| [UI/Http/JobListController.php](UI/Http/JobListController.php) | Filtered job history in the WCP360 layout and existing workbench API. |
| [Application/RunHostingPlan.php](Application/RunHostingPlan.php) | Orchestrate current authorization and bounded Go rendering between committed queue transitions. |
| [Infrastructure/HostingPlanQueue.php](Infrastructure/HostingPlanQueue.php) | Fixed claim/publication transactions, compare-and-set outcomes and bounded interruption maintenance with mandatory audit. |
| [Infrastructure/WorkbenchStore.php](Infrastructure/WorkbenchStore.php) | Candidate persistence, job reads and audit writes; its existing tenant/admin authorization and visibility facade delegates to IdentityAccess. |
| [Infrastructure/WorkbenchSchema.php](Infrastructure/WorkbenchSchema.php) | Candidate account, tenant, site, job and audit schema. |
| [UI/Console/WorkCommand.php](UI/Console/WorkCommand.php) | Worker-context command for the implemented candidate operations. |

## Atomic plan transitions and bounded recovery

[HostingPlanQueue](Infrastructure/HostingPlanQueue.php) owns the existing candidate lifecycle, with fixed SQL and no public endpoint or new permission. It returns only the minimal claim metadata. [RunHostingPlan](Application/RunHostingPlan.php) retains its existing constructor/entry points and orchestrates current authorization plus Go rendering outside database transactions. Every queue operation owns its transaction: an already-active caller transaction is refused before any transition, so rendering cannot start on a claim that is merely nested/uncommitted.

A claim updates QUEUED to RUNNING and appends its correlated started occurrence atomically. PostgreSQL uses ordered `FOR UPDATE SKIP LOCKED`; SQLite retains writer serialization and can report contention instead of silently claiming twice. Publication rereads/locks the running job, uses its persisted actor/tenant/site/origin and performs a compare-and-set update. Job result, site state/revision and terminal audit commit together. Missing, unclaimed, terminal or uncertain jobs cannot be published or audited again.

Rendering failure publishes FAILED. If current authority is denied at publication, the rendered result is discarded and a known FAILED outcome is committed instead of leaving a known refusal RUNNING. Database/verification/audit infrastructure errors are not converted into success; transaction failure preserves the previously committed state for explicit recovery. The worker still does not dispatch privileged effects. General policy/effect fencing and revocation races after the current-authority check remain open.

The existing `wcp:candidate:work --reconcile-interrupted` option processes at most **100** RUNNING plans per call, ordered by creation time/UUID. It locks/skips occupied PostgreSQL rows, updates only still-running jobs to UNKNOWN_OUTCOME, audits only actual transitions and returns their actual count. Any required audit failure rolls back that batch. It leaves site state, uncertain results and original references intact, never requeues work and refuses stale result publication afterward.

**Stop all rendering workers first.** Row locks do not detect a renderer outside its database transaction, a live process or a lost host. Repeat maintenance as needed and inspect remaining running rows/locks: a zero count is not proof that all interrupted work is reconciled. There is no automatic retry, lease expiry, liveness detector, general cancellation or compensation in this slice.

[Transition tests](../../../tests/Integration/HostingPlanQueueTest.php) define successful/refused paths, audit rollback, caller-transaction refusal, more-than-one-batch recovery and a private SQLite journey. [PostgreSQL concurrency tests](../../../tests/Integration/HostingPlanQueueConcurrencyTest.php) use separate live connections and held row locks, without sleep-based timing, to exercise overlapping claims/recovery and terminal-state preservation. [Restricted-role checks](../../../tests/Integration/CandidateDatabaseRolesTest.php) retain worker capabilities and deny USER claims. Definitions are not execution evidence; source-bound CI is tracked in draft PR #116. No dependency or grant change is added by the queue guard. The request-correlation and site-profile migrations remain required before running the matching worker source.

## Site-profile validation at publication

The [site profile](../Hosting/Domain/SiteRuntimeProfile.php) records the existing Classic/PHP-FPM or Advanced/Worker topology and contract revision 1. Apply [Version20261102000200](../../../migrations/Version20261102000200.php) through setup maintenance before running the matching worker source. It backfills only site metadata and preserves existing jobs/results, fingerprints, IDs, lifecycle revisions and audit history. Engine/global-setting values, public projections, native-stack behavior and existing grants stay unchanged; Modern remains outside durable plan selection.

[RunHostingPlan](Application/RunHostingPlan.php) validates the stored pair before Go rendering and passes the captured profile as the optional third argument to `HostingPlanQueue::finish`. In the existing publication transaction, a still-running job is locked and current tenant authority rechecked. For a successful guarded result, the queue also locks the site row on PostgreSQL and compares its validated engine/topology/revision with the captured value before publishing. SQLite retains its transaction serialization and may report contention; it does not gain PostgreSQL row-lock semantics.

Changed or invalid profiles discard the result and commit the existing FAILED/PLAN_FAILED outcome with required correlated audit. State/result/site revision and terminal audit remain atomic; audit/database failure still rolls the transition back. The optional guard does not change two-argument generic transition callers or reinterpret their result documents. Existing claim metadata and HTTP/API response shapes remain unchanged.

This guards the rendering/publication interval only. There is no site-switch endpoint, profile-revision increment workflow, immutable job topology history, lease or general effect fence. Future actual stack migration must snapshot/fence job intent and coordinate listeners, TLS ownership, worker draining, health and recovery. A plan/3 Modern preview does not enter this queue or authorize activation.

[Profile unit definitions](../../../tests/Unit/SiteRuntimeProfileTest.php), [consumer regressions](../../../tests/Integration/SiteRuntimeProfileWorkflowTest.php) and [restricted-role checks](../../../tests/Integration/CandidateDatabaseRolesTest.php) cover valid/malformed pairs, changed-profile publication, retained generic behavior and existing permissions. They are definitions, not local execution evidence; current-source CI is tracked in [draft PR #116](https://github.com/yyogas/wcp360/pull/116).

## Durable hosting-plan request correlation

[PlanCorrelationSchema](Infrastructure/PlanCorrelationSchema.php) and [Version20261102000100](../../../migrations/Version20261102000100.php) add nullable `request_id` columns to the existing candidate jobs and audit tables, constrained to 32 lowercase hexadecimal characters when present, plus an audit lookup index. Apply the migration with the existing setup maintenance procedure before running this source. Existing rows remain NULL; historical audit is neither backfilled nor rewritten, and append-only protection remains applied. No database-role grant changes.

[Shared RequestId](../../Shared/Correlation/RequestId.php) owns generation and validation. HTTP plan submission passes its already server-generated main-request reference as a separate internal argument; caller body fields cannot set it and caller tracing headers remain ignored. Direct non-HTTP use generates an independent reference when omitted. Correlation metadata is excluded from the intent fingerprint and success payload: an idempotent retry returns the same job, keeps its first origin and adds no duplicate queued occurrence. Its HTTP response header still describes the current request.

The job and queued audit reference commit atomically. A fresh worker reads the stored origin for started, succeeded, failed and interrupted occurrences; audit failure rolls back the associated transaction. Legacy tasks retain NULL through subsequent transitions. Existing authorization and outcome rules remain authoritative. This does not resolve in-flight revocation races or add retries, cancellation, reconciliation or leases.

The authorized task detail shows the creation reference separately from the current response header, with an explicit unavailable state for historical rows. List/API summary projections stay unchanged; references never serve as an access key. The application does not gain audit-table reads. Operators with existing setup authority can correlate candidate job/audit metadata using the fixed resource and reference fields; there is no new general audit-query UI or privileged SQL endpoint.

[Unit definitions](../../../tests/Unit/RequestIdTest.php), [service definitions](../../../tests/Integration/PlanCorrelationTest.php), [HTTP journeys](../../../tests/Integration/PlanCorrelationJourneyTest.php), [SQLite/PostgreSQL upgrade definitions](../../../tests/Integration/PlanCorrelationSchemaTest.php) and [actual restricted-role handoff](../../../tests/Integration/CandidateDatabaseRolesTest.php) cover format/refusal, restart, replay, legacy data, audit rollback/immutability and tenant access. Executed outcomes remain bound to exact source/CI in draft PR #116. IPC propagation, global audit/trace conventions, all resource lifecycles and canonical production correlation remain unfinished.

## Filtered hosting-job inventory

[JobListQuery](Domain/JobListQuery.php) reuses [shared tenant-page bounds](../../Shared/Http/TenantPageQuery.php) with the [prepared-site inventory](../Hosting/README.md#prepared-site-inventory); both browser views reuse one [cursor-navigation component](../../../templates/components/data/cursor-pagination.html.twig). It validates the existing tenant scope, five supported job states, an ASCII hostname fragment and page sizes of 25, 50 or 100. Unknown fields, arrays, oversized input, SQL wildcard characters, invalid UUIDs and simultaneous before/after cursors fail before querying job data.

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
