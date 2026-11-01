# ExtensionManagement

**Status: a fixed reference-plugin lifecycle candidate is implemented. A general plugin manager and production qualification remain unfinished.**

Package inventory, compatibility, capabilities, lifecycle, health, supervision and approved contribution registration.

Start here when changing: **Plugin installation, activation, disablement, upgrade and removal.**

| Implemented candidate source | Responsibility |
| :--- | :--- |
| [Application/PluginRegistry.php](Application/PluginRegistry.php) | Administrator-authorized install, enable, disable and uninstall transitions for `wcp360-reference-report` only. |
| [Lifecycle tests](../../../tests/Lifecycle/PluginLifecycleTest.php) | Check transitions, disabled access, tenant authorization and continued Core operation after plugin removal or unavailable package files. |

The registry accepts one fixed development manifest/API/version and compares its hash before enabling the installed package. It loads no plugin hooks, PHP code or dependencies into Core. An enabled reference summary still requires the caller's tenant permission; package state is not an authorization grant.

Arbitrary package installation, signature/trust verification, compatibility and dependency resolution, scoped capability grants, process supervision, health/quarantine, upgrades and rollback remain to be implemented and qualified. The reference lifecycle proves a narrow integration boundary, not support for the complete extension catalog.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Detached plugin code and dependencies are not loaded into the Core container, Twig paths or Composer graph.

- [Core roadmap and completion estimate](../../../ROADMAP.md)
- [Core navigation index](../../../NAVIGATION.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)
