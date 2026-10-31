# WCP360 — SC-M01 control boundary C03/C06/C07: decision-ready options v0.1

**Date:** 2026-10-26  
**Status:** R&D PROPOSAL — REVIEW REQUIRED — NON-NORMATIVE — NOT SOURCE-CODE CONSUMABLE  
**Owners for normative acceptance:** API / IPC / CONTRACTS (C06/C07), SECURITY / HARDENING (C03/trust), DEVOPS / BUILD / RELEASE (socket/service), DATABASE (durable jobs), TESTS / QA (cases)  
**References:** WCP360-BM-0001 FINAL; API 1.0; IPC-B/1; Security v1.0; DevOps v1.0; DOC v4.0.21.23; DESIGN V2.5.3.36.4  
**Tracking:** #13, #41; AUDIT-WCP-0105, 0107, 0108, 0117/0118 where applicable  
**Baseline change:** NONE. **SC-M01:** PAUSED. **CODE-BASELINE:** NONE.

## 1 — Scope

C03 (peer-to-surface mapping), C06 (unpublished backend-to-wcpd wire protocol), C07 (responsibility split) directly block the first safe control-plane daemon integration.

This memo separates **existing owner-final constraints** from **proposed decisions**. It does NOT republish the unavailable C01 owner-final document, assign a new canonical protocol version, redefine IPC-B, or authorize production code.

### 1.1 Already established in currently final sources

- Distinct service/runtime Unix identities: `wcp360-user`, `wcp360-admin`, `wcpd`, `wcp360-worker`; only the two web runtimes have transport membership in `wcp360-control` by default.
- WCP-USER and WCP-ADMIN are distinct security and authorization surfaces.
- Backend does not call root broker directly; `wcpd` is non-root and the sole normal client of privileged IPC-B.
- `wcp-privd` validates bounded technical `ipc_operation_id` and resource constraints; `wcpd` validates origin permission/action binding; no generic privilege forwarding.
- Socket IPC-B: `/run/wcp360/priv/wcp-privd.sock`, root:`wcp360-priv`, 0660, `SO_PEERCRED` exact wcpd identity; strict length-prefixed JSON/size bounds and bounded operations are explicitly canonical **for IPC-B only**.
- `origin_surface` exists in the trusted authorization context. Group membership grants only transport access, never application authorization.
- Recovered C01/M1 consensus cites `/run/wcp360/wcpd.sock`, `wcpd:wcp360-control`, 0660; current owner republication/ratification is still required.
- Worker has no direct privileged socket and no control group access by default.
- Audit is durable; mutation intent is persisted before privileged dispatch; post-dispatch uncertainty must become `UNKNOWN_OUTCOME` and reconcile before any retry.

**Important:** The IPC-B framing/limits must not be silently assumed to govern the distinct control socket.

## 2 — Explicit decisions required

| Decision | Already fixed | Still unapproved / decision needed |
|---|---|---|
| C01 | Historical owner consensus on one control socket | Current DevOps/API owner republication and provenance |
| C03 | USER/ADMIN Unix identities and separated authorization surfaces | Exact SO_PEERCRED UID/cgroup→surface binding, mismatches, unknown peers, any service/system surface |
| C06 | Backend→wcpd boundary exists | Protocol identity, request/response, framing, size/deadline/retry/errors, compatibility, context resolution |
| C07 | wcpd control orchestration + origin auth binding; wcp-privd technical enforcement | Who persists jobs/context, when authorization is repeated, async result reconciliation, worker integration |
| C04 | Worker has no direct privileged IPC-B access | Indirect authorized worker → control path; requires DB #6 if persistent |
| T01 | 5s per full frame read/write recovered owner-final for IPC-B | Do not assign the same limit to control by accident |
| T02/T03/T04 | Deadline field and UNKNOWN_OUTCOME principles exist for IPC-B | Control and IPC-B clock/budget composition and precise before/after dispatch errors |
| S06 | Deny/restrictive policies remain mandatory | Positive account override ceiling unresolved; never infer escalation from candidate entitlements |

## 3 — Proposed C03 peer/surface binding

### Option C03-A — one control Unix socket, explicit UID mapping (recommended for owner review)

Current C01 recovered socket is used as a single transport endpoint. On each accepted connection, wcpd obtains Linux `SO_PEERCRED`, validates the actual kernel peer identity and expected service context, then derives **allowed surface**:

| Peer (kernel identity) | Proposed accepted declared origin_surface | Result |
|---|---|---|
| `wcp360-user` service | `WCP-USER` only | Permit to continue to application authorization checks |
| `wcp360-admin` service | `WCP-ADMIN` only | Permit to continue to application authorization checks |
| `wcp360-worker` | NONE by default | Reject control socket transport |
| unknown Unix UID / hosting user | NONE | Reject transport |
| legitimate peer asserting opposite surface | NONE | Reject, audit mismatch, **no fallback** |
| system/installer peer | NOT DEFINED | Separate owner decision; no implicit bypass |

