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
