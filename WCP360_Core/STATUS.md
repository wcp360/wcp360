# Current WCP360 Core status

**Updated: November 2, 2026. Executable development candidate; complete hosting and production qualification remain open.**

Read [WORKING-GUIDE.md](WORKING-GUIDE.md) to resume work. Use [ROADMAP.md](ROADMAP.md) for dependency order and acceptance, [CAPABILITY-MAP.md](CAPABILITY-MAP.md) for the 233 capability statuses and [NAVIGATION.md](NAVIGATION.md) to locate code.

## Verified executable checkpoint

| Item | Exact reference |
| :--- | :--- |
| Development branch | `implementation/core-executable-foundation-20261031` |
| Review | [Draft PR #116](https://github.com/yyogas/wcp360/pull/116); no merge or production promotion implied. |
| Runtime source | [da265278c20e230d5ba4614647a8271f4b0d7506](https://github.com/yyogas/wcp360/commit/da265278c20e230d5ba4614647a8271f4b0d7506) |
| Source tree | `dc0620b968ca01360f9c8f8d5675b688715a6757` |
| Executed evidence | [Workflow 41460846945](https://github.com/yyogas/wcp360/actions/runs/41460846945): **14/14 jobs successful**. |
| Local execution | Stopped under the current working instruction; documentation/source inspection does not restart it. |
| Production state | Not qualified; `CODE-BASELINE=NONE`. No stable release or `latest` pointer. |

This is a source-bound executable checkpoint. Later documentation edits do not create another runtime qualification result. When new code is verified, replace this checkpoint and its evidence together; do not label an older workflow as the result of a newer source.

## What exists in the candidate

| Area | Implemented scope and detailed owner guide | Remaining boundary |
| :--- | :--- | :--- |
| Application | Symfony 7.4/PHP 8.4, Twig, Turbo/Stimulus, AssetMapper and candidate PostgreSQL persistence; [Core overview](README.md). | Production boot/integration and a complete hosting engine are unqualified. |
| Identity and accounts | Username login, optional administrator MFA, own-password/session flows and bounded recovery; [IdentityAccess](src/Module/IdentityAccess/README.md). | Canonical permissions/delegation, production recovery/identity isolation and independent emergency access remain open. |
| Hosting and execution | Durable candidate jobs, Classic/Advanced profiles, guarded Go planning and isolated provisioning experiments; [Hosting](src/Module/Hosting/README.md), [Operations](src/Module/Operations/README.md). | Real-domain/site lifecycle, production provider admission, effect fencing and general apply/recovery remain incomplete. |
| Web configuration previews | [Seven topology recipes](src/Module/Hosting/APPLICATION-PROFILES.md), including inert Nginx → private Apache event → FPM preview/2 across three routing profiles. | No changed installation default, durable selection or live activation of the new topology; Apache/event/FPM/HTTP acceptance remains open. |
| ROOT observations | [Server capacity](src/Module/Platform/README.md#root-server-overview) and [nine service families/fourteen fixed unit lookups](src/Module/Platform/SERVICES.md). | Current process namespace; no health guarantee or start/stop/restart control. Positive native systemd-host observation still needs distinct evidence. |
| Application catalogue | [Bounded Core bridge](src/Module/Hosting/APPLICATION-CATALOGUE.md) to [410 editorial references](../WCP360_App_Deploy/README.md). | Catalogue entries remain non-installable; recipes, providers, install/update/remove and Docker workload lifecycle are not supplied by this read path. |
| Extensions | Fixed reference-package lifecycle and isolation experiments; [ExtensionManagement](src/Module/ExtensionManagement/README.md). | General trusted package admission, version compatibility, commercial rights and production lifecycle remain incomplete. |
| Setup and recovery | Checkout preparation/checkpoints, native read-only preflight, offline pinned artifact inspection and candidate database backup/restore; [installer](installer/README.md). | No full native production installer/wizard, tenant-wide backup engine or qualified release. |

The existing 13 owners cover all 233 mapped capabilities. Tenancy, Data, Files, Mail, BackupRestore and Transfers still need their own business implementations; existing helpers elsewhere count only for their specific candidate scope.

The 24 [service-resource families](resources/services/ROADMAP.md) contain 18 reservations, five external Go-template references and one family with three disposable-VM systemd units. These are not 24 installed providers. Application, host, observability, Toolbox, extension and AI resources are additional preparation registers.

## Evidence boundaries

| Check family | What the successful checkpoint covers | What it does not establish |
| :--- | :--- | :--- |
| Application and analysis | Composer/dependency audit, Symfony container/YAML/Twig checks, PHPStan/regressions and full PHPUnit suite against PostgreSQL 17. | Complete capability delivery, current dependency advisories forever or production acceptance. |
| Five target userspaces | Source-built PHP 8.4 Core journeys on Debian 12/13 and Ubuntu 22.04/24.04/26.04. | Native PHP 8.1/8.2/8.3/8.5 running Core, native PostgreSQL providers or full host installations. |
| Native package probes | Selected package-family and isolated Nginx/FPM assertions on the five targets. | Complete service lifecycle or production package admission. |
| Browser and Worker | Panther journeys and the separate FrankenPHP Worker experiment. | Every interface, third-party application or cross-request production identity/session safety. |
| Debian VM/reboot | Bounded Debian 13 systemd/provisioning, recovery and actual reboot rehearsal. | Privileged qualification on the other four OS targets. |
| Service inventory | Strict parsing, application authorization, HTTP refusals and adapter contracts/unavailable-manager behavior. | Successful native collection, service health or service-control authority. |

The [runtime matrix](../documentation/development/CORE-RUNTIME-MATRIX.md), [VM guide](../documentation/development/DEBIAN-VM.md) and domain guides retain the detailed acceptance. No production percentage is inferred from the number of passing jobs.

## Current product decisions

| Decision | Implemented choice and source | Open integration |
| :--- | :--- | :--- |
| Product name | WCP360; Standalone is the server-installed panel, Core is its built-in engine. | Folder names do not rename the product. |
| ROOT | Server administrator's panel context, with current active ADMIN authority. | PHP/wcpd remain non-root; Unix root and the minimal privileged broker are separate. |
| Login and MFA | Username login; administrator MFA optional/off by default, enforced once enabled; [IdentityAccess](src/Module/IdentityAccess/README.md). | Historical mandatory-MFA production baseline wording requires M1 reconciliation; it is not silently amended or declared satisfied. |
| Web strategy | Target new general-purpose stack: Nginx → Apache event → FPM; existing durable Classic remains Nginx/FPM; [strategy](src/Module/Hosting/WEB-SERVER-STRATEGY.md). | Preview generation does not switch defaults or existing sites. |
| FrankenPHP | Classic application previews and explicitly acknowledged compatible Worker plans; Caddy is embedded where applicable. | Do not assume WordPress Worker compatibility, native .htaccess in a proxy-only chain or performance without acceptance/measurement. |
| Core license and editions | [AGPL-3.0-only](LICENSING.md) for original Core source; [Community/PRO strategy](../documentation/development/EDITIONS-AND-PREMIUM.md) and external planning catalogues are documented. | Package source models, general admission, entitlement implementation and PRO App Install qualification remain open. |
| Optional integrations | Plugins/premium rights, Docker, Toolbox and AI/MCP retain their [existing owners](STRUCTURE.md); the [infrastructure coverage register](../documentation/development/INFRASTRUCTURE-PREMIUM-COVERAGE.md) adds explicit multi-server, storage, Kubernetes, VM, performance/CDN and defensive-security planning. | Proposal catalogues do not implement providers or change maturity. Essential hosting, ordinary cache/compression, security and local recovery remain independent of optional packages and vendor connectivity. |

These record implemented product decisions and their limits. They are not replacement API/IPC/permission schemas, new operation IDs or amendments to accepted owner specifications.

## Next bounded checkpoint

**Candidate continuation:** complete the native Apache event/modules and FPM validation path for the existing Nginx → Apache → FPM preview, using its exact artifacts and admitted synthetic identities. Extend positive/refusal checks remotely; retain explicit unavailable/unexecuted stages. No installation, default migration or apply action is enabled by that work.

**Production dependency:** [M1](ROADMAP.md#m1) and [M2](ROADMAP.md#m2) remain open. Current authority, identity/permission composition, durable intent, effect revisions/fencing, audit, uncertain outcomes and recovery must be settled for the first production business path. Read-only WS1 qualification does not bypass those gates.

Then follow the existing milestone order for native setup, Tenancy/quotas, full Hosting, hosted Data, BackupRestore/Mail and optional package integration. Do not start six parallel placeholder implementations merely because their folders exist. The [web-server roadmap](src/Module/Hosting/WEB-SERVER-ROADMAP.md) owns WS1 detail; the [Core roadmap](ROADMAP.md) owns the complete dependency sequence.

## Maintaining this checkpoint

After an executable increment, record the exact revision, workflow, environments, result and remaining limits here. Update the owning guide/registers where behavior changed. Reassess completion only when the [roadmap's weighted method](ROADMAP.md#how-the-percentage-is-estimated) and evidence justify it; the existing approximately 20% estimate remains a dated assessment, not a new measurement from this document.
