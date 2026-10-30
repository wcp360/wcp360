# WCP360 — SC-M01 F01–F10 Toolchain / Build Owner Options v0.1

**Date:** 2026-10-26  
**Status:** R&D RECOMMENDATION / NON-CANONICAL — NOT IMPLEMENTATION AUTHORITY  
**Owner ratification:** ENGINEERING / CODE REVIEW + DEVOPS / BUILD / RELEASE; SECURITY + TESTS / QA reviews; Architecture Review + MASTER governance  
**References:** WCP360-BM-0001 FINAL, SC-M01-SCOPE, DevOps v1.0, Security v1.0, Engineering #9, QA #7, owner-decision matrix F01–F10  
**CODE-BASELINE = NONE; SC-M01 PAUSED; SC-M02 NOT AUTHORIZED**

This is a candidate, independently researched after the cancelled Sprint 0. It MUST NOT be presented as recovered historical Engineering v0.2 content, a deployed stack, a pinned release, or an owner-approved toolchain.

## 1 — Final constraints supplied by WCP360, not newly chosen here

- Initial target: Debian GNU/Linux 13 (trixie), amd64/x86_64.
- WCP-USER and WCP-ADMIN are separate application and security surfaces; no UI-only privilege checks.
- Backend → wcpd (non-root) → authenticated bounded IPC-B → wcp-privd (minimal root), typed allowlisted primitives only.
- No generic shell.exec / arbitrary command execution / generic sudo/systemctl proxy / arbitrary filesystem write/privileged target.
- Control-plane PostgreSQL is the authority for logical state, distinct from databases hosted for customers.
- Product binaries under /usr/lib/wcp360, runtime/secret/state files in their approved separate locations.
- No official CODE-BASELINE and cancelled Sprint 0/V4.1 cannot supply automatic implementation choices.

## 2 — Verified platform and upstream facts (2026-10-26)

### Debian 13 package channels

| Component | Debian 13 trixie package fact (current indexed package metadata) | Interpretation |
|---|---|---|
| PHP-FPM | php8.4-fpm 8.4.27-1~deb13u1 | PHP 8.4 is an OS-native candidate for isolated web processes. |
| Go compiler | golang-go 2:1.24~2; depends on golang-1.24-go | The default Debian compiler may not match the currently upstream-supported Go major releases. This is a security/supply-chain review issue, not a claim Debian has no security patches. |
| PostgreSQL | postgresql 17+278 depends on postgresql-17 | PostgreSQL 17 is the default platform candidate for the control plane, not automatically a final Data Model owner decision. |
| Node.js | nodejs 20.19.2+dfsg-1+deb13u3 | Debian-native Node 20 exists, but that does not make Node 20 upstream-supported as of Oct 2026. |

Official package sources:
- https://packages.debian.org/trixie/php8.4-fpm
- https://packages.debian.org/trixie/golang-go
- https://packages.debian.org/trixie/postgresql
- https://packages.debian.org/trixie/nodejs

### Upstream lifecycle constraints

- Laravel 13 was released 2026-03-17, supports PHP 8.3–8.5 including Debian 13 PHP 8.4, and its published security-fix horizon is 2028-03-17. This makes it a *compatible candidate*, not a final architecture choice. Source: https://laravel.com/framework/docs/releases
- Go's upstream release policy supports a major release only until two newer major versions exist. Go 1.27.3 was published 2026-10-19, with security fixes; using the Debian-default Go 1.24 compiler in 2026 requires an explicit Debian security/backport strategy and acceptance of support divergence. Sources: https://go.dev/doc/devel/release and https://packages.debian.org/trixie/golang-go
- Node.js official release schedule marks v20 end-of-life (2026-03-24), with v22/v24 LTS as of 2026-10-26. The Debian Node v20 package can still receive distro updates, but toolchain governance should not mistake distro packaging for upstream LTS. Sources: https://nodejs.org/en/about/previous-releases and https://packages.debian.org/trixie/nodejs

**Time-sensitivity:** Exact patch versions can change. At a later decision/CI run, pin and verify the then-supported patch level, digests and actual package source; never hard-code this research snapshot as a perpetual production lock.

## 3 — Three plausible backend architectures

