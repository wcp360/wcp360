# MFA / identity / data / sensitive operations — targeted engineering alignment

Date: 2026-11-04. PR #135. Scope decision: D-ENG-MFA-ALIGN-0001.
Status: LOCAL REVIEW / UNPUBLISHED DEVELOPMENT CANDIDATE. No canonical owner approval is asserted.

> **Publication checkpoint — 2026-11-04:** this is a local engineering review dossier, not committed implementation. PR #135 contains only the scope decision at `0f0160db98c54ea703fc6d5f18e606b4de1cd267`. The source-publication attempt was blocked by a tool safety control. Product-source writes stopped; the blocked file was not published through an alternative route. No application implementation, new tests or routing amendment from this dossier is on the branch. No merge or exact-candidate CI success is claimed. This archive contains documentation and evidence metadata only, not application source or an executable patch.


Source baseline: `bcc6f7ab4cb6a36b377ae40e3bb9258506396be9` (PR #134 integrated).
DOC v4.0.21.23 / DESIGN V2.5.3.36.4 / BM FINAL / CODE NONE remain unchanged.

## 1. Authority, exact inputs and the unresolved normative conflict

This applies the owner's optional-enrollment direction in issue #132 without
recreating the [existing amendment packet](https://github.com/wcp360/wcp360/issues/132#issuecomment-6626870560)
or presenting current-assistant engineering as independent Security/API/DB review.
The current session has no callable sub-agent executor. The owner packets below
are deliverables, not claims that separate agents or conversations approved them.

| Input at source baseline | Verified blob | Relevant scope |
|---|---|---|
| Security v1.0 canonical implementation export | `6d74f2f363524511424f839ca7262143c2af95ab` | Section 13 mandatory ADMIN MFA conflicts with the later product direction; separation and operation-specific gates remain |
| API v1.0 canonical implementation export | `4893c5191b4928ecfa5a70f4dc14b39a8939fcea` | AUTH-U/A/B, AUTH-PRIV, AUTH-E, errors and semantic-version rule |
| DB v1.0 canonical implementation contract | `2660e057bad1d00592c57f354b1f3745ccccbe7d` | Separate user/admin session relations, surface claims and integer auth_strength |
| SessionBoundary.php | `9e9e626cdf2314459fde225aad2fde77c4c88677` | Existing final login listener, local source proposal; not committed |
| ChangeOwnPassword.php | `0861ae9ee4999aeff12a382d70f4601b7e4f3c82` | Shared-database transactional proof checks, local source proposal; not committed |
| RegenerateMfaRecoveryCodes.php | `13c652ee850d7422cc39caf10cb9c9d411e0a1e5` | Current principal, factor revision, strength 2 and atomic show-once disclosure, local source proposal; not committed |
| AdminTwoFactorProvider.php | `28fca2786b1140ea8457dd81b028c148b9119dd7` | ADMIN-only factor verification; unchanged |
| MfaSessionGuard.php | `b333d24a1d9d7069040b9903226a672998eb1e12` | Server-created proof and current revision admission; unchanged |

No frozen export is edited. Proposed Security table wording remains **optional
enrollment; enforced when enabled** for USER and ADMIN. That is a behavior change,
not a typographic correction. Formal Security version impact and affected API
authentication-semantics disposition remain REVIEW; no automatic patch-version or
major-version approval is invented. The API's semantic-change rule must be applied
by its owner before a public contract is changed.

## 2. Composed behavior, not an implicit privilege grant

| Path | Required identity and evidence | Persisted authority and effects |
|---|---|---|
| Ordinary login without an enabled factor | Valid primary credential, active same-surface principal and all existing admission controls | Candidate strength 1 only; no fabricated MFA proof |
| Ordinary login with an enabled factor | Primary authentication remains pending until actual factor verification | Pending strength 1 is not full MFA authentication; missing key must not turn MFA off |
| ADMIN MFA completion | Same ADMIN principal and pending selector/binding; current enabled/configured factor; exact factor revision; server proof age in [0,300) seconds | Rotate SID before existing upgrade; only the existing verified completion can reach strength 2 |
| Own password change, shared DB path | Current-password confirmation remains required; ADMIN proof matches the locked current factor revision; configured factor and strength 2 when enabled, otherwise existing primary-proof/strength-1 path | Existing conditional credential write, same-account session revocation and audit remain transactional |
| MFA recovery-code renewal | Current-password confirmation, current ADMIN identity/credential snapshot, current factor revision, recent MFA proof and bound strength-2 session | Existing factor/old-code replacement, revision increment, session revocation and audit commit before disclosure; uncertain commit never returns codes |
| Privileged, destructive and emergency operations | Their exact existing permission, entitlement, scope, policy, approval and fresh-proof contracts | No fallback from optional ordinary enrollment; no canonical dispatcher activated |

`RecentAuthenticationProof::isCurrent()` checks only an existing proof's actor,
integer revision and integer timestamp. It does not check a password, verify TOTP,
grant authentication strength, choose the proof method, bind a session, authorize
a surface or approve an operation. Those checks remain with the calling boundary.
The two proof namespaces `_wcp_password_proof` and `_wcp_mfa_proof` remain distinct.
Proofs are server-side session metadata, never a public request DTO or bearer token.

The common helper uses subtraction after ordering checks, avoiding integer-addition
overflow while preserving the exact existing five-minute expiry at age 300.
SessionBoundary uses its already-injected server clock for proof checking and
registration stamps. The existing primary-login optional-enrollment helper and
all idle/absolute lifetimes are unchanged.

## 3. Data model and concurrency disposition

The candidate's `auth_strength=1/2`, current factor revision and server-side proof
metadata already represent this bounded change. No table, column, index, grant,
constraint, stored routine or migration is added. This is not approval to rename
candidate tables into canonical schemas or ratify all canonical factor storage.

The frozen DB contract requires separate `wcp_iam.user_sessions` and
`wcp_iam.admin_sessions`, verifier-only storage, distinct surface claims and
cross-surface credential restrictions. Those requirements remain; the candidate
session relations are not silently promoted to those canonical implementations.

Password change and recovery renewal retain their existing transaction boundaries,
account locking, current principal comparisons and session-strength admission.
The session-isolated PostgreSQL password-change routine is untouched: adoption of
the common PHP helper in the shared path does not qualify or replace that routine.

**Remaining atomicity gap:** the new completion-time factor recheck is a current
snapshot. It is not an atomic factor-revision comparison inside the durable
session-upgrade transaction. A factor change between the snapshot and upgrade
still requires a dedicated DB/Security contract, implementation and concurrent
negative test. Sequential factor-change tests below do not claim to close that
race. Existing downstream current-state admission remains unchanged.

**Remaining surface gap:** CandidateMfa and AdminTwoFactorProvider currently serve
ADMIN. The owner policy also makes USER enrollment optional, but this patch does not
implement USER MFA enrollment or claim that an enabled USER factor was exercised.
That needs its own identity/factor contract and complete positive/negative journey.

## 4. Exact API / IPC impact handoff

No request/response schema, route, error-code registry, permission/action or IPC
identifier changes. Local listener failure stays the existing redacted HTTP 403;
it is not claimed as canonical MFA_REQUIRED or AUTHORIZATION_DENIED conformance.

The following existing API families must retain their independent gates:
`API-WCP-0706` privilege elevation, `0707` impersonation, `0708` emergency access;
`0802/0803` service lifecycle, `0002` reboot, `0805` updates, `0807` failover,
`0809/0810` firewall/routes, `0812` storage expansion, `0003/0007` process signals,
`0814` system settings and `0816` service configuration. AUTH-E explicitly retains
MFA; optional ordinary login never creates an emergency or privileged session.
This list identifies impacted contract families, not new implementation authority.

The existing twelve-operation IPC DENY decision from #91 remains binding. There
is no new root effect, generic command facility or candidate-to-canonical alias.

## 5. QA mapping and actual evidence boundaries

The seven scenarios from the existing #132 packet map as follows. These row numbers
are packet-local references, not newly registered canonical TEST IDs.

| Packet scenario | Executable coverage / remaining criterion |
|---|---|
| 1. Ordinary no-factor login | Existing OptionalLoginMfaPolicyTest and journeys; new completion test proves password-only ADMIN remains strength 1. USER factor support is not qualified by this increment |
| 2. Enabled-factor enforcement | Existing CandidateMfaTest/MfaJourneyTest; new MfaCompletionProofTest checks missing/malformed/foreign/expired proof and unavailable factor keys |
| 3. Enrollment and rotation | Existing enrollment journeys; new positive test checks durable strength 2, SID rotation and refusal of the old SID |
| 4. Sensitive disable/recovery | Existing MfaRecoveryRenewalTest and ChangeOwnPasswordTest retain their checks; new common proof vectors exercise type/revision/time boundaries. Disable flow is not rewritten here |
| 5. Revision and invalidation | New completion tests reject changed/disabled factor snapshots without durable session or audit mutation. Atomic compare-and-upgrade concurrency remains unqualified |
| 6. High-risk gates | Existing contracts and IPC DENY retained; this PR grants no privileged authority and is not canonical privileged-runtime certification |
| 7. Optional helper and unchanged timeouts | Existing OptionalLoginMfaPolicyTest and SessionTimestampConsistencyTest; 30 new RecentAuthenticationProofTest vectors preserve exact proof freshness |

Added PHPUnit cases: **30 unit vectors + 18 listener/database integration cases**.
The integration fixture uses real CandidateMfa, CandidateSessions, application DB,
Symfony session and LoginSuccessEvent; only the authenticator instance is an
uninitialized type marker because the listener does not call authenticator methods.
It is not an end-to-end password/TOTP/firewall test. Existing browser journeys
remain mandatory in full CI.

Executed locally on PHP 8.4.24: syntax validation; all 30 exact unit data vectors
through a dependency-free harness; actual listener-source probe with test doubles
(8 cases: old source fails old/missing revision, disabled and unconfigured factor;
patched source passes all 8). Three discarded mutations of revision, future-time
and exact-deadline checks are detected by those vectors. This is not local PHPUnit,
Symfony, PostgreSQL or browser execution. Full CI must be read at the published
HEAD; no old green run or test count establishes a new PASS.

The unpublished local routing proposal is constrained to PR #135 + the existing historical ref + develop.
Its local self-test passes 11 admitted and 42 forbidden cases. No live rule changes.

## 6. Owner handoffs and integration gate

Security: dispose the exact #132 amendment wording/version impact and keep strong-
auth/high-risk and emergency boundaries explicit; review the new snapshot guard.
API/IPC: retain the exact family gates above and decide any public semantic-version
impact; do not infer canonical errors or new operations from this candidate.
Database: confirm no migration for this bounded candidate; specify atomic final
factor-revision/session-upgrade semantics and the future USER factor model separately.
QA: register exact canonical scenario IDs, execute this HEAD and keep sequential,
concurrent, listener, browser and production evidence distinct.
Documentation: normalize only approved delta passages; do not rescan the archive.
MASTER/Engineering: inspect source and terminal CI before a separate merge decision.

Rollback is a reviewed revert through an existing-ref PR, not a direct permanent-
branch push. No data rollback is required. No production/release, SC-M02,
independent E3, full M1 completion or complete #132 resolution is asserted.

## 7. Delivery checkpoint and exact execution provenance

The PR's changed-file list was read back and contains one file only:
`documentation/governance/decisions/D-ENG-MFA-ALIGN-0001.md`.
Its head is `0f0160db98c54ea703fc6d5f18e606b4de1cd267`; its base is
`bcc6f7ab4cb6a36b377ae40e3bb9258506396be9`. It remains open and draft.

The only workflow observed for the scope head was Repository guardrails
`41865030143`, queued at readback. It is not a run of the unpublished code.

`local-test-evidence.json` records actual local commands, exits and outputs.
The expected nonzero exit of the original-listener negative control is a
reproduction of missing checks, not a failing production deployment.
`local-proposal-manifest.json` identifies the local proposal bytes only; it does
not attest remote publication, approval, installation or release.

Public source references (inspected through the GitHub connector):
- PR: https://github.com/wcp360/wcp360/pull/135
- Existing amendment: https://github.com/wcp360/wcp360/issues/132#issuecomment-6626870560
- Source base: https://github.com/wcp360/wcp360/tree/bcc6f7ab4cb6a36b377ae40e3bb9258506396be9

This dossier records engineering progress and remaining work. It is not
authorization to bypass the blocked publication or merge a scope-only PR as a fix.
