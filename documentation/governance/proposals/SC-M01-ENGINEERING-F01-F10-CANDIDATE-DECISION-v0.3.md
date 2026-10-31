# WCP360 — Proposed Engineering + DevOps amendment: executable M01-01 toolchain choices

**Date:** 2026-10-27  
**Status:** OWNER DECISION REQUEST — NON-CANONICAL P1 — NOT AN ENGINEERING APPROVAL  
**Proposed version identity:** `ENG-WCP-RULESET-0001 v0.3-CANDIDATE` **only if Engineering ratifies an amendment**  
**Historical logical identity:** `ENG-WCP-RULESET-0001 v0.2` (exact prior full body not recovered)  
**Reference:** WCP360-BM-0001 FINAL; DOC v4.0.21.23; DESIGN V2.5.3.36.4; CODE-BASELINE NONE  
**Owner:** ENGINEERING / CODE REVIEW; **co-owners:** DEVOPS / BUILD / RELEASE, Security / Hardening, Tests / QA, Architecture Review as scoped  
**Issues:** #9 Engineering, #13 MASTER, #41 Audit.

## 1 — Why this proposal differs from an exact historical republication

The old Engineering `v0.2` final metadata is known, but the complete exact final source is unavailable. A newly decided framework/toolchain/quality stack is *new content*, **not** byte-identical republication.

Choose exactly one path:

- **E-A:** Full retained v0.2 original source is found and independently verifiable → publish exact with original provenance; this v0.3 proposal is not needed.
- **E-B (recommended for review, not approved):** Engineering explicitly decides missing ENG-R01..ENG-R20 / F01..F10 with current owner constraints, and declares `CONTENT CHANGE = YES`, actual version bump decision (candidate `v0.3`) and dependent owner signoffs. The canonical owner file needs complete amended content, not just this F01..F10 delta.

Do not choose E-A merely because the recovered v0.2 content has a final-status annotation.

## 2 — Official platform facts and current upstream support sources

**Already FINAL WCP360:** Debian 13 trixie / amd64 is initial supported target. The canonical DevOps and Security v1.0 exports require separate `wcp360-user`, `wcp360-admin`, `wcpd`, `wcp-privd`, worker identities; backend → nonroot wcpd → IPC-B → minimal-root wcp-privd only. PostgreSQL control-plane is not the hosted customer DB.

**Current external platform facts (checked 2026-10-27; not WCP360 decisions):**

- Debian 13 provides PHP-FPM 8.4 (with security-package updates). Source: https://packages.debian.org/trixie/php8.4-fpm
- Laravel 13 (released 2026-03-17) supports PHP 8.3–8.5, with published security-fix horizon to 2028-03-17. Source: https://laravel.com/framework/docs/releases
- Debian 13 default `golang-go` is based on Go 1.24, while upstream Go 1.27.3 was released 2026-10-19 including a `crypto/x509` security fix. Go upstream supports a release until two newer major versions exist. Source: https://packages.debian.org/trixie/golang-go and https://go.dev/doc/devel/release
- Debian 13 default `postgresql` meta-package selects PostgreSQL 17. Source: https://packages.debian.org/trixie/postgresql
- Debian 13 default Node.js meta-package supplies Node 20, while upstream Node20 support reached EOL in 2026. A build-time-only supported LTS runtime needs its own pinned supply-chain decision. Sources: https://packages.debian.org/trixie/nodejs and https://nodejs.org/en/about/previous-releases

**Important:** Exact patch versions, hashes, binary provenance and package repository mirrors must be freshly verified at approval/build time. This dated analysis must not be mistaken for a permanently pinned production manifest.

## 3 — Concrete candidate decisions for F01–F10

Every row is **PROPOSED FOR OWNER ACCEPTANCE**; none is FINAL.

