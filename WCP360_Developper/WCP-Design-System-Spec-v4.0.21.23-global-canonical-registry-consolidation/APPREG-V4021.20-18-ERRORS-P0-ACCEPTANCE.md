# V4.0.21.20 --- Files / Runtime Errors & P0 Acceptance

Stable errors include `PATH_OUTSIDE_MANAGED_ROOT`,
`SYMLINK_ESCAPE_DENIED`, `ARCHIVE_UNSAFE`, `FILESYSTEM_QUOTA_EXCEEDED`,
`ACCESS_MODE_DENIED`, `GIT_REMOTE_DENIED`, `DEPLOYMENT_CONFLICT`,
`RUNTIME_UNAVAILABLE`, `DEPENDENCY_JOB_FAILED`, `SECRET_ACCESS_DENIED`,
`SCHEDULE_INVALID`, `WORKER_LIMIT_EXCEEDED`, `STAGING_SAFETY_BLOCKED`,
`WP_SSO_REPLAY`.

P0 acceptance proves tenant containment, last-known-good deployment
preservation, runtime/resource ceilings, secret redaction, bounded
scheduled execution, safe staging side effects and idempotent durable
Jobs.
