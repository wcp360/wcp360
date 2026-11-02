# Shared foundations

Reuse cohesive typed primitives when their semantics are genuinely shared. Domain-specific decisions remain in their module; HTTP, Console and message adapters should reuse the owning application operation.

This area already contains candidate runtime code as well as documentation reservations. Its name does not establish a complete shared framework, production acceptance or independent policy authority. Follow the [Core structure](../../STRUCTURE.md), [Core status](../../README.md) and [development/acceptance guide](../../../documentation/development/CORE-ENGINE.md).

## Named shared source anchors

| Location | Current responsibility and boundary |
| :--- | :--- |
| [Http](Http/README.md) | Candidate request/error/cursor/preview transport boundaries and presentation; domain endpoints and business policy remain domain-owned. |
| [Security](Security/README.md) | Candidate runtime/session/context and datastore boundary helpers; authoritative identity and permission policy remains with IdentityAccess and the applicable contracts. |
| [Correlation/RequestId.php](Correlation/RequestId.php) | Generate/validate bounded request references. A reference is not an identity, permission, operation ID or idempotency key. |
| [Platform](Platform) | Existing target/runtime/identity inspection, bounded Linux observations and fixed web/application recipe catalogues; observation or catalogue membership does not admit native activation. |
| [Console](Console/README.md) | Documentation reservation for reused CLI presentation; no common command framework, JSON format or registry is implemented. |

Do not collect unrelated behavior in global functions.php, Utils, a universal Services folder or a service locator. Extract shared code only where meaning, invariants and ownership are demonstrably common. Keep dependencies in their reviewed language manifests/locks rather than a generic dependencies directory.

General operation policy belongs to [Operations](../Module/Operations/README.md); approved system effects belong behind [agent contracts](../../agents/README.md). [Development tooling](../../tools/README.md), [specialized diagnostics](../../diagnostics/README.md) and the [product toolbox](../../resources/toolbox/README.md) have distinct responsibilities and do not become execution APIs through inclusion here.

- [Command inventory and CLI direction](../../bin/COMMANDS.md)
- [Capability ownership map](../../CAPABILITY-MAP.md)
- [Module layout](../Module/README.md#layout-inside-an-implemented-module)
