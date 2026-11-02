# Application lifecycle and recovery

**Status: target requirements for candidate recipes. No lifecycle executor is implemented by this document.**

A recipe describes an application requirement; the Core accepts and executes an authorized resource operation. Use one Hosting application inventory and the normal Operations/provider boundary for PHP, native services and admitted containers.

## Shared operation contract

Bind the current actor, tenant, application/resource and expected revision to exact recipe, application, template/policy and provider versions. Record immutable artifacts and dependency references, a scoped idempotency key, current permission and applicable approval, resource budgets and recovery inputs. An operation reference does not authenticate its caller.

Configuration has bounded typed fields and secret references. It must not accept arbitrary filesystem paths, upstream URLs, command fragments, service names or free-form Compose/hooks. Application builds and upstream installers execute code: their pinned inputs, process identity, network, filesystem and resource limits need separate admission. No build may inherit panel credentials or a host Docker socket.

## Lifecycle decisions

| Operation | Required behavior |
| :--- | :--- |
| Inspect | Read only authorized observed state; distinguish installed, unavailable, unhealthy and unknown. An editorial row is not installation evidence. |
| Plan installation | Check target/provider/runtime, topology, storage, quotas, dependency ownership, artifacts/licensing and ingress reservations. Show the intended effects and recovery implications. |
| Install | Prepare admitted artifacts, owned files/data/secrets and dependencies, then initialize under the approved identity. Publish availability only after readiness and expected behavior are observed. |
| Configure | Validate typed settings and expected revision; show a redacted diff, stage exact configuration and use the owning operation. Verify health and effective settings after apply. |
| Update | Resolve an explicit supported source/destination pair, back up consistently, drain relevant traffic/jobs, apply admitted code/data changes and verify the observed result. |
| Backup and restore | Include application files/uploads, databases, configuration, secrets/recovery references and ownership. Restore on a clean target and compare actual application state. |
| Uninstall | Check dependent resources, drain/fence outstanding work, revoke routes/credentials/schedules and remove only admitted owned components. Retain data by default. |
| Purge | Separate explicit authorization and retention/recovery decision over declared owned data. Never infer purge from uninstall, expiry or a missing application package. |
| Reconcile | Observe a partial/uncertain operation before any replay; resume, recover or require an operator decision according to the resource's supported contract. |

Start/stop/suspend/resume and runtime changes must preserve explicit ownership and data policy. Stopping a UI contribution is different from stopping its hosted workload.

## Update and data boundaries

A recipe's version is distinct from the application version and database schema version. Image replacement or reverting a configuration symlink does not undo an irreversible database migration. An update must state whether configuration reversal, application rollback or restore is supported; otherwise retain an explicit recovery requirement.

Reuse existing hosted Data/Files/BackupRestore operations rather than writing Core tables or administering the panel's database from a recipe. An application's dependencies must not silently reuse the panel PostgreSQL cluster, queue/cache credentials or another tenant's service.

Prevent duplicate or stale effects with accepted intent, leases/fencing and execution-time resource/authority checks. A timeout, cancelled request, worker crash or lost acknowledgement may leave an effect applied; do not treat it as permission to repeat installation or deletion. Mandatory audit failure and uncertain commits must retain their actual semantics.

## Removal and continuity

Uninstalling a recipe or management plugin must not erase its application instance inventory, drop unrelated databases/volumes, stop unrelated sites or discard the only recovery path. Refuse removal of an essential adapter until dependent resources have an admitted replacement or ownership transfer.

Trial/subscription expiry and vendor outage follow an explicit commercial policy. They do not authorize customer-data deletion, essential hosting suspension or blocking local administration/security/baseline recovery. Permission, entitlement and artifact trust remain separate checks.

## Refusal and recovery acceptance

Exercise another tenant's resource, revoked actor, stale revision, altered artifact, missing dependency, unsupported topology, path/link escape, escaped mount and unapproved network target. Inject concurrent configure/update/delete, failed readiness, interrupted data migration, exhausted capacity, process crash, host reboot and lost acknowledgement.

Inspect the actual resource after each failure. Claim rollback only when its data contract permits it, and retain machine-readable unresolved outcomes with safe operator guidance. Future execution evidence belongs to the exact recipe/provider/application/OS tuple; no such result is asserted here.