Mapping must be checked again against a **trusted server-side resolved authorization context**, not the self-declared request. Control group membership alone never authorizes any action.

**Advantages:** consistent with recovered C01; explicit audit source, fewer sockets. **Risks:** larger parser surface shared by two trust classes; requires strict peer→surface isolation and denial semantics.

### Option C03-B — separate USER and ADMIN control sockets

Distinct sockets/Unix groups/policies with independent peer admission. Stronger OS-level partition at cost of changing recovered C01/DevOps runtime membership and multiplying lifecycle and tests. Do **not** adopt without a revised owner-approved C01/DevOps contract and Architecture Review.

**Recommendation for owner decision:** A, contingent on reliable UID/service checks and explicit negative tests; do not treat as already FINAL.

## 4 — Proposed C06 wire protocol

### Choice C06-A — bounded length-prefixed typed JSON (proposed, not IPC-B copy)

Define a **separate versioned control protocol namespace**, candidate label `WCP360-CONTROL/1` (NOT RESERVED/APPROVED), with:

- `uint32` big-endian frame byte length, then exactly that many bytes of strict UTF-8 JSON object.
- Candidate size limits: request 32768 bytes, response 131072 bytes. Reject zero-length, trailing bytes, unknown/duplicate JSON keys, invalid JSON/UTF-8, NaN/Infinity, oversize, wrong version and incompatible schema.
- Candidate connection budget: 1s; full-frame read/write budget: 5s; bounded operation/ack budget decided per typed control operation. Client-to-server effective deadline composition is **not yet final**.
- Strict typed operation allowlist. No `shell`, `exec`, `sudo`, `systemctl`, arbitrary file path, `ipc_operation_id`, caller-provided executable, role or permission grant is accepted in the control payload.
- Idempotency and `UNKNOWN_OUTCOME` are **not** solved by framing. Mutation IDs, durable intent, result lookup and retry conditions depend on owner-approved API/DB contract.
- No secrets, cookies, raw bearer tokens, browser credentials, private keys or session blobs in control messages.

**Rationale:** deterministic framing, bounded parsing and typed envelopes align with verified patterns in IPC-B while remaining an explicitly independent owner decision. Reuse *concepts*, not automatically the physical IPC-B version or limits.

### Choice C06-B — established control IPC transport library / typed binary encoding

Owner may evaluate a pinned transport/runtime that supports peer credentials, strict schema versioning and bounded message sizes without leaking trust context. Requires R&D/DevOps review of packaging/toolchain F01/F02, dependency burden and downgrade compatibility. Do not choose implicitly.

**Recommendation for owner review:** C06-A for minimal dependencies in the initial Debian 13/amd64 foundation; final parameters MUST be ratified and versioned by API/IPC, Security and DevOps.

### Proposed request envelope — illustration, NOT normative

```json
{
  "protocol_version": "WCP360-CONTROL/1-CANDIDATE",
  "request_id": "11111111-1111-4111-8111-111111111111",
  "correlation_id": "22222222-2222-4222-8222-222222222222",
  "origin_surface": "WCP-USER",
  "authorization_context_ref": "33333333-3333-4333-8333-333333333333",
  "control_operation_id": "job.intent.submit",
  "action_id": "applications.create",
  "resource_type": "application",
  "resource_id": "44444444-4444-4444-8444-444444444444",
  "idempotency_key": "submitted-key-example",
  "payload": {
    "intent_ref": "55555555-5555-4555-8555-555555555555"
  }
}
```

Here `authorization_context_ref` is an opaque local reference resolved by wcpd **from a trusted owner-approved producer**; it is **not** a bearer credential and the browser cannot supply it. The example `control_operation_id` and its payload are placeholders requiring owner registration.

### Proposed response envelope

```json
{
  "protocol_version": "WCP360-CONTROL/1-CANDIDATE",
  "request_id": "11111111-1111-4111-8111-111111111111",
  "correlation_id": "22222222-2222-4222-8222-222222222222",
  "outcome": "ACCEPTED",
  "job_ref": "55555555-5555-4555-8555-555555555555",
  "error_code": null,
  "reconciliation_required": false
}
```

This does not imply the job is completed or the privileged operation succeeded. Unknown post-dispatch outcomes are not transformed into transport `SUCCESS`.

### Mandatory C06 owner decisions before implementation

1. Final protocol namespace/version and compatibility/deprecation.
2. Exact wire format, framing and maximum sizes.
3. Accepted operation IDs, schemas and origin-to-technical binding.
4. Peer UID/service verification and secure socket lifecycle.
5. Authorization context producer, persistence, expiry, revocation, revalidation and TOCTOU rule.
6. Request ID, correlation ID, job ID, idempotency semantics and uniqueness.
7. Deadline representation and effective budget from web request through IPC-B.
8. Safe typed error/response mapping, including truncation and lost reply.
9. Redaction, audit event IDs, trace and incident correlation.
10. Reconciliation/worker/job state ownership, duplicate delivery and crash recovery.
11. Exact tests and observable evidence.

## 5 — Proposed C07 responsibility boundary

