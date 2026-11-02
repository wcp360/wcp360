# WCP360 Core

**WCP360** is the project name. **WCP360 Standalone** is the hosting control panel installed on a server. **WCP360 Core** is its built-in application engine, maintained in `WCP360_Core/`; it must operate without optional plugins. The local checkout directory name does not rename the product.

**Status: an executable development candidate is available; the complete hosting engine and production release remain unqualified.**

The [Core delivery roadmap](ROADMAP.md) records the current completion estimate, implemented candidate paths, remaining work, dependency order and acceptance criteria. The [capability register](CAPABILITY-MAP.md#delivery-snapshot) assigns a delivery status to each of the 233 capabilities; source-folder coverage is separate from product completion.

The [Core structure and growth index](STRUCTURE.md) maps the existing modules to CLI, local licensing/premium packages, containers, Toolbox, optional AI/models/MCP and future node scaling. Prepared design/resource folders record ownership and delivery gaps; they do not enable those runtimes or change capability status.

Start with the [candidate run guide](../documentation/development/EXECUTABLE-CANDIDATE.md) for Docker/native setup, account enrollment, job processing, plugin lifecycle and the exact remaining acceptance work. Symfony 7.4 LTS/PHP 8.4, Twig, Turbo/Stimulus, candidate persistence, a Go plan builder and automated checks are now implemented on the dedicated draft branch.

The [checkout installer](installer/recipes/README.md) now records private durable checkpoints, prevents concurrent runs and provides read-only `--status` plus explicit `--resume`. It verifies source/configuration identity, rebuilds repeatable preparation outputs and blocks automatic replay after failed or interrupted database migrations. It prepares an existing trusted development checkout; native production provisioning and the graphical first-run wizard remain separate work.

[Username authentication](src/Module/IdentityAccess/README.md#username-authentication) uses a hosting username, separate from the contact email, in both USER and ADMIN. Apply the [username migration](migrations/Version20261101000500.php) before starting the updated application; existing accounts retain their IDs, password hashes, permissions and MFA. Previous email-based sessions must sign in again.

[Administrator MFA](src/Module/IdentityAccess/README.md#administrator-mfa-candidate) is **optional per account and off by default**. Password-only administrators can use the Core; account security offers password-confirmed TOTP activation, encrypted seeds, replay/attempt controls and ten single-use backup codes. Once enabled, the second factor is required at each login for that account until password-confirmed disablement. Sensitive mutations require authentication less than five minutes old: password alone when MFA is off, or completed MFA when it is on, plus exact Origin and form CSRF. Identity confirmation reuses the normal login pipeline and returns to the dashboard for an explicit action retry. Shared password-confirmation limits also protect MFA settings and own-session revocation. Fresh checkout installation creates an independent `WCP_MFA_KEY`; missing encryption configuration only blocks MFA setup or access to accounts with MFA enabled. Password recovery, complete permissions, canonical production integration and Unix/PostgreSQL identity separation remain open, so M1 is still incomplete.

[MFA backup-code renewal](src/Module/IdentityAccess/README.md#renewing-mfa-backup-codes) lets an administrator replace lost codes while retaining the authenticator. Current password, recent MFA, exact Origin and CSRF protect an atomic replacement, session revocation and audit; the new codes are shown once after commit and normal login is required. MFA remains optional and disabled by default.

[Own-account password changes](src/Module/IdentityAccess/README.md#own-account-password-change) are available at `/account/password` for fully authenticated USER and ADMIN accounts in the shared-database candidate and the explicitly configured `session-isolated` PostgreSQL profile. Current-password confirmation, CSRF/Origin checks and the shared attempt budget protect the update; credential replacement, all own-context session revocations and audit commit together. Enabled MFA is preserved. The isolated profile uses [two fixed PostgreSQL operations](src/Module/IdentityAccess/README.md#isolated-postgresql-password-operations); direct writes to credential columns remain denied. Apply the new migration and enable the matching operation through setup maintenance before using it. Forgotten-password recovery and production integration remain open.

[Operator-assisted password recovery](installer/recovery/ACCOUNT-PASSWORD.md) is available through `wcp:candidate:account:recover` in the setup context. A read-only account plan precedes explicit, enabled interactive application; hidden confirmation, stable target identity, conditional credential replacement, own-context session revocation and mandatory audit protect the operation. MFA and backup codes are preserved. This is local candidate maintenance; self-service email recovery and production integration remain open.

[Short-lived recovery codes](installer/recovery/RECOVERY-CODES.md) let the owner choose a replacement password at `/reset-password` after an explicit private operator handoff. Independent USER/ADMIN keys, a 15-minute lifetime, current-state binding, CSRF/Origin, attempt limits and atomic credential/session/audit changes protect the shared-profile candidate. Success invalidates all codes from the old password state and requires normal login with existing MFA. Email delivery, isolated redemption and production integration remain open.

[Shared HTTP errors](src/Shared/Http/README.md) now provide server-generated request references, fixed JSON errors for the existing workbench API and branded browser error pages. Authentication redirects stay in the browser; API clients receive structured refusals. Sensitive exceptions share the same safe renderer, with a plain-text fallback if templates fail. This is candidate transport plumbing, not complete production API/audit correlation.

[Filtered hosting-job history](src/Module/Operations/README.md#filtered-hosting-job-inventory) is available from « Tâches » at `/workbench/jobs` and through the existing workbench API. Tenant/state/domain filters, 25/50/100-row pages and bidirectional cursors expose a minimal read projection. Every query retains current tenant authority. The dashboard and plugin summary reuse the same query with their existing output.

The [candidate authorization boundary](src/Module/IdentityAccess/README.md#candidate-authorization-and-access-blocks) now shares the same current-database rule across tenant visibility, HTTP/API use cases and queued hosting-plan execution. It recognizes only `candidate.hosting.plan`: an active USER account needs the exact tenant membership and no explicit access block. Authenticated administrators can list memberships and add or remove audited blocks at `/workbench/access`; removing a block never creates a membership. A worker/setup-only CLI provides a read-only decision. Older checkouts must apply [the new candidate migration](migrations/Version20261101000200.php) before using these checks. This bounded candidate does not implement the canonical permission registry, role/delegation engine or production identity separation.

The [candidate session registry](src/Module/IdentityAccess/README.md#candidate-session-inventory-and-revocation) adds `/account/sessions` for USER and authenticated ADMIN accounts. Each account can inspect and revoke its own sessions in the current browser context, with exact Origin, target-bound CSRF and current-password confirmation; administrator changes also require recent password authentication, or recent MFA when enabled. Durable, separate candidate tables store session metadata and a SHA-256 binding, while Symfony's session payload remains in server-side files. Older checkouts must apply [Version20261101000300](migrations/Version20261101000300.php) and sign in again: existing cookies are never automatically registered. Revocation blocks subsequent admitted requests; complete production session contracts, database grants and in-flight effect fencing remain open.

[ROOT global settings](src/Module/Platform/README.md#root-global-runtime-settings) provides a dedicated PHP-FPM / FrankenPHP Worker default page and protected browser API. They reuse the existing settings writer, refuse stale revisions, show current values for explicit review and correlate changes with audit references. The choice affects new inheriting plans; existing sites and the panel runtime remain unchanged. Worker-planning availability does not imply installed-service readiness.

[Disconnecting other sessions](src/Module/IdentityAccess/README.md#disconnecting-other-sessions) lets USER and ROOT keep the current browser signed in while revoking their other registrations. The action shares current-password confirmation, exact Origin, its own CSRF token and the existing attempt budget. A locked credential/session/factor recheck and aggregate audit protect the account-scoped update; enabled MFA stays enforced and optional MFA stays optional. It covers all other registrations, including those outside the displayed page, using existing PostgreSQL grants.

The standalone [runtime identity inspection](installer/preflight/README.md#runtime-identity-inspection) compares live Linux credentials with the accepted USER, ADMIN, worker, wcpd or wcp-privd identity profile. It checks all four UID/GID credentials, actual supplementary groups, canonical account/group separation, no-login service accounts and a conservative process-hardening baseline. It loads no application configuration and grants no authority. This diagnostic does not create identities, enforce runtime admission, qualify the root broker or establish filesystem/PostgreSQL isolation.

The optional [candidate database profiles](config/contexts/DATABASE-PROFILES.md) select a private PostgreSQL credential for USER, ADMIN, worker or setup. Before admitting a new isolated application connection, a DBAL gate checks role restrictions and effective candidate session-table privileges: USER/ADMIN require their own CRUD and no access to the opposite registry; workers require no access to either. Shared development access remains the default. The [maintenance-only role recipe](installer/recipes/CANDIDATE-DATABASE-ROLES.md) can now create fresh development roles with explicit rights over the 16 candidate tables, constant-zero row-lock markers and an atomic audit event. Native production grant provisioning, canonical database policy and production qualification remain open.

The standalone [native-installation inspection](installer/preflight/README.md#read-only-native-installation-handoff) observes the actual target, available memory, `/var` free space, TCP/UDP sockets and canonical product paths before Composer installation. Operator-selected checks expose conflicts and unknowns; no production defaults are assumed. The report keeps native application blocked until its provider, artifacts and controlled installation effects are implemented and qualified.

The [offline candidate artifact verifier](installer/preflight/README.md#offline-candidate-artifact-verification) checks an Ed25519 signature using an explicit operator public-key pin, binds the expected component/version/target and streams payload size/SHA-256 verification. It reads protected local files without extracting or installing them. Successful verification authenticates observed bytes under that pin; official publisher trust, revocation, immutable staging and native installation remain separate requirements.

The Core owns essential hosting, local administration/recovery and the extension manager. It must run with zero optional plugins. Detached packages use the public SDK/API and their own processes; installing a plugin does not modify the Core Composer graph or inject its code into Symfony.

Start with the [change-location index](NAVIGATION.md) to find a function, command, dependency or Linux-service configuration. The [dependency guide](DEPENDENCIES.md) identifies manifests, locks and generated libraries.

## Hosting web stacks

The [adopted web-server strategy](src/Module/Hosting/WEB-SERVER-STRATEGY.md) sets **Nginx → Apache event → PHP-FPM** as the target default for new general-purpose installations, with controlled native .htaccess handling for every request, including static files. The [web-server roadmap](src/Module/Hosting/WEB-SERVER-ROADMAP.md) covers its implementation, alternative PHP stacks, managed native/container applications, Docker/Discourse and measured performance acceptance.

The table below describes the **current candidate**, whose default remains Nginx → PHP-FPM. The new three-layer topology is not implemented or selectable yet; adopting the strategy changes no service or stored assignment. The Core currently uses one [stack catalogue](src/Shared/Platform/WebStackCatalogue.php) across installer inspection, ROOT settings and the hosting-plan form. Stack topology and PHP execution mode are separate concepts.

| Profile | Intended topology | Current implementation |
| :--- | :--- | :--- |
| **Classic — current candidate default** | Nginx + PHP-FPM; managed multi-version PHP is the target. | Candidate plans and separately guarded isolated FPM activation. One reviewed PHP series today; complete multi-PHP management remains open. |
| **Modern** | Standalone Caddy/FrankenPHP Worker, replacing the web server and PHP-FPM. | Separate inert Go driver and ROOT preview implemented for one loopback-only synthetic site. Durable selection, listeners/TLS ownership and activation remain unavailable. |
| **Legacy / compatibility** | Apache + PHP-FPM for Apache-specific application behavior. | Planned/unavailable. Any future mod_php variant requires its own explicit isolation and qualification decision. |
| **Advanced mix — optional** | An edge proxy with a separately managed backend. | Existing opt-in Nginx → Caddy/FrankenPHP Worker plans; activation unavailable. Other compositions remain planned. |

FrankenPHP embeds PHP with Caddy and can serve directly; Worker mode requires application-state reset and bounded recycling. HTTP/3 and automatic HTTPS depend on the selected public listeners, networking, TLS ownership and configuration; they are not enabled by catalogue membership. See the official [migration guide](https://frankenphp.dev/docs/migrate/) and [Worker documentation](https://frankenphp.dev/docs/worker/).

The [ROOT Modern preview](src/Module/Hosting/README.md#standalone-modern-preview) prepares a Caddyfile and a systemd unit through the separate plan/3 driver. It requires operator Worker opt-in and compatibility acknowledgement. A new synthetic identifier and fixed, unreserved HTTP loopback port prevent the preview from selecting an existing resource; no site, job, configuration or service is created. Browser and API access retain current ROOT authentication, fresh password/MFA, exact Origin and a dedicated CSRF intent. Modern is still unavailable as a persisted hosting-plan choice.

[Reusable PHP application profiles](src/Module/Hosting/APPLICATION-PROFILES.md) now separate ordinary scripts, front-controller frameworks and the WordPress reference case from six Nginx/Apache/Caddy × PHP-FPM/FrankenPHP-classic recipes. ROOT can inspect their generated configuration without creating a site or service. Optional native syntax checks now report exact artifact hashes and separate Nginx grammar, Caddy adaptation, unavailable providers and unexecuted runtime stages; they never authorize activation. Native .htaccess requirements are explicit, Worker mode stays application-specific and dynamic page caching remains disabled until privacy/purge/bypass rules and benchmarks are qualified. The new previews do not change durable engine selectors or establish live compatibility/performance.

The [application-catalogue bridge](src/Module/Hosting/APPLICATION-CATALOGUE.md) reads the sibling [WCP360_App_Deploy workspace](../WCP360_App_Deploy/README.md) through a bounded local CSV source. Worker/setup CLI inspection preserves classifications across 410 editorial references and reports installation/deployment unavailable. Versioned draft recipes and lifecycle guides live in that workspace; they are separate from the six PHP topology previews.

[Persisted site profiles](src/Module/Hosting/README.md#persisted-site-runtime-profiles) now record `web_stack` and `stack_revision=1` explicitly. Apply [Version20261102000200](migrations/Version20261102000200.php) through setup maintenance before running this source: existing FPM sites become Classic and existing Worker sites become Advanced, preserving their IDs, lifecycle revisions and stored plans. Workers, exports and isolated FPM activation validate the metadata; a profile change observed during rendering prevents successful publication. This prerequisite does not enable Modern selection or stack switching.

Existing `frankenphp_worker` sites and plan/2 keep their Nginx-edge meaning. Their current Caddy socket backend disables automatic HTTPS and the admin API. A future standalone Modern profile must not silently reinterpret or migrate those assignments. Changes between stacks need typed persisted profile/revision, versioned Go templates, config diff, capacity/compatibility checks, drain, health verification and recovery. A global default affects only new inheriting plans; the panel's own runtime stays separate.

Use `php installer/preflight/runtime-plan.php --web-stacks` from the Core directory to inspect the catalogue. ROOT also exposes `GET /workbench/v1/web-stacks`. These are read-only candidate capabilities, not a production installer or general migration workflow. [Hosting details](src/Module/Hosting/README.md#web-stack-profiles-and-development-direction) and the [preflight guide](installer/preflight/README.md#hosting-web-stack-inspection) explain the boundaries.

See [application-server profiles](../documentation/development/APPLICATION-SERVERS.md) for PHP-FPM defaults, per-site FrankenPHP Worker plans, compatibility and runtime-isolation requirements.

See [isolated provisioning](../documentation/development/ISOLATED-PROVISIONING.md) for the real-effects PHP-FPM experiment and [testing](../documentation/development/TESTING.md) for PHPUnit, PHPStan and Panther commands.

The operating-system targets are **Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS**. The [platform guide](../documentation/development/SUPPORTED-PLATFORMS.md) separates native-package probes from a complete PHP 8.4 Core installation and production qualification. The shared catalogue lives in `config/platforms/targets.json`; OS detection does not authorize privileged installation or relax the Debian 13 VM guards.

The [Core runtime matrix](../documentation/development/CORE-RUNTIME-MATRIX.md) uses the reviewed PHP 8.4 source profile from `config/platforms/runtime-providers.json`, compiled inside each target userspace, with a real PostgreSQL 17 verification service. It covers non-root checkout preparation, application checks, enrollment, real Nginx/FPM HTTP, jobs, reference-plugin lifecycle and logical database recovery. It remains development verification; native host installation and production runtime providers need separate qualification. Inspect exact per-profile job outcomes before treating any configured check as passing.

[Hosting-plan request correlation](src/Module/Operations/README.md#durable-hosting-plan-request-correlation) now retains the first submission reference in the job and its queued/started/outcome audit occurrences. The authorized task detail shows this creation reference; retries preserve the original, and historical rows remain empty. Apply [Version20261102000100](migrations/Version20261102000100.php) before using this source. This is candidate diagnostics, not a permission or complete distributed tracing.

[Atomic plan transitions](src/Module/Operations/README.md#atomic-plan-transitions-and-bounded-recovery) now separate queue persistence from Go rendering, reject uncommitted caller transactions and prevent stale completion from overwriting an uncertain outcome. Interruption maintenance handles at most 100 plans per call, counting only actual changes; stop rendering workers first. A revocation observed at publication discards the result and records failure. General leases, retries and production fencing remain unfinished.

[Prepared-site inventory](src/Module/Hosting/README.md#prepared-site-inventory) is available at `/workbench/sites` and `/workbench/v1/sites`: tenant-scoped domain search, preparation/runtime filters, deterministic A–Z order, cursor pages and links to task history. It reuses shared list validation and pagination with the task inventory. A prepared plan does not assert deployed-service health; complete hosting/domain lifecycle remains unfinished.

[Prepared-plan files](src/Module/Hosting/README.md#prepared-plan-file-inspection-and-downloads) can now be previewed and downloaded individually from a successful task: Nginx/PHP-FPM or Nginx/Caddy/systemd for Worker plans. The renderer and export path share one bounded document validator; attachments require current tenant access and never read/write server files or apply configuration. Corrupt or unfinished output cannot be exported.

The [administrator audit journal](src/Module/Observability/README.md) provides bounded event and request-reference inspection in the WCP360 ADMIN interface. Restricted PostgreSQL reads require a separate explicit setup opt-in; default role grants remain unchanged.

The ADMIN surface represents the [server root administrator](src/Module/IdentityAccess/README.md#server-root-administrator) and is presented as ROOT. Its [server overview](src/Module/Platform/README.md#root-server-overview) shows observed OS, architecture, memory and storage capacity; the PHP runtime remains separated from privileged Linux execution.

The [ROOT hosting account inventory](src/Module/IdentityAccess/README.md#root-hosting-account-inventory) lists Core login identities with bounded search/state filters and opens the selected user's existing access-management workflow. Account metadata reads require current ROOT authority and preserve default PostgreSQL grants.

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
