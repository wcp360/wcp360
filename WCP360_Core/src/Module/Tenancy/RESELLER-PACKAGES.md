# Reseller packages, plans and quota enforcement

**Status: Core design preparation, November 2, 2026.** This guide organizes existing requirements; it implements no plan builder, quota engine, database schema, permission identifier, API or provisioning action. [M4](../../../ROADMAP.md#m4) and the [233-capability map](../../../CAPABILITY-MAP.md) retain their delivery status.

**Edition-scope amendment, November 3, 2026:** the [owner's reseller decision](../../../../documentation/governance/decisions/OWNER-DECISION-COMMUNITY-RESELLER-2026-11-03.md) excludes reseller management and reseller-specific delegation from Community. An optional commercial workflow may be considered later; no reseller package is available or qualified, and PRO inclusion, package identity and terms remain unconfirmed. Essential account/tenant ownership, ordinary hosting plans/quotas, IAM, isolation, audit and local recovery remain Core duties. The thirteen owners, existing licence and canonical identifiers are unchanged.

The former edition inclusion statement is retained below as **superseded product scope**, not current Community requirements:

> Package templates, reseller delegation, resource ceilings, assignment and authorized exceptions belong to the **AGPL Core**. They are not premium-only features. An optional agency/billing package may coordinate advanced portfolios through accepted Core use cases; it cannot replace Tenancy or become a condition for ordinary reseller hosting.

All detailed specifications and refusal criteria below are preserved. Requirements concerning ordinary account plans and quotas remain essential Core requirements; reseller hierarchy/delegation/portfolio requirements are retained design for a possible separately accepted workflow. Their presence does not implement that workflow or commit its commercial placement. Future reseller operations must reuse accepted Core ownership and authorization boundaries; moving the product surface does not authorize paid code to bypass them.

## Product vocabulary

| Term | Meaning |
| :--- | :--- |
| Hosting plan/package | A versioned set of resource limits and allowed hosting features assigned to an account or reseller. |
| Plan template | An authoring source for a plan version, with units, scope, defaults and validation. |
| Assignment | A specific account/reseller bound to an explicit accepted plan version. |
| Override | A separately authorized, scoped exception with reason, limits, optional expiry and audit; not an edit to the shared template. |
| PRO edition entitlement | Commercial eligibility for an optional package, separate from hosting plans, IAM authority and resource capacity. |

Starter, Pro and Enterprise can be hosting-plan names. A plan called Pro does not activate the WCP360 PRO edition, install code, grant ROOT authority or confer third-party subscriptions.

## Ownership

| Owner | Responsibility |
| :--- | :--- |
| [Tenancy](README.md) | Plans, immutable versions, assignments, hierarchy, limits, reservations and effective policy. |
| [IdentityAccess](../IdentityAccess/README.md) | Current actor, reseller delegation, target ownership and separate authority for exceptional changes. |
| [Platform](../Platform/README.md) | Admitted enforceable host/node/storage/container limits and capacity observations. |
| Resource owner | Hosting, Mail, Data and Files retain authoritative resource inventory and provisioning/removal rules. |
| [Operations](../Operations/README.md) | Durable intent, idempotency, current-authority execution, concurrency/fencing, uncertain outcomes and reconciliation. |
| [Observability](../Observability/README.md) | Usage source, unit, interval, freshness, missing samples and supported alerts. |
| [BackupRestore](../BackupRestore/README.md) | Supported preservation, export, restore and recovery; a plan change is not permission to delete data. |

No premium controller, billing webhook, node label or external provider response may write Core assignments/tables or bypass these owners.

## Template builder and versioning

The future form must describe permitted accounts, websites, domains, databases, mailboxes, technical access and named features, together with resource ceilings and policy defaults. Validate supported feature dependencies before publication; a checkbox does not make an absent engine or connector available.

Use explicit immutable published versions. Draft editing creates no active entitlement. Assignments retain their accepted version until an authorized, reviewed transition; changing a template cannot silently mutate every reseller. Show the before/after effective policy, affected accounts/resources and enforcement limits before applying a transition.

Define finite numeric ranges, overflow handling, units, periods, rounding, zero/disabled semantics and an explicit unlimited policy. Reject negative, malformed, incompatible or ambiguous values. Do not treat an empty string, missing metric or provider error as unlimited capacity.

## Resource limits and measurements

| Resource family | Required distinction |
| :--- | :--- |
| Accounts and service counts | Requested, reserved, active and deleting resources; reseller aggregate versus individual customer limit. |
| Storage and inodes | Allocated, reserved and observed usage; pool/tier availability, effective ownership and what the selected backend can enforce. NVMe is a storage characteristic, not proof of dedicated I/O. |
| Bandwidth | Direction, unit, interval/reset, source and collection gaps; measured traffic versus a hard traffic cap. |
| CPU, RAM, processes and I/O | Requested allocation, applied limit, measured usage and underlying provider capability. Qualify overcommit and noisy-neighbor behavior rather than promising dedicated capacity. |
| Container/VM resources | Tenant/account allocation and actual provider/guest enforcement; a plan value does not itself create a cgroup, disk quota or hypervisor reservation. |
| Jobs and API usage | Concurrency, rate/window and bounded queue/resource budgets; credential scope remains independent. |
| External API/model credits | Accepted internal budget and reported third-party consumption are different records. Pricing, delayed usage and provider billing need their own terms and evidence. |

Observations must include freshness and failure status. Do not display a limit as enforced unless its provider/profile has evidence. Missing or delayed usage must remain visible; a stale sample is not proof that the next allocation fits.

## Delegation and effective policy

Resolve the server policy, reseller ceiling, account assignment, current authorized overrides and resource-specific restrictions through one Core policy owner. The exact production contract must settle precedence and aggregate accounting before implementation; this guide adds no replacement permission registry.

A reseller cannot exceed the scope or aggregate capacity delegated by ROOT. Child plans cannot manufacture features, external credits or permissions absent from that delegation. A hosting feature allowance, a PRO commercial right and current actor permission remain three independent requirements.

Overrides need explicit authorized scope, reason and resulting effective limits. Increasing a child value still requires parent capacity and server policy. An exception may relax a selected hosting limit where permitted; it cannot bypass tenant isolation, essential security, resource ownership or publisher trust. Show inherited versus overridden values and support a reviewed return to inheritance.

## Assignment and provisioning

A one-click assignment is a user-experience goal, not a promise of instant completed infrastructure.

1. Authenticate the current actor and resolve the exact reseller/account ownership and selected immutable plan version.
2. Compute current effective policy, existing usage/reservations, provider readiness and available aggregate capacity. Present unsupported limits and affected resources.
3. Record the accepted assignment/intent and capacity reservation with the required atomicity, audit and idempotency under the production contracts.
4. Schedule bounded owning use cases through Operations. Recheck current authority, resource revisions, capacity and the relevant fence before effects.
5. Track observed outcomes per resource. Release reservations only when reconciliation proves the corresponding allocation did not occur or was safely removed.
6. Expose success, partial failure, uncertainty and recovery separately. Keep unrelated hosting resources available.

Concurrency, multiple nodes and delayed provider responses must not allocate the same remaining quota twice. An interrupted job or missing acknowledgement is not proof that nothing was created and is not permission for blind replay.

## Changes, downgrade and recovery

Plan updates require a preview of effective differences. Below-current-usage downgrades must select an explicit reviewed behavior: reject, schedule remediation, constrain future allocations or apply another accepted policy. Do not silently delete files, mailboxes, databases, volumes or accounts to fit the new number.

Removing an override, changing an owner, closing an account or retiring a reseller must account for live consumers, retention, pending jobs and outstanding reservations. Provider failure, premium expiry or billing outage cannot remove essential Core plan inspection, current administration or supported recovery.

## Acceptance before implementation

| Boundary | Required evidence |
| :--- | :--- |
| Access | Anonymous, wrong role/tenant/reseller, revoked membership, forged target/version and unauthorized override fail; browser mutations also require the applicable CSRF, Origin and reauthentication controls. |
| Numeric policy | Negative/overflow/ambiguous units, zero/unlimited edges, incompatible features and unsupported enforcement are rejected or explicitly unavailable. |
| Hierarchy | Parent and child limits, overrides, expiry, changed assignment and aggregate usage remain consistent; commercial rights never widen delegated permission. |
| Concurrency | Parallel account creation, retries, cancellation, node partitions, stale measurements and delayed acknowledgements cannot create unaccounted duplicate allocations. |
| Lifecycle | Interrupted provisioning, downgrade, transfer, failed enforcement and deletion reconcile honestly while retained resources and recovery remain accessible. |
| Evidence and privacy | Explain effective limits and measured enforcement; keep secrets and customer content out of ordinary logs, exports and billing/AI payloads. |

These are acceptance requirements, not executed tests. Continue through the [Core roadmap](../../../ROADMAP.md), [current status](../../../STATUS.md) and [platform integration register](../../../../documentation/development/PLATFORM-INTEGRATIONS-COVERAGE.md). The guide creates no production readiness or completion credit.
