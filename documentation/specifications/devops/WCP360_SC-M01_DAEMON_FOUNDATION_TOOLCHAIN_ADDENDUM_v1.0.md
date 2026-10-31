# WCP360 — SC-M01 Daemon Foundation Toolchain Addendum

**Artifact:** `WCP360_SC-M01_DAEMON_FOUNDATION_TOOLCHAIN_ADDENDUM_v1.0.md`  
**Addendum version:** `v1.0`  
**Owner:** `WCP360 — DEVOPS / BUILD / RELEASE`  
**Engineering basis:** `ENG-WCP-RULESET-0001 v0.3`  
**Reference:** `WCP360-BM-0001 FINAL / SC-M01 FIRST EXECUTABLE SLICE / A1 TOOLCHAIN DECISIONS`  
**Status:** **FINAL APPROVED — COMPONENT-SCOPED CANONICAL ADDENDUM**  
**Date:** 2026-10-28  
**DEVOPS baseline:** `v1.0` — unchanged  
**Engineering ruleset:** `ENG-WCP-RULESET-0001 v0.3` — unchanged  
**CODE-BASELINE:** `NONE`  
**Source Code authorization:** **NOT GRANTED BY THIS ADDENDUM**

## 1. Scope and activation boundary

This addendum closes only the activated SC-M01 first-slice decisions:

- **F02** — daemon implementation language/toolchain;
- **F04** — dependency source/lock/integrity policy;
- **F06** — daemon module root and reproducible clean-checkout build/unit-test commands;
- **F07** — formatter/static/security/unit quality gates;
- **F08** — exact Debian 13 amd64 build/test environment.

The first executable slice is intentionally inert:

```text
IN SCOPE
- one Go daemon module foundation
- minimal inert wcpd executable target
- compilation
- pure/local unit tests
- quality/toolchain evidence

OUT OF SCOPE / DEFERRED
- sockets/listeners
- backend↔wcpd control transport
- IPC-B
- wcp-privd privileged behavior
- database access or migrations
- external mutation
- package/installer/release implementation
- WCP-USER/WCP-ADMIN web runtime
- frontend/Node
```

Therefore F01, F03, F05, F09 and F10 remain deferred unless a later MASTER-selected slice activates them. This addendum does not reopen or alter their owner contracts.

## 2. Canonical source inputs

The following canonical repository sources were consumed:

| Source | Blob SHA | Role |
|---|---|---|
| `manifests/WCP360-BM-0001.md` | `27bedef6ff5a57c61a015ee874b33de5d3566061` | final baseline manifest / 8-of-8 state |
| `specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md` | `61edf698c50cf58779b75ba02fba913bf8276c61` | supported platform / DevOps invariants |
| `specifications/engineering/WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md` | `a2d5035454d90f0a252da7041b3a6b239265c557` | normative Engineering v0.3 declared inside retained legacy locator |
| `specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | `6d74f2f363524511424f839ca7262143c2af95ab` | security boundary / forbidden execution / secrets |
| `specifications/tests/WCP360_SC-M01_Test_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | `e0cdf924c5d7353624c069552f921f587cad8561` | QA execution/evidence/build families |
| `governance/handoffs/SC-M01-FIRST-CODE-DEPENDENCY-PLAN-2026-10-26.md` | `38273686e7a23c2bdea6c3b7d629c648af1e1e59` | MASTER-selected inert first slice and required F02/F04/F06/F07/F08 closure |

The non-canonical proposal `governance/proposals/SC-M01-ENGINEERING-F01-F10-CANDIDATE-DECISION-v0.3.md` was used only as dated technical input; this addendum is the owner decision for the activated subset.

## 3. F02 — exact daemon implementation language and toolchain

### 3.1 Language/runtime

```text
wcpd implementation language      = Go
wcp-privd implementation language = Go
canonical toolchain               = Go 1.27.3
GOOS                              = linux
GOARCH                            = amd64
normal build CGO                  = disabled
GOTOOLCHAIN                       = local
```

The first slice creates/builds only the inert `wcpd` target. Selecting Go for `wcp-privd` closes F02 language consistency but does **not** authorize privileged implementation.

### 3.2 Exact compiler provenance

Official Go distribution input:

```text
artifact = go1.27.3.linux-amd64.tar.gz
source   = https://go.dev/dl/go1.27.3.linux-amd64.tar.gz
sha256   = 65efbc80fdc1fa2cd2631cad5b481777a68e8725f34e89f50fc6ac714e379b40
```

Upstream release history records Go 1.27.3 as released on 2026-10-19 with a `crypto/x509` security fix.

### 3.3 Version/update policy

