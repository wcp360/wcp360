# Optional AI and MCP integration preparation

**Status: design reservation. No AI provider, model, assistant mission or MCP endpoint is enabled.**

AI is optional integration functionality, not a second Core security or privileged-operation engine. Platform coordinates provider/runtime boundaries; ExtensionManagement admits detached contributions; IdentityAccess controls current data/tool scope; Operations coordinates approved actions. Each business owner retains validation. See the [Core structure](../../../../../STRUCTURE.md) and [AI resource index](../../../../../resources/ai/README.md).

## Four distinct concepts

| Concept | Responsibility and boundary |
| :--- | :--- |
| Provider | Versioned local/cloud inference connection, admitted endpoint, secret reference, privacy, timeout and availability/cost policy. |
| Model | Identity/version/provenance/license/digest, capabilities and RAM/VRAM/context limits. Model weights/caches stay outside Git. |
| Assistant / agent mission | Bounded goal, admitted tools, data scope, proposal/approval state, time/turn/token/cost budget and stop/resume policy. |
| MCP adapter | Versioned transport/tool integration; it does not grant Core permission or install arbitrary tools. |

The Go system agents under agents/ remain wcpd and wcp-privd. Assistant agents are missions that can request approved use cases; model output never becomes a shell command, SQL, package operation or authorization grant.

## Read assistance before mutations

Start with explicitly authorized, minimized and redacted observations. Operator prompts, logs, documents, retrieved content, model output and remote tool descriptions are untrusted data. Keep customer contents, provider requests and credentials separated by tenant and context; a commercial entitlement does not waive privacy or current permission.

For a mutation, show the actual target, diff/plan, expected revision, effects and recovery implications. Follow the owning use case's approval requirements and bind any approval to that immutable proposal. Execution rechecks current authority, resource state, capability and budget through the normal durable job/agent path. Changing a proposal invalidates its approval.

AI unavailability, exhausted credits or disabling its plugin must leave normal CLI/browser/API administration, essential hosting, security and recovery available. Explicitly scope retained prompts/results and usage telemetry; do not log raw secrets or silently send support bundles to a provider.

## MCP client and server boundaries

MCP client-to-provider calls and a future Core-facing MCP server need separate reviewed identities and grants. Negotiate an admitted protocol version and verify the actual transport contract; catalogue discovery or read-only/destructive annotations are hints, not authorization.

For HTTP, validate endpoint/issuer/audience/scopes, token handling, redirects and network destinations according to the selected authorization profile. Do not pass a caller's token through to an unrelated service. A browser session cookie is not automatically a suitable MCP credential. For stdio, admit the installed executable/package/argv/environment and process identity; do not expose tenant-selected subprocess launch as a convenience.

The official [authorization specification](https://modelcontextprotocol.io/specification/2026-07-28/basic/authorization) distinguishes HTTP from stdio. The [security guidance](https://modelcontextprotocol.io/docs/2026-07-28/tutorials/security/security_best_practices) addresses token passthrough, SSRF and confused-deputy risks. These references inform a future contract; this README does not select or implement a public MCP API.

## Delivery and refusal evidence

1. Admit one isolated provider and one bounded read-only data/tool capability with secret redaction and explicit retention.
2. Add budget/cancellation/revocation and provider/model health, without altering core authorization or essential availability.
3. Add immutable action proposals through existing owner workflows, then a qualified MCP transport adapter.
4. Test prompt/tool injection, foreign tenant data, forged annotations, stale approval, revoked caller, token audience, redirect/rebinding escape, unapproved stdio launch, floods and provider failure.

Detached model runtimes and MCP servers keep their own dependencies/processes. They never receive direct Core-table writes, Docker API access, wcp-privd sockets or a generic system terminal.
