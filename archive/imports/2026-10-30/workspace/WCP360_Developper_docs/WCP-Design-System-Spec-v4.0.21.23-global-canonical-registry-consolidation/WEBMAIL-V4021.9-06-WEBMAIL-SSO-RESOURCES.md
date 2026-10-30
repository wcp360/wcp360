# V4.0.21.9 --- Webmail / SSO Resource Dictionaries

## `webmail_backend`

`id`, `backend_type`, `endpoint_ref`, `status`, `capabilities`,
`version`.

## `webmail_sso_ticket`

`id`, `principal_id`, `mailbox_id`, `backend_id`, `secret_ref`,
`created_at`, `expires_at`, `consumed_at?`, `source_session_id`,
`status`.

## Rules

SSO tickets are short-lived, single-use, mailbox-scoped and never logged
in plaintext. Roundcube/SnappyMail or another certified backend is an
adapter; the WCP authorization model remains backend-independent.
