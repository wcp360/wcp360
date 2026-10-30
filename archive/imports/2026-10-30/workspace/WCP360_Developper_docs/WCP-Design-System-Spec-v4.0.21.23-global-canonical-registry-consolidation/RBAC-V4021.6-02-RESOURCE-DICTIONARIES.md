# V4.0.21.6 --- RBAC, Scope & Delegation Resource Dictionaries

## Statut

**NORMATIVE B1 CLOSURE --- AUTHORIZATION FAMILY**

### `role`

`id`, `owner_scope_type`, `owner_scope_id`, `code`, `display_name`,
`status`, `is_system`, `created_at`, `version`.

### `permission`

Canonical immutable registry entry: `id`, `resource_family`, `action`,
`scope_types`, `delegable_to_reseller`, `step_up_class`, `description`,
`registry_version`.

### `role_permission`

`role_id`, `permission_id`, `constraint_set`, `created_at`. No implicit
wildcard may grant a new future permission unless the wildcard itself is
an explicitly governed permission contract.

### `role_binding`

`id`, `principal_id`, `role_id`, `scope_type`, `scope_id`, `valid_from`,
`expires_at`, `status`, `created_by`, `version`.

### `delegation`

`id`, `delegator_principal_id`, `delegate_principal_id`, `scope_type`,
`scope_id`, `permission_subset`, `valid_from`, `expires_at`, `status`,
`reason`, `version`. Delegation cannot exceed the delegator's effective
authority or reseller ceiling.

### `impersonation_session`

`id`, `initiator_principal_id`, `target_principal_id`, `tenant_id`,
`scope_id`, `reason`, `ticket_reference?`, `started_at`, `expires_at`,
`ended_at`, `status`, `restriction_profile_id`, `correlation_id`.

### `authorization_decision`

Append-only evidence: `id`, `principal_id`, `effective_actor_id`,
`operation_id`, `resource_type`, `resource_id?`, `tenant_id`,
`decision`, `reason_code`, `policy_version`, `entitlement_version?`,
`step_up_state`, `created_at`, `correlation_id`.

## Invariants

Business code checks permissions, never hard-coded role names. Scope is
mandatory. Impersonation preserves initiator identity. Denial is
auditable without cross-tenant enumeration.
