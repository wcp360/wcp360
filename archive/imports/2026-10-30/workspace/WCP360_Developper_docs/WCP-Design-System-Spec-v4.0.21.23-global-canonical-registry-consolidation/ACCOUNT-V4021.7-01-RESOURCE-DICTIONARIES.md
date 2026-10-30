# V4.0.21.7 --- Hosting Account & System Identity Resource Dictionaries

## Statut

**NORMATIVE B1 --- HOSTING ACCOUNT FAMILY**

### `hosting_account`

`id`, `tenant_id`, `reseller_id?`, `login`, `display_name`, `status`,
`account_mode`, `plan_assignment_id`, `isolation_assignment_id`,
`system_identity_id?`, `primary_contact_principal_id`, `created_at`,
`suspended_at?`, `deletion_requested_at?`, `version`.

`status`: `PROVISIONING`, `READY`, `SUSPENDED`, `NONCOMPLIANT`,
`DELETING`, `DELETED`, `ERROR`.

### `system_identity`

`id`, `account_id`, `unix_username`, `uid`, `primary_gid`,
`supplementary_group_refs`, `home_path_ref`, `shell_policy`, `state`,
`observed_at`, `version`.

The control plane never accepts arbitrary UID/GID/home paths from
WCP-USER. They are allocated by policy and verified against observed OS
state.

### `account_lifecycle_event`

Append-only: `id`, `account_id`, `event_type`, `previous_state`,
`new_state`, `reason_code`, `actor_id`, `effective_actor_id`, `job_id?`,
`created_at`, `correlation_id`.

### `account_contact_binding`

`id`, `account_id`, `principal_id`, `contact_role`, `status`,
`created_at`, `version`.

## Invariants

A hosting account belongs to one tenant and at most one reseller owner.
A system identity belongs to exactly one hosting account. Suspension is
non-destructive. Deletion is a workflow, not a direct row removal.
