# Operator toolbox resources

**Status: documentation reservation. No additional runtime or production capability is implemented here.**

Owner: [Observability](../../src/Module/Observability/README.md), with Platform for presentation, Operations for durable execution and affected domains for resource actions.

Future entries describe specifically named diagnostics: purpose, owning use case, identity/scope, typed inputs, consulted data, read-only/mutating classification, targets, deadlines/resource budget and redacted result. DNS/HTTP/TLS checks, service observations and recovery inspections each need a bounded contract; this is not arbitrary arguments behind a command-name allowlist.

Code remains in its module or [specialized diagnostics](../../diagnostics/README.md), with tool-local dependencies. Do not add another scripts, commands or collectors registry.

A diagnostic requires authority for its target. Paths, network destinations, credentials and privileged effects cannot be selected freely through a textbox, plugin or AI prompt. Active probes and mutations require their declared scope and controls; read-only does not mean unlimited collection.

Keep useful local diagnostics without a vendor service. Protect tenant visibility, evidence, cancellation, quotas and logs. Discoverability or installation is not permission to run a tool.

See [observability resources](../observability/README.md) and [AI boundaries](../ai/README.md).

See the [resource index](../README.md) and [Core structure](../../STRUCTURE.md). Add concrete resource files only with their reviewed contract and consuming implementation; no fake configuration or empty template tree is needed.
