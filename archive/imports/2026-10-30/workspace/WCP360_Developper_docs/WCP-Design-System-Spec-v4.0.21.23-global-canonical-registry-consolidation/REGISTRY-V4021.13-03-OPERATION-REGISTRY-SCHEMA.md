# V4.0.21.13 --- Canonical Operation Registry Schema

Chaque ligne Operation Registry : `operation_id`, `module_id`,
`resource_type`, `verb`, `mutation`, `permission_id`, `scope_rule`,
`entitlement_key`, `quota_guard`, `capability_guard`, `lifecycle_guard`,
`step_up`, `idempotency`, `job_type|NO_JOB`,
`decision_type|NO_DECISION`, `action_ids`, `audit_event`, `api_ref`,
`cli_ref`, `ui_ref`, `error_set`, `acceptance_refs`, `source_documents`,
`closure_state`.

## Règle

Une simple mention textuelle d'une Operation ID ne suffit pas à la
déclarer fermée. Le registre doit prouver chaque lien.
