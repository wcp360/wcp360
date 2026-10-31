# WCP360 — CANONICAL IMPLEMENTATION EXPORT — IPC CONTRACTS
**Owner:** `WCP360 — API / IPC / CONTRACTS`  
**Artifact version:** `WCP360-IPC-B/1`  
**Status:** `FINAL APPROVED CONTENT`  
**Publication type:** `CONTENT-NEUTRAL CANONICAL IMPLEMENTATION EXPORT`
```text
DOC-BASELINE    = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4
CODE-BASELINE   = NONE
IPC CONTRACT VERSION = WCP360-IPC-B/1
PRIVILEGED IPC OPERATIONS = 47
CONTENT CHANGE = NONE
HISTORICAL CONTENT INCLUDED = NO
```
## 1. Fixed architecture

```text
backend → wcpd(non-root) → bounded authenticated IPC-B → wcp-privd(root minimal) → explicit bounded primitive
```

Direct backend/WCP-USER/WCP-ADMIN/worker/hosting access to `wcp-privd` is forbidden.
## 2. Physical contract

```text
SOCKET = /run/wcp360/priv/wcp-privd.sock
SERVER = wcp-privd
AUTHORIZED CLIENT = wcpd only
OWNER = root
GROUP = wcp360-priv
MODE = 0660

DIRECTORY = /run/wcp360/priv
DIRECTORY OWNER = root
DIRECTORY GROUP = wcp360-priv
DIRECTORY MODE = 0750

PEER AUTH = SO_PEERCRED exact wcpd UID + expected wcpd service/cgroup verification
APPLICATION SHARED SECRET = NONE

FRAMING = uint32 big-endian payload length + strict UTF-8 JSON
PROTOCOL = WCP360-IPC-B/1
REQUEST MAX = 65536 bytes
RESPONSE MAX = 262144 bytes
CONNECT TIMEOUT = 1 second
I/O TIMEOUT = 5 seconds
DEFAULT OPERATION TIMEOUT = 30 seconds
MAX OPERATION TIMEOUT = 900 seconds
```
## 3. Request / response

Request:
```text
{protocol_version,op_version,request_id,correlation_id,job_id?,action_id,ipc_operation_id,resource_type,resource_id,authorization_context_ref,deadline_ms,payload}
```

Response:
```text
{protocol_version,request_id,correlation_id,ipc_operation_id,outcome,error_code?,result?,reconciliation_required}
```

Cookies, raw bearer tokens, passwords, caller-declared roles/permissions, arbitrary commands, arbitrary executables, arbitrary signals, arbitrary privileged paths, generic env/working-directory fields are forbidden.
## 4. Validation / errors

Malformed framing, invalid UTF-8/JSON, duplicate keys, missing required fields, invalid types, oversize frames or incompatible protocol version:
```text
NO privileged dispatch
NO mutation
connection closed
security evidence recorded
```

Unknown `ipc_operation_id`:
```text
IPC_OPERATION_NOT_ALLOWED
NO system call/subprocess/service/filesystem mutation
NO dynamic fallback
connection closed
```

Error registry:
```text
IPC_CALLER_UNTRUSTED
IPC_SCHEMA_INVALID
IPC_OPERATION_NOT_ALLOWED
AUTHORIZATION_DENIED
VALIDATION_FAILED
RESOURCE_NOT_FOUND
RESOURCE_CONFLICT
CONCURRENCY_CONFLICT
OPERATION_TIMEOUT
DEPENDENCY_FAILURE
UNKNOWN_OUTCOME
INTERNAL_ERROR
```
## 5. Audit / retry / reconciliation

Every mutation requires durable pre-dispatch evidence and correlated completion/failure evidence. Silent audit loss is forbidden.

```text
timeout != definite failure
retry != reconciliation
rollback != compensation
execution success != verified success
```

If execution may have occurred but cannot be proven:
```text
UNKNOWN_OUTCOME → reconciliation → no blind retry
```

`PARTIAL_EFFECT` is a result classification, not a canonical Job state.
## 6. Complete 47-operation registry

