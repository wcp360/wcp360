# V4.0.21.10 --- Files / Access / Apps / Runtimes P0 Security & Edge Cases

## Files

Cross-tenant read/write DENY; traversal/symlink/hardlink escape DENY;
zip bomb bounded; archive special device DENY; concurrent editor
conflict detected; quota exhaustion leaves no partial corrupt committed
file.

## Access

Root/sudo entitlement impossible; revoked SSH key denied; SFTP cannot
escape root; FTP cleartext unavailable; SSH server config failure rolls
back.

## Git/deploy

Credential in URL not persisted; unknown host key rejected per policy;
webhook replay rejected; deploy race serialized; failed health check
rolls back; release rollback does not roll back mutable shared data
silently.

## WordPress

Staging cannot send production email by default; serialized URL
replacement safe; vulnerable plugin quarantine auditable; failed
core/plugin update restores checkpoint where supported.

## Runtimes/dependencies

Package scripts never run root; fork/memory/disk limits enforced;
runtime version unavailable → explicit denial; secret absent from
argv/log/build output.

## Cron/workers

DST behavior deterministic; overlapping run policy honored; crash loop
bounded; worker stays in account cgroup; disabled/suspended account
cannot keep executing scheduled tasks unless explicit platform
maintenance exception exists.
