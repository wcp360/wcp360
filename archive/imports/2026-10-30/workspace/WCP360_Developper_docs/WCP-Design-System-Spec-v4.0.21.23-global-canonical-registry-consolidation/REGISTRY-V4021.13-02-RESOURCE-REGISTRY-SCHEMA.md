# V4.0.21.13 --- Canonical Resource Registry Schema

Chaque ligne Resource Registry doit contenir : `resource_type`,
`module_id`, `owner_scope`, `parent_types`, `identity_fields`,
`lifecycle_enum_ref`, `desired_state`, `observed_state`,
`version_field`, `secret_classification`, `pii_classification`,
`quota_keys`, `capability_keys`, `delete_semantics`,
`retention_semantics`, `backup_class`, `api_representation_ref`,
`source_documents`, `closure_state`.

## États

`READY`, `PARTIAL`, `MISSING_CONTRACT`, `DECISION_REQUIRED`,
`SUPERSEDED`.

## Gate

`READY` est interdit si lifecycle, ownership/scope, deletion ou secret
classification sont absents pour une ressource qui en a besoin.
