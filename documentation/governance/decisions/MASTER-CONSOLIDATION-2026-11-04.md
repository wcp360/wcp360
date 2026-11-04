# MASTER — targeted existing-branch documentation reconciliation

Date: 2026-11-04. Scope: PR #133, the two existing commits ending at
`f35ac5765f864820e658bb0ef0e31c800e1f72c3`, reviewed against the current MASTER
records. This is same-author/single-maintainer review, not independent E3.

## Accepted documentary material

Preserve the recorded Free/unlimited, optional administrator MFA, reseller scope,
account-naming and explicit twelve-operation DENY product decisions, with their
stated implementation/contract limits. Preserve the production-readiness guide,
SDK external-checkout corrections and upgrade-helper limitations. No license
text, dependency lock, runtime, migration, permission ID or frozen export changes.
Commercial licensing direction is not a commercial license grant. Pricing in the
November 3 record remains a dated owner statement, not a new validated offer.

## Resolved documentary contradictions

The candidate incorrectly treated an earlier publication-stage PAUSED paragraph
as superseding the manifest's current limited-resume section. Current authority
is 8/8 accepted inputs, inert-daemon SC-M01 only, separate Core M1-S1 development
scope in #130, SC-M02 unauthorized, CODE NONE. CONTRIBUTING and STATUS now state
this explicitly. No additional hosting/WS1 or privileged scope is authorized.

The candidate changes to `WCP360_Core/WORKING-GUIDE.md`,
`WCP360_Core/src/Module/Hosting/WEB-SERVER-ROADMAP.md` and
`documentation/governance/PROJECT-STATUS.md` were not accepted as active gate
updates: their preceding repository versions are retained to avoid importing a
false blanket stop. This is an explicit review resolution, not lost history;
the original candidate remains in PR ancestry. Their historical branch/status
sections are not current integration authority. Use this record and current
MASTER #131/#130 for the current existing-PR-to-develop workflow.

Useful WS1 detail from the rejected gate update is preserved here: a proposed
Debian 13 harness would feed unchanged plan/2 artifacts to native Nginx, Apache
event-module and FPM checks, then exercise loopback static content, .htaccess
front-controller routing and protected-file/upload-PHP refusals with exact
artifact hashes/package versions. This documentation supplies no evidence that
that harness ran and does not authorize extending or running it. Missing older
coordination snapshots must not be reconstructed or used as current authority.

The added AGENTS guide is English. Localhost Grav is an optional operator-local
entry point, not a prerequisite that blocks remote repository work. New code and
developer documentation remain English; no wholesale legacy rename is requested.

## Privileged-operation record clarification

The November 3 IPC DENY decision is restrictive: its twelve listed operations
remain unavailable until individually approved. Its reference to SC-M01 being
paused describes its recording context, not today's active inert/M1-S1 scopes.
It supplies no runtime enforcement tests and does not by itself close #91 or
AUDIT-WCP-0104. Frozen owner sources and operation-specific approvals still govern.

## Integration and retirement boundaries

PR #133 uses the already-existing docs branch and an exact route exception.
It incorporates the already-merged #128 guardrails to test against the actual
develop base. No new branch, release or deployment is created. Integration needs
terminal applicable checks and a separate MASTER decision; cleanup needs the
exact branch HEAD preserved in target ancestry and actual ref-deletion evidence.
A closed PR is not proof its branch was deleted. #129 remains the live protection
record; no settings change is claimed by committing disabled templates.

DOC v4.0.21.23 / DESIGN V2.5.3.36.4 / WCP360-BM-0001 FINAL / CODE NONE.
