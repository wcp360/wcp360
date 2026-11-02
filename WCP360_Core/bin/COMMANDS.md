# Core command inventory and CLI direction

This document describes the inspected development candidate and a proposed CLI direction. It does not register commands, install an executable, authorize privileged effects or define a new accepted production contract. [Core structure](../STRUCTURE.md) assigns each responsibility to its owner.

## Implemented entry point and discovery

[console](console) boots the Symfony Console application through the existing [Kernel](../src/Kernel.php). The Kernel accepts enabled candidate boot in dev/test only and refuses production boot. Symfony framework and dependency commands may also be registered; the table below inventories the seven WCP360-owned command classes currently declared with AsCommand.

[services.yaml](../config/services.yaml) discovers Core source with autowiring/autoconfiguration and explicit exclusions. Command discovery and a successful container build do not establish permission to invoke a command or perform an effect. Current command execution checks RuntimeContext; the four contexts are not yet independently composed production containers. [Context packaging and identities](../config/contexts/README.md) remain separate acceptance work.

## Seven implemented candidate commands

| Command | Source owner | Required candidate context | Existing behavior and additional boundary |
| :--- | :--- | :--- | :--- |
| `wcp:candidate:enroll` | [SetupCommand](../src/Module/Setup/UI/Console/SetupCommand.php) | setup | Interactive enrollment of a local account with username, contact email and hidden password; a USER account also receives distinct account/tenant identities and one candidate membership. Not a native installer. |
| `wcp:candidate:database:roles` | [DatabaseRolesCommand](../src/Module/Setup/UI/Console/DatabaseRolesCommand.php) | setup | Read-only plan by default. Explicit `--apply` requires the enable flag. Fresh-role creation consumes private setup passwords; `--password-changes` and `--audit-reads` select mutually exclusive fixed capability upgrades. |
| `wcp:candidate:account:recover` | [RecoverAccountPasswordCommand](../src/Module/Setup/UI/Console/RecoverAccountPasswordCommand.php) | setup | Requires username and `--surface` (`user` or `admin`). Inspection is read-only. `--apply` additionally requires the enable flag, an interactive confirmation and hidden password input; recovery revokes account sessions and preserves MFA. |
| `wcp:candidate:account:recovery-code` | [IssuePasswordRecoveryCodeCommand](../src/Module/Setup/UI/Console/IssuePasswordRecoveryCodeCommand.php) | setup | Requires username and `--surface` (`user` or `admin`). Inspection is read-only. `--issue` requires enabled issuance for the surface and interactive confirmation; the private short-lived code is displayed once and no email is sent. |
| `wcp:candidate:access:check` | [CheckAccessCommand](../src/Module/IdentityAccess/UI/Console/CheckAccessCommand.php) | worker or setup | Read-only check for supplied actor and tenant UUIDs; the implemented permission is `candidate.hosting.plan`. Supplying an actor UUID does not authenticate as that actor or grant access. |
| `wcp:candidate:work` | [WorkCommand](../src/Module/Operations/UI/Console/WorkCommand.php) | worker | Processes at most one queued hosting-plan job. `--reconcile-interrupted` marks at most 100 still-running plans UNKNOWN_OUTCOME per invocation; stop rendering workers first. No automatic replay or privileged effect. |
| `wcp:candidate:activate` | [ActivationCommand](../src/Module/Hosting/UI/Console/ActivationCommand.php) | worker | Processes one admitted isolated PHP-FPM activation experiment. `--reconcile` observes uncertain outcomes without applying again; stop other activation workers first. The explicit disposable-environment admission remains mandatory. |

