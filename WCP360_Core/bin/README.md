# Application entry points and command locations

[bin/console](console) is the implemented Symfony entry point for the WCP360 Standalone development candidate. Commands live in their owning modules. Production boot remains disabled.

| Kind of command | Source owner | Execution boundary |
| :--- | :--- | :--- |
| Domain CLI command | src/Module/<Domain>/UI/Console/ | Symfony Console adapter calling the authorized application use case |
| Job/worker entry point | Operations and the approved bootstrap/supervisor | Dedicated worker identity, bounded lifecycle and reauthorization |
| First installation/repair/upgrade | installer/ and Setup | Autonomous reviewed bootstrap followed by restricted application setup |
| Linux service/file action | Approved agent component and registered primitive | Non-root wcpd to minimal-root wcp-privd through accepted bounded IPC |
| Specialized diagnostic | diagnostics/<tool>/ | Bounded non-root collection and redacted results |

The setup-only `wcp:candidate:database:roles` command is read-only by default. Explicit `--apply` additionally needs its enable flag and private setup passwords; it creates only fresh reviewed development roles. See the [recipe](../installer/recipes/CANDIDATE-DATABASE-ROLES.md) for migration, grant and recovery requirements.

The worker-only `wcp:candidate:work --reconcile-interrupted` option marks up to 100 still-running hosting plans UNKNOWN_OUTCOME per call. Stop all rendering workers first. The count covers actual transitions; locked PostgreSQL rows may be skipped, so zero is not proof that all interrupted work is resolved. Repeat as needed and inspect remaining state/locks; no plan is automatically replayed. See [queue behavior and limits](../src/Module/Operations/README.md#atomic-plan-transitions-and-bounded-recovery).

A command's name, controller or executable path is not an authorization rule. Use explicit actor/tenant/surface context, typed inputs, bounded work and durable results. HTTP and Console adapters reuse one application operation rather than duplicating business functions.

Domain-namespaced commands require verified service discovery/autoconfiguration or explicit registration in the implemented bootstrap. CLI registration does not grant privileged system execution. Keep any fixed native executable/arguments in the accepted agent contract; never append arbitrary browser/plugin strings to a shell command.

- [Module ownership and layout](../src/Module/README.md)
- [Installer](../installer/README.md)
- [Agent boundary](../agents/README.md)
- [Core navigation](../NAVIGATION.md)
- [Symfony Console documentation](https://symfony.com/doc/current/console.html)
