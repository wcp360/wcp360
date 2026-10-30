# V4.0.21.19 --- Webmail / SSO Contract

Roundcube/SnappyMail-like tools are adapters.

`WEBMAIL-SSO-LAUNCH` issues short-lived single-use ticket bound to
actor/session, tenant, mailbox, target webmail, nonce and expiry.
Plaintext mailbox credentials are not placed in query strings or
control-plane logs.

Ticket replay is denied. Impersonation/support launches preserve
initiator identity and may be prohibited by privacy policy.
