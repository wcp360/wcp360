# WCP360 Implementation Contracts

Published owner contract bodies are in [specifications/](../specifications/README.md). This directory provides contract navigation and consumption guidance; it does not create a second authority or duplicate the specification bodies.

- [API](../specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md)
- [IPC-B](../specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md)
- [Permissions](../specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md)
- [Selected baseline](../manifests/WCP360-BM-0001.md)

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