| Option | WCP-USER/WCP-ADMIN backend | Control/privileged daemons | Advantages | Tradeoffs |
|---|---|---|---|---|
| A (recommended for owner review) | PHP 8.4 FPM with Laravel 13, separate runtime identities, strict surface/session boundaries | Go using supported patched toolchain, two separate daemon trust roles | Mature auth/validation/database queue ecosystem; canonical PHP↔Go control flow remains feasible; Debian FPM native | Two languages, dependency review, distinct contexts/serialization, session isolation tests mandatory |
| B | PHP 8.4 with a smaller framework or custom HTTP layer | Go daemons | Smaller framework surface if carefully designed | Hand-rolled auth/error/schema conventions may enlarge security risk and test burden |
| C | Go backend (with SSR/static UI) and Go daemons | Go daemons | Fewer languages and shared typed models | Larger change to existing design/functional workflow assumptions; web auth/session/CSRF/templating must be rebuilt and audited; not auto-approved |

**Option A is a recommendation only.** It does not decide whether USER and ADMIN are two independently deployed PHP applications, two FPM pools within one codebase, distinct domains/origins, or a shared backend service. Security S01–S04 and Architecture Review own that choice.

Design V2.5.3.36.4 remains visual authority. Do not replace its canonical surfaces or CSS with a redesign simply because a framework is selected.

## 4 — Proposed F01–F10 decision matrix

All cells labeled PROPOSED are **OPEN pending owner approval**.

| ID | Proposal | Owner verification / still open |
|---|---|---|
| F01 Backend | **PROPOSE** PHP 8.4 with Laravel 13; separate FPM service identities/sessions and explicitly scoped shared application services. Composer dependencies locked/reviewed. | Engineering + Security ratify framework; Architecture Review validates the deployable/surface topology; decide exact PHP patch and Composer lock. |
| F02 Daemons | **PROPOSE** Go with Go 1.27 maintained security patch level for build; runtime binaries owned by immutable Debian package. | Engineering + DevOps decide upstream signed toolchain vs Debian-maintained compiler with backport provenance; do not reuse cancelled Sprint 0 Go settings. |
| F03 PostgreSQL | **PROPOSE** PostgreSQL 17 as Debian-native initial control-plane engine; isolated managed runtime and explicit major-upgrade policy. | DATABASE + DevOps must ratify exact compatibility/driver/migration protocol and backup/restore; not a schema definition. |
| F04 Dependency sources | **PROPOSE** official OS APT release/security channels plus allowlisted verified Composer/Go/module package registries; lock versions/digests, run vulnerability/license review. | DevOps + Security ratify permitted sources, offline packaging, mirror strategy, hash/signature controls and patch cadence. |
| F05 Node/frontend | **PROPOSE** no Node.js **in production/runtime** by default. If Design owner approves build-time frontend modules, use a currently upstream-supported Node LTS (e.g., 24 LTS at research date) only in pinned CI image, lock dependencies and publish static assets. | Design System + Engineering + DevOps decide build-time need and verify bundler/runtime compatibility; no accidental Node 20 runtime adoption. |
| F06 Entry points | **PROPOSE** one documented reproducible build/test/lint entry point per language plus overall CI matrix; no network-time random package install during release promotion. | Exact commands/package manager/lock paths depend on F01/F02/F05 owner decisions; no invention of final build commands. |
| F07 Static analysis | **PROPOSE** PHP formatter/static analysis/security lint, Go gofmt/go vet plus pinned analyzer where warranted, typed contract checks and negative privileged-operation scans, migration/schema drift checks. | Engineering owns tools, versions, numeric thresholds and necessary review exceptions; TESTS/QA owns actual assertions. |
| F08 CI builder | **PROPOSE** pinned ephemeral Debian 13/amd64 build image where practical, non-root/test-only runner, network restrictions, deterministic seed/clock and cache-independent verification; signing outside general runner. | DevOps validates real runner availability and exact image digest, reproducibility and supply-chain trust. |
| F09 Config/generation | **PROPOSE** schema-validated config/secrets separate from source, generated assets reproducible and version-traced, no secrets in git/package/CI logs; a single authority for every generated artifact. | Security + DevOps + Engineering ratify config precedence, owners, rotations, template generation and operational state. |
| F10 Artifact identity | **PROPOSE** immutable artifact SHA/digest, SBOM and provenance attestation, verify before package promotion, signed release catalog, explicit rollback/data-migration compatibility. | DevOps/Security choose signing root, key custody, SBOM/provenance schema and reproducible publication gate. |

## 5 — Crucial upstream-vs-distro support decision (F02/F05)

The default compiler versions in a stable OS and the versions supported by an upstream language project can diverge.

