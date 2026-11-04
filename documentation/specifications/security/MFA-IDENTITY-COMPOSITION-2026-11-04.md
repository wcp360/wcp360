# MFA, identity, data and sensitive-operation composition

Date: 2026-11-04. Parent scope: D-ENG-MFA-ALIGN-0001 / PR #135 / issue #132.
Status: ENGINEERING REVIEW. Product direction is recorded; runtime qualification
and canonical Security/API/DB amendment approval are NOT established here.

## Delivered scope and authority

The owner requires optional MFA enrollment on WCP-USER and WCP-ADMIN. That decision
is not pending a vote by the owner's alternate accounts. Enrolled factors remain
enforced. This document gives concrete implementation requirements and acceptance
criteria; it does not silently amend the frozen Security v1.0 export, approve a
public authentication semantic-version change, or grant privileged operations.

The previous SessionBoundary source-publication attempt remains blocked. This
increment publishes only repository verification tools, a review ledger, the
transaction review model and documentation. No blocked application file, helper,
test blob or equivalent runtime change is republished through another tool.
No authentication implementation, DB schema, migration, permission, dependency,
public API/IPC identifier, production mode or live GitHub rule changes.

The review is performed by the current assistant under the single-maintainer
workflow. No independent agent, specialist approval or third-party audit is claimed.
Existing branch and PR #135 only; no new branch or replacement issue.

## Source-bound inputs

`MFA-ALIGNMENT-COVERAGE.json` records eight exact Git blobs at source reference
`bcc6f7ab4cb6a36b377ae40e3bb9258506396be9`: Security, API and Database canonical
exports plus SessionBoundary, CandidateSessions, CandidateMfa, MfaSessionGuard and
RegenerateMfaRecoveryCodes. Their hashes are checked against checkout bytes by
`tools/repository/check_mfa_alignment.py`, not merely copied into a report.
A changed input requires a deliberate review and an updated source ledger.

The frozen Security section 13 still says mandatory ADMIN MFA. Its replacement
wording is already proposed in issue #132 comment 6626870560: optional enrollment,
enforced when enabled, for both surfaces. The ledger records this conflict instead
of representing the frozen wording as current owner intent or a resolved amendment.
DOC v4.0.21.23 / DESIGN V2.5.3.36.4 / BM FINAL remain; CODE NONE.

## Authentication is not enrollment, permission or privilege

| Flow | Existing/proposed invariant | Distinct remaining acceptance |
|---|---|---|
| Ordinary login without a factor | Primary credential, current active principal, exact surface and session controls; no fabricated MFA proof | Positive USER and ADMIN journeys |
| Login with an enabled factor | Incomplete/wrong challenge or unavailable key cannot complete enrolled-factor authentication | Actual firewall/browser/key-failure evidence |
| Candidate pending MFA | Strength 1 alone does not distinguish password-only full login from a pending challenge; verify the separate challenge state | No protected authority while pending |
| ADMIN MFA completion | Same account and session, actual factor proof, current factor generation, valid lifetime; rotate the binding | Atomic compare-and-upgrade under the account transaction |
| Own password change | Keep current-password confirmation, bound session and factor-specific recent proof; revoke same-account sessions and audit atomically | Shared and session-isolated DB equivalence |
| Recovery-code renewal | Current-password confirmation, enabled configured factor, recent MFA proof and strength-2 bound session | Atomic commit before show-once disclosure; uncertain outcomes tested |
| Privileged or emergency actions | Keep exact permission/entitlement/scope/approval and independent proof requirements | No grant inferred from optional ordinary enrollment |

Proof metadata is server-owned. Request bodies, headers and caller claims are not
proof producers. Password proof and MFA proof remain separate namespaces. A proof
consistency check does not verify a password/TOTP, choose a surface, bind a cookie,
grant authentication strength or authorize an operation.

## API composition: preserve the actual profiles

The checker verifies 15 sensitive API rows against the frozen registry.
`API-WCP-0706` (privilege elevation) is **AUTH-P**, not AUTH-PRIV: policy admission
must establish eligibility rather than treating a privileged context as already
present. `API-WCP-0708` remains **AUTH-E**. The 13 other mapped actions retain
**AUTH-PRIV**, including impersonation, service lifecycle, reboot, updates, failover,
firewall/routes, storage expansion, process signals and system configuration.

No new public request/response, canonical error or transport mapping is introduced.
A local candidate 403 must not be advertised as a validated canonical error envelope.
Public semantic changes remain subject to the API owner's version policy; this
review does not classify the MFA amendment as a harmless patch-version change.
The twelve-operation restrictive decision in #91 remains binding.

## Data composition: physical separation and strength are different facts

The canonical session relations are `wcp_iam.user_sessions` and
`wcp_iam.admin_sessions`, with distinct surface claims, verifier-only persistence
and runtime credentials that cannot read/write the other surface's sessions.
Candidate relations and strength values 1/2 are not automatically the canonical
physical model or a complete definition of canonical authentication assurance.

