# V4.0.21.7 --- Reseller Resource Dictionaries

## Statut

**NORMATIVE B1 --- RESELLER FAMILY**

### `reseller`

`id`, `tenant_id`, `code`, `display_name`, `status`,
`owner_principal_id`, `ceiling_id`, `branding_profile_id?`,
`created_at`, `suspended_at?`, `version`.

### `reseller_ceiling`

`id`, `reseller_id`, `version`, `feature_ceiling`, `resource_ceiling`,
`security_floor_ref`, `effective_from`, `created_by`, `created_at`.

### `reseller_usage_snapshot`

`id`, `reseller_id`, `captured_at`, `account_count`, `disk_bytes`,
`bandwidth_bytes`, `cpu_allocation`, `memory_allocation`,
`mailbox_count`, `database_count`, `site_count`, `snapshot_version`.

## Invariants

A reseller ceiling is always bounded by the platform ceiling. A ceiling
reduction below observed descendant allocation never deletes resources;
descendants become constrained/noncompliant according to reconciliation
policy.
