# V4.0.21.19 --- Mail Errors / Audit / P0 Acceptance

Stable errors include `MAIL_DOMAIN_CONFLICT`, `MAILBOX_CONFLICT`,
`MAILBOX_OVER_QUOTA`, `MAIL_RELAY_POLICY_DENIED`, `MAIL_FORWARD_LOOP`,
`DKIM_KEY_ERROR`, `MAIL_AUTH_DNS_MISMATCH`, `SPAM_PROVIDER_UNAVAILABLE`,
`AV_PROVIDER_UNAVAILABLE`, `MAIL_QUEUE_SCOPE_DENIED`,
`WEBMAIL_SSO_EXPIRED`, `WEBMAIL_SSO_REPLAY`.

P0: open relay impossible; cross-tenant mailbox/queue access denied;
DKIM private key never exposed; mandatory AV/spam floor cannot be
disabled; forward loop blocked; queue delete step-up/audited; webmail
ticket single-use; mailbox delete obeys retention; DNS auth desired vs
observed mismatch surfaced; service config failure rolls back; abuse
containment cannot cross tenant.
