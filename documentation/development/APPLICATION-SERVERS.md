# Hosted PHP application servers

Status: experimental candidate for PR #116. This design extends hosting-plan preparation; it neither installs a production runtime nor changes accepted IPC/API/schema contracts.

## Selection and scope

| Setting | Behavior |
| :--- | :--- |
| Initial global default | PHP-FPM. Existing candidate sites migrate to this value. |
| Global administrator setting | PHP-FPM or FrankenPHP Worker; revision-checked, currently-active-admin-only, CSRF-protected and audited. Applies to subsequent new plans. |
| Per-site request | Inherit the current global default, explicitly select PHP-FPM, or explicitly select FrankenPHP Worker. A tenant can own several sites with different choices. |
| Worker availability | WCP_ALLOW_FRANKENPHP_PLANS=0 by default. An operator must set it to 1 in USER, ADMIN and job-worker environments to experiment with worker plans. This is a planning switch, not runtime installation or qualification. |
| Worker acknowledgement | Every worker site, including an inherited selection, requires explicit confirmation of application compatibility and per-request state cleanup. This declaration is not an automated compatibility check. |
| Existing sites and jobs | The resolved mode is stored on the site. Changing the global default never migrates an existing site or rewrites a queued plan. |
| Idempotency | Hash the caller's requested mode and acknowledgement. A retry with the same intent returns the original job before resolving today's default; a changed intent with the same key conflicts. Legacy requests without optional fields retain their fingerprint. |
| Core itself | Remains on its current request-scoped HTTP bootstrap. This hosted-site selector does not switch ADMIN or USER into a persistent process. |

For native development, migrate the database, build the Go planner, then set WCP_ALLOW_FRANKENPHP_PLANS=1 in the private environment of all three participating processes. In Compose, set it in the private Compose .env and recreate user, admin and worker. The admin dashboard exposes the global default; the user hosting form exposes the site override. Rebuild compiled frontend assets after changing their source.

Both modes currently generate inert plans for lowercase ASCII .test hostnames. No tenant input selects an executable, worker filename, arbitrary PHP directive, service name or output path.

## Choosing a mode

| Profile | Suitable applications | Required qualification |
| :--- | :--- | :--- |
| PHP-FPM | Ordinary request-scoped PHP applications, WordPress and legacy scripts, subject to their PHP version/extensions and application requirements. | Pool identity, filesystem permissions, extensions, limits, health, request behavior and upgrade/recovery tests. No universal compatibility guarantee. |
| FrankenPHP Worker | Applications with an explicit persistent-worker bootstrap and reliable per-request reset behavior, such as a correctly integrated Symfony application. | Repeated and concurrent requests, identity/session changes, exceptions, reset hooks, memory growth, recycling, background work and drain behavior. Benchmark the actual workload; no latency or throughput multiplier is promised. |

FrankenPHP also has a classic mode; this selector deliberately exposes only the two profiles above. FrankenPHP is a Go/Caddy application server and is independent of WCP360's Go system daemons. PHP version/extensions must be provided by the selected runtime; selecting a label does not make arbitrary extensions compatible with its ZTS build.

Persistent application memory can retain globals, static properties, service instances and caches. Cleanup belongs to the application/framework; recycling reduces accumulation but cannot prevent data leakage within one worker's lifetime. Symfony 7.4's native integration uses its Runtime component and the appropriate entry point. The candidate Core currently constructs a fresh Kernel manually, so running its existing public/index.php as a persistent worker is not supported.

## Process and network boundary

~~~mermaid
flowchart LR
    Browser --> Edge[Nginx: edge TLS and host routing]
    Edge -->|FastCGI Unix socket| FPM[PHP-FPM: isolated pool]
    Edge -->|HTTP Unix socket| Worker[FrankenPHP / Caddy: dedicated site process]
    Worker --> App[Explicitly compatible application worker]
    Admin[Authorized configuration] --> Plan[Durable inert plan]
    Plan -. future reviewed lifecycle .-> Broker[wcpd / wcp-privd]
~~~

The worker plan emits:

