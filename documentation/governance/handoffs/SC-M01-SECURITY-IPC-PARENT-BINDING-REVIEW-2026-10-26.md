# WCP360 — Security / IPC privileged operation binding review

> Historical integration note (2026-11-02): this dated branch document is retained
> as development-source evidence. Its original decisions, pending states, path
> literals and execution claims describe the recorded snapshot; they do not
> override the current Core implementation or grant owner approval, independent
> E3 review, a CODE-BASELINE, deployment or production qualification. See the
> [tooling integration guide](../maintenance/BRANCH-TOOLING-INTEGRATION-2026-11-02.md)
> for current paths and separate current/historical CI scopes.
**Date:** 2026-10-26
**Classification:** DERIVED EVIDENCE / WCP360 HANDOFF — NOT CANONICAL AUTHORITY
**Source Security:** `specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` (Git blob `6d74f2f363524511424f839ca7262143c2af95ab`)
**Source IPC:** `specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md` (Git blob `fec9c74e8413db822662e47c76c40486e282334f`)
**Tracking:** #13; AUDIT-WCP-0104; #41; integration PR #90
**Owner:** SECURITY / HARDENING (permission/parent authorization) + API / IPC / CONTRACTS (typed action/job/primitive tuples)
**Related:** TESTS / QA, MODULES, DATABASE when persistence is needed, ARCHITECTURE REVIEW
**Change of official baseline:** NONE

## 1. Read-only audit result

The final Security v1.0 matrix and final IPC-B/1 registry have exactly **47 identical `ipc_operation_id` values**. For the security matrix's *permission expression field*:

- 35 rows contain one direct active `permission_id`;
- 12 rows contain composite or abstract approved-parent / provisioning / OR expressions requiring further owner-ratified context and binding.

**Neither category is a production-ready dispatcher authorization.** Direct permission names do not establish the full authorization-context/tenant/surface/primitive/grant binding automatically.

A machine check and 19 adversarial reference tests were run in integrated PR #90, combined with the other read-only/prototype suites (89 tests across all four families):
https://github.com/yyogas/wcp360/actions/runs/40415211697

No application implementation test was executed by this work.

## 2. The 12 owner-binding decisions

The following table is derived from the **existing final Security and IPC exports**; it does not propose approving a permission OR expression.

| Privileged operation | IPC contract | Security permission expression | Security origin/parent action expression | Existing TEST-IPC reference |
|---|---|---|---|---|
| `filesystem.directory.create` | `IPC-WCP-1005` | files.write + approved provisioning binding | same approved parent action | TEST-IPC-1005 |
| `filesystem.ownership.apply` | `IPC-WCP-1006` | files.permissions.manage + approved provisioning binding | same approved parent action | TEST-IPC-1006 |
| `filesystem.permissions.apply` | `IPC-WCP-1007` | files.permissions.manage + approved provisioning binding | same approved parent action | TEST-IPC-1007 |
| `filesystem.quota.apply` | `IPC-WCP-1008` | iam.account_overrides.manage OR applications.runtime.manage | same approved parent action | TEST-IPC-1008 |
| `nginx_vhost.validate` | `IPC-WCP-1009` | approved app/domain/admin binding | same approved parent action | TEST-IPC-1009 |
| `nginx_vhost.apply` | `IPC-WCP-1010` | approved app/domain/admin binding | same approved parent action | TEST-IPC-1010 |
| `nginx_vhost.remove` | `IPC-WCP-1011` | approved app/domain/admin binding | same approved parent action | TEST-IPC-1011 |
| `nginx.reload` | `IPC-WCP-1012` | approved technical parent OR servers.services.manage | same approved parent action | TEST-IPC-1012 |
| `php_pool.validate` | `IPC-WCP-1013` | approved app/admin binding | same approved parent action | TEST-IPC-1013 |
| `php_pool.apply` | `IPC-WCP-1014` | approved app/admin binding | same approved parent action | TEST-IPC-1014 |
| `php_pool.remove` | `IPC-WCP-1015` | approved app/admin binding | same approved parent action | TEST-IPC-1015 |
| `php_fpm.reload` | `IPC-WCP-1016` | approved technical parent OR servers.services.manage | same approved parent action | TEST-IPC-1016 |

### Owner decision needed per row

SECURITY must produce an explicit, testable authorization rule for each acceptable origin surface and policy context. API/IPC must publish the exact origin `action_id` → technical `ipc_operation_id` binding (and typed `job_type` where applicable), including approved parent workflows and denial cases.

Minimum conditions:

- exactly one canonical base `permission_id` for an authorized origin action, with any additional gates explicitly specified;
- no implicit wildcard, string-prefix permission match, OR-expression passed straight to code, or caller-provided technical operation ID;
- durable context preservation and execution-time reauthorization where specified;
- target/resource/tenant isolation, revision check, privilege elevation/MFA/approval when applicable;
- Security and API/IPC owner approval + negative tests before dispatch;
- proper audit event and job correlation; no silent status transition on timeout.

For any row with several permitted origin actions, document **distinct exact allowed tuples**, not an inferred blanket permission.

## 3. Two approved audit lifecycle annotations

The Security matrix annotates these operations with **two semantic audit event types each**:

| Operation | Security matrix source expression |
|---|---|
| `server.reboot` | `server.reboot.requested + server.reboot.completed` |
| `server.shutdown` | `server.shutdown.requested + server.shutdown.completed` |

Therefore the 47 Security operations expose **49 audit event-type references** across the matrix. An implementation must not treat a plus-separated annotation as a single valid `audit_event_id` or invent one success event for both stages.

When a request is accepted but completion is unobserved, preserve the canonical unknown-outcome/reconciliation requirements rather than recording fictitious completion. SRE/QA should verify event ordering, idempotency and lost-result behavior.

## 4. Decision response template

```text
HANDOFF ID = SC-M01 SECURITY/IPC PARENT BINDINGS
OPERATION ID =
IPC CONTRACT ID =
ALLOWED ORIGIN ACTION ID =
BASE PERMISSION ID =
ADDITIONAL AUTHORIZATION/POLICY GATES =
ALLOWED SURFACE / TENANT/RESOURCE SCOPE =
PARENT WORKFLOW / JOB TYPE =
EXPECTED RESOURCE REVISION / CONCURRENCY =
MANDATORY AUDIT EVENTS =
NEGATIVE TEST IDS & OUTCOMES =
SECURITY APPROVAL =
API/IPC APPROVAL =
QA DEPENDENCIES =
VERSION IMPACT =
STATUS = DRAFT / REVIEW / APPROVED
```

Unresolved semantics must be returned to MASTER as a `WCP360 HANDOFF RETURN`, not filled in by SOURCE CODE.

## 5. Gate

```text
REQUIRED OWNER-FINAL ARTIFACTS = 8
PUBLISHED AND CONSUMABLE = 5
MISSING = DB #6, QA #7, Engineering #9

SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

This derived handoff cannot authorize shell commands, root execution, a new IPC operation, a database migration, or a code baseline.