No speculative factor/session column is required by this review. Before an actual
schema change, state the storage need, migration/rollback and its owning decision.
The bounded atomicity work can first be evaluated against existing candidate
account/factor/session relations; that does not ratify their production schema.

## Transaction contract for final factor/session comparison

This is a concrete design for review, NOT deployed code or a new public operation.
The current CandidateSessions::upgrade transaction checks a pending registration,
its binding and lifetime but does not take an expected factor generation parameter.
A factor snapshot taken before that transaction is not an atomic guarantee.

Required ordering for the proposed adapter:

1. Enter a bounded transaction before observing authority for the final upgrade.
   Use the same authoritative account-row lock as every factor-changing flow.
2. Under that lock, read the current active same-surface principal, current factor
   generation/state and pending session registration. If additional row locks
   are used, their order is account, factor, session. No factor writer may take
   the inverse order or bypass account serialization.
3. Compare the server-proof actor and integer generation to the locked current
   factor generation. Require an enabled usable factor, current pending session,
   old binding and authoritative proof freshness. Validate all timestamps; a
   future timestamp is not a valid proof. Exact age 300 seconds is expired for
   the existing five-minute ADMIN proof policy.
4. Rotate the session binding and upgrade only the one matching unrevoked,
   unexpired, strength-1 pending registration. Conditional update count must be
   exactly one. Preserve the absolute lifetime and the existing audit requirement.
5. Commit before the application presents completed authority. Definite failure
   rolls back data and audit. If commit outcome is uncertain, do not claim either
   rollback or success, disclose no new credentials, invalidate local authority
   and re-observe authoritative state before a new admission attempt.
6. Factor disable/replacement/recovery generation changes must use the same lock
   and invalidate registrations dependent on the prior generation in the same
   transaction. Other accounts and the other surface must remain untouched.

The adapter must coordinate SID/session-storage failure with durable state:
rotating a browser SID before DB commit is not itself a successful upgrade. A
failed upgrade must not leave a browser token with authority or a durable session
that can be presented through the obsolete binding. Exact compensation and retry
semantics must be exercised by the HTTP/database integration tests.

This contract does not allow replay of a consumed factor result, relabel a
password-only session as MFA-proven, or make enrollment mandatory at ordinary login.
USER factor enrollment and USER factor persistence require their own integration;
the existing ADMIN-only provider is not made cross-surface by this document.

## Deterministic acceptance schedules

| Case | Required observation |
|---|---|
| Upgrade commits before factor change | Upgrade may succeed first; subsequent generation change invalidates dependent registrations atomically |
| Factor change commits before upgrade | Stale generation cannot upgrade; no new authority or upgrade audit |
| Simultaneous writers | Account lock serializes; no observer sees partial factor/session/audit writes |
| Old or missing proof generation | Reject before upgrade, independent of clock validity |
| Factor disabled or configuration unavailable | No downgrade to password-only as a workaround for enrolled-factor completion |
| Failed audit or definite commit failure | No partially committed session elevation or credential disclosure |
| Unknown commit outcome | Treat both persisted and unpersisted outcomes as possible until reconciliation |
| Wrong account/surface/binding or revoked registration | No mutation to the target or another account |

The accompanying ten model tests enumerate 70 order-preserving schedules for each
of three symbolic mutations: disable, replace and recovery. With the proposed
lock held for all four steps, two schedules serialize and 68 encounter the lock.
They test state visibility and definite failures and demonstrate counterexamples
when revision comparison or dependent-session revocation is deliberately removed.
This is a model of the proposed invariants, NOT PostgreSQL, DBAL, Symfony or the
current application runtime. Lock scheduling, deadlocks, connection failure,
key changes and HTTP compensation still require actual adapter tests.

## QA traceability and publication rules

The seven original #132 acceptance scenarios are mapped to eight existing PHP
source files. File presence and a green metadata check do not execute those tests.
The checker emits `SOURCE_TRACEABILITY_ONLY`, `application_tests_executed=false`,
`canonical_amendment_approved=false` and `runtime_qualification=NOT_ESTABLISHED`.
It refuses a missing/stale source, changed sensitive API profile, omitted scenario,
duplicate JSON field, unsafe path, silent policy reversal or unsupported PASS claim.

Run from repository root:

```text
python3 -I tools/repository/test_mfa_alignment.py
python3 -I tools/repository/test_mfa_transaction_model.py
python3 -I tools/repository/check_mfa_alignment.py --root . --report publication-evidence/mfa-alignment.json
```

All six declared gates remain OPEN: canonical version disposition, atomic adapter,
USER MFA journey, isolated-DB equivalence, blocked runtime publication and full-stack
qualification. Closing one requires new source-bound evidence and an explicit
reviewed ledger/checker update; this tool cannot promote a status automatically.
The guardrails workflow runs the model and traceability checks as repository QA
only. It does not redeclare old application CI as a new functional test campaign.

The actual runtime fix remains unpublished. No tool control, branch protection or
owner-domain authority is bypassed. Keep PR #135 DRAFT and do not merge this
verification increment as though it completed the application fix.
