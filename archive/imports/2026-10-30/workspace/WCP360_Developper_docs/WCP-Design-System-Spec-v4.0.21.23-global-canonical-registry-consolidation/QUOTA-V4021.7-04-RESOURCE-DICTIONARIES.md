# V4.0.21.7 --- Quota, Usage & Isolation Assignment Dictionaries

## Statut

**NORMATIVE B1 --- QUOTA FAMILY**

### `resource_quota`

`id`, `owner_type`, `owner_id`, `resource_key`, `limit_type`,
`requested_value`, `effective_value`, `enforcement_mode`, `backend_id?`,
`grace_policy_ref?`, `status`, `version`.

### `usage_snapshot`

`id`, `owner_type`, `owner_id`, `resource_key`, `observed_value`,
`source`, `captured_at`, `freshness_state`.

### `quota_event`

`id`, `owner_type`, `owner_id`, `resource_key`, `event_type`,
`threshold`, `observed_value`, `created_at`, `correlation_id`.

### `isolation_assignment`

`id`, `account_id`, `profile_id`, `profile_version`, `effective_from`,
`state`, `last_verified_at?`, `version`.

## Supported limit classes

Disk bytes/inodes, bandwidth accounting, CPU, memory, swap, PIDs, I/O,
mailbox quota, DB count/connections, cache memory, runtime
process/workers, cron concurrency, backup concurrency and API rate
limits.

Commercial allowance and enforceable kernel/service limit are distinct
fields.
