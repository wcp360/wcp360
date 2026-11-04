# Current WCP360 Core status

**Updated: November 4, 2026. Executable development candidate; complete hosting and production qualification remain open.**

Read [WORKING-GUIDE.md](WORKING-GUIDE.md), [current consolidation](../documentation/governance/decisions/MASTER-CONSOLIDATION-2026-11-04.md), [ROADMAP.md](ROADMAP.md), [CAPABILITY-MAP.md](CAPABILITY-MAP.md) and [NAVIGATION.md](NAVIGATION.md) for their respective scope, delivery and source responsibilities.

## Verified executable checkpoint

| Item | Exact reference |
| :--- | :--- |
| Current integration target | `develop`, through existing authorized PRs; no direct push or new branch. The November 2/3 main consolidation is historical. |
| Latest tested HTTP/session candidate | PR #127 at `401289a74ce7756d7472c416bb17cf4e3631ba08`, not an official baseline. Integration status must be read from the PR, not inferred from this dated checkpoint. |
| Exact-source evidence | [Workflow 41810328812](https://github.com/wcp360/wcp360/actions/runs/41810328812): 14/14 jobs successful. Inspected Debian 13 suite: 1416 tests / 14795 assertions, without the previous two PHPUnit notices. |
| Persistent-worker evidence | 505 real HTTP requests exercised actual Core HTTP components in an isolated FrankenPHP probe. This is not complete-application or production qualification. |
| Historical executable checkpoint | `5bd4b54f1f2f12d6783d018f94b04ed48acd12cb`, tree `d42ddb97a101b32140f900f77321b8f4d80e221a`, [run 41545847754](https://github.com/wcp360/wcp360/actions/runs/41545847754). Retained evidence, not the result of newer source. |
| Historical publication | [PR #116](https://github.com/wcp360/wcp360/pull/116), [PR #125](https://github.com/wcp360/wcp360/pull/125), [integration register](../documentation/governance/branches/BRANCH-INTEGRATION-2026-11-02.md), [November 3 closure](../documentation/governance/branches/BRANCH-COMPLETION-2026-11-03.md). These do not close the newer branches. |
| Local application execution | Stopped under the existing instruction; documentation inspection does not restart services. |
| Production state | Not qualified; CODE-BASELINE=NONE. No stable release or latest pointer. |

A changed source or integration base requires renewed evidence. Documentation edits do not retest the application or transform a previous successful run into qualification of a new revision.

## What exists in the candidate

| Area | Implemented scope and detailed owner guide | Remaining boundary |
| :--- | :--- | :--- |
| Application | Symfony 7.4/PHP 8.4, Twig, Turbo/Stimulus, AssetMapper and candidate PostgreSQL persistence; [Core overview](README.md). | Production boot/integration and a complete hosting engine are unqualified. |
| Identity and accounts | Username login, optional administrator MFA, own-password/session flows and bounded recovery; [IdentityAccess](src/Module/IdentityAccess/README.md). | Canonical permissions/delegation, production recovery/identity isolation and independent emergency access remain open. |
| Hosting and execution | Durable candidate jobs, Classic/Advanced profiles, guarded Go planning and isolated provisioning experiments; [Hosting](src/Module/Hosting/README.md), [Operations](src/Module/Operations/README.md). | Real-domain/site lifecycle, production provider admission, effect fencing and general apply/recovery remain incomplete. |
| Web configuration previews | [Seven topology recipes](src/Module/Hosting/APPLICATION-PROFILES.md), including inert Nginx → private Apache event → FPM preview/2 across three routing profiles. | No changed installation default, durable selection or live activation of the new topology; Apache/event/FPM/HTTP acceptance remains open. |
| ROOT observations | [Server capacity](src/Module/Platform/README.md#root-server-overview) and [nine service families/fourteen fixed unit lookups](src/Module/Platform/SERVICES.md). | Current process namespace; no health guarantee or start/stop/restart control. Positive native systemd-host observation still needs distinct evidence. |
| Application catalogue | [Bounded Core bridge](src/Module/Hosting/APPLICATION-CATALOGUE.md) to [410 editorial references](../WCP360_App_Deploy/README.md). | Entries remain non-installable; providers, install/update/remove and Docker lifecycle are not supplied by this read path. |
| Extensions | Fixed reference-package lifecycle and isolation experiments; [ExtensionManagement](src/Module/ExtensionManagement/README.md). | General trusted package admission, version compatibility, commercial rights and production lifecycle remain incomplete. |
| Setup and recovery | Checkout preparation/checkpoints, native read-only preflight, offline pinned artifact inspection and candidate database backup/restore; [installer](installer/README.md). | No full native production installer/wizard, tenant-wide backup engine or qualified release. |

The existing 13 owners cover the preserved 233-capability inventory. Tenancy, Data, Files, Mail, BackupRestore and Transfers still need their own business implementations; helpers elsewhere count only for their specific scope. The reseller edition amendment does not silently alter the inventory or grant completion credit.

The 24 [service-resource families](resources/services/ROADMAP.md) contain 18 reservations, five external Go-template references and one family with three disposable-VM systemd units, not 24 installed providers. Application, host, observability, Toolbox, extension and AI resources remain additional preparation registers.

The [production-readiness review](../documentation/development/PRODUCTION-READINESS-REVIEW.md) links watchdog, update/recovery, isolation/session, hosting-page, notification and infrastructure gates. Its coverage does not establish implementation, security parity, a new percentage or production qualification.

## Evidence boundaries

| Check family | What the successful checkpoint covers | What it does not establish |
| :--- | :--- | :--- |
| Application and analysis | Configured dependency audit, Symfony container/YAML/Twig checks, PHPStan/regressions and PHPUnit against PostgreSQL 17. | Complete capability delivery, perpetually current advisories or production acceptance. |
| Five target userspaces | Source-built PHP 8.4 candidate journeys on Debian 12/13 and Ubuntu 22.04/24.04/26.04. | Native PHP 8.1/8.2/8.3/8.5 Core support, all native PostgreSQL providers or full host installation. |
| Native package probes | Selected package-family and isolated Nginx/FPM assertions on five targets. | Complete service lifecycle or production package admission. |
| Browser and Worker | Panther journeys and the bounded actual-component FrankenPHP worker probe. | Every interface, third-party application, concurrent-load or full authentication-stack isolation. |
| Debian VM/reboot | Bounded Debian 13 systemd/provisioning, recovery and actual reboot rehearsal. | Privileged qualification on the other four OS targets. |
| Service inventory | Strict parsing, authorization, HTTP refusals and unavailable-manager behavior. | Successful native collection, service health or control authority. |

The [runtime matrix](../documentation/development/CORE-RUNTIME-MATRIX.md), [VM guide](../documentation/development/DEBIAN-VM.md) and domain guides retain detailed acceptance. Passing-job counts do not create production maturity.

## Current product decisions

| Decision | Current direction | Open integration |
| :--- | :--- | :--- |
| Name | WCP360; Standalone is the installed panel and Core its engine. | Folder names do not rename the product. |
| ROOT | Server administrator's panel context with current ADMIN authority. | PHP/wcpd remain non-root; Unix root and the minimal broker are separate. |
| Login and MFA | Username login; MFA enrollment optional for USER/ADMIN, enforced once enabled. | Frozen mandatory-ADMIN language and the policy helper require explicit #132 alignment; optional enrollment does not bypass an enabled factor. |
| Web strategy | Target Nginx → Apache event → FPM; existing durable Classic Nginx/FPM. | Preview does not change defaults or existing sites. See [strategy](src/Module/Hosting/WEB-SERVER-STRATEGY.md). |
| FrankenPHP | Classic previews and explicitly acknowledged compatible Worker plans. | No assumption of WordPress Worker compatibility, native .htaccess in proxy-only mode or measured performance. |
| License and editions | Public Core [AGPL-3.0-only](LICENSING.md); Free/PRO unlimited websites by edition, Free without premium packages, PRO packages after individual qualification. | No unlimited-hardware claim, commercial grant, available premium catalogue or completed entitlement engine. |
| Optional integrations | Existing [owners](STRUCTURE.md), [infrastructure register](../documentation/development/INFRASTRUCTURE-PREMIUM-COVERAGE.md). | No available providers inferred from proposals; essential hosting, security and local recovery stay autonomous. |

The [November 3 owner record](../documentation/governance/decisions/OWNER-DECISION-FREE-SITES-MFA-2026-11-03.md) and [edition strategy](../documentation/development/EDITIONS-AND-PREMIUM.md) retain product provenance and limits. Product direction is not an amended frozen API/IPC/permission schema or runtime acceptance.

## Next bounded checkpoint

SC-M01 remains limited to the inert-daemon foundation. Core M1-S1 is a distinct bounded HTTP/session development/test slice in #130, under the [single-maintainer controls](../documentation/governance/decisions/D-GOV-SOLO-0001.md). Its merge, actual GitHub protection and production acceptance remain distinct decisions.

**WS1 is not authorized by those slices.** Native Apache event/modules and FPM validation for the Nginx → Apache → FPM preview remains a proposed next slice using exact artifacts and synthetic identities. The previously described native harness has no new execution evidence from this documentation change. Keep unavailable/unexecuted stages explicit; do not present a general SC-M01 pause as the reason or assume that a roadmap grants CI execution authority.

[M1](ROADMAP.md#m1) and [M2](ROADMAP.md#m2) production dependencies remain open: current identity/permission composition, durable intent, revisions/fencing, audit, uncertain effects and recovery must be settled for each production path. Then follow native setup, Tenancy/quotas, Hosting, hosted Data, BackupRestore/Mail and optional-package dependencies. Do not start parallel placeholder implementations because folders exist. The [web roadmap](src/Module/Hosting/WEB-SERVER-ROADMAP.md) retains design ordering only.

## Maintaining this checkpoint

Record exact revision, workflow, environment, results and limits after an executed increment. Update owning guides where behavior changes. The [roadmap weighting](ROADMAP.md#how-the-percentage-is-estimated) and approximately 20% historical assessment are not new measurements from this document. Preserve old evidence instead of assigning it to new source.
