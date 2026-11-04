# MASTER Decision — SC-M01 Inert Daemon Foundation

- **Reference:** `WCP360-BM-0001 FINAL / SC-M01`
- **Authority:** WCP360 — MASTER / Project Control
- **Decision date:** 2026-11-03
- **Status:** **GO LIMITED — INERT FOUNDATION ONLY; EXPLICITLY RATIFIED**
- **Code baseline:** `NONE`

## Ratification and source reference

The project owner explicitly ratified this decision as MASTER on 2026-11-03. This record captures that ratification; it is not a cryptographic signature or an independent review.

The observed `main` commit `d42d86ab12968ea0cbb3b0dec2a2eee4c9ec625f` is an inventory reference only. It is not a newly approved source SHA, code baseline, or retroactive approval of changes already merged. The resulting handoff and any candidate commit must identify their own exact revisions and evidence.

This decision supersedes the earlier pause for this one bounded increment only. It does not resume general product development or replace any specialized approval.

## Authorized increment

`SC-M01 — INERT DAEMON FOUNDATION`

Permitted work is limited to:

- verifying and consolidating the already-present inert Go `wcpd` foundation;
- minimal corrections to defects inherent to that foundation;
- validating the CLI bootstrap and import boundaries;
- checking dependencies and build reproducibility;
- strengthening directly related positive and negative tests; and
- producing exact-revision CI evidence and the independent-review handoff.

No new system, business, or privileged functionality is authorized. Do not duplicate the daemon under `WCP360_Core/agents/` or modify the Core application under this decision.

## Source and trust boundary

The only implementation source is `SOURCE/daemon/`; the only authorized CI workflow is `.github/workflows/sc-m01-daemon-foundation.yml`. Documentation and governance records may be updated solely to record this decision, its evidence, and its handoff.

The current inventory contains a finite CLI, source-derived contract metadata and pure policy candidates. Their presence, previous CI, or previous review does not grant new approval. In particular:

- no listener, socket, backend transport or IPC-B dispatcher may be activated;
- no `wcp-privd`, root operation, service mutation, database access/migration, web endpoint, installer or release may be implemented;
- no permission, IPC operation or PostgreSQL structure may be invented; and
- metadata lookup or a positive policy predicate is not authorization to execute or replay an operation.

## Contract and provenance references

The following exact inputs are consumed by, or directly constrain, the existing candidate. SHA-1 values below are Git blob identities, not security signatures. Historical locators are retained where the addendum records an approved source that is absent from the `d42d86a` tree.

| Input | Version | Source path | Blob |
|---|---|---|---|
| Toolchain addendum | v1.0 | `specifications/devops/WCP360_SC-M01_DAEMON_FOUNDATION_TOOLCHAIN_ADDENDUM_v1.0.md` at publication commit `1ff4c195e75dade00cda13215b92ffe7576637b7` | `f8608d6258f87b8c1099b9b3a7e32ec95c72dc27` |
| Engineering ruleset | `ENG-WCP-RULESET-0001 v0.3` | `specifications/engineering/WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md` | `a2d5035454d90f0a252da7041b3a6b239265c557` |
| DevOps baseline | v1.0 | `specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md` | `61edf698c50cf58779b75ba02fba913bf8276c61` |
| Test baseline | v1.0 | `specifications/tests/WCP360_SC-M01_Test_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | `e0cdf924c5d7353624c069552f921f587cad8561` |
| Security baseline | v1.0 | `documentation/specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | `6d74f2f363524511424f839ca7262143c2af95ab` |
| API contract | 1.0 | `documentation/specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | `4893c5191b4928ecfa5a70f4dc14b39a8939fcea` |
| Permission registry | v1.0 | `documentation/specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | `81f3d425f36470167aba4bfb7ba069268626342f` |
| IPC contract | `WCP360-IPC-B/1` | `documentation/specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md` | `fec9c74e8413db822662e47c76c40486e282334f` |
| Database model | v1.0 | `documentation/specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md` | `2660e057bad1d00592c57f354b1f3745ccccbe7d` |

The module registry, OS matrix, Design System and DOC baseline are not imported by the daemon implementation in this increment. Their listed versions do not authorize a new module, cross-platform claim or product behavior. The daemon uses only the Debian/Go profile selected by the cited DevOps addendum.

## Required build and test environment

```text
OS = Debian GNU/Linux 13.7
ARCH = linux/amd64
GO = 1.27.3
GOTOOLCHAIN = local
UID/GID = 10001:10001
RUNTIME DEPENDENCIES = Go standard library only
```

```text
GO ARCHIVE SHA-256 = 65efbc80fdc1fa2cd2631cad5b481777a68e8725f34e89f50fc6ac714e379b40
DEBIAN IMAGE = debian@sha256:918311b7b6c4c6f68b232ba516584925f6c78ad82b6fd534b98979df6438e483
GOVULNCHECK = v1.9.0
```

Use the digest-pinned Debian image and checksum-pinned Go archive in the workflow. Product build/test steps run non-root and offline. `govulncheck` is pinned and may access only the controlled vulnerability-service egress defined by the workflow.

Do not run Core/daemon build or tests locally in violation of the active stop instruction. Use the authorized GitHub workflow; an unavailable or non-exact run is `NOT RUN`/`BLOCKED`, never `PASS`.

## Evidence and reviews

The handoff must identify the exact candidate SHA, source inventory, changed files, contract provenance, environment, commands, positive/negative outcomes, binary/input fingerprints, CI artifacts, residual findings and limitations.

Required before merge:

- independent Engineering / Code Review E3 on the exact candidate SHA;
- Security / Hardening review of privilege boundaries, refusals and isolation;
- Tests / QA review of positive, negative and traceable assertions; and
- DevOps / Build / Release review of the toolchain, workflow, provenance and reproducibility.

Contract, database, observability or performance reviews remain conditional on a demonstrated impact. CI is not E3, no review may be self-approved, and no review on an earlier SHA transfers automatically.

```text
SC-M01 = LIMITED SCOPE
SC-M02 = NOT AUTHORIZED
PRODUCTION RELEASE = NOT AUTHORIZED
INDEPENDENT E3 = REQUIRED
MASTER ACCEPTANCE = REQUIRED
CODE-BASELINE = NONE
```

At the end of this increment, stop and hand the package to MASTER. No automatic merge, production qualification, stable release or new code baseline follows.
