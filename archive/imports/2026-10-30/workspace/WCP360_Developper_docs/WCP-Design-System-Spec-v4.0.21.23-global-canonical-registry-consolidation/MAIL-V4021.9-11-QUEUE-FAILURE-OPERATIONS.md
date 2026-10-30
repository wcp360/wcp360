# V4.0.21.9 --- Mail Queue, Failure & Recovery Contracts

## Queue operations

Admin-scoped operations may inspect safe envelope metadata, retry
eligible deferred deliveries, hold/release queue entries and remove
messages only under explicit policy. Message content access is
separately permissioned and not a default support capability.

## Failure modes

Disk full, mailbox quota exceeded, DNS failure, remote deferral,
RBL/reputation event, DKIM signer unavailable, antispam unavailable,
antivirus unavailable, IMAP unavailable and certificate failure each
have distinct reason codes.

## Safety

No public generic `postqueue`, `postsuper`, `doveadm`, `rspamc` or shell
executor. Adapters map typed Operation IDs to closed privileged actions.
