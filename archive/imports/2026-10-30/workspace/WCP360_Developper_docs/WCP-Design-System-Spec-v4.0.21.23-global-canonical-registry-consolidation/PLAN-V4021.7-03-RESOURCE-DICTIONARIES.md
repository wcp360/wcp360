# V4.0.21.7 --- Plans, Entitlements & Effective Policy Dictionaries

## Statut

**NORMATIVE B1 --- PLAN / ENTITLEMENT FAMILY**

### `hosting_plan`

`id`, `owner_scope_type`, `owner_scope_id`, `code`, `display_name`,
`status`, `version`, `feature_set_version`, `created_at`,
`archived_at?`.

### `plan_assignment`

`id`, `account_id`, `plan_id`, `assigned_plan_version`, `state`,
`effective_from`, `assigned_by`, `version`.

### `entitlement`

Immutable registry entry: `key`, `module_id`, `value_type`,
`default_value`, `allowed_operations`, `security_class`,
`registry_version`.

### `plan_entitlement`

`plan_id`, `plan_version`, `entitlement_key`, `typed_value`,
`created_at`.

### `entitlement_override`

`id`, `account_id`, `resource_id?`, `entitlement_key`, `operation`,
`typed_value?`, `reason`, `expires_at?`, `created_by`, `version`.

### `effective_entitlement_snapshot`

`id`, `account_id`, `resource_id?`, `resolver_version`, `inputs_hash`,
`effective_values`, `created_at`. This is evidence/cache, not the
primary authority.

## Resolver

`platform ceiling ∩ reseller ceiling ∩ plan ∩ account override ∩ resource override ∩ account mode ∩ backend capability ∩ security floor`.

A more permissive child value can never exceed a parent ceiling or
weaken a security floor.
