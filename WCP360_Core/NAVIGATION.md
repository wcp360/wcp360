# Find and change Core code

Start here to locate a change. This index documents the intended source locations; the directories currently contain documentation scaffolds, not a complete Symfony application or deployable Linux configurations.

## Find the right owner

| I need to change… | Start here | Then follow |
| :--- | :--- | :--- |
| A business function or validation rule | [Module index](src/Module/README.md) | Owning module's Domain/ or Application/ |
| A function reused across several domains | [Shared foundations](src/Shared/README.md) | A typed cohesive service; keep domain-specific policy in its module |
| A panel CLI command | [Command guide](bin/README.md) | src/Module/<Domain>/UI/Console/ calling the same application use case as HTTP |
| A worker/job handler | [Operations](src/Module/Operations/README.md) and the affected domain | Shared lifecycle in Operations; resource decisions in the owning module |
| An API controller or form | [Module layout](src/Module/README.md) | UI/Http/ or UI/Form/; authorization and invariants stay server-side |
| Symfony packages, service wiring or routes | [Application configuration](config/README.md) | config/packages/, config/routes/ and reviewed service imports |
| ADMIN, USER or worker identity/settings | [Runtime contexts](config/contexts/README.md) | Explicit per-context access, sessions, secrets and connections |
| Nginx/Apache virtual hosts or PHP pools | [Linux services](resources/services/README.md) | nginx/, apache/, php-fpm/ and the Hosting module |
| Mail servers and spam policy | [Mail](src/Module/Mail/README.md) | postfix/, dovecot/, rspamd/ resources plus the owning adapter |
| MySQL, MariaDB or PostgreSQL service settings | [Data](src/Module/Data/README.md) | mysql/, mariadb/, postgresql/ resources; tenant and control-plane grants stay separate |
| Redis, DNS, SSH, firewall or supervision | [Service index](resources/services/README.md) | Typed adapter and approved agent operations, not a PHP shell call |
| Composer, Go, Python or frontend dependencies | [Dependency guide](DEPENDENCIES.md) | Component manifest/lock and its reviewed build procedure |
| An administrator/user page or reusable component | [Templates](templates/README.md) and [assets](assets/README.md) | Owning module presenter, first-party Twig, Stimulus and styles |
| Database schema | [Migrations](migrations/README.md) and the owning domain | Accepted DB contract, migrations and upgrade/recovery evidence |
| Installation and upgrade | [Installer](installer/README.md) and [Setup](src/Module/Setup/README.md) | Bootstrap source, restricted wizard and durable progress |
| Go daemon/broker behavior | [Agents](agents/README.md) | Separate wcpd / wcp-privd components and accepted protocol/privilege contracts |
| Python diagnostics | [Diagnostics](diagnostics/README.md) | Specialized, bounded, non-root tooling |
| Regression/contract/target tests | [Tests](tests/README.md) | Unit, Integration, Contract, Security, Fixtures and other relevant suites as implemented |
| Artifact assembly or a release | [Build](../WCP360_Build/README.md) | [Qualified release records](../WCP360_Releases/README.md) |

## Source tree

```text
WCP360_Core/
├── NAVIGATION.md                   # This change-location index
├── DEPENDENCIES.md                 # Manifests, locks, generated dependencies
├── src/
│   ├── Shared/                     # Stable shared primitives and policies
│   └── Module/                     # Twelve named business-domain boundaries
├── bin/                            # Application entry points and command guide
├── config/                         # Symfony configuration only
│   ├── packages/                   # Framework/library configuration
│   ├── routes/                     # HTTP route wiring
│   └── contexts/                   # ADMIN / USER / worker context documentation
├── resources/
│   └── services/                   # Versioned Linux-service template/profile sources
│       ├── nginx/
│       ├── apache/
│       ├── php-fpm/
│       ├── postfix/
│       ├── dovecot/
│       ├── rspamd/
│       ├── postgresql/
│       ├── mysql/
│       ├── mariadb/
│       ├── redis/
│       ├── dns/
│       ├── systemd/
│       ├── ssh/
│       ├── firewall/
│       ├── logrotate/
│       └── backup/
├── templates/                      # Browser-facing first-party Twig views
├── assets/                         # Stimulus, styles and approved asset pipeline
├── public/                         # Published entry points/assets only
├── migrations/                     # Reviewed control-plane schema migrations
├── translations/
├── agents/                         # wcpd and wcp-privd source components
├── diagnostics/                    # Non-root Python tools
├── installer/                      # Installation/bootstrap and recovery sources
└── tests/                          # Test code and synthetic fixtures
```

When an approved bootstrap exists, Composer manifests/locks will be real root files, vendor/ will be generated and ignored, and var/ will hold disposable application-local state. These generated directories and empty manifests are not created as placeholders.

## Example: change a site's PHP version

1. Find Hosting in the module index. Its application use case owns the site/runtime change; its Domain rules own permitted versions and quotas.
2. Change the approved PHP-FPM pool/profile source under resources/services/php-fpm/ if configuration text needs to change.
3. Update the owning Infrastructure adapter and accepted agent operation only if their contracts require it.
4. Update the page/form or Console adapter that calls the use case, without copying the business rule into each transport.
5. Run relevant unit/contract checks, then verify generated configuration, the actual service result and failure recovery on a disposable supported target.

This example does not define a new API endpoint, IPC operation, CLI name or production file allowlist.

## Before editing a server configuration

Locate the versioned template and owning adapter here. An applied file under /etc belongs to the installed machine and its managed-configuration boundary. Durable settings, service state, secret references and operator overrides must be reconciled by the approved lifecycle; copying /etc into Git or editing a generated file is not a source change.

A directory name never enables an engine, installs its Debian package or authorizes privileged execution. [Current specifications and gates](../documentation/manifests/WCP360-BM-0001.md) remain authoritative.
