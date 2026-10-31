# Built-in Core modules

Each module has one owner and one [module README](../../NAVIGATION.md). These are source organization boundaries inside the Core, independent of detached optional plugins.

| Module | Change this area when working on |
| :--- | :--- |
| [IdentityAccess](IdentityAccess/README.md) | Identity, roles, session and recovery behavior. |
| [Tenancy](Tenancy/README.md) | Account ownership, plans and delegated quotas. |
| [Hosting](Hosting/README.md) | Website creation, virtual hosts, certificates and PHP pool settings. |
| [Mail](Mail/README.md) | Mailbox, routing, delivery and retention behavior. |
| [Data](Data/README.md) | Tenant database creation, grants and engine policy. |
| [Files](Files/README.md) | File listing, upload policy, rename, trash and restore behavior. |
| [Operations](Operations/README.md) | Shared job scheduling, progress, retry and recovery mechanics. |
| [BackupRestore](BackupRestore/README.md) | Backup policy, restore plans and recovery behavior. |
| [Transfers](Transfers/README.md) | Instance migration, resource mapping, cutover and post-transfer verification. |
| [Observability](Observability/README.md) | Audit queries, health checks, alerts and diagnostic presentation. |
| [Platform](Platform/README.md) | Node/service settings, approved agent integration and infrastructure policy. |
| [ExtensionManagement](ExtensionManagement/README.md) | Plugin installation, activation, disablement, upgrade and removal. |
| [Setup](Setup/README.md) | Wizard steps, setup validation and interrupted-install recovery. |

## Layout inside an implemented module

Create code directories when their first reviewed implementation exists. The per-module README is the current navigation anchor; empty classes, commands and fake configuration are not supplied.

```text
src/Module/<Domain>/
├── Domain/                 # Invariants, value objects, domain policies and repository interfaces
├── Application/            # Use cases, services, command/query handlers and typed ports
├── Infrastructure/         # Doctrine repositories, approved adapters and transport implementations
└── UI/
    ├── Http/               # Thin HTTP/API controllers and transport mapping
    ├── Console/            # Domain CLI adapters registered as Symfony services
    └── Form/               # Symfony forms and presentation-level input mapping
```

A PHP function belongs to a cohesive class/service in the owning module. Pure shared primitives belong in [Shared](../Shared/README.md) only when the semantics are actually shared. Do not collect unrelated behavior in global functions.php, Utils, a universal Service directory or duplicate CLI/HTTP business implementations.

HTTP, CLI and message handlers invoke the same application use case with explicit tenant/surface/actor context. Native system commands belong behind approved agent operations; a Console command is not permission to execute a root shell.

Symfony command registration and service discovery must be verified against the actual bootstrap when implemented. Domain-namespaced Console classes are a WCP360 convention; they require service discovery/wiring and command tests. See [Symfony Console](https://symfony.com/doc/current/console.html).

The [README capability map](../../CAPABILITY-MAP.md) assigns all 233 listed capabilities to primary owners and collaborators across the thirteen modules. README capability domains and implementation modules are different groupings; a folder does not establish implementation or runtime authority.
