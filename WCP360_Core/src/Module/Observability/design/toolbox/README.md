# Product Toolbox preparation

**Status: design reservation. No new diagnostic executable or repair action is exposed.**

The Toolbox groups approved observations and owned operations; it is not a generic terminal. Observability owns diagnostic results, redaction, health interpretation and evidence. Platform presents node-specific tools, while each resource owner validates its own inspection/repair. Operations owns jobs; IdentityAccess scopes every invocation.

See the [Core structure](../../../../../STRUCTURE.md), [resource profiles](../../../../../resources/toolbox/README.md), [specialized diagnostics](../../../../../diagnostics/README.md) and [CLI inventory](../../../../../bin/COMMANDS.md). Developer checks under tools/ remain separate.

## Catalogue contract to implement

| Field group | Required boundary |
| :--- | :--- |
| Identity | Stable admitted tool/version, owning module and actual installed provider/capability. |
| Input | Typed bounded settings and owned resource IDs; no arbitrary shell, path, URL, SQL or service name. |
| Authority | Surface, actor/tenant/resource and current permission; read-only status never substitutes for permission. |
| Cost | Deadline, bytes/rows/log limits, CPU/RAM/process/network budget and cancellation behavior. |
| Output | Structured status, units, timestamps, current scope, unavailable/partial evidence and redacted details. |
| Side effects | Observation vs a separately authorized repair/use case, expected revision, diff and recovery. |
| Evidence | Correlation, source/provider/tool versions and limited private retention; no secret-bearing support exports. |

Initial families can reuse actual accepted observations: identity, OS/provider readiness, storage/capacity, native configuration inspection and operation outcome. A future network diagnostic needs admitted targets and SSRF/egress controls.

Do not fabricate healthy results for absent tools. Distinguish passed, failed, not_run, not_configured and not_applicable, plus actual tool/process lifecycle where relevant. Collection success is not proof of service health or safe deployment.

## Sharing interfaces

Browser, domain CLI, API and a future approved MCP adapter call the same owning use case. Catalogue metadata does not load arbitrary plugin executables into Core; contributions are admitted by ExtensionManagement with version/capability checks.

Non-root specialized Python tools belong under diagnostics/<specific-tool>/ with their own reviewed dependencies and input/output contract. A repair request remains separate from collecting evidence and cannot accept a generated AI command.

## Acceptance

Exercise foreign resource/context, revoked permission, oversized/invalid input/output, missing provider, timeout/flood, network escape, secret redaction, ambiguous scope, disk pressure and unavailable dependencies. Repairs additionally need stale-revision, lost acknowledgement, interruption and recovery cases on disposable targets. Local administration and baseline diagnostics must remain usable when optional providers/plugins are down.
