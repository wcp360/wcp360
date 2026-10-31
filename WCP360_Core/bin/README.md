# Application entry points and command locations

This folder is reserved for reviewed application entry points. The actual Symfony bootstrap will supply bin/console; no executable application command is supplied by this README.

| Kind of command | Source owner | Execution boundary |
| :--- | :--- | :--- |
| Domain CLI command | src/Module/<Domain>/UI/Console/ | Symfony Console adapter calling the authorized application use case |
| Job/worker entry point | Operations and the approved bootstrap/supervisor | Dedicated worker identity, bounded lifecycle and reauthorization |
| First installation/repair/upgrade | installer/ and Setup | Autonomous reviewed bootstrap followed by restricted application setup |
| Linux service/file action | Approved agent component and registered primitive | Non-root wcpd to minimal-root wcp-privd through accepted bounded IPC |
| Specialized diagnostic | diagnostics/<tool>/ | Bounded non-root collection and redacted results |

A command's name, controller or executable path is not an authorization rule. Use explicit actor/tenant/surface context, typed inputs, bounded work and durable results. HTTP and Console adapters reuse one application operation rather than duplicating business functions.

Domain-namespaced commands require verified service discovery/autoconfiguration or explicit registration in the implemented bootstrap. CLI registration does not grant privileged system execution. Keep any fixed native executable/arguments in the accepted agent contract; never append arbitrary browser/plugin strings to a shell command.

- [Module ownership and layout](../src/Module/README.md)
- [Installer](../installer/README.md)
- [Agent boundary](../agents/README.md)
- [Core navigation](../NAVIGATION.md)
- [Symfony Console documentation](https://symfony.com/doc/current/console.html)
