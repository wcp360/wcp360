# AI assistants and mission resources

**Status: documentation reservation. No additional runtime or production capability is implemented here.**

Owners: [Operations](../../../src/Module/Operations/README.md), [ExtensionManagement](../../../src/Module/ExtensionManagement/README.md) and Platform, with IdentityAccess and Observability.

An AI agent here means an assistant orchestrating a bounded mission. It is not Go wcpd, the minimal privileged wcp-privd broker or a Linux root identity. A model generates proposals; ordinary Core use cases remain the authority for actions. No autonomous runner is implemented here.

Future descriptors define mission/version, actor/tenant scope, admitted data/tools, provider/model references, steps/stop conditions, turn/time/cost/resource budgets, cancellation and audited results. Start with redacted read-only data. Mutations require applicable human approval, current permissions and ordinary durable operation/revision checks.

Treat prompts, retrieved text, logs and tool results as untrusted data. They cannot add tools, expand scope, bypass approval or become shell commands. Private conversations do not belong in resources.

Disabling this optional integration revokes future work safely while preserving Core resource ownership and recovery.

See [AI boundaries](../README.md), [MCP](../mcp/README.md), [toolbox](../../toolbox/README.md) and [Go system agents](../../../agents/README.md).

See the [resource index](../../README.md) and [Core structure](../../../STRUCTURE.md). Add concrete resource files only with their reviewed contract and consuming implementation; no fake configuration or empty template tree is needed.
