# V4.0.21.9 --- Anti-spam / Antivirus / Sieve Resource Dictionaries

## `spam_policy`

`id`, `owner_scope`, `engine_backend`, `score_thresholds`,
`action_profile`, `allowlist_refs`, `blocklist_refs`, `learning_policy`,
`version`.

## `mail_filter`

`id`, `owner_scope`, `filter_type`, `typed_conditions`, `typed_actions`,
`priority`, `status`, `version`.

## `antivirus_policy`

`id`, `owner_scope`, `engine_backend`, `scan_direction`,
`action_on_detect`, `quarantine_policy_ref?`, `failure_mode`, `version`.

## `mail_security_event`

Append-only: `id`, `account_id`, `mail_domain_id?`, `mailbox_id?`,
`event_type`, `engine`, `verdict`, `reason_code`,
`message_metadata_safe`, `created_at`, `correlation_id`.

## `sieve_script`

`id`, `mailbox_id`, `script_name`, `validated_source_ref`, `active`,
`validation_state`, `version`.

## Rules

Antispam/AV failure behavior is explicit (`fail-open` or `fail-closed`)
per certified profile; it is never accidental. Sieve is validated before
activation. Message bodies/credentials are excluded from normal audit
logs.
