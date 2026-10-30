# V4.0.21.9 --- Database Resource Dictionaries

## Statut

**NORMATIVE B1 --- DATABASE FAMILY**

### `database_instance`

`id`, `backend_type`, `backend_version`, `service_ref`, `host_scope`,
`status`, `capabilities`, `observed_at`, `version`.

### `database`

`id`, `account_id`, `instance_id`, `logical_name`, `physical_name`,
`charset`, `collation`, `quota_policy_id?`, `status`, `created_at`,
`version`.

### `database_user`

`id`, `account_id`, `instance_id`, `logical_name`, `physical_name`,
`credential_ref`, `auth_plugin`, `allowed_origin_policy`, `status`,
`created_at`, `rotated_at?`, `version`.

### `database_grant`

`id`, `database_id`, `database_user_id`, `privilege_profile`,
`effective_privileges`, `status`, `version`.

### `database_backup`

`id`, `database_id`, `backup_artifact_id`, `consistency_mode`,
`engine_metadata`, `created_at`, `verification_state`.

## Invariants

Physical names are generated/canonicalized and cannot collide across
managed accounts. Credentials are SecretStore references. Grants are
typed profiles, not arbitrary SQL. WCP-USER never receives server-wide
administrative credentials.
