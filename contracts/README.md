# WCP360 Implementation Contracts

This directory is for implementation-consumable canonical contracts.

Expected contract families:

- API
- IPC
- permissions
- errors

Current approved references:

```text
API CONTRACT VERSION = 1.0
IPC CONTRACT VERSION = WCP360-IPC-B/1
PERMISSION REGISTRY VERSION = v1.0
```

No contract may introduce generic privileged execution.

Forbidden examples include:

- `shell.exec`
- arbitrary command execution
- arbitrary executable execution
- generic sudo proxy
- generic systemctl proxy
- arbitrary signal
- unrestricted filesystem primitive
