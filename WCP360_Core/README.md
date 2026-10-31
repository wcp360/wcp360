# WCP360 Core

**Status: implementation layout documented; no executable Symfony bootstrap or complete hosting engine is supplied by this scaffold.**

The Core owns essential hosting, local administration/recovery and the extension manager. It must run with zero optional plugins. Detached packages use the public SDK/API and their own processes; installing a plugin does not modify the Core Composer graph or inject its code into Symfony.

Start with the [change-location index](NAVIGATION.md) to find a function, command, dependency or Linux-service configuration. The [dependency guide](DEPENDENCIES.md) identifies manifests, locks and generated libraries.

## Source layout

```text
WCP360_Core/
├── README.md / NAVIGATION.md / DEPENDENCIES.md / CAPABILITY-MAP.md
├── src/
│   ├── Shared/
│   │   ├── Http/
│   │   └── Security/
│   └── Module/
│       ├── IdentityAccess/
│       ├── Tenancy/
│       ├── Hosting/
│       ├── Mail/
│       ├── Data/
│       ├── Files/
│       ├── Operations/
│       ├── BackupRestore/
│       ├── Transfers/
│       ├── Observability/
│       ├── Platform/
│       ├── ExtensionManagement/
│       └── Setup/
├── bin/                         # Reviewed CLI entry points when implemented
├── config/
│   ├── packages/
│   ├── routes/
│   └── contexts/
├── resources/services/          # Sixteen documented Linux-service source locations
├── templates/
│   ├── admin/
│   ├── user/
│   ├── setup/
│   ├── layouts/
│   ├── errors/
│   └── components/
│       ├── forms/
│       ├── tables/
│       ├── feedback/
│       ├── charts/
│       └── navigation/
├── assets/
│   ├── controllers/
│   ├── styles/
│   └── images/
├── public/
├── translations/
├── migrations/                  # Core schema migrations, not instance transfers
├── tests/
│   ├── Unit/
│   ├── Contract/
│   ├── Integration/
│   ├── Security/
│   ├── Lifecycle/
│   ├── Installer/
│   ├── Browser/
│   └── Fixtures/
├── agents/
│   ├── wcpd/
│   └── wcp-privd/
├── diagnostics/
└── installer/
    ├── preflight/
    ├── recipes/
    ├── upgrades/
    ├── recovery/
    └── scaffolds/               # Historical guarded templates, not stable installers
```

The Symfony bootstrap will add its real composer.json/lock, bootstrap/configuration and application commands after toolchain/slice acceptance. Composer generates vendor/; runtime var/ and build/cache output are not source. No placeholder dependency lock or synthetic application kernel is presented as an implemented platform.

## README capability coverage

The [capability-to-owner map](CAPABILITY-MAP.md) accounts for all 233 README capabilities across 21 functional domains. They are grouped into thirteen Core modules, with shared HTTP/security primitives and chart components explicitly indexed. It also distinguishes the 149 proposed catalog entries from implemented plugin packages. This is ownership/structure coverage, not functional acceptance.

## Configuration and commands

Symfony configuration belongs in config; Linux-service template/profile sources belong in [resources/services](resources/services/README.md). Applied /etc files and machine-specific secrets/state stay outside source. [The navigation index](NAVIGATION.md) maps all thirteen built-in domains and sixteen service-resource locations to their owners.

Business functions are cohesive services in the owning module. CLI adapters belong in that module's UI/Console layer and call the same authorized use cases as HTTP. bin contains entry points; agents contain the accepted controlled system operations. See the [command guide](bin/README.md).

## Frontend, installation and verification locations

- [Frontend sources](assets/README.md): controllers, styles and first-party images.
- [Twig sources](templates/README.md): context-specific pages, layouts, safe error pages and shared forms/tables/charts/feedback/navigation.
- [Installer source map](installer/README.md): preflight, recipes, supported upgrades, interrupted-install recovery and historical scaffolds.
- [Test source map](tests/README.md): unit, contract, integration, security, lifecycle, installation, browser and synthetic-fixture locations.

These directories contain navigation/responsibility documents. They do not supply executable suites, a Symfony bootstrap, deployable service configuration or a working installer. Create actual implementation files with their accepted contracts and relevant verification. Generated dependencies/caches and machine state remain outside versioned source.

## Development boundaries

- PHP/Symfony renders Twig with Turbo/Stimulus; frontend assets and components are shared first-party code.
- Domain use cases are services under src/Module, with Domain, Application, Infrastructure and UI boundaries. Internal modules are not detachable third-party bundles.
- ADMIN, USER, workers, wcpd and wcp-privd preserve separate runtime identities/access. Sharing source code does not share sessions or privileges.
- Core engine adapters cannot be removed while active hosted resources depend on them.
- Plugin installation/activation/disablement/removal is an authorized durable operation, with dependency checks and failure containment.
- Existing daemon/platform candidates on SOURCE/ branches remain separate and require explicit tested integration into this target layout.

## Guides

- [Development strategy and dependency locations](../documentation/development/README.md)
- [Core domain ownership and acceptance](../documentation/development/CORE-ENGINE.md)
- [Plugin lifecycle](../documentation/development/PLUGIN-LIFECYCLE.md)
- [Installer and release qualification](../documentation/development/DELIVERY-AND-RELEASES.md)
- [Current baseline manifest](../documentation/manifests/WCP360-BM-0001.md)
- [Architecture](../ARCHITECTURE.md)
