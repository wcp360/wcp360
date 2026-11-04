# Owner decision: deny unresolved privileged IPC bindings

**Recorded: 2026-11-03. Authority: explicit project-owner decision supplied during work on [issue #91](https://github.com/wcp360/wcp360/issues/91). Decision: deny all twelve unresolved operations below until each is separately approved. Scope: a restrictive owner policy; no runtime, canonical-export, or implementation-gate change.**

## Decision

None of the twelve operations listed below may be dispatched by a WCP360 privileged dispatcher or represented as available for a production path while its origin-action binding remains unresolved. This prohibition applies even when a caller presents one of the permission names, an administrator surface, a parent job, or `servers.services.manage`. There is no implicit parent, OR branch, wildcard, or technical-operation exception.

This is the fail-closed choice for the current gate. It does not remove any permission from the registry, approve a parent action, add an API action, or revise Security v1.0 or IPC-B/1. The frozen exports remain unchanged. The restriction applies to these canonical IPC operation IDs; a separate candidate protocol that does not implement those IDs is neither approved by nor proven equivalent to IPC-B/1.

## Explicitly prohibited operations

| IPC operation | IPC ID | Existing Security expression retained as reference only | Success audit event; never emit for a denied attempt |
|---|---|---|---|
| `filesystem.directory.create` | `IPC-WCP-1005` | `files.write + approved provisioning binding` | `system.filesystem.directory_created` |
| `filesystem.ownership.apply` | `IPC-WCP-1006` | `files.permissions.manage + approved provisioning binding` | `system.filesystem.ownership_applied` |
| `filesystem.permissions.apply` | `IPC-WCP-1007` | `files.permissions.manage + approved provisioning binding` | `system.filesystem.permissions_applied` |
| `filesystem.quota.apply` | `IPC-WCP-1008` | `iam.account_overrides.manage OR applications.runtime.manage` | `system.filesystem.quota_applied` |
| `nginx_vhost.validate` | `IPC-WCP-1009` | `approved app/domain/admin binding` | `system.nginx_vhost.validated` |
| `nginx_vhost.apply` | `IPC-WCP-1010` | `approved app/domain/admin binding` | `system.nginx_vhost.applied` |
| `nginx_vhost.remove` | `IPC-WCP-1011` | `approved app/domain/admin binding` | `system.nginx_vhost.removed` |
| `nginx.reload` | `IPC-WCP-1012` | `approved technical parent OR servers.services.manage` | `system.nginx.reloaded` |
| `php_pool.validate` | `IPC-WCP-1013` | `approved app/admin binding` | `system.php_pool.validated` |
| `php_pool.apply` | `IPC-WCP-1014` | `approved app/admin binding` | `system.php_pool.applied` |
| `php_pool.remove` | `IPC-WCP-1015` | `approved app/admin binding` | `system.php_pool.removed` |
| `php_fpm.reload` | `IPC-WCP-1016` | `approved technical parent OR servers.services.manage` | `system.php_fpm.reloaded` |

The expressions above quote the selected Security Baseline v1.0 only to identify the unresolved rows. They are not dispatch rules or grants.

## Required refusal behavior

When any of these known operation IDs is requested before a later per-operation approval:

- refuse with the existing IPC error `AUTHORIZATION_DENIED`; retain `IPC_OPERATION_NOT_ALLOWED` for an unknown operation ID;
- do not send an IPC request to `wcp-privd`, invoke a system call/subprocess, mutate a service or filesystem, or select a generic/different operation as fallback;
- do not claim a successful result or emit the operation's success audit event; preserve the refusal and correlation through the existing authorization/audit policy without creating a new event identifier here;
- if the request belongs to an already accepted parent workflow, preserve that workflow's truthful refusal/unknown-outcome and audit semantics; never mark it successful because the privileged step was denied.

In particular, the separate `service.reload` operation and `servers.services.manage` do not provide a bypass for the denied Nginx/FPM-specific operations. Any separately proposed route to the same effect requires its own exact resource/service authorization and owner decision.

## Conditions to reconsider one operation

The prohibition can be lifted only for an individually named operation after all of the following are recorded:

1. SECURITY approves its exact base permission, additional policy gates, caller surface, authentication strength, tenant/resource scope, revocation and execution-time recheck.
2. API / IPC / CONTRACTS approves the exact `origin_action_id` → typed parent job/step → `ipc_operation_id` tuple, target resolver, resource revision and compatibility behavior. No caller-supplied technical operation ID or implicit OR is accepted.
3. TESTS / QA assigns positive and negative test identifiers and expected no-side-effect outcomes, including wrong action/surface/tenant, missing grant, stale revision, revocation/race, uncertain outcome, and success-audit lifecycle.
4. The authorized implementation slice adds the deny-by-default dispatch guard and tests; independent review and any required MASTER/manifest authorization are completed before merge or activation.

Until then, the answer for every row in the table is **DENY**. This decision does not itself authorize the implementation or test execution.

## Gate and evidence

The current [baseline manifest](../../manifests/WCP360-BM-0001.md) remains authoritative: SC-M01 is paused pending a separately selected and authorized implementation slice; SC-M02 is not authorized; `CODE-BASELINE = NONE`. This record does not amend those gates or claim that a production dispatcher exists.

The historical [Security/IPC handoff](../handoffs/SC-M01-SECURITY-IPC-PARENT-BINDING-REVIEW-2026-10-26.md) and [issue #91](https://github.com/wcp360/wcp360/issues/91) remain open for implementation-bound refusal tests and independent review. The handoff's 5/8 count is historical; the current manifest records 8/8 accepted inputs. No test, code change, independent E3 review, GitHub issue update, or production qualification is claimed by this documentation decision.

Sources: [Security Baseline v1.0](../../specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md), [IPC-B/1](../../specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md), [Permission Registry v1.0](../../specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md), and the [derived owner handoff](../handoffs/SC-M01-SECURITY-IPC-PARENT-BINDING-REVIEW-2026-10-26.md).
