# WCP360 repository working instructions

Scope: this repository. External Grav sites, vendor services and local XAMPP
workspaces retain their own source, contracts and dependency graphs.

## Current authority and orientation

Start with [CONTRIBUTING](CONTRIBUTING.md), [SECURITY](SECURITY.md), the
[Core working guide](WCP360_Core/WORKING-GUIDE.md) and the
[November 4 consolidation record](documentation/governance/decisions/MASTER-CONSOLIDATION-2026-11-04.md).
The local documentation site `http://localhost/WCP360-DocumentationOfDeveloppement/`
is a useful entry point only when available in the operator's own environment;
it must not prevent remote work from using the versioned repository sources.

Read only relevant files and changed deltas. Do not restart H3, rescan more than
1000 Markdown files, reconstruct missing original archives, or revive Sprint 0.
A folder, plan, catalogue entry or passing test is not production qualification.

The [manifest](documentation/manifests/WCP360-BM-0001.md) preserves 8/8 accepted
inputs and limited inert-daemon SC-M01 authorization. Core M1-S1 is a separately
bounded development/test slice in #130. SC-M02 is not authorized. Do not confuse
historical paused snapshots with current explicit authorization, or extend that
authorization to an unrelated hosting/provisioning slice.

## Single-maintainer workflow

Follow [D-GOV-SOLO-0001](documentation/governance/decisions/D-GOV-SOLO-0001.md).
The owner's alternate accounts are not independent reviewers. Record assistant
assessment as same-author review, never as independent E3 or invented consent.
For each change, identify exact HEAD/base, actual diff, contract/security impact,
executed tests, unresolved defects, rollback and the separate integration decision.

Never create a branch, including a temporary test or replacement branch. Work on
existing branches and PRs. No direct permanent-branch push or force push. Preserve
unrelated changes and branch history. A merged PR does not prove branch deletion
or activation of GitHub protection. Delete only explicitly requested refs after
confirming their latest HEAD is integrated and no dependent work would be lost.

Write new/modified code, comments, tests and developer documentation in English.
User-facing translations may use i18n. Do not rename canonical identifiers,
standard paths, dependencies or historical sources as a language cleanup.

## Architecture and security

PHP/Symfony Core, Twig/UX/Turbo/Stimulus and PostgreSQL remain distinct from the
non-root Go agent and narrowly authorized privileged broker. No generic shell,
SQL, systemctl or arbitrary privileged-execution proxy is allowed.

Core must remain operable without optional packages or vendor connectivity.
Keep internal licensing/catalogue/distribution services out of public builds.
Preserve WCP-USER/WCP-ADMIN origin, runtime, cookie, authorization and data boundaries.
MFA enrollment is optional on both surfaces; an enabled factor remains enforced.
The frozen Security export and policy helper still require the targeted #132
alignment; do not silently alter them or assert production compliance.

Contracts remain owned by their domains. Cross-domain changes require a targeted
HANDOFF; contradictions require an explicit CONFLICT/decision. No speculative
DDL, new permissions or operation IDs. Denied/unapproved privileged bindings
remain unavailable even when a caller has a broad service-management permission.

## Checks and evidence

Use [TESTING](documentation/development/TESTING.md), the
[security matrix](documentation/development/SECURITY-TEST-MATRIX.md), and the
[quality catalogue](documentation/development/QUALITY-SECURITY-STACK.md).
Do not install global tooling or transmit credentials implicitly. Local application
and service execution remains stopped; use authorized existing CI for PHP, Go,
browser, database and Linux runtime verification. Static repository-tool tests
and documentation checks do not start those services.

Reference commands in an authorized environment, after documented prerequisites:

| Scope | Checks |
| --- | --- |
| Core with PHP 8.4, locked dependencies, planner and private test database | `composer check` |
| Browser candidate with Chromium/ChromeDriver and private fixtures | `composer test:browser` |
| Each existing Go module | `go test ./...`, `go vet ./...`; race/fuzz/Linux effects only within scope |
| Documentation/repository tooling | Whitespace, changed links, preserved identifiers, configured offline validators |

Test negative paths, wrong roles/surfaces/tenants, CSRF, malicious input, revoked
credentials, expiry, races and uncertain outcomes relevant to the change. Use
synthetic fixtures and private disposable storage; never destructive customer
operations or production secrets. Do not weaken analysis or suppress failures to
make CI green. Report passed, failed, not_run, not_configured or not_applicable
truthfully, bound to exact source and environment. A source/base change requires
renewed integration evidence. CODE-BASELINE remains NONE.
