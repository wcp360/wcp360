# V4.0.21.19 --- Mail Domain / Mailbox Operations

Operations: `MAIL-DOMAIN-CREATE`, `MAIL-DOMAIN-SUSPEND`,
`MAIL-DOMAIN-DELETE`, `MAILBOX-CREATE`, `MAILBOX-UPDATE`,
`MAILBOX-PASSWORD-ROTATE`, `MAILBOX-LOCK`, `MAILBOX-UNLOCK`,
`MAILBOX-DELETE`, `MAIL-ALIAS-CREATE`, `MAIL-ALIAS-DELETE`,
`MAIL-FORWARD-CREATE`, `MAIL-FORWARD-DELETE`, `AUTORESPONDER-SET`,
`AUTORESPONDER-DISABLE`.

All are scope/entitlement/quota/lifecycle gated. Mailbox credentials are
SecretStore-backed. Delete has retention/backup semantics; suspension
does not silently remove mailbox data. Forwarding is anti-abuse
constrained and loop validated.
