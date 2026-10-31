# V4.0.21.19 --- SMTP / IMAP Stack Adapter Contract

Canonical adapters separate SMTP transport/submission from
IMAP/LMTP/mailbox storage. A certified Host Profile declares supported
stack/provider versions.

Desired config follows render → validate → stage → atomic publish →
reload → protocol health probes → observed state → rollback on failure.

Submission requires authenticated policy/TLS according to profile. Open
relay is a P0 invariant violation. Administrative daemon credentials are
never mailbox credentials. Service reload/restart is capability-gated
and closed-action only.