- The first implementation slice is pinned to **Go 1.27.3 exactly**.
- `GOTOOLCHAIN=local` is mandatory in CI/build commands; automatic toolchain download is forbidden.
- A Go 1.27 patch update requires an explicit DevOps/Engineering toolchain metadata update plus re-execution of the applicable QA build/unit/security gates.
- Any Go minor/major change requires owner review before use.
- Cancelled Sprint 0 toolchain choices are not valid provenance.

## 4. F04 — dependency source, lock and integrity policy

### 4.1 First-slice runtime dependency profile

```text
third-party runtime modules = NONE
standard library            = ALLOWED
```

The initial daemon foundation must not introduce a third-party application/runtime module. Any later addition requires an owner-reviewed dependency change before merge.

### 4.2 Module/integrity policy

The future daemon module must contain:

```text
repository module root = daemon/
Go module path         = github.com/yyogas/wcp360/daemon
minimum Go/toolchain   = Go 1.27.3
```

Policy:

- `go.mod` is mandatory.
- `go.sum` is mandatory once any non-standard-library module is introduced.
- `go mod tidy -diff` must report no required manifest/lock changes.
- `go mod verify` must succeed.
- Floating `@latest` is forbidden in product dependency declarations.
- Local-path `replace` directives are forbidden in the canonical branch.
- Direct VCS fallback is forbidden for the official first-slice build.
- Unreviewed pseudo-versions are forbidden.
- Vendoring is not used in this first slice; introducing it requires a later DevOps/Engineering decision.

Canonical networked restore environment:

```text
GOPROXY   = https://proxy.golang.org
GOSUMDB   = sum.golang.org
GOPRIVATE = empty
GONOSUMDB = empty
```

No `,direct` fallback is permitted.

After dependency/tool restoration, build and unit-test stages run with `GOPROXY=off`; cache/network availability must not change source semantics or build inputs.

### 4.3 Security scanner tool

```text
tool    = govulncheck
module  = golang.org/x/vuln/cmd/govulncheck
version = v1.9.0
source  = Go project module / go.googlesource.com/vuln
install = exact @v1.9.0 only; never @latest
```

`govulncheck` is a CI/security tool and is not an application runtime dependency.

## 5. F06 — module roots and canonical commands

### 5.1 Canonical repository/module/output roots

```text
repository root       = <repo>/
daemon Go module root = <repo>/daemon/
first binary target   = <repo>/daemon/cmd/wcpd
build output root     = <repo>/out/sc-m01/daemon/
first binary output   = <repo>/out/sc-m01/daemon/wcpd
coverage output       = <repo>/out/sc-m01/daemon/coverage.out
```

`out/` is generated build evidence, not source authority and must not be committed as product source.

No `cmd/wcp-privd` behavior is activated by this slice.

### 5.2 Networked restore / integrity phase

From repository root:

```sh
env \
  GOTOOLCHAIN=local \
  GOPROXY=https://proxy.golang.org \
  GOSUMDB=sum.golang.org \
  GOPRIVATE= \
  GONOSUMDB= \
  go -C daemon mod download

env \
  GOTOOLCHAIN=local \
  GOPROXY=https://proxy.golang.org \
  GOSUMDB=sum.golang.org \
  go -C daemon mod verify

env \
  GOTOOLCHAIN=local \
  GOPROXY=https://proxy.golang.org \
  GOSUMDB=sum.golang.org \
  go -C daemon mod tidy -diff
```

### 5.3 Offline quality/build phase

After successful restore:

```sh
mkdir -p out/sc-m01/daemon

test -z "$(gofmt -l daemon)"

env GOTOOLCHAIN=local GOPROXY=off \
  go -C daemon vet -mod=readonly ./...

env GOTOOLCHAIN=local GOPROXY=off CGO_ENABLED=0 \
  go -C daemon test -mod=readonly -count=1 \
  -coverprofile=../out/sc-m01/daemon/coverage.out ./...

env GOTOOLCHAIN=local GOPROXY=off GOOS=linux GOARCH=amd64 CGO_ENABLED=0 \
  go -C daemon build -mod=readonly -trimpath -buildvcs=true \
  -o ../out/sc-m01/daemon/wcpd ./cmd/wcpd
```

The implementation must record:

```sh
go version
go env GOOS GOARCH CGO_ENABLED GOTOOLCHAIN GOPROXY GOSUMDB
go -C daemon list -m all
sha256sum out/sc-m01/daemon/wcpd
```

### 5.4 Security scan phase

With network restricted to the Go vulnerability service:

```sh
govulncheck -test ./daemon/...
```