| contract_id | action_id | job_type | ipc_operation_id | resource identifier | validated input schema | bounded primitive | timeout | idempotency | concurrency scope | UNKNOWN_OUTCOME/reconciliation | tests |
|---|---|---|---|---|---|---|---|---|---|---|---|
| `IPC-WCP-1001` | `linux_user.create` | `linux_user.create` | `linux_user.create` | `hosting_account` | `{hosting_account_id,unix_identity_id,uid_gid_policy_id,home_root_id,shell_policy_id}` | `identity-adapter.create_registered_user` | `60s` | `key-required` | `hosting_account` | `inspect/reconcile identity registry↔OS` | `TEST-IPC-1001` |
| `IPC-WCP-1002` | `linux_user.lock` | `linux_user.lock` | `linux_user.lock` | `unix_identity` | `{unix_identity_id,expected_revision}` | `identity-adapter.lock_registered_user` | `15s` | `replay-safe` | `unix_identity` | `observe lock state` | `TEST-IPC-1002` |
| `IPC-WCP-1003` | `linux_user.unlock` | `linux_user.unlock` | `linux_user.unlock` | `unix_identity` | `{unix_identity_id,expected_revision}` | `identity-adapter.unlock_registered_user` | `15s` | `replay-safe` | `unix_identity` | `observe lock state` | `TEST-IPC-1003` |
| `IPC-WCP-1004` | `linux_user.delete` | `linux_user.delete` | `linux_user.delete` | `unix_identity` | `{unix_identity_id,deletion_mode,expected_revision}` | `identity-adapter.delete_registered_user` | `120s` | `key-protected/no blind retry` | `unix_identity` | `inspect user/home state` | `TEST-IPC-1004` |
| `IPC-WCP-1005` | `filesystem.directory.create` | `filesystem.directory.create` | `filesystem.directory.create` | `managed_path_ref` | `{managed_path_ref,permission_profile_id}` | `fs-adapter.mkdir_managed_ref` | `10s` | `replay-safe` | `managed_path_ref` | `observe existence/profile` | `TEST-IPC-1005` |
| `IPC-WCP-1006` | `filesystem.ownership.apply` | `filesystem.ownership.apply` | `filesystem.ownership.apply` | `managed_path_ref` | `{managed_path_ref,ownership_profile_id,expected_revision}` | `fs-adapter.apply_ownership_profile` | `30s` | `revision-protected` | `managed_path_ref` | `observe owner/group` | `TEST-IPC-1006` |
| `IPC-WCP-1007` | `filesystem.permissions.apply` | `filesystem.permissions.apply` | `filesystem.permissions.apply` | `managed_path_ref` | `{managed_path_ref,permission_profile_id,expected_revision}` | `fs-adapter.apply_permission_profile` | `30s` | `revision-protected` | `managed_path_ref` | `observe permission profile` | `TEST-IPC-1007` |
| `IPC-WCP-1008` | `filesystem.quota.apply` | `filesystem.quota.apply` | `filesystem.quota.apply` | `hosting_account` | `{hosting_account_id,quota_profile_id,expected_revision}` | `quota-adapter.apply_registered_profile` | `30s` | `desired-value protected` | `hosting_account` | `observe quota` | `TEST-IPC-1008` |
| `IPC-WCP-1009` | `nginx_vhost.validate` | `N/A` | `nginx_vhost.validate` | `application_revision` | `{application_id,vhost_revision_id}` | `nginx-adapter.validate_generated_revision` | `15s` | `replay-safe` | `application/revision` | `validation result` | `TEST-IPC-1009` |
| `IPC-WCP-1010` | `nginx_vhost.apply` | `nginx_vhost.apply` | `nginx_vhost.apply` | `application_revision` | `{application_id,vhost_revision_id,expected_active_revision}` | `nginx-adapter.atomic_apply_revision` | `30s` | `revision-protected` | `application-vhost` | `compare active revision/rollback previous revision` | `TEST-IPC-1010` |
| `IPC-WCP-1011` | `nginx_vhost.remove` | `nginx_vhost.remove` | `nginx_vhost.remove` | `application_revision` | `{application_id,vhost_revision_id,expected_active_revision}` | `nginx-adapter.remove_managed_vhost` | `30s` | `key-protected` | `application-vhost` | `inspect managed target` | `TEST-IPC-1011` |
| `IPC-WCP-1012` | `nginx.reload` | `nginx.reload` | `nginx.reload` | `service` | `{service_id,vhost_revision_id}` | `service-adapter.reload_registered_service` | `30s` | `replay-safe same revision` | `service` | `verify service health/revision` | `TEST-IPC-1012` |
| `IPC-WCP-1013` | `php_pool.validate` | `N/A` | `php_pool.validate` | `application_revision` | `{application_id,pool_revision_id,runtime_profile_id}` | `php-adapter.validate_pool_revision` | `15s` | `replay-safe` | `application/revision` | `validation result` | `TEST-IPC-1013` |
| `IPC-WCP-1014` | `php_pool.apply` | `php_pool.apply` | `php_pool.apply` | `application_revision` | `{application_id,pool_revision_id,runtime_profile_id,expected_active_revision}` | `php-adapter.atomic_apply_pool_revision` | `30s` | `revision-protected` | `application-pool` | `compare active revision` | `TEST-IPC-1014` |
| `IPC-WCP-1015` | `php_pool.remove` | `php_pool.remove` | `php_pool.remove` | `application_revision` | `{application_id,pool_revision_id,expected_active_revision}` | `php-adapter.remove_managed_pool` | `30s` | `key-protected` | `application-pool` | `observe pool state` | `TEST-IPC-1015` |
| `IPC-WCP-1016` | `php_fpm.reload` | `php_fpm.reload` | `php_fpm.reload` | `service` | `{service_id,runtime_profile_id,config_revision_id}` | `service-adapter.reload_registered_service` | `30s` | `replay-safe same revision` | `service` | `verify health/config revision` | `TEST-IPC-1016` |
| `IPC-WCP-1017` | `certificate.deploy` | `certificate.deploy` | `certificate.deploy` | `certificate_deployment` | `{certificate_id,deployment_target_id,certificate_revision}` | `certificate-adapter.atomic_deploy_secret_ref` | `60s` | `revision-protected` | `deployment_target` | `verify deployed fingerprint` | `TEST-IPC-1017` |
| `IPC-WCP-1018` | `certificate.remove` | `certificate.remove` | `certificate.remove` | `certificate_deployment` | `{certificate_id,deployment_target_id,expected_revision}` | `certificate-adapter.remove_managed_deployment` | `30s` | `key-protected` | `deployment_target` | `inspect target state` | `TEST-IPC-1018` |
| `IPC-WCP-1019` | `database.create` | `database.create` | `database.create` | `database` | `{database_id,engine_profile_id}` | `database-adapter.create_registered_database` | `120s` | `key-required` | `database_id` | `inspect hosted-engine inventory` | `TEST-IPC-1019` |
| `IPC-WCP-1020` | `database.drop` | `database.drop` | `database.drop` | `database` | `{database_id,expected_revision,deletion_authorization_ref}` | `database-adapter.drop_registered_database` | `120s` | `not blindly retryable` | `database_id` | `inspect hosted-engine inventory` | `TEST-IPC-1020` |
| `IPC-WCP-1021` | `database_user.create` | `database_user.create` | `database_user.create` | `db_identity` | `{database_id,db_identity_id}` | `database-adapter.create_registered_identity` | `60s` | `key-required` | `database/db_identity` | `reconcile identity state` | `TEST-IPC-1021` |
| `IPC-WCP-1022` | `database_user.drop` | `database_user.drop` | `database_user.drop` | `db_identity` | `{database_id,db_identity_id,expected_revision}` | `database-adapter.drop_registered_identity` | `60s` | `not blindly retryable` | `db_identity` | `observe identity state` | `TEST-IPC-1022` |
| `IPC-WCP-1023` | `database_grant.apply` | `database_grant.apply` | `database_grant.apply` | `grant_set` | `{database_id,db_identity_id,grant_set_id,expected_revision}` | `database-adapter.apply_registered_grants` | `60s` | `revision-protected` | `database/db_identity` | `compare observed grants` | `TEST-IPC-1023` |
| `IPC-WCP-1024` | `database_grant.revoke` | `database_grant.revoke` | `database_grant.revoke` | `grant_set` | `{database_id,db_identity_id,grant_set_id,expected_revision}` | `database-adapter.revoke_registered_grants` | `60s` | `revision-protected` | `database/db_identity` | `compare observed grants` | `TEST-IPC-1024` |
| `IPC-WCP-1025` | `firewall.policy.validate` | `N/A` | `firewall.policy.validate` | `firewall_revision` | `{server_id,policy_revision_id}` | `firewall-adapter.validate_structured_policy` | `15s` | `replay-safe` | `policy_revision` | `validation result` | `TEST-IPC-1025` |
| `IPC-WCP-1026` | `network.firewall.apply` | `firewall.policy.apply` | `firewall.policy.apply` | `firewall_revision` | `{server_id,policy_revision_id,expected_active_revision}` | `firewall-adapter.atomic_apply_revision` | `30s` | `revision-protected` | `server-firewall` | `observe active policy` | `TEST-IPC-1026` |
| `IPC-WCP-1027` | `firewall.policy.rollback` | `firewall.policy.rollback` | `firewall.policy.rollback` | `firewall_revision` | `{server_id,target_known_good_revision,current_revision}` | `firewall-adapter.apply_known_good_revision` | `30s` | `revision-protected` | `server-firewall` | `verify target revision` | `TEST-IPC-1027` |
| `IPC-WCP-1028` | `servers.service.status` | `N/A` | `service.status` | `service` | `{server_id,service_id}` | `service-adapter.status_registered_service` | `10s` | `replay-safe` | `read` | `read-only verification` | `TEST-IPC-1028` |
| `IPC-WCP-1029` | `servers.service.reload` | `service.reload` | `service.reload` | `service` | `{server_id,service_id,config_revision_id?}` | `service-adapter.reload_registered_service` | `60s` | `revision/replay protected` | `service` | `health/config observation` | `TEST-IPC-1029` |
| `IPC-WCP-1030` | `servers.service.restart` | `service.restart` | `service.restart` | `service` | `{server_id,service_id}` | `service-adapter.restart_registered_service` | `120s` | `operation-ID protected` | `service` | `health/process-generation observation` | `TEST-IPC-1030` |
| `IPC-WCP-1031` | `updates.metadata.refresh` | `package.refresh_metadata` | `package.refresh_metadata` | `package_repository_set` | `{server_id,repository_set_id}` | `package-adapter.refresh_registered_repositories` | `120s` | `replay-safe` | `server-package-manager` | `refresh-state observation` | `TEST-IPC-1031` |
| `IPC-WCP-1032` | `updates.plan` | `package.plan` | `package.plan` | `package_plan` | `{server_id,repository_snapshot_id,update_selection_id}` | `package-adapter.build_registered_plan` | `120s` | `replay-safe same snapshot` | `server-package-manager` | `plan reconciliation` | `TEST-IPC-1032` |
| `IPC-WCP-1033` | `updates.apply` | `package.apply_plan` | `package.apply_plan` | `package_plan` | `{server_id,package_plan_id}` | `package-adapter.apply_registered_plan` | `900s` | `plan-ID protected` | `server-package-manager` | `inventory/plan reconciliation` | `TEST-IPC-1033` |
| `IPC-WCP-1034` | `updates.hold` | `package.hold` | `package.hold` | `package` | `{server_id,package_id,reason_code}` | `package-adapter.hold_registered_package` | `60s` | `desired-state protected` | `package_id` | `observe hold state` | `TEST-IPC-1034` |
| `IPC-WCP-1035` | `updates.unhold` | `package.unhold` | `package.unhold` | `package` | `{server_id,package_id}` | `package-adapter.unhold_registered_package` | `60s` | `desired-state protected` | `package_id` | `observe hold state` | `TEST-IPC-1035` |
| `IPC-WCP-1036` | `servers.reboot` | `server.reboot` | `server.reboot` | `server` | `{server_id,operation_nonce}` | `power-adapter.REBOOT_registered_server` | `90s` | `NOT_SAFELY_RETRYABLE` | `server-power` | `reconcile boot identity/health` | `TEST-IPC-1036` |
| `IPC-WCP-1037` | `servers.shutdown` | `server.shutdown` | `server.shutdown` | `server` | `{server_id,operation_nonce}` | `power-adapter.POWEROFF_registered_server` | `60s` | `NOT_SAFELY_RETRYABLE` | `server-power` | `out-of-band/health observation` | `TEST-IPC-1037` |
| `IPC-WCP-1038` | `processes.terminate` | `process.term` | `process.term` | `process_instance` | `{server_id,process_instance_id}` | `process-adapter.fixed_SIGTERM_registered_instance` | `10s` | `target-instance protected` | `process_instance` | `observe same process instance` | `TEST-IPC-1038` |
| `IPC-WCP-1039` | `processes.kill` | `process.kill` | `process.kill` | `process_instance` | `{server_id,process_instance_id}` | `process-adapter.fixed_SIGKILL_registered_instance` | `10s` | `NOT_RETRY_LOOPED` | `process_instance` | `observe same process instance` | `TEST-IPC-1039` |
| `IPC-WCP-1040` | `storage.volume.expand` | `volume.expand` | `volume.expand` | `volume` | `{volume_id,target_size_bytes,expected_revision}` | `storage-adapter.expand_registered_volume` | `900s` | `target-size protected` | `volume` | `observe provider size; never shrink` | `TEST-IPC-1040` |
| `IPC-WCP-1041` | `storage.filesystem.expand` | `filesystem.expand` | `filesystem.expand` | `filesystem` | `{filesystem_id,target_size_bytes,expected_revision}` | `storage-adapter.expand_registered_filesystem` | `300s` | `target-size protected` | `filesystem` | `observe filesystem size` | `TEST-IPC-1041` |
| `IPC-WCP-1042` | `storage.mount.verify` | `N/A` | `mount.verify` | `mount` | `{mount_id}` | `storage-adapter.verify_registered_mount` | `30s` | `replay-safe` | `read` | `verification only` | `TEST-IPC-1042` |
| `IPC-WCP-1043` | `cluster.node.drain` | `node.drain` | `node.drain` | `cluster_node` | `{cluster_id,node_id,drain_policy_id,operation_id}` | `cluster-adapter.drain_registered_node` | `900s` | `operation-ID protected` | `node` | `observe workloads/node state` | `TEST-IPC-1043` |
| `IPC-WCP-1044` | `cluster.node.uncordon` | `node.uncordon` | `node.uncordon` | `cluster_node` | `{cluster_id,node_id,expected_state}` | `cluster-adapter.uncordon_registered_node` | `60s` | `desired-state protected` | `node` | `observe state` | `TEST-IPC-1044` |
| `IPC-WCP-1045` | `cluster.failover.prepare` | `cluster.failover` | `cluster.failover.prepare` | `cluster` | `{cluster_id,failover_operation_id,target_node_id}` | `cluster-adapter.prepare_registered_failover` | `300s` | `operation-ID protected` | `cluster` | `observe readiness` | `TEST-IPC-1045` |
| `IPC-WCP-1046` | `clusters.failover` | `cluster.failover` | `cluster.failover.execute` | `cluster` | `{cluster_id,failover_operation_id,target_node_id}` | `cluster-adapter.execute_prepared_failover` | `900s` | `NOT_BLINDLY_RETRYABLE` | `cluster` | `topology reconciliation` | `TEST-IPC-1046` |
| `IPC-WCP-1047` | `cluster.failover.verify` | `cluster.failover` | `cluster.failover.verify` | `cluster` | `{cluster_id,failover_operation_id,expected_primary_node_id}` | `cluster-adapter.verify_failover_state` | `300s` | `replay-safe` | `read/cluster` | `verify final topology` | `TEST-IPC-1047` |