| Step | Proposed owner (NOT FINAL) | Must be validated with |
|---|---|---|
| Browser HTTP/session/CSRF/CORS | WCP-USER or WCP-ADMIN backend | Security S01–S04, API H01–H10 |
| Business input validation and entitlement | Backend application service | Modules, Security S05–S09 |
| Durable idempotency + operation intent + required audit/outbox | Backend transaction service (candidate) | Database #6, API T05/T06 |
| Trusted authorization context creation | Backend authorization service (candidate) | Security/Authz C05 and Database |
| Peer verification, origin action/permission binding, current resource/revision recheck | `wcpd` | Security C03/S05, API D01–D03 |
| IPC-B operation selection and dispatch | `wcpd` sole IPC-B client | API/IPC canonical 47, Security |
| Bounded primitive and resource/OS validation | `wcp-privd` | Final IPC-B and Security |
| Observed result + reconciliation | `wcpd` with durable DB owner path (candidate) | Database T07, SRE, QA |
| Web response for accepted async job | Backend, never a fictional final success | API H05/H06, QA |
| Worker retries/cancellation | No direct privileged access, exact indirect path not yet decided | DevOps C04, DB #6 |

**Do not implement this split as authority** until all consumers and producers approve ownership and persistence.

## 6 — Failing tests to define (PLANNED only)

| Candidate case | Target | Required observable result |
|---|---|---|
| CTRL-Q01 | Unknown Unix UID | Refuse connection/request; no state/effect |
| CTRL-Q02 | USER UID + ADMIN claim | DENY; no surface fallback |
| CTRL-Q03 | ADMIN UID + USER claim | DENY; no surface fallback |
| CTRL-Q04 | Missing/expired/revoked context | DENY; no dispatch |
| CTRL-Q05 | Caller-provided privilege/IPC operation | Reject unknown field, no dispatch |
| CTRL-Q06 | Bad frame/truncation/extra bytes | Reject before business action |
| CTRL-Q07 | Duplicate JSON keys | Reject, never last-key-wins |
| CTRL-Q08 | Malformed UTF-8 or JSON NaN | Reject |
| CTRL-Q09 | Request size above limit | Reject without allocation amplification |
| CTRL-Q10 | Unknown protocol version or operation | Reject; no fallback |
| CTRL-Q11 | Missing idempotency on mutation | Reject; no effect |
| CTRL-Q12 | Same key different semantic request | Idempotency conflict, no duplicate mutation |
| CTRL-Q13 | Lost response after effect | UNKNOWN_OUTCOME; reconcile; no blind retry |
| CTRL-Q14 | Worker trying direct IPC-B/control without authorization | Refuse |
| CTRL-Q15 | Resource tenant differs from trusted context | DENY; no dispatch |
| CTRL-Q16 | Stale revision / expired lease | Reject stale executor update |
| CTRL-Q17 | Mandatory audit persistence fails | DB mutation rolled back or durable unknown-outcome evidence |
| CTRL-Q18 | Invalid server-side peer credentials context | Refuse even if socket group admits transport |

No case is `READY`, `PASS`, `FAIL` or a canonical TEST ID by virtue of this memo. Positive fixtures and expected error registry remain conditional on owner decisions.

## 7 — R&D parser fixture

`research/architecture-lab/sc-m01-control-protocol/` contains an **inert pure-Python parsing/trust-assertion proof-of-concept** for C03-A/C06-A. The code does not open sockets, inspect real kernel credentials, persist jobs, authorize a real caller, or execute privileged operations. Synthetic peer identities and trusted-context dictionaries are **explicit test fixtures**, not trusted substitutes for production `SO_PEERCRED` or authorization.

Passing R&D tests proves only selected parser rejection behavior, not production readiness or C06 correctness/approval.

## 8 — Owner decision response form

```text
DECISION = C03 / C06 / C07 / C01 (when applicable)
OWNER =
EXISTING FINAL SOURCES = exact path + Git SHA
SELECTED OPTION =
REJECTED OPTIONS / SECURITY TRADEOFFS =
EXACT NORMATIVE SCHEMA/ERROR/TIMEOUT/RETRY RULES =
PEER→SURFACE TABLE =
CONTEXT LIFECYCLE / REVOCATION =
JOB/IDEMPOTENCY/DATABASE DEPENDENCIES =
WORKER / RECOVERY BOUNDARY =
VERSION IMPACT =
QA SCENARIOS (PLANNED/READY only) =
CROSS-OWNER REVIEW =
STATUS = DRAFT / REVIEW / APPROVED
```

Ratification chain: API/IPC + Security + DevOps + Database (where persistence needed) → Audit/Architecture Review → MASTER → versioned canonical owner contract → Tests/QA → Source Code only when gate satisfied.

## 9 — Gate

```text
C03 = OPEN / PARTIALLY DEFINED
C06 = OPEN
C07 = OPEN / PARTIALLY DEFINED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

No `src/`, DDL, migration, daemon, systemd service or release is authorized by this proposal.