Equivalent execution from the module directory is allowed if it produces the same selected package set and records the exact command.

### 5.5 Deferred race gate

`go test -race` is **not activated for this inert slice** because no shared mutable/concurrent behavior is part of the selected implementation. If concurrency is introduced, the race gate becomes mandatory and F08 must explicitly add the required CGO/C compiler environment before that change merges.

## 6. F07 — exact quality gates for this slice

The following gate identifiers are canonical for the first daemon foundation. GitHub branch-protection status-check wiring may be activated only once the corresponding jobs exist.

| Gate | Tool/version | Required result |
|---|---|---|
| `daemon-toolchain` | Go 1.27.3 | exact version/platform recorded; mismatch fails |
| `daemon-mod-tidy` | Go 1.27.3 | `go mod tidy -diff` produces no diff |
| `daemon-mod-verify` | Go 1.27.3 | succeeds |
| `daemon-no-third-party-runtime-deps` | Go 1.27.3 | application module contains no third-party runtime module in this slice |
| `daemon-gofmt` | gofmt 1.27.3 | zero unformatted Go files |
| `daemon-vet` | go vet 1.27.3 | zero diagnostics |
| `daemon-unit` | go test 1.27.3 | all selected unit tests pass when implementation exists |
| `daemon-vulncheck` | govulncheck v1.9.0 | no reachable known vulnerability; any finding blocks pending Security disposition |
| `daemon-build` | go build 1.27.3 | linux/amd64 inert wcpd build succeeds |
| `daemon-evidence` | QA evidence contract | exact SHA/environment/tool versions/commands/results/digest retained without secrets |

No global numeric line/branch coverage threshold is introduced. Coverage is generated and retained; uncovered critical logic remains review-blocking under Engineering v0.3 regardless of aggregate percentage.

No universal complexity/duplication threshold is introduced. Engineering review policy remains the governing rule for this first slice.

## 7. F08 — exact Debian 13 amd64 build/test environment

Canonical base environment:

```text
distribution = Debian GNU/Linux 13.7 (trixie)
architecture = linux/amd64
OCI source   = docker.io/library/debian:13.7-slim
platform manifest digest =
sha256:918311b7b6c4c6f68b232ba516584925f6c78ad82b6fd534b98979df6438e483
```

This is the Docker Official Image platform-specific amd64 manifest for the selected Debian 13.7-slim image at the decision date.

The canonical toolchain layer is constructed only from the pinned Go 1.27.3 official archive and checksum declared in F02 plus explicitly pinned CI tools. No floating `debian:latest`, `debian:13`, `go:latest` or unpinned builder image is an official input.

### 7.1 Build identity

```text
CI build/test UID = 10001
CI build/test GID = 10001
root build/test execution = forbidden
```

Root may be used only while assembling the trusted ephemeral toolchain image before untrusted repository source is introduced. Normal dependency restore, formatting, vet, unit tests, vulnerability scan and compilation run non-root.

### 7.2 Network boundary

- Toolchain image assembly: only the declared official image source and `go.dev` toolchain source are permitted.
- Go dependency restore: only `proxy.golang.org` and `sum.golang.org`.
- Vulnerability scan: `vuln.go.dev` may be contacted by `govulncheck`.
- Compilation/unit-test phase: `GOPROXY=off`; no dependency download.
- No Docker daemon/socket, host root socket or privileged container is available to untrusted PR build/test jobs.

### 7.3 Secrets/tokens

The first-slice build/test requires **no production secret**.

Untrusted PR jobs must not receive:

- production credentials;
- release/signing keys;
- deployment credentials;
- privileged WCP360 secrets.

Repository checkout credentials, when required by the CI provider, are read-only/minimum-scope and are not passed into product tests.

### 7.4 Cache and reproducibility

- Caches are optimization only.
- Clearing Go build/module caches must not change declared dependency resolution or semantic output.
- Reproducibility evidence must include source SHA, base-image digest, Go archive checksum/version, module state, exact commands and output SHA-256.
- A no-cache clean run is required before MASTER accepts the implementation slice as integration-ready.
- Release/signing/promotion is not activated by this slice.

## 8. Engineering concurrence for activated decisions

This addendum consumes and closes the component-level selections left open by Engineering v0.3:

- **ENG-R06:** exact Go/compiler/runtime/build toolchain is now selected for the daemon slice.
- **ENG-R07:** deterministic formatter/static/security checks and exact tool versions are selected for the daemon slice.
- **ENG-R10:** required semantic check classes are mapped to concrete daemon gate identifiers; enforcement wiring waits for real jobs.
- **ENG-R14:** no-third-party-runtime-dependency profile and locked/proxied integrity policy are selected.
- **ENG-R18:** exact Debian 13 amd64 build provenance is selected for this slice; production release remains deferred.

