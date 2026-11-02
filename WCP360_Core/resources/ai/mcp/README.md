# MCP integration resource references

**Status: documentation reservation. No additional runtime or production capability is implemented here.**

Owners: [Platform](../../../src/Module/Platform/README.md), [ExtensionManagement](../../../src/Module/ExtensionManagement/README.md) and Operations, with IdentityAccess.

No MCP client/server, tool permission or process launcher is implemented here. Future descriptors declare role, pinned protocol/package revision, admitted transport and endpoint/process reference, tools, identity mapping, scope, limits, revocation and audit. Discovery is not execution permission.

HTTP authorization and local stdio have different contracts. Use the [versioned MCP authorization specification](https://modelcontextprotocol.io/specification/2026-07-28/basic/authorization) for HTTP issuer/audience/scopes. For stdio, admit a fixed trusted package/process with scoped credentials; refuse arbitrary command lines, startup package installers and inherited Core secrets.

Do not pass upstream tokens through as arbitrary downstream credentials. Validate endpoint/discovery/redirect destinations, consent and token audience; enforce network controls against SSRF and control-plane access. Follow [official security guidance](https://modelcontextprotocol.io/docs/2026-07-28/tutorials/security/security_best_practices).

MCP adds no Docker socket, shell, database or privileged-broker access. Mutating tools enter the same authorized use case, approval and durable operation path as the panel. Tool output remains untrusted data.

See [AI boundaries](../README.md), [providers](../providers/README.md) and [toolbox](../../toolbox/README.md).

See the [resource index](../../README.md) and [Core structure](../../../STRUCTURE.md). Add concrete resource files only with their reviewed contract and consuming implementation; no fake configuration or empty template tree is needed.
