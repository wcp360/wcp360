# Optional AI resource integrations

**Status: documentation reservation. No additional runtime or production capability is implemented here.**

Owners: [Platform](../../src/Module/Platform/README.md), [ExtensionManagement](../../src/Module/ExtensionManagement/README.md) and [Operations](../../src/Module/Operations/README.md), with IdentityAccess and Observability for permission/evidence boundaries.

| Resource family | Boundary |
| :--- | :--- |
| [Providers](providers/README.md) | Local/remote capabilities, admitted endpoints, secret references and data/cost policy. |
| [Models](models/README.md) | Model versions, provenance/license and observed capacity requirements; no weights in Git. |
| [Assistants and missions](agents/README.md) | Bounded orchestration, tool allowlists, scope, approval and audit. |
| [MCP](mcp/README.md) | Client/server role, transport, identity, authorization and tool admission. |

No model runtime, assistant, MCP server or provider connection is implemented by these reservations. A model generates output; an assistant orchestrates approved tools; Go wcpd/wcp-privd perform separately bounded system operations.

Start with authorized read-only, redacted data. Generated recommendations, prompts, retrieved content and tool responses are data, not permission. Mutations use the ordinary owning use case, current authority and applicable approval/job contract. No direct shell, broker or Docker socket access is introduced.

Keep credentials, private prompts, weights, embeddings and conversation state outside Git. An optional provider's absence/failure must not stop essential hosting, administration or recovery. See [extensions](../extensions/README.md) and [toolbox](../toolbox/README.md).

See the [resource index](../README.md) and [Core structure](../../STRUCTURE.md). Add concrete resource files only with their reviewed contract and consuming implementation; no fake configuration or empty template tree is needed.
