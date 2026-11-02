# Shared Console presentation

**Status: documentation reservation; no shared Console implementation is supplied here.**

This location may hold genuinely reused CLI presentation/transport helpers when implemented commands need them. Domain commands stay in `src/Module/<Owner>/UI/Console/` and call the same authorized application operation as HTTP/message adapters. The [command inventory](../../../bin/COMMANDS.md) distinguishes today's seven candidate commands from future conventions.

## Suitable shared responsibilities

| Concern | Presentation boundary |
| :--- | :--- |
| Human/machine rendering | Map an already authorized result to a documented format; future shared formats need explicit versioning and acceptance. |
| Error presentation | Render bounded fixed error codes/references without native stderr, secrets or exception internals. |
| Progress and references | Present operation status and correlation metadata without changing durable state or treating a reference as authority. |
| Exit mapping | Apply an explicitly accepted command-output contract; current command-specific behavior must remain distinguishable. |

Do not add empty helper classes, base commands or interfaces merely to populate the directory. Extract a shared implementation only when its semantics and call sites are genuinely common.

## Responsibilities that stay elsewhere

IdentityAccess owns identity and permission decisions. The resource module owns business validation, actor/tenant/resource scope and its use cases. Operations owns scheduling, durable state, idempotency, recovery and future leases/fencing/backpressure. Platform and the approved agent boundary own controlled system integration.

Shared Console must not become a service locator, dynamic command registry, container/plugin loader, second permission engine or arbitrary subprocess runner. It must not read caller-selected configuration files, decide privileged effects or duplicate HTTP policy.

[services.yaml](../../../config/services.yaml) currently discovers Core source with autoconfiguration; a new adapter still requires verified command wiring and context refusal behavior. A named context is not proof of the calling Unix identity or production packaging.

The proposed human/JSON formats, namespaces and automation behavior in [bin/COMMANDS.md](../../../bin/COMMANDS.md#proposed-automation-contract) are not implemented by this README. Existing output and exit codes retain their command-specific meaning until a reviewed migration.

- [Core structure](../../../STRUCTURE.md)
- [Shared foundations](../README.md)
- [Module layout](../../Module/README.md#layout-inside-an-implemented-module)
- [Operations ownership](../../Module/Operations/README.md)
- [Product toolbox](../../../resources/toolbox/README.md)
