# WCP360 Core

**WCP360** is the project name. **WCP360 Standalone** is the hosting control panel installed on a server. **WCP360 Core** is its built-in application engine, maintained in `WCP360_Core/`; it must operate without optional plugins. The local checkout directory name does not rename the product.

**Status: an executable development candidate is available; the complete hosting engine and production release remain unqualified.**

The [Core delivery roadmap](ROADMAP.md) records the current completion estimate, implemented candidate paths, remaining work, dependency order and acceptance criteria. The [capability register](CAPABILITY-MAP.md#delivery-snapshot) assigns a delivery status to each of the 233 capabilities; source-folder coverage is separate from product completion.

Start with the [candidate run guide](../documentation/development/EXECUTABLE-CANDIDATE.md) for Docker/native setup, account enrollment, job processing, plugin lifecycle and the exact remaining acceptance work. Symfony 7.4 LTS/PHP 8.4, Twig, Turbo/Stimulus, candidate persistence, a Go plan builder and automated checks are now implemented on the dedicated draft branch.

The [checkout installer](installer/recipes/README.md) now records private durable checkpoints, prevents concurrent runs and provides read-only `--status` plus explicit `--resume`. It verifies source/configuration identity, rebuilds repeatable preparation outputs and blocks automatic replay after failed or interrupted database migrations. It prepares an existing trusted development checkout; native production provisioning and the graphical first-run wizard remain separate work.

[Username authentication](src/Module/IdentityAccess/README.md#username-authentication) uses a hosting username, separate from the contact email, in both USER and ADMIN. Apply the [username migration](migrations/Version20261101000500.php) before starting the updated application; existing accounts retain their IDs, password hashes, permissions and MFA. Previous email-based sessions must sign in again.

[Administrator MFA](src/Module/IdentityAccess/README.md#administrator-mfa-candidate) is **optional per account and off by default**. Password-only administrators can use the Core; account security offers password-confirmed TOTP activation, encrypted seeds, replay/attempt controls and ten single-use backup codes. Once enabled, the second factor is required at each login for that account until password-confirmed disablement. Sensitive mutations require authentication less than five minutes old: password alone when MFA is off, or completed MFA when it is on, plus exact Origin and form CSRF. Identity confirmation reuses the normal login pipeline and returns to the dashboard for an explicit action retry. Shared password-confirmation limits also protect MFA settings and own-session revocation. Fresh checkout installation creates an independent `WCP_MFA_KEY`; missing encryption configuration only blocks MFA setup or access to accounts with MFA enabled. Password recovery, complete permissions, canonical production integration and Unix/PostgreSQL identity separation remain open, so M1 is still incomplete.

[Own-account password changes](src/Module/IdentityAccess/README.md#own-account-password-change) are available at `/account/password` for fully authenticated USER and ADMIN accounts in the shared-database candidate. Current-password confirmation, CSRF/Origin checks and the shared attempt budget protect the update; credential replacement, all own-context session revocations and audit commit together. Enabled MFA is preserved. Isolated database mode remains unavailable without widening its credential grants. Forgotten-password recovery and production integration remain open.

The [candidate authorization boundary](src/Module/IdentityAccess/README.md#candidate-authorization-and-access-blocks) now shares the same current-database rule across tenant visibility, HTTP/API use cases and queued hosting-plan execution. It recognizes only `candidate.hosting.plan`: an active USER account needs the exact tenant membership and no explicit access block. Authenticated administrators can list memberships and add or remove audited blocks at `/workbench/access`; removing a block never creates a membership. A worker/setup-only CLI provides a read-only decision. Older checkouts must apply [the new candidate migration](migrations/Version20261101000200.php) before using these checks. This bounded candidate does not implement the canonical permission registry, role/delegation engine or production identity separation.

The [candidate session registry](src/Module/IdentityAccess/README.md#candidate-session-inventory-and-revocation) adds `/account/sessions` for USER and authenticated ADMIN accounts. Each account can inspect and revoke its own sessions in the current browser context, with exact Origin, target-bound CSRF and current-password confirmation; administrator changes also require recent password authentication, or recent MFA when enabled. Durable, separate candidate tables store session metadata and a SHA-256 binding, while Symfony's session payload remains in server-side files. Older checkouts must apply [Version20261101000300](migrations/Version20261101000300.php) and sign in again: existing cookies are never automatically registered. Revocation blocks subsequent admitted requests; complete production session contracts, database grants and in-flight effect fencing remain open.

The standalone [runtime identity inspection](installer/preflight/README.md#runtime-identity-inspection) compares live Linux credentials with the accepted USER, ADMIN, worker, wcpd or wcp-privd identity profile. It checks all four UID/GID credentials, actual supplementary groups, canonical account/group separation, no-login service accounts and a conservative process-hardening baseline. It loads no application configuration and grants no authority. This diagnostic does not create identities, enforce runtime admission, qualify the root broker or establish filesystem/PostgreSQL isolation.

The optional [candidate database profiles](config/contexts/DATABASE-PROFILES.md) select a private PostgreSQL credential for USER, ADMIN, worker or setup. Before admitting a new isolated application connection, a DBAL gate checks role restrictions and effective candidate session-table privileges: USER/ADMIN require their own CRUD and no access to the opposite registry; workers require no access to either. Shared development access remains the default. The [maintenance-only role recipe](installer/recipes/CANDIDATE-DATABASE-ROLES.md) can now create fresh development roles with explicit rights over the 16 candidate tables, constant-zero row-lock markers and an atomic audit event. Native production grant provisioning, canonical database policy and production qualification remain open.

The standalone [native-installation inspection](installer/preflight/README.md#read-only-native-installation-handoff) observes the actual target, available memory, `/var` free space, TCP/UDP sockets and canonical product paths before Composer installation. Operator-selected checks expose conflicts and unknowns; no production defaults are assumed. The report keeps native application blocked until its provider, artifacts and controlled installation effects are implemented and qualified.

The [offline candidate artifact verifier](installer/preflight/README.md#offline-candidate-artifact-verification) checks an Ed25519 signature using an explicit operator public-key pin, binds the expected component/version/target and streams payload size/SHA-256 verification. It reads protected local files without extracting or installing them. Successful verification authenticates observed bytes under that pin; official publisher trust, revocation, immutable staging and native installation remain separate requirements.

The Core owns essential hosting, local administration/recovery and the extension manager. It must run with zero optional plugins. Detached packages use the public SDK/API and their own processes; installing a plugin does not modify the Core Composer graph or inject its code into Symfony.

Start with the [change-location index](NAVIGATION.md) to find a function, command, dependency or Linux-service configuration. The [dependency guide](DEPENDENCIES.md) identifies manifests, locks and generated libraries.

See [application-server profiles](../documentation/development/APPLICATION-SERVERS.md) for PHP-FPM defaults, per-site FrankenPHP Worker plans, compatibility and runtime-isolation requirements.

See [isolated provisioning](../documentation/development/ISOLATED-PROVISIONING.md) for the real-effects PHP-FPM experiment and [testing](../documentation/development/TESTING.md) for PHPUnit, PHPStan and Panther commands.

The operating-system targets are **Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS**. The [platform guide](../documentation/development/SUPPORTED-PLATFORMS.md) separates native-package probes from a complete PHP 8.4 Core installation and production qualification. The shared catalogue lives in `config/platforms/targets.json`; OS detection does not authorize privileged installation or relax the Debian 13 VM guards.

The [Core runtime matrix](../documentation/development/CORE-RUNTIME-MATRIX.md) uses the reviewed PHP 8.4 source profile from `config/platforms/runtime-providers.json`, compiled inside each target userspace, with a real PostgreSQL 17 verification service. It covers non-root checkout preparation, application checks, enrollment, real Nginx/FPM HTTP, jobs, reference-plugin lifecycle and logical database recovery. It remains development verification; native host installation and production runtime providers need separate qualification. Inspect exact per-profile job outcomes before treating any configured check as passing.

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
│   ├── platforms/               # Exact target catalogue; native package families and profile IDs
│   ├── routes/
│   └── contexts/
├── resources/services/          # Seventeen documented Linux-service source locations
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
├── tools/phpstan/                  # Synthetic analysis container and rule-regression fixtures
└── installer/
    ├── preflight/
    ├── recipes/
    ├── upgrades/
    ├── recovery/
    └── scaffolds/               # Historical guarded templates, not stable installers
```

The candidate supplies real composer.json/composer.lock, Kernel, HTTP/CLI entry points, configuration, migration and commands. It requires WCP_CANDIDATE=1 with APP_ENV=dev/test and refuses production boot. The experimental wcp_candidate_* tables and /workbench routes are separate from canonical production schemas/contracts. Composer generates vendor/; var/, public/assets/ and build/cache outputs are not source.

## README capability coverage

The [capability-to-owner map](CAPABILITY-MAP.md) accounts for all 233 README capabilities across 21 functional domains. They are grouped into thirteen Core modules, with shared HTTP/security primitives and chart components explicitly indexed. It also distinguishes the 149 proposed catalog entries from implemented plugin packages. This is ownership/structure coverage, not functional acceptance.

## Configuration and commands

Symfony configuration belongs in config; Linux-service template/profile sources belong in [resources/services](resources/services/README.md). Applied /etc files and machine-specific secrets/state stay outside source. [The navigation index](NAVIGATION.md) maps all thirteen built-in domains and seventeen service-resource locations to their owners.

Business functions are cohesive services in the owning module. CLI adapters belong in that module's UI/Console layer and call the same authorized use cases as HTTP. bin contains entry points; agents contain the accepted controlled system operations. See the [command guide](bin/README.md).

## Frontend, installation and verification locations

- [Frontend sources](assets/README.md): controllers, styles and first-party images.
- [Twig sources](templates/README.md): context-specific pages, layouts, safe error pages and shared forms/tables/charts/feedback/navigation.
- [Installer source map](installer/README.md): preflight, recipes, supported upgrades, interrupted-install recovery and historical scaffolds.
- [Test source map](tests/README.md): unit, contract, integration, security, lifecycle, installation, browser and synthetic-fixture locations.

The candidate implements a Symfony bootstrap, selected application suites, inert hosting configuration plans and development-only installer/recovery helpers. Other domains remain navigation/responsibility documents; they are not completed features or production-qualified adapters. Generated dependencies/caches and machine state remain outside versioned source.

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
- [Tests and static analysis](../documentation/development/TESTING.md)
- [Target operating systems and qualification](../documentation/development/SUPPORTED-PLATFORMS.md)
- [Five-platform PHP 8.4 Core runtime verification](../documentation/development/CORE-RUNTIME-MATRIX.md)
- [Engineering quality and security toolchain](../documentation/development/QUALITY-SECURITY-STACK.md)
- [Security test and release evidence matrix](../documentation/development/SECURITY-TEST-MATRIX.md)
- [Current baseline manifest](../documentation/manifests/WCP360-BM-0001.md)
- [Architecture](../ARCHITECTURE.md)

### Debian VM verification and embedded demo application

The isolated provisioning candidate supports a separate [disposable Debian 13 VM profile](../documentation/development/DEBIAN-VM.md), using the distribution PHP-FPM package and explicit boot-bound root intent. The shared harness deploys a reviewed embedded PHP demo with sessions, CSRF and tenant-private persistent state. This does not enable arbitrary application uploads or production installation.

### Candidate service recovery

The disposable Debian VM uses [systemd supervision and strict recovery](../documentation/development/SYSTEMD-RECOVERY.md) for confirmed sites. An explicit machine-bound root opt-in permits boot re-arming only in the test VM. Changed/partial state refuses recovery; production installation and power-loss qualification remain separate.