The environment context is a candidate selector, not Unix identity proof. The intended worker service identity, ADMIN/USER identities and transient setup authority are documented in the [context profiles](../config/contexts/README.md#inspect-the-calling-identity); production service packaging/enforcement is not supplied by this inventory. PHP and ordinary workers do not receive direct privileged-broker access.

The server administrator's ROOT surface and the Linux root account are different trust boundaries. A hosting username named root remains subject to its actual role and scope. No command obtains root authority from its name, context option or selected username.

### Current output and exit behavior

Current output is command-specific. Enrollment uses terminal prompts; several maintenance/check commands emit their own versioned JSON reports; code issuance and worker commands also use text. Interactive prompts and a later JSON result can share the output stream. There is no implemented universal `--format=json` switch or common CLI report envelope.

The source uses Symfony SUCCESS (0), FAILURE (1) and, in several commands, INVALID (2). These are command-process outcomes. A successfully processed `work` or `activate` invocation can return a job ID whose durable business outcome still needs inspection. An empty queue is also successful. Recovery cancellation can return 0 without a mutation. Scripts must follow the specific command contract rather than infer a completed hosting operation from exit 0.

A private recovery code is intentionally displayed only through its explicit issuance path. Do not capture it in public logs, generic machine reports or diagnostic bundles. The existing [database-role recipe](../installer/recipes/CANDIDATE-DATABASE-ROLES.md) documents its separate maintenance prerequisites.

## Proposed domain namespaces

These examples are design proposals, not implemented commands, aliases or promised options:

| Proposed namespace/example | Owner and intended responsibility |
| :--- | :--- |
| `wcp:hosting:site:plan` | Hosting validates an authorized site change and submits its typed operation. |
| `wcp:operations:work`, `wcp:operations:job:show` | Operations owns bounded processing and scoped durable outcome inspection. |
| `wcp:platform:doctor` | Platform coordinates fixed read-only infrastructure checks. |
| `wcp:extensions:list`, `wcp:extensions:disable` | ExtensionManagement exposes admitted inventory/lifecycle use cases. |
| `wcp:setup:inspect` | Setup inspects installation/repair state under restricted lifecycle authority. |

Keep transport adapters in `src/Module/<Owner>/UI/Console/`. HTTP, Console and message adapters call the same authorized application use case, with explicit trusted execution identity and resource scope. Do not copy validation or permission policy into command classes. Existing candidate names and meanings must remain stable until an explicit migration/deprecation decision.

[Shared Console](../src/Shared/Console/README.md) may later hold genuinely reused output/transport presentation. It must not become a second command registry, service locator, authorization engine or arbitrary executor.

## Proposed automation contract

The following conventions require implementation, versioning and acceptance before clients rely on them:

| Area | Proposed convention |
| :--- | :--- |
| Human output | Default readable output, explicit prompts and redacted error references. |
| Machine output | An explicit JSON format, one bounded versioned result on stdout, progress/diagnostics on stderr, no ANSI sequences or interactive prompts. This flag/envelope is not currently implemented. |
| Exit codes | Retain 0 for the command's declared successful contract, 1 for an unsuccessful/refused/uncertain outcome and 2 for invalid invocation where applicable. A structured status must distinguish accepted, completed, refused and uncertain results; no shared mapping is adopted yet. |
| Durable actions | Return an authorized operation reference and its observed status. An accepted asynchronous request is distinct from effect completion. |
| Sensitive inputs | Use the operation's reviewed private input mechanism; no general password/token argv options or secret-bearing diagnostic output. |
| References | Keep request correlation, operation identity, attempt identity and idempotency keys distinct. None grants authority. |
| Compatibility | Document supported flags, report versions and deprecation behavior; do not silently repurpose candidate commands. |

## Bounded work and recovery

The existing [queue](../src/Module/Operations/README.md#atomic-plan-transitions-and-bounded-recovery) already commits plan claims and audit before rendering and conditionally publishes outcomes. Its interruption maintenance cannot identify live renderers or prove that zero returned rows means all work is resolved.

A general worker needs accepted contracts for per-node/tenant/type concurrency, bounded queue admission, time/memory limits, graceful drain, leases and effect-side fencing. Future worker limit options may narrow an admitted budget; they must not override a server-side safety ceiling. These general mechanisms are not implemented by the current one-job commands.

Idempotency binds a scoped validated intention to its first durable operation. A lease alone cannot prevent an old worker applying a delayed effect: the recipient must reject stale fencing tokens/resource revisions. Unknown outcomes require observation before replay; retry, cancellation and compensation must reflect the particular resource's semantics. [Operations](../src/Module/Operations/README.md) owns these mechanisms, while each module owns its business operation.

## Diagnostics and system effects

The [product toolbox reservation](../resources/toolbox/README.md) describes operator-facing fixed checks. [diagnostics](../diagnostics/README.md) owns specialized bounded non-root collectors. [tools](../tools/README.md) contains development tooling, including PHPStan integration; it is not a product execution API.

Native actions use reviewed typed agent operations with fixed executables, arguments and resource selectors. A CLI adapter must not append browser/plugin strings to shell commands, accept arbitrary service names or expose a root shell. [Agent separation](../agents/README.md) remains authoritative.

## Acceptance before extending the CLI

Verify actual discovery and the context/identity/grant matrix, positive and refusal behavior, shared HTTP/CLI use-case semantics, bounded/redacted output and compatibility. Mutation qualification additionally needs idempotent replay, stale revisions/fences, revocation, cancellation/crash recovery and overload evidence appropriate to its effects. Tests and runtime results remain tied to their exact source and environment; documentation adds no executable acceptance.

- [Core structure](../STRUCTURE.md)
- [Module layout](../src/Module/README.md#layout-inside-an-implemented-module)
- [Core roadmap](../ROADMAP.md)