- A fixed Nginx upstream pointing at /run/wcp360-frankenphp/<site UUID>/http.sock. Hidden paths and direct PHP-path requests are denied; application routes enter through the fixed front controller.
- A Caddy configuration bound only to that Unix socket, with the Caddy admin API and automatic HTTPS disabled. Nginx owns edge TLS. Worker count, total threads, PHP memory and recycling budgets are bounded.
- An inert systemd unit for one process and Unix identity per site, with no capabilities, no-new-privileges, private temporary storage, a read-only system view and process/memory limits.

Different sites never share a FrankenPHP process or use worker threads as a tenant security boundary. The application stays in its own group; a reviewed provisioner may grant Nginx access to that specific socket group. Adding the application to Nginx's shared group would expose other sites and is forbidden by this profile. Runtime directories and socket modes must agree with this model. Site code and the root-managed Caddyfile are not application-writable.

The fixed worker script is public/index.php. These paths are candidate-owned examples, not a newly approved production file allowlist. PHP-FPM output keeps plan schema wcp360.candidate.plan/1; worker output uses wcp360.candidate.plan/2. The Go planner validates bounded typed input and renders text; it performs no system calls, filesystem changes, network listening or privileged execution.

## Safe runtime transition to implement

Changing a live site's engine is a separate future lifecycle operation. It must authorize and check the current revision, verify installed extensions and bootstrap compatibility, render and syntax-check staged configuration, start the new isolated process, check health, switch only the intended upstream, drain old requests, then retire the old runtime. Record observed outcomes and preserve a tested rollback where possible. Unknown outcomes require reconciliation rather than blind retry.

Cache invalidation, worker restart after code/configuration/secret changes, protected trusted-proxy settings, deployment hooks, metrics, log isolation, backups and verified rollback remain required for production. The existing plan form does not pretend to perform these steps.

## Platform support and verification

| Platform | Current evidence boundary |
| :--- | :--- |
| Debian 13 / amd64 | Candidate container tests for PHP-FPM and a separate immutable official FrankenPHP/PHP 8.4 ZTS image. Consult the exact CI run for pass/fail; this is not a complete fresh-host or production qualification. |
| Ubuntu | Planned adapter and test matrix: explicitly select supported releases, package sources, PHP/ZTS extensions, service units, permissions and recovery behavior. Not qualified. |
| AlmaLinux | Planned RPM/DNF and systemd adapter with supported-version selection, SELinux labels/policy, package provenance and recovery tests. SELinux must remain enabled; no disabling workaround. Not qualified. |

The application suite checks defaults, migration of old sites, malformed choices, tenant/admin authority, revision conflicts, compatibility acknowledgement, idempotency across global changes, availability rechecks and inert worker plans.

The separate Linux smoke image validates the generated Caddyfile, starts a non-root FrankenPHP worker behind real Nginx over a site-owned 0660 Unix socket, sends successive requests through the same persistent worker and verifies explicit application cleanup. It also checks neighbor-file read denial, hidden-file/direct-PHP rejection and absence of Caddy TCP/admin listeners. This small fixture proves the tested profile; it does not establish that an arbitrary customer application is worker-safe. The systemd unit is emitted but not activated in this probe.

## Source locations

| Concern | Source |
| :--- | :--- |
| Mode, policy and plan submission | WCP360_Core/src/Module/Hosting/{Domain,Application}/ |
| Global default and current admin authority | Platform/Application/ApplicationServerSettings and Operations/Infrastructure/WorkbenchStore |
| Upgrade | WCP360_Core/migrations/Version20261031000300.php; earlier migrations remain unchanged |
| Go protocol and authoritative candidate templates | WCP360_Core/agents/wcpd/candidate/planner/ |
| Shared field and pages | WCP360_Core/templates/components/forms/application-server.html.twig and templates/user/ |
| Verification | ApplicationServerTest, ApplicationServerUiTest, Go tests and WCP360_Build/verification/candidate-frankenphp-smoke.sh |

Primary references: [FrankenPHP worker lifecycle](https://frankenphp.dev/docs/worker/), [configuration](https://frankenphp.dev/docs/config/), [Symfony integration](https://frankenphp.dev/docs/symfony/), [Symfony Runtime](https://symfony.com/doc/7.4/components/runtime.html), [Caddy Unix listener syntax](https://caddyserver.com/docs/conventions), [official Docker images](https://frankenphp.dev/docs/docker/).
