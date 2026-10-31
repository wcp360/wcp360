# Shared foundations

Reuse typed errors/results, correlation, operation ports and stable cross-cutting policies. Avoid global functions/Utils, a service locator or duplicated authorization. Domain-specific decisions remain in their module.

This is an implementation reservation. Follow the [Core status](../../README.md) and [development/acceptance guide](../../../documentation/development/CORE-ENGINE.md) before adding runtime code.

## Named shared source anchors

| Location | Responsibility |
| :--- | :--- |
| [Http](Http/README.md) | Accepted HTTP envelopes, error presentation and correlation plumbing; domain endpoints remain domain-owned. |
| [Security](Security/README.md) | Typed context/secret-reference/redaction primitives; authoritative policy stays with its owning module. |

These are documentation/source locations, not an implemented framework or independent API/security authority. See the [capability ownership map](../../CAPABILITY-MAP.md).
