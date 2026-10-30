# V4.0.21.20 --- Application Logs / Build Output

Build/deploy/runtime/worker/cron output is tenant-scoped, size/rate
bounded, retention-controlled and redacted. Streaming endpoints require
authorization throughout connection lifetime and terminate when
access/session is revoked where technically supported.

ANSI/control sequence handling and browser rendering are sanitized. Logs
are observability data, not a secret retrieval mechanism.