| ID | Recommended owner decision | Required independent/owner checks | Outcome if rejected |
|---|---|---|---|
| **F01** | PHP 8.4 / Laravel 13 application services behind separate `wcp360-user` and `wcp360-admin` PHP-FPM identity/session boundaries. Shared domain code only without a shared authorization surface. | Security S01–S04 topology/cookie/CSRF/Origin; Architecture Review; Design compatibility; selected actual Laravel patch+Composer lock. | Owner chooses and versions another framework; no silent manual auth implementation. |
| **F02** | Non-root `wcpd` + root-minimal `wcp-privd` built using Go 1.27 maintained patch line, with tested Go 1.27.3 initial *candidate* and pinned verified upstream/toolchain digest. The R&D Go 1.24.13 runner is NOT acceptance evidence for production. | DevOps provenance/signature/reproducibility; Security compiler/crypto maintenance; test on Debian 13 amd64; Go race/negative handling. Compare distro-maintained Go vs upstream signed package and document risk acceptance. | Explicit owner choice of supported/backported Go with proof and version delta. |
| **F03** | PostgreSQL 17 **control plane** (Debian13 native) with separately managed credentials, roles and backup/restore; hosted tenant engine managed resources remain distinct. | Database owner #6 physical model, FKs, transaction/locking/migrations; DevOps upgrade/rollback and supported driver. | Database owner selects supported major and records compatibility. |
| **F04** | Official Debian trusted signed APT channels, approved Composer/Packagist mirror and pinned checksummed Go module supply; immutable `composer.lock` / `go.sum`; no unreviewed release-time downloads/installer scripts. | Security dependency ownership, provenance, license scan, CVE policy, offline/reproducible build and mirroring. | Owner supplies equally auditable, signed registry strategy. |
| **F05** | No Node.js process in WCP360 production/runtime by default. If build-time frontend assets are needed by Design V2.5.3.36.4, use a currently supported Node 24 LTS pinned CI builder with dependency lock, publish static assets only. | Design owner decides bundler/asset compilation; DevOps audits Node version/provenance/lock/cached clean build. | Explicit no-Node static pipeline or other supported build-only tool. |
| **F06** | Candidate build commands, subject to owner toolchain choice: PHP `composer validate --strict` and reproducible `composer install`; Go `go test ./...`, `go vet ./...`, `go test -race ./...` for applicable concurrency; QA SC-M01 suites by accepted IDs. No auto-fetch of unapproved dependencies. | Exact `composer.json`, pinned dependencies and CI package roots first; full build commands must be recorded once executable code is authorized. | Owner chooses equivalent reproducible entry points. |
| **F07** | Candidate quality check families: PSR-12 formatter, PHPStan/Larastan compatible with adopted framework, typed PHP 8.4 static analysis, Go `gofmt`, `go vet`, vetted pinned static scanner, reference/contract drift, secret scan and forbidden privileged-op tests. | Engineering chooses **exact** tools/versions/check names, lint/rule thresholds, false-positive waiver policy and DoD; QA owns negative assertions. | No arbitrary threshold/tool inherited from Sprint 0. |
| **F08** | Pinned, ephemeral **Debian 13 amd64** build and test environment with non-root CI steps, approved network/mirror limits, protected promotion/signing stage isolated from untrusted PRs. | DevOps validates runner image digest, CPU/race coverage, cache independence and reproducibility; Security reviews PR token permissions/secrets. | Document supported equivalent target with compatibility proof. |
| **F09** | Typed, startup-validated config with clear precedence, per-consumer secret access, immutable `/usr/lib/wcp360` product tree, approved `/etc/wcp360` secure config and `/var/lib/wcp360` operational state, no runtime config-to-shell interpolation. | DevOps/Security ratify config format/precedence, secret storage/rotation/revocation and generated assets ownership; never log secrets. | Owner specifies alternative with same fail-closed properties. |
| **F10** | Reproducible artifact output with Git SHA, toolchain+dependency lock digest, SHA-256 artifact integrity, SBOM/provenance and signing by isolated release identity. Release exactly the artifact CI verified; keep rollback/data-migration compatibility classified. | DevOps signs off package format/signature/trust root/reproducibility and upgrade/rollback boundary; Security key custody. | Owner supplies alternate fully auditable signature/provenance scheme. |

## 4 — Mandatory security/architecture separation for first M01-01

The runtime stack may only be chosen if it **preserves**:

```text
WCP-USER ≠ WCP-ADMIN  (OS identity / secrets / cookies / sessions / CSRF / authz)

user/admin backend → wcpd (non-root, control/orchestration)
                 → bounded authenticated IPC-B (wcpd only)
                 → wcp-privd (minimal root)
                 → registered typed privileged primitive

account_id ≠ organization_id ≠ tenant_id
CONTROL-PLANE POSTGRESQL ≠ HOSTED DB ENGINE / CUSTOMER DATA
```

Before implementing real control transports, separate API/IPC/Security/DevOps must settle C01/C03/C06/C07. Before job/idempotency/audit persistence, Database #6 must be a full owner-approved physical model. Before a 12-operation parent-binding dispatcher, Security/API #91 must ratify exact `permission_id / origin_action_id / ipc_operation_id` tuples, not string-prefix, OR or generic runtime fallback.

