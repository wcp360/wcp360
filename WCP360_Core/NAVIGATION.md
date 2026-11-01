# Find and change Core code

Start here to locate a change. This index documents the intended source locations; the experimental Symfony candidate implements the selected paths in the [run guide](../documentation/development/EXECUTABLE-CANDIDATE.md); other domains remain documentation scaffolds. Production Linux provisioning is not implemented.

The [README capability map](CAPABILITY-MAP.md) traces 233 capabilities across 21 functional domains to their Core owners and distinguishes proposed plugins from missing source folders.

## Find the right owner

| I need to change… | Start here | Then follow |
| :--- | :--- | :--- |
| Current completion estimate, delivered paths and remaining Core work | [Core roadmap](ROADMAP.md) | Weighted maturity, ordered milestones, acceptance criteria and the [233-capability delivery register](CAPABILITY-MAP.md#delivery-snapshot) |
| A business function or validation rule | [Module index](src/Module/README.md) | Owning module's Domain/ or Application/ |
| A function reused across several domains | [Shared foundations](src/Shared/README.md) | Shared Http/Security primitives where applicable; keep domain-specific policy in its module |
| A panel CLI command | [Command guide](bin/README.md) | src/Module/<Domain>/UI/Console/ calling the same application use case as HTTP |
| A worker/job handler | [Operations](src/Module/Operations/README.md) and the affected domain | Shared lifecycle in Operations; resource decisions in the owning module |
| An API controller or form | [Module layout](src/Module/README.md) | UI/Http/ or UI/Form/; authorization and invariants stay server-side |
| Symfony packages, service wiring or routes | [Application configuration](config/README.md) | config/packages/, config/routes/ and reviewed service imports |
| Debian/Ubuntu target recognition and package differences | [Platform guide](../documentation/development/SUPPORTED-PLATFORMS.md) | config/platforms/targets.json, Shared/Platform/TargetPlatform.php and installer/preflight; native package probes remain separate from privileged VM admission |
| PHP 8.4 runtime planning, PostgreSQL readiness and five-target Core checks | [Core runtime matrix](../documentation/development/CORE-RUNTIME-MATRIX.md) | config/platforms/runtime-providers.json, Shared/Platform/RuntimePlan.php and RuntimeReadiness.php, standalone preflight commands and WCP360_Build/environments/platform-core/ |
| Read-only host inventory and blocked native-installation handoff | [Native-plan preflight](installer/preflight/README.md#read-only-native-installation-handoff) | Shared/Platform/LinuxHostInventory.php, Setup/Application/NativeInstallationPlan.php and installer/preflight/native-plan.php; requested ports or explicit none, available-memory and /var free-space thresholds; no host mutation or native-provider qualification |
| Offline candidate artifact signature, identity and payload checks | [Artifact preflight](installer/preflight/README.md#offline-candidate-artifact-verification) | Setup/Application/CandidateArtifactVerifier.php, Setup/Infrastructure/ArtifactInput.php and installer/preflight/artifact.php; operator-pinned Ed25519 key, exact signed manifest and actual payload size/SHA-256; no download, extraction, native application or release qualification |
| ADMIN, USER or worker identity/settings | [Runtime contexts](config/contexts/README.md) | Explicit per-context access, sessions, secrets and connections |
| Nginx/Apache virtual hosts or PHP pools | [Linux services](resources/services/README.md) | nginx/, apache/, php-fpm/ and the Hosting module |
| PHP-FPM / FrankenPHP Worker selection | [Application-server guide](../documentation/development/APPLICATION-SERVERS.md) | Hosting policy, Platform default settings, shared Twig field and Go candidate templates |
| Mail servers and spam policy | [Mail](src/Module/Mail/README.md) | postfix/, dovecot/, rspamd/ resources plus the owning adapter |
| MySQL, MariaDB or PostgreSQL service settings | [Data](src/Module/Data/README.md) | mysql/, mariadb/, postgresql/ resources; tenant and control-plane grants stay separate |
| Redis, DNS, SSH, firewall or supervision | [Service index](resources/services/README.md) | Typed adapter and approved agent operations, not a PHP shell call |
| Composer, Go, Python or frontend dependencies | [Dependency guide](DEPENDENCIES.md) | Component manifest/lock and its reviewed build procedure |
| An administrator/user page or reusable component | [Templates](templates/README.md) and [assets](assets/README.md) | Domain presenter; layouts/errors and shared form/table/chart/feedback/navigation components; controllers/styles/images source |
| Transfer hosting resources between instances | [Transfers](src/Module/Transfers/README.md) | Coordinate existing account/site/file/database/mail owners and durable jobs; separate from Core schema migrations |
| Database schema | [Migrations](migrations/README.md) and the owning domain | Accepted DB contract, migrations and upgrade/recovery evidence |
| Candidate checkout installation, status or resume | [Installer recipes](installer/recipes/README.md) and [Setup](src/Module/Setup/README.md) | candidate-install.sh / candidate-install.php; Infrastructure/CandidateInstaller.php, InstallationJournal.php and InstallationInputs.php; private var/installer/candidate-install.json |
| Interrupted installation or migration uncertainty | [Recovery](installer/recovery/README.md) | Read-only --status, explicit --resume, input binding and migration reconciliation; no reset flag |
| Production installation, upgrades or a graphical first-run wizard | [Installer](installer/README.md) and [Setup](src/Module/Setup/README.md) | Reserved production boundaries and acceptance requirements; not delivered by candidate checkout preparation |
| Go daemon/broker behavior | [Agents](agents/README.md) | Separate wcpd / wcp-privd components and accepted protocol/privilege contracts |
| Python diagnostics | [Diagnostics](diagnostics/README.md) | Specialized, bounded, non-root tooling |
| Symfony-aware static analysis | [Testing guide](../documentation/development/TESTING.md#symfony-aware-analysis) | phpstan.neon and tools/phpstan/: synthetic Kernel/container preparation, Console loader and deliberately invalid rule fixtures |
| Regression/contract/target tests | [Tests](tests/README.md) | Executable Unit, Integration, Lifecycle and Browser suites; Contract, Security and Installer also retain organizational source locations |
| Artifact assembly or a release | [Build](../WCP360_Build/README.md) | [Qualified release records](../WCP360_Releases/README.md) |

## Source tree

```text
WCP360_Core/
├── NAVIGATION.md                   # This change-location index
├── DEPENDENCIES.md                 # Manifests, locks, generated dependencies
├── src/
│   ├── Shared/                     # Stable shared primitives and policies
│   │   ├── Http/
│   │   └── Security/
│   └── Module/                     # Thirteen named business-domain boundaries
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
│       ├── frankenphp/
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
├── assets/                         # Frontend source; generated output stays out of source
│   ├── controllers/
│   ├── styles/
│   └── images/
├── public/                         # Published entry points/assets only
├── migrations/                     # Reviewed control-plane schema migrations
├── translations/
├── agents/                         # wcpd and wcp-privd source components
├── diagnostics/                    # Non-root Python tools
├── tools/phpstan/                  # Static-analysis bootstrap, Console loader and rule fixtures
├── installer/                      # Installation/bootstrap and recovery sources
│   ├── preflight/
│   ├── recipes/
│   ├── upgrades/
│   ├── recovery/
│   └── scaffolds/                  # Historical non-operational templates
└── tests/                          # Prepared suite/fixture source locations
    ├── Unit/
    ├── Contract/
    ├── Integration/
    ├── Security/
    ├── Lifecycle/
    ├── Installer/
    ├── Browser/
    └── Fixtures/
```

The candidate has real Composer manifests/locks and a bootstrap; vendor/ is generated and ignored, and var/ holds disposable candidate state. No generated dependency or runtime directory is a source placeholder.

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
