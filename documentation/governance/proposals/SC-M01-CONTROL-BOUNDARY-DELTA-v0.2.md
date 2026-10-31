# WCP360 — Control Boundary backend/worker → wcpd — proposal v0.2

Date: 2026-10-02
Status: PROPOSAL — REVIEW — NON-NORMATIVE
Supersedes for review: SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md
Related finding: AUDIT-WCP-0105

This v0.2 incorporates recovered final M1 owner consensus without promoting the recovery note into a canonical owner artifact.

Evidence companion:

governance/proposals/SC-M01-RECOVERED-CONTROL-BOUNDARY-M1.md

## 1. Points no longer treated as blank design

Recovered final H3-CM M1 consensus states:

    socket path = /run/wcp360/wcpd.sock
    owner = wcpd
    group = wcp360-control
    mode = 0660

Default transport clients:

    wcp360-user
    wcp360-admin

Default denied identities:

    wcp360-worker
    hosting identities

This aligns with current final DevOps/Security use of wcp360-control for WCP-USER/WCP-ADMIN and no supplementary group for the worker.

Therefore C01 is reclassified:

    C01 = RECOVERED OWNER CONSENSUS
          REPUBLICATION / OWNER CONFIRMATION REQUIRED
          NOT A NEW DESIGN DECISION

SOURCE CODE still cannot consume the recovered note as canonical authority until the owner republishes/confirms it in the current normative source.

## 2. C02 — transport access does not equal authorization

Current DevOps final already states that wcp360-control grants transport access only and does not replace authentication, authorization, entitlement, ownership, delegation, tenant or policy checks.

Current Security final independently requires the full authorization intersection.

Therefore:

    C02 = ALREADY DEFINED
    NORMATIVE EVIDENCE = DevOps v1.0 §5.1 + Security v1.0 §1
    REQUIRED NEW DECISION = NONE

Tests are still required.

## 3. Remaining owner decisions

Only C03–C07 remain actual control-boundary decisions.

### C03 — USER/ADMIN surface binding

Required:

    Unix peer identity
    → permitted origin surface(s)
    → accepted authentication/context class
    → action/permission/resource validation

A wcp360-user transport peer must not act under an ADMIN context.
A wcp360-admin peer must not silently downgrade/cross-bind to a USER context to bypass a rule.

A shared socket does not collapse surfaces.

Owner:
API/IPC + Security.

### C04 — worker normal-control path

Historical M1 consensus denied wcp360-worker access to the control socket by default.
Current DevOps also gives the worker no supplementary group and forbids direct IPC-B.

Therefore:

    W1 direct worker client of /run/wcp360/wcpd.sock
    = CHANGE TO HISTORICAL CONSENSUS
    = requires explicit owner amendment

The non-amending default direction is:

    worker
    → durable job/state/control-plane mechanism
    → authorized control component
    → wcpd when required

But the exact producer/consumer/claim mechanism is still open and depends on Database/jobs.

C04 remains OPEN for the exact indirect path.

### C05 — trusted authorization-context resolution

C05 is **OPEN / PARTIALLY DEFINED**, not blank design.

Current canonical Security already defines the trusted-context fields, rejects direct browser bearer use, requires unknown permission/action/IPC binding to DENY with no side effect, and states:

    wcpd validates the origin (permission_id, action_id) binding
    wcp-privd enforces bounded ipc_operation_id and target constraints

Current IPC-B already carries `authorization_context_ref`.

Recovered final H3-V2 evidence additionally states that `authorization_context_id` is created/validated by `wcpd` and that the trusted context preserves origin permission/action, resource, tenant when applicable, job, correlation, technical IPC operation and decision/time. This context is policy/accountability data, not peer authentication.

Evidence companion:

    governance/proposals/SC-M01-RECOVERED-AUTHORIZATION-CONTEXT-OUTCOME.md

The remaining owner decision is narrower:

- exact reference creation/representation;
- storage/lookup/resolver;
- integrity/authenticity of resolved context;
- expiry/revocation;
- execution-time freshness/revalidation;
- unavailable resolver behavior;
- replay/substitution prevention;
- retention/audit lifecycle;
- exact worker interaction.

No database table, signed token, TTL or cache model is implied by the recovery.

C05 remains OPEN for this unresolved lifecycle, but owners should not redesign the already-defined field/binding responsibilities.

### C06 — control protocol

The physical socket evidence does not define the complete control protocol.

Owner must publish:

- protocol/version identifier;
- framing;
- request/response envelope;
- max sizes;
- timeouts;
- error model;
- correlation/job semantics;
- idempotency/retry behavior;
- compatibility rules.

Do not copy IPC-B mechanically. The trust boundary and clients differ.

C06 remains OPEN.

### C07 — wcpd responsibility boundary

Owner must state which operations belong to:

- application/backend;
- control daemon;
- persistent job system;
- privileged broker.

At minimum the architecture must preserve:

    no generic command passthrough
    no arbitrary ipc_operation_id from UI text
    no caller-declared permissions becoming trusted
    no direct backend/worker → wcp-privd fallback

C07 remains OPEN.

## 4. Revised QA scenarios

The v0.1 CQ01–CQ12 remain useful, with these clarifications:

- CQ01 now verifies the recovered/republished socket peer policy.
- CQ07 direct worker → IPC-B remains forbidden.
- CQ08 direct worker → control socket is expected DENY under recovered M1 unless owners explicitly amend C04.
- CQ09/CQ11 physical claim/recovery assertions remain dependent on Database #6.

No test is executed by this proposal.

## 5. Revised decision status

    C01 = RECOVERED OWNER CONSENSUS / REPUBLICATION REQUIRED
    C02 = ALREADY DEFINED
    C03 = OPEN
    C04 = OPEN
    C05 = OPEN / PARTIALLY DEFINED
    C06 = OPEN
    C07 = OPEN

Actual unresolved control-boundary decisions:

    5

Evidence/normalization points not requiring new design:

    2

## 6. Owner response expected

For C01:

    CONFIRM HISTORICAL CONSENSUS / AMEND WITH VERSION IMPACT
    CANONICAL DESTINATION =
    OWNER =
    COMMIT =

For C03–C07:

    ACCEPT / AMEND / REJECT / ALREADY DEFINED
    NORMATIVE EVIDENCE =
    REQUIRED DELTA =
    VERSION IMPACT =
    TESTS =
    DEPENDENCIES =

## 7. Gate

    CANONICAL CONTENT CHANGED BY THIS FILE = NONE
    APPLICATION CODE = NONE
    SC-M01 = PAUSED — TARGETED EVIDENCE
    SC-M02 = NOT AUTHORIZED
    CODE-BASELINE = NONE

The recovery reduces ambiguity; it does not bypass owner republication or the 8/8 gate.