**For Go (root-adjacent daemon):** do not take the Debian 13 default Go 1.24 as automatically compliant with the current upstream support policy. Choose explicitly:

- **F02-A:** Debian-native Go 1.24 with documented Debian security maintenance and reproducible patch provenance + risk acceptance.
- **F02-B (recommended for review):** maintained upstream Go 1.27.x patch, source/binary signature or checksum verification, locked build image and reproducible binary/package promotion.
- **F02-C:** Debian backports of a maintained Go major after verifying exact version and security maintenance.

Security/DevOps must determine whether statically linked runtime, cgo/systemd usage, TLS/CA behavior, dependency licensing and binary traceability meet WCP360's isolation requirements. The compiler alone does not confer daemon security.

**For Node:** if a frontend build is eventually authorized, keep it in build isolation and avoid relying on EOL upstream major v20 simply because Debian packages it. Verify build tool compatibility; no Node daemon should be added silently to production.

## 6 — Release and platform test obligations (PROPOSED, NOT PASS)

| Proposed test label | Required assertion |
|---|---|
| TOOL-Q01 | Clean Debian 13 amd64 builder reproduces the same app+daemon bytes from pinned inputs or documents allowed reproducibility boundaries |
| TOOL-Q02 | PHP 8.4/Laravel 13 bootstraps without a privilege escalation or cross-surface cookie/session sharing |
| TOOL-Q03 | The two web runtime Unix identities cannot read each other's secrets or bypass control-daemon authorization |
| TOOL-Q04 | Go toolchain major/patch is supported or explicit verified distro maintenance proof exists; correct build digest embedded in provenance |
| TOOL-Q05 | Go daemon artifact runs on minimum Debian 13 glibc/systemd environment and respects non-root/root-minimal identity split |
| TOOL-Q06 | PostgreSQL 17 control-plane backup/restore and migration-downgrade safety are tested only after DB owner contract exists |
| TOOL-Q07 | No runtime Node dependency if not expressly approved; any build-time Node is pinned and absent from runtime packages |
| TOOL-Q08 | Tampered lockfile, dependency checksum or package digest blocks build/release |
| TOOL-Q09 | No prod secret/credential in Git, generated assets, packages, logs or CI outputs |
| TOOL-Q10 | build/lint/static/security gate failure blocks required merge once repository protections are enforced |
| TOOL-Q11 | partial CI or quarantined/rerun-to-green test is not represented as product PASS |
| TOOL-Q12 | package upgrade/rollback/restore preserves tenant-hosted data and does not claim DB rollback when impossible |
| TOOL-Q13 | control wire/IPC-B never accepts arbitrary commands, executables, systemd unit names or unrestricted paths |
| TOOL-Q14 | deployed USER and ADMIN surfaces maintain separate trust/session/policy controls despite common libraries |
| TOOL-Q15 | exact release artifacts, SBOM, provenance, signing and author/reviewer identities are recorded and reproducible |

These candidate QA labels are **not TEST IDs**, and no application test has executed.

## 7 — Owner decision form

    F-ID =
    OWNER =
    APPROVED OPTION =
    REJECTED ALTERNATIVES =
    PLATFORM PACKAGE SOURCE / UPSTREAM SUPPORT EVIDENCE =
    PINNED RUNTIME & TOOLCHAIN VERSIONS =
    LOCKFILE / CHECKSUM / SIGNING AUTHORITY =
    BUILD & TEST COMMANDS =
    REQUIRED SECURITY/QA VERIFICATION =
    CHANGE / VERSION IMPACT =
    STATUS = DRAFT / REVIEW / APPROVED

For a final Engineering/DevOps contract, owners must explicitly differentiate:

    HISTORICAL EXACT V0.2 REPUBLICATION
    !=
    NEWLY ADOPTED / VERSIONED OWNER AMENDMENT

Do not assign CONTENT CHANGE = NONE to newly decided stack choices.

## 8 — Exit gate

After Engineering + DevOps + Security/QA owner review and Architecture Review:

1. Publish the approved exact rules in owner canonical artifacts, with provenance and version impact.
2. Refresh MASTER baseline manifest, applicable semantic gates and test cases.
3. Independently verify 8/8 canonical owner artifacts and SC-M01 component readiness.
4. Only then authorize executable SC-M01 foundation implementation and a first CODE-BASELINE.

**This memo does not close issue #9 or #13, does not choose a canonical toolchain and does not authorize source-code implementation.**