Engineering v0.3 itself is unchanged. No E3 independent implementation review is claimed here; E3 remains required for the future implementation PR.

## 9. Security validation applicable to this slice

Security v1.0 constraints are preserved:

- no root requirement for `wcpd` foundation;
- no direct or indirect privileged operation;
- no socket/listener;
- no generic shell/command/sudo/systemctl/filesystem mutation capability;
- no production secret in source/build/test;
- dependency/tool provenance is explicit and pinned;
- vulnerability findings fail closed pending Security disposition;
- no privileged container/Docker socket in the untrusted build/test stage.

Because IPC-B, peer authentication, filesystem privilege and `wcp-privd` effects are not activated, their positive implementation tests are **not applicable to this first slice** and are not claimed PASS.

## 10. QA validation applicable to this slice

The canonical Test Baseline v1.0 is consumed as follows.

Applicable families:

```text
QA-SC-M01-BOOTSTRAP = READY FOR FUTURE EXECUTION
QA-SC-M01-BUILD     = READY FOR FUTURE EXECUTION
UNIT                = READY FOR FUTURE EXECUTION
INSTALL/BUILD       = only the build/toolchain subset, not installer/package tests
```

Required future evidence:

1. clean Debian 13 amd64 checkout uses only declared pinned inputs;
2. wrong Go version fails the `daemon-toolchain` gate;
3. module/lock drift fails visibly;
4. formatting/vet/security scan failures block;
5. actual unit tests are tied to exact candidate SHA;
6. inert `wcpd` build is non-root and has no socket/DB/privileged behavior;
7. evidence records exact OS image digest, Go version/checksum, commands, attempts, result and binary digest;
8. no repository CI result is relabeled as broad product QA PASS.

Current state:

```text
PRODUCT TESTS EXECUTED BY THIS ADDENDUM = 0
PASS = 0
FAIL = 0
IMPLEMENTATION-DEPENDENT TESTS = READY / NOT_EXECUTED
```

## 11. Deferred decisions

The following remain intentionally deferred because the first slice does not activate them:

| Decision | State |
|---|---|
| F01 — web/backend PHP/framework | DEFERRED |
| F03 — PostgreSQL/client/migration tooling | DEFERRED |
| F05 — frontend/Node | DEFERRED |
| F09 — runtime configuration/generated-artifact implementation | DEFERRED except existing immutable/product-state invariants |
| F10 — production SBOM/signing/release mechanism | DEFERRED; source/binary SHA evidence only for this slice |
| control socket / IPC-B | DEFERRED |
| database access | DEFERRED |
| privileged effects / wcp-privd implementation | DEFERRED |
| installer/systemd/package publication | DEFERRED |

These deferrals are not blockers for the inert daemon module/build/unit-test slice.

## 12. Owner publication decision

```text
OWNER =
DEVOPS / BUILD / RELEASE

ENGINEERING CONCURRENCE BASIS =
ENG-WCP-RULESET-0001 v0.3 / ENG-R06, R07, R10, R14, R18

ADDENDUM =
WCP360_SC-M01_DAEMON_FOUNDATION_TOOLCHAIN_ADDENDUM_v1.0.md

ADDENDUM VERSION =
v1.0

F02 =
APPROVED FOR SELECTED SLICE

F04 =
APPROVED FOR SELECTED SLICE

F06 =
APPROVED FOR SELECTED SLICE

F07 =
APPROVED FOR SELECTED SLICE

F08 =
APPROVED FOR SELECTED SLICE

FINAL APPROVED CONTENT =
YES

SECURITY VALIDATION =
APPLICABLE CANONICAL CONSTRAINTS CONSUMED / NO NEW SECURITY EXCEPTION

QA VALIDATION =
APPLICABLE BUILD/BOOTSTRAP/UNIT FAMILIES READY / NOT EXECUTED

CONTENT CHANGE =
ADDITIVE COMPONENT-SCOPED TOOLCHAIN DECISION

DEVOPS BASELINE VERSION =
v1.0 UNCHANGED

ENGINEERING RULESET VERSION =
v0.3 UNCHANGED

READY FOR SOURCE CODE CONSUMPTION =
YES — ONLY AFTER MASTER ISSUES THE LIMITED FIRST-SLICE RESUME AUTHORIZATION

BLOCKER =
NONE WITHIN F02/F04/F06/F07/F08 FOR THE SELECTED INERT SLICE

CODE-BASELINE =
NONE

SC-M02 =
NOT AUTHORIZED
```
