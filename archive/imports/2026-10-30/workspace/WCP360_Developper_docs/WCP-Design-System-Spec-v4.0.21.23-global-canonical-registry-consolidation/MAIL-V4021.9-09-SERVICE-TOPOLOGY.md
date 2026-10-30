# V4.0.21.9 --- Mail Service Topology & Adapter Model

## Logical topology

`SMTP ingress → reputation/rate controls → antispam → antivirus → routing/MTA → local delivery/LMTP → mailbox store → IMAP → webmail/client`.

Outbound:
`authenticated submission → policy/rate limits → DKIM signing → MTA queue → remote MX`,
with TLS policy and delivery telemetry.

## Adapter principle

Concrete MTA/IMAP/filter products are adapters behind canonical
capabilities. The corpus may certify Postfix/Dovecot/Rspamd/ClamAV or
alternatives, but WCP-USER contracts do not depend on product-specific
command strings.

## Health

Queue depth, deferred mail, auth failures, scanner health, signing
health, IMAP health and DNS authentication observations feed
monitoring/readiness.
