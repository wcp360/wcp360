# V4.0.21.5 — Isolation B1 Resource Dictionaries

## Statut
**NORMATIVE — V1 STANDALONE — NO CODE — REPOSITORY BLOCKED**

Cette famille transforme les options d’isolation et de commandes de V4.0.21.4.1 en contrats B1/B2 explicites.

## `isolation_profile`
| Champ | Type | Req. | Null | Mutabilité | Référence / règle | Classe | Writer |
|---|---|---:|---:|---|---|---|---|
| `id` | UUID | oui | non | immutable | opaque | INTERNAL | system |
| `owner_scope_id` | UUID | oui | non | immutable | platform/reseller scope | INTERNAL | control-plane |
| `code` | STRING | oui | non | immutable | unique dans owner scope | INTERNAL | admin |
| `display_name` | STRING | oui | non | mutable | 1..120 | PUBLIC | admin |
| `version` | UINT64 | oui | non | mutable | optimistic concurrency | INTERNAL | system |
| `status` | ENUM | oui | non | mutable | DRAFT/ACTIVE/ARCHIVED | INTERNAL | admin |
| `cpu_policy` | POLICY_REF | oui | non | mutable | resource-limit set | INTERNAL | admin |
| `memory_policy` | POLICY_REF | oui | non | mutable | resource-limit set | INTERNAL | admin |
| `pid_policy` | POLICY_REF | oui | non | mutable | resource-limit set | INTERNAL | admin |
| `io_policy` | POLICY_REF | oui | non | mutable | resource-limit set | INTERNAL | admin |
| `filesystem_policy` | POLICY_REF | oui | non | mutable | managed roots/quota | SENSITIVE | admin |
| `runtime_policy` | POLICY_REF | oui | non | mutable | PHP/app isolation | INTERNAL | admin |
| `shell_policy` | ENUM | oui | non | mutable | DISABLED/RESTRICTED/STANDARD_USER | INTERNAL | admin |
| `mac_policy` | POLICY_REF | non | oui | mutable | optional adapter | INTERNAL | admin |
| `network_policy` | POLICY_REF | non | oui | mutable | optional egress adapter | INTERNAL | admin |
| `created_at` | TIMESTAMP | oui | non | immutable | — | INTERNAL | system |

## `isolation_assignment`
`id`, `account_id`, `profile_id`, `profile_version`, `state`, `effective_from`, `assigned_by`, `reason`, `version`. Un compte STANDARD/TRIAL ne possède qu’une affectation active. DEMO n’en provisionne aucune réelle.

## `isolation_capability`
Décrit la capacité **observée** d’un nœud : `node_id`, `capability_key`, `backend`, `availability`, `enforcement_modes[]`, `detected_value`, `detected_at`, `evidence_hash`, `error_code?`. `availability ∈ AVAILABLE|DEGRADED|UNAVAILABLE`.

## `resource_limit`
`id`, `owner_type`, `owner_id`, `limit_key`, `requested_value`, `unit`, `enforcement_mode`, `effective_value`, `source_chain[]`, `backend`, `version`.

Clés V1 minimales : `cpu.weight`, `cpu.max`, `memory.high`, `memory.max`, `memory.swap.max` si disponible, `pids.max`, `io.weight`, `io.read_bps`, `io.write_bps`, `storage.bytes`, `storage.inodes`, `bandwidth.bytes_period`, `php.workers`, `app.processes`, `cron.concurrent`, `worker.concurrent`, `db.connections`, `redis.memory`, `mailbox.bytes`, `backup.concurrent`, `api.rate`.

## `resource_usage_snapshot`
`id`, `account_id`, `resource_id?`, `metric_key`, `value`, `unit`, `window_start`, `window_end`, `observed_at`, `freshness_state`, `collector_id`.

## `resource_limit_event`
`id`, `account_id`, `resource_id?`, `limit_key`, `event_type`, `observed_value`, `effective_limit`, `backend`, `occurred_at`, `job_id?`, `ack_state`.

`event_type`: THRESHOLD, THROTTLED, OOM, PID_DENIED, IO_THROTTLED, QUOTA_SOFT, QUOTA_HARD, BACKEND_DEGRADED, DRIFT.

## `command_invocation`
Métadonnée control-plane seulement : `id`, `operation_id`, `actor_id`, `effective_actor_id`, `scope_id`, `target_refs[]`, `safe_options`, `request_id`, `idempotency_key?`, `dry_run`, `execution_class`, `job_id?`, `result_code`, `started_at`, `finished_at?`.

**Interdit :** champ shell libre, argv root arbitraire, secret en clair, chemin root fourni par client.