No `shell.exec`, arbitrary commands/executables, generic `sudo/systemctl`, arbitrary privileged filesystem paths or unrestricted plugin execution.

## 5 — First implementation feasibility audit (M01-00/M01-01 only)

| Foundation item | Supported enough to implement **after** owner final + global gate? | Missing exact check |
|---|---|---|
| Repository project/bootstrap structure | Yes, if this F01–F10 and ENG-R rules are accepted, and QA #7 is final | Approved source tree, dependency lockfiles, exact test entry points, independent review and MASTER GO |
| Two PHP web entrypoints | Conditional | Security S01–S04 deployment topology, CSRF and cookie/session namespace; Design mapping |
| Go privileged IPC-B parser skeleton | Conditional | F02 version, authenticated socket peer, canonical framing/limits, Security/E3; R&D #94 cannot substitute production code |
| Backend→wcpd control client | **NO** until C03/C06/C07 ratified | Control wire framing/peer/trusted context and durable job ownership |
| PostgreSQL migrations | **NO** until DB #6 final | No executable DDL from reconstructed fragments |
| Root privileged effects | **NO** until full Security/API bindings and tests | #91, exact target/primitives and E3 review |
| Release/installers | **NO** by default as M01-01 | Separate DevOps release/installer authorization and rollback evidence |

## 6 — Candidate engineering review/DoD semantics

**Propose** the following explicit, enforceable owner gates, without guessing numeric thresholds:

1. Every implementation PR references an accepted requirement/module/contract/security decision and the exact owner-approved version and SHA.
2. Type-check static tools, formatting and unit/contract/security tests use pinned versions and actual CI paths.
3. Every changed privilege boundary has negative tests: wrong surface/tenant/permission/job/operation, forged/stale context, malformed frames and no-effect assertions.
4. No added DB table/column/constraint without Database model owner approval and versioned migration.
5. No source secret or production credential, dynamic privileged commands, false success on `UNKNOWN_OUTCOME`, cross-tenant leakage, or direct backend/worker→root path.
6. Required QA test evidence is tied to exact SHA/environment/attempt history. Quarantined/blocked required tests cannot be silently passed.
7. E1 automated checks + **E3 independent Engineering technical review** for R1/I1/S1 code; Security/DevOps/Database owner reviews as applicable. Same-author E2 does not count as E3.
8. Artifact/version/provenance/compatibility/rollback evidence accompanies approved release scope. Numeric thresholds require explicit Engineering/QA owner decision, not an inferred v0.2 requirement.

## 7 — Owner response (exact decision required)

```text
OWNER = WCP360 — ENGINEERING / CODE REVIEW
DECISION PATH = E-A (EXACT HISTORICAL REPUBLICATION) / E-B (OWNER AMENDMENT)
F01 = ACCEPT / AMEND / REJECT / DEFER
F02 = ACCEPT / AMEND / REJECT / DEFER
F03 = ACCEPT / AMEND / REJECT / DEFER
F04 = ACCEPT / AMEND / REJECT / DEFER
F05 = ACCEPT / AMEND / REJECT / DEFER
F06 = ACCEPT / AMEND / REJECT / DEFER
F07 = ACCEPT / AMEND / REJECT / DEFER
F08 = ACCEPT / AMEND / REJECT / DEFER
F09 = ACCEPT / AMEND / REJECT / DEFER
F10 = ACCEPT / AMEND / REJECT / DEFER
ENG-R01..R20 = EXACT FINAL SOURCE / RATIFIED NEW DECISIONS
VERSION IMPACT = explicit, candidate v0.3 only if ratified
CONTENT CHANGE = NONE only under proven exact E-A source; otherwise YES
SECURITY / DEVOPS / QA HANDOFFS = exact owner refs
TESTS / CHECKS REQUIRED = explicit
FINAL ARTIFACT PATH = specifications/engineering/<owner-final-filename>
FINAL OWNER APPROVAL + COMMIT SHA = required
STATUS = DRAFT / REVIEW / APPROVED
```

After owner approval, publish the **complete**, self-contained final Engineering ruleset under `specifications/engineering/`, with version and provenance. This delta alone cannot close #9.

## 8 — Current status

```text
ENGINEERING v0.2 EXACT FINAL BODY = MISSING
F01–F10 = PROPOSED ONLY
GLOBAL SC-M01 OWNER ARTIFACTS = 5/8
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

This is a concrete versioned technical recommendation, not an automatic reactivation of the cancelled Sprint 0 or V4.1, and not authority to produce source code, DDL, systemd units or packages.
