# WCP360 Core

**Status: implementation layout documented; no executable Symfony bootstrap or complete hosting engine is supplied by this scaffold.**

The Core owns essential hosting, local administration/recovery and the extension manager. It must run with zero optional plugins. Detached packages use the public SDK/API and their own processes; installing a plugin does not modify the Core Composer graph or inject its code into Symfony.

## Source layout

```text
WCP360_Core/
├── bin/                         # Reviewed application CLI entry points
├── config/                      # Services, routes, packages and distinct runtime contexts
├── src/
│   ├── Shared/                  # Reusable policies, typed plumbing and ports
│   └── Module/
│       └── ExtensionManagement/ # Built-in plugin lifecycle domain
├── templates/
│   ├── admin/
│   ├── user/
│   ├── components/
│   └── setup/
├── assets/                      # Stimulus, styles and approved frontend pipeline
├── public/                      # Published entry points only
├── translations/
├── migrations/
├── tests/
├── agents/
│   ├── wcpd/                    # Non-root Go agent
│   └── wcp-privd/               # Separate minimal-root Go broker
├── diagnostics/                 # Specialized non-root Python tools
└── installer/
    └── scaffolds/               # Earlier guarded installer templates
```

The Symfony bootstrap will add its real composer.json/lock, bootstrap/configuration and application commands after toolchain/slice acceptance. Composer generates vendor/; runtime var/ and build/cache output are not source. No placeholder dependency lock or synthetic application kernel is presented as an implemented platform.

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
