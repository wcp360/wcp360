# Application entry points and command locations

[bin/console](console) is the implemented Symfony entry point for the WCP360 development candidate. The [command inventory and CLI direction](COMMANDS.md) lists the eight WCP360 command classes, their current context checks and output limits. Production boot remains disabled.

| Kind of command | Source owner | Execution boundary |
| :--- | :--- | :--- |
| Domain CLI command | src/Module/<Owner>/UI/Console/ | Thin Symfony Console adapter invoking the authorized application operation |
| Job/worker entry point | Operations and the approved bootstrap/supervisor | Dedicated identity, bounded lifecycle, durable outcomes and current authorization |
| First installation/repair/upgrade | installer/ and Setup | Reviewed autonomous bootstrap followed by restricted application setup |
| Linux service/file action | Approved agent component and registered primitive | Non-root wcpd to minimal-root wcp-privd through bounded accepted IPC |
| Product diagnostic | Platform/Observability and diagnostics/<tool>/ | Fixed read-only checks, bounded non-root collection and redacted results |
| Development quality tool | tools/<named-tool>/ | Maintainer/CI toolchain; no product or privileged operation authority |

The setup-only `wcp:candidate:database:roles` command is read-only by default. Explicit `--apply` additionally needs its enable flag and the selected operation's private maintenance inputs. See the [recipe](../installer/recipes/CANDIDATE-DATABASE-ROLES.md) for fresh roles, fixed upgrades and recovery requirements.

The worker-only `wcp:candidate:work --reconcile-interrupted` option marks up to 100 still-running hosting plans UNKNOWN_OUTCOME per call. Stop all rendering workers first. Locked PostgreSQL rows may be skipped, so zero is not proof that all interrupted work is resolved. No plan is automatically replayed. See [queue behavior and limits](../src/Module/Operations/README.md#atomic-plan-transitions-and-bounded-recovery).

A command's name or configured context is not an authorization rule or Unix identity proof. HTTP and Console adapters reuse one application operation; domain-specific permissions and resource scope remain with their owners. Registration requires verified service discovery/autoconfiguration or explicit bootstrap wiring. It grants no privileged execution authority.

The [CLI direction](COMMANDS.md#proposed-automation-contract) separates current behavior from future machine formats, namespaces and worker budgets. Those proposals do not add flags or commands. A general executor, shell command field or duplicated CLI policy registry is not part of this design.

- [Core structure](../STRUCTURE.md)
- [Module ownership and layout](../src/Module/README.md)
- [Shared Console presentation](../src/Shared/Console/README.md)
- [Product toolbox](../resources/toolbox/README.md)
- [Development tools](../tools/README.md)
- [Installer](../installer/README.md)
- [Agent boundary](../agents/README.md)
- [Core navigation](../NAVIGATION.md)
