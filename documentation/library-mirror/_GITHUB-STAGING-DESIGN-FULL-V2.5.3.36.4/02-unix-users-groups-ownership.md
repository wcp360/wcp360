# Unix Users / Groups / Ownership Model

## System identities

### `wcp360-web`
Runs web/API application processes.

Properties:
- non-root;
- no login shell;
- cannot write daemon binary/config roots;
- cannot directly execute privileged system operations.

### `wcp360-daemon`
Logical service identity for daemon-owned files.

The daemon process itself may require root/elevated execution for bounded operations, but files/socket ownership should remain explicit.

### `wcp360`
Shared system group

Used only where controlled cross-component read/write is required.

## Tenant identities

Each hosting account/application must map to a dedicated Unix identity according to deployment policy.

Example naming pattern:

```text
wcp_<short-resource-id>
```

Do not derive raw Unix usernames from untrusted free-form display names.

## Ownership table

| Path | Owner | Group | Suggested mode | Notes |
|---|---|---|---|---|
| `/usr/local/wcp360/bin` | root | root | 0755 | immutable executables |
| `/etc/wcp360` | root | wcp360 | 0750 | system config |
| `/etc/wcp360/secrets` | root | wcp360-daemon or root | 0700/0750 | never web-readable |
| `/var/lib/wcp360` | root/wcp360-daemon | wcp360 | 0750 | persistent state |
| `/var/log/wcp360` | root | wcp360 | 0750 | split subdir ACLs/modes |
| `/run/wcp360` | root | wcp360 | 0750 | runtime only |
| `/run/wcp360/wcpd.sock` | root | wcp360 | 0660 | peer-auth + group bounded |
| `/var/cache/wcp360` | wcp360-web/daemon by subdir | wcp360 | 0750 | rebuildable |
| `/srv/wcp360/apps/<id>` | tenant user | tenant group | policy | app content |

## Rules

1. `wcp360-web` is not root.
2. `wcp360-web` is not granted unrestricted sudo.
3. Tenant users are not members of WCP system-admin groups.
4. Tenant users cannot access `/var/lib/wcp360`.
5. Daemon socket group membership is tightly bounded.
6. Secrets path is not readable by the web process unless a specific mediated retrieval flow exists.