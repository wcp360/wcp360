# V4.0.21.9 --- Mail Domain / Mailbox / Routing Resource Dictionaries

## `mail_domain`

`id`, `account_id`, `domain_id`, `status`, `local_delivery`,
`catchall_policy`, `quota_policy_id?`, `created_at`, `version`.

## `mailbox`

`id`, `mail_domain_id`, `local_part`, `address_canonical`,
`credential_ref`, `quota_bytes`, `usage_bytes_observed`, `status`,
`created_at`, `version`.

## `mail_alias`

`id`, `mail_domain_id`, `source_address`, `destination_set`, `status`,
`version`.

## `mail_forwarder`

`id`, `mail_domain_id`, `source_address`, `destination_set`,
`keep_local_copy`, `status`, `version`.

## `autoresponder`

`id`, `mailbox_id`, `subject_template`, `body_template`, `active_from?`,
`active_until?`, `rate_policy`, `status`, `version`.

## `mail_route`

`id`, `mail_domain_id`, `route_type`, `destination_ref`, `priority`,
`tls_policy`, `status`, `version`.

## Rules

Address normalization, loop detection, forwarding policy and quota
enforcement are explicit. Passwords are never retrievable. Catch-all is
disabled by default unless policy enables it.
