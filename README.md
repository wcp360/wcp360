# WCP360

WCP360 is a web hosting control-plane project with two explicitly separated application surfaces:

- **WCP-USER** — customer / reseller hosting administration.
- **WCP-ADMIN** — server, infrastructure, services and orchestration administration.

## Current governance state

This repository is the future versioned source of truth for WCP360 specifications, canonical implementation artifacts, architecture decisions, tests and implementation.

Current implementation gate:

```text
WCP360-BM-0001 = FINAL
PHASE K = GO

SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED

CODE-BASELINE = NONE
```

No production implementation is considered canonical until the SC-M01 implementation package is produced, independently reviewed and approved.

## Active baselines

```text
DOC-BASELINE = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4

MODULE REGISTRY VERSION = v1.0
PERMISSION REGISTRY VERSION = v1.0
SECURITY BASELINE VERSION = v1.0
DB MODEL VERSION = v1.0
API CONTRACT VERSION = 1.0
IPC CONTRACT VERSION = WCP360-IPC-B/1
TEST BASELINE VERSION = v1.0
DEVOPS BASELINE VERSION = v1.0
OS SUPPORT MATRIX VERSION = v1.0
OBSERVABILITY BASELINE VERSION = v1.0
PERFORMANCE BASELINE VERSION = v1.0
ENGINEERING RULESET = ENG-WCP-RULESET-0001 v0.2
```

Initial supported platform:

```text
Debian GNU/Linux 13 (trixie)
amd64 / x86_64
```

## Security invariant

Privileged execution must preserve:

```text
backend
→ wcpd (non-root)
→ bounded authenticated IPC
→ wcp-privd (minimal root TCB)
→ explicit privileged primitive
```

Generic privileged execution such as `shell.exec`, arbitrary command execution, generic sudo/systemctl proxying, arbitrary signals, caller-controlled executables and unrestricted filesystem primitives is forbidden.

## Repository rule

Approved specifications and decisions must be versioned here so that ChatGPT conversations are never the sole source of truth.

Cancelled or superseded material must never be reintroduced as active implementation authority.

Notably excluded:

- cancelled Sprint 0;
- cancelled V4.1 “Executable Contracts”;
- obsolete `WC-CLIENT` / `WC-ADMIN` naming.


## SC-M01 evidence tracking

Canonical artifact status is tracked in:

- `governance/CANONICAL-ARTIFACT-INDEX.md`
- `governance/SC-M01-CANONICAL-ARTIFACT-STATUS.md`
- GitHub issue #13

SOURCE CODE must not resume until the canonical evidence gate reaches 8/8.

## Current project status

The current repository/project status is maintained in:

`governance/PROJECT-STATUS.md`

For the latest reviewable SC-M01 owner priorities, unresolved 8/8 evidence and current non-production CI/research state, read [SC-M01 Current Readiness — 2026-10-27](governance/SC-M01-CURRENT-READINESS-2026-10-27.md). This coordination snapshot does not replace canonical owner contracts or authorize production coding.

## Publication verification — 2026-10-02

See the [publication verification report](governance/publication/PUBLICATION-VERIFICATION-2026-10-02.md) for exact commits, paths, hashes, CI evidence and remaining source gaps.

At the inspected main commit, all **147/147 mirror entries match their committed manifest**, and all four AUDIT-WCP-0100 files are present. This is not certification that every original archive, owner export or binary asset is published. PR #42 maintenance and PR #43 contract proposals are separately published for review; branch presence is not a merge or owner approval.

The report also locates all 114 full Design Markdown mirror entries, distinguishes them from the smaller dedicated directory, and records the previously unpublished [project-flow visual](documentation/visuals/README.md). The seven original branding PNGs are still **not imported**; their [pending-source catalog](governance/publication/evidence/pending-binary-assets-2026-10-02.json) is not a substitute for their bytes.

Implementation gates and all canonical baseline versions above remain unchanged.
