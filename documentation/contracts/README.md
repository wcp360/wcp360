# WCP360 Implementation Contracts

Published owner contract bodies are in [specifications/](../specifications/). This directory provides contract navigation and consumption guidance; it does not create a second authority or duplicate the specification bodies.

- [API](../specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md)
- [IPC-B](../specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md)
- [Permissions](../specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md)
- [Selected baseline](../manifests/WCP360-BM-0001.md)
- [November 3 owner decisions: Free site limits and administrator MFA](../governance/decisions/OWNER-DECISION-FREE-SITES-MFA-2026-11-03.md) — current product policy and pending M1 contract review; preserves frozen canonical exports and does not claim independent acceptance.
- [November 3 owner decision: deny unresolved privileged IPC bindings](../governance/decisions/OWNER-DECISION-SC-M01-PRIVILEGED-IPC-BINDINGS-2026-11-03.md) — explicitly prohibits all twelve issue #91 operations until individually approved; does not amend frozen exports or authorize implementation.

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
