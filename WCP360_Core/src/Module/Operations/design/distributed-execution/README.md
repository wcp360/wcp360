# Durable execution and scale preparation

**Status: design reservation extending a bounded candidate. General leases, fencing, outbox, retries and backpressure are not implemented here.**

[Operations](../../README.md) owns durable lifecycle mechanics shared by browser, CLI, native services, containers and optional automation. Hosting/Mail/Data and other owners retain business policy. See [Core structure](../../../../../STRUCTURE.md), [CLI contract preparation](../../../../../bin/COMMANDS.md) and [node topology](../../../Platform/design/scalability/README.md).

## Existing source to preserve

The hosting queue commits claim/result/audit transitions, uses PostgreSQL SKIP LOCKED and conditional publication, and can mark interrupted running plans UNKNOWN_OUTCOME. Rendering occurs outside those transactions. That candidate has no general automatic retry, heartbeat, lease or downstream effect fence.

RunHostingPlan currently constructs its queue and uses the concrete Go renderer. Introduce and inject narrow ports when generalizing; do not copy a second queue/orchestrator for containers, AI, CLI or premium modules. Keep old payloads/jobs and their idempotency meaning stable.

## Contract increments

| Concern | Required design |
| :--- | :--- |
| Identity | Distinguish originating request reference, durable operation ID and attempt ID; include current actor/tenant/resource/node context. |
| Intent | Version/type, admitted typed inputs, expected resource revision, provider/template/policy versions and immutable artifact/plan hashes. |
| Idempotency | Scope key to the appropriate actor/tenant/use case and fingerprint intent; replay an accepted result only under current visibility/authority. |
| Ownership | Durable claim, lease/heartbeat and monotonic fencing token; effect receiver refuses stale ownership. |
| Scheduling | Bounded queues/concurrency per node/tenant/type, fairness, admission rejection and operational capacity reserve. |
| Execution | Current authorization and entitlement where relevant, bounded deadlines/resources, cancellation/drain and revocation policy. |
| Outcome | Known rejection, known applied effect, known failure and uncertain outcome with evidence for inspection. No blind retry after possible effect. |
| Delivery | Transactional outbox and deduplicated consumers if events/transports are introduced; Redis does not replace durable intent. |
| Recovery | Versioned observation/reconciliation, reversible compensation only, reboot/partition recovery and explicit operator handoff. |
| Evidence | Mandatory audit/correlation without secrets; exact source/provider/target/tool results and retained recovery inputs. |

Bind approvals to actual immutable plans and current resource revisions. An operation ID, queued message, license, agent reply or model-generated proposal is not effect authority.

## Implementation order

1. Define typed operation/queue/effect ports while preserving existing hosting behavior and restricted grants.
2. Establish durable ownership and downstream fencing before increasing worker concurrency or remote-node execution.
3. Add admission/fairness/backpressure and shutdown/drain with independent resource-owner limits.
4. Introduce selected transport/outbox/retry only after idempotence and uncertain-effect recovery are proved.
5. Admit additional types, including container lifecycle and approved assistant proposals, through the same boundary.

## Acceptance

Race claims and resource changes; revoke the actor; lose the reply after an effect; expire a lease while the old worker lives; crash/reboot; fill queues/disks; delay/redeliver messages; fail audit and dependencies. Observe no unauthorized or duplicated destructive effect, bounded resource use, current read visibility and recoverable or explicitly unresolved outcomes.

Do not mark the milestone complete because new folders, interfaces, a Redis service or a second worker exist. Production integration remains subject to Core M1/M2 and each resource's qualification.
