# Runtime Socket / Locks / IPC Placement

## Canonical socket

```text
/run/wcp360/wcpd.sock
```

## Socket requirements

- owned by root/daemon;
- group restricted to authorized WCP caller identity;
- mode `0660` or stricter;
- peer credential verification where platform permits;
- protocol version check;
- maximum message size;
- request timeout;
- correlation ID mandatory;
- no arbitrary binary payload unless explicitly specified.

## Runtime lock directory

```text
/run/wcp360/locks/
```

Purpose:
- short-lived process coordination;
- not authoritative persistent lock state.

Persistent lock ownership must also exist in the job/state datastore.

## PID/runtime files

If needed:

```text
/run/wcp360/runtime/
```

Do not place runtime PID/socket files in `/tmp`.

## Restart behavior

After reboot:
- `/run/wcp360` is recreated;
- stale locks are not blindly restored;
- daemon reconciles persistent job/lock state before resuming execution.
