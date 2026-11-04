# Targeted technical remediation and retained acceptance criteria

Date: 2026-11-04. Base: `d72945ba8ec36d6b68b818a371cb3cf39612a12e`.
Work: PR #134, existing historical work ref, D-TECH-RESOLUTION-0001.
This report supersedes neither the historical finding evidence nor an owner
contract. Closed/not_planned issue state is not technical completion.

## Actual source work

- The common PHP session policy now rejects future activity for all four kinds.
  Original bytes were verified against the Git metadata inventory. A standalone
  PHP 8.4.24 probe reproduced four failures on the prior helper; the changed
  helper passed all 32 checks. Eighteen PHPUnit cases are added for full CI.
- The candidate Go server uses the same decode-before-handler function tested
  by the new admission suite. Six top-level tests / 50 subtests cover all twelve
  denied names and their IPC-WCP-1005..1016 identifiers, aliases, caller authority
  fields, foreign framing/schema, stale revision, malformed envelopes and the
  two already-existing valid candidate operations. Reader failure and a missing
  handler are separately rejected. Denied input never reaches the handler and
  never echoes untrusted job/site IDs as a successful response.
- Local Go 1.23.2 tests, including the race detector, passed on the actual copied
  protocol and planner files/templates (their original Git blob hashes checked).
  This was a dependency-free file-set test, not execution of the complete Linux
  server or a production service. A local mutant skipping strict Decode failed
  the negative tests as intended, then was discarded. Full repository CI remains
  necessary, particularly to compile and run the modified Serve integration.
- The routing guard permits only PR #134 + the exact existing historical ref +
  develop, in addition to earlier valid routes. Ten allowed / 35 rejected route
  fixtures pass. No arbitrary legacy branch family, new branch or bypass.

These candidate rejection tests are **not IPC-B runtime certification**. The
candidate wire reply is REJECTED, not a claimed implementation of canonical
AUTHORIZATION_DENIED. No new dispatcher, IPC ID or privileged operation is added.
The twelve-operation owner DENY decision remains effective until separately
approved exact bindings and implementation-bound canonical tests exist.

## All 18 audit findings: current scope and remaining evidence

| Finding | Technical assessment | Remaining acceptance |
|---|---|---|
| 0101 readiness vs artifact presence | Partial: source/test/production states distinguished | Consistent current-state consumers; ticket state must never imply PASS |
| 0102 owner artifacts | 8/8 canonical input gate delivered | QA/Engineering owner provenance for the complete applicable scope |
| 0103 API transport | Candidate source exists, not complete endpoint qualification | Exact activated route/DTO/auth/error/idempotency mappings |
| 0104 privileged bindings | Restrictive DENY recorded; candidate refusal regressions added | Canonical tuples/prohibitions composition and actual IPC-B no-effect tests |
| 0105 control boundary | Inert/candidate evidence only | Production peer/context/surface transport composition |
| 0106 persistent idempotency | Not globally qualified by this patch | Accepted physical/concurrency/crash/reuse assertions |
| 0107 IPC deadlines | No timeout policy changed | Exact phase/outcome/partial-frame execution proof |
| 0108 durable jobs/recovery | Candidate rehearsals exist | Fencing, stale executor and uncertain-outcome proof for canonical runtime |
| 0109 Git enforcement | Develop active; legitimate merge observed | Main activation, safe remaining negative tests and recovery procedure |
| 0110 independent review | Candidate mono-maintainer alternative explicitly accepted | Preserve same-author labeling; release-specific review is distinct |
| 0111 source tracking | Merged repository guardrails have executed evidence | Do not infer new source PASS or main/release acceptance |
| 0112 original DOC provenance | 1,458 extracted files inventoried from CI metadata | Original ZIP bytes/digest/provenance still required |
| 0113 build inputs | Inert-daemon artifact and Core candidate CI exist | Production build/runtime and release qualification beyond those scopes |
| 0114 executable QA | Exact-source tests exist; new regressions added | Terminal new CI and full requirement coverage, not the ticket count |
| 0115 authority composition | Domain precedence and explicit owner policy retained | Verify each actual cross-domain amendment; no silent override |
| 0116 bounded scope | Current patch grants no new effects or SC-M02 | Wider M1/privileged slices require their own concrete contracts/tests |
| 0117 cookie topology | Browser isolation tests exist | Canonical cookies, TLS/proxy/origin and non-exposure deployment proof |
| 0118 permission composition | Existing Security intersection retained | Effective override/delegation/revocation algorithm and executed cases |

No eighteen-finding blanket closure is recorded. Historical CLOSURE-MATRIX.md
continues to preserve the original detailed acceptance criteria.

## Six previously closed tickets: technical status, not administrative status

- **#16:** the CI metadata contains an extracted 1,458-file corpus, 2,853,454
  bytes. The adjacent inventory summary pins the artifact/tree and metadata
  fingerprint. This improves the old mirror-only description, but is not the
  original ZIP's identity. A targeted private-documentation code search returned
  no match with incomplete_results=true; it is not proof of absence. Historical
  conversation provenance names a prior sandbox publication, not retrievable
  bytes or a verified checksum. Do not invent or rebuild the original ZIP.
- **#41:** use the per-finding matrix above; no general H3/corpus re-audit.
- **#91:** preserve all twelve explicit DENY rows and no-fallback rules. This
  patch proves refusal at the candidate handler boundary only; canonical IPC-B
  transport, audit/error projection and enforcement remain separately unqualified.
- **#129:** develop is protected; main was still unprotected at readback. The
  connection has no Ruleset mutation capability. Main's currently committed
  two-approval proposal is not a single-maintainer release decision. No Settings
  activation, destructive test, secret/token request or bypass is attempted.
- **#130:** the common timestamp gap is corrected and candidate admission proof
  strengthened. This does not finish complete M1 identity/data contracts,
  canonical cookies, production proxy/TLS or full-application worker acceptance.
- **#132:** optional enrollment remains the owner requirement and the integrated
  helper already matches it. Enabled factors stay enforced. The existing exact
  amendment packet is not recreated, and frozen Security v1.0 is not relabeled
  ratified. High-risk/API/DB composition is a real technical task, not a request
  for the owner's alternate accounts to post approval templates.

No new application test run is claimed until its result is read. Local probes
are explicitly scoped above. Full technical resolution is not yet established;
remaining archive/admin requirements cannot be satisfied by a status change.
DOC/DESIGN/BM unchanged; CODE NONE; no production or SC-M02 authorization.