## 7. Security invariants

No operation accepts caller-controlled generic:
```text
shell
command
executable
argv
sudo_args
systemd_unit
signal_number
absolute_path
environment
working_directory
```

Filesystem targets are typed managed references. `process.term` selects fixed `SIGTERM` internally. `process.kill` selects fixed `SIGKILL` internally. Hosted database operations accept no arbitrary SQL. Service operations accept only registered `service_id`. Package operations accept only WCP-generated plans/registered package identifiers.
## 8. Compatibility

```text
protocol = WCP360-IPC-B/1
op_version = 1
```

Unknown operation version is rejected. Required input change or semantic primitive change requires a new `op_version`. Security weakening is never compatible. No alias silently redirects one privileged operation to another.
## 9. Tests

Every `TEST-IPC-*` covers: peer authentication, non-wcpd denial, protocol/op version, schema validation, oversize rejection, resource/scope validation, bounded primitive selection, generic-exec impossibility, timeout, idempotency, concurrency, UNKNOWN_OUTCOME reconciliation, audit durability, secret redaction and operation-specific postcondition verification.

Implementation-dependent tests remain `PLANNED` or `READY`; no PASS result is published.
## 10. Publication declaration

```text
ACTIVE PRIVILEGED IPC OPERATIONS = 47
ALIASES = 0
DUPLICATES = 0
GENERIC EXECUTORS = 0
UNCONTRACTED CURRENT OPERATIONS = 0

IPC ARTIFACT VERSION = WCP360-IPC-B/1
FINAL APPROVED CONTENT = YES
HISTORICAL CONTENT INCLUDED = NO
CONTENT CHANGE = NONE
READY FOR SOURCE CODE CONSUMPTION = YES
```