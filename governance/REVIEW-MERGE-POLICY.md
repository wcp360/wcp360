# WCP360 — Review and Merge Evidence Policy

Status: GOVERNANCE NORMALIZATION — CURRENT REPOSITORY POLICY
Reference date: 2026-10-02
Related findings: AUDIT-WCP-0109, AUDIT-WCP-0110
Current repository reality: mono-maintainer GitHub ownership; main protection currently not enforced.

This policy makes explicit what counts as publication, automated verification, technical review and independent approval.

It does not invent a second reviewer, and it does not claim that GitHub enforcement is already active.

## 1. Four distinct evidence levels

### E0 — authored

A contributor or assistant has produced a change.

Evidence:
- commit SHA;
- changed paths;
- author identity/account;
- change description.

E0 is never review.

### E1 — automated checks

CI or deterministic tooling executes against an exact candidate SHA or GitHub merge test.

Examples:
- lint;
- unit tests;
- repository guardrails;
- secret-pattern checks;
- publication/inventory checks;
- contract/schema validation;
- builds.

E1 proves only what the automated checks actually exercise.

E1 is never an independent human/technical review.

### E2 — same-author verification / publication control

The author may:
- re-read the diff;
- verify changed paths;
- reproduce tests;
- check hashes;
- leave a GitHub COMMENT review;
- document limitations.

This is useful evidence but remains same-author verification.

It must be labeled as such and must never be counted as independent Engineering review.

### E3 — independent technical review

A technically separate reviewer examines the exact candidate SHA and records:
- scope reviewed;
- findings;
- required changes;
- approval / request changes / reservations;
- dependencies not reviewed;
- final SHA to which the review applies.

The reviewer must not be fabricated, and an author reviewing their own PR through the same identity does not satisfy E3.

For WCP360, E3 is the evidence expected by Engineering / Code Review for implementation changes before first CODE-BASELINE validation.

## 2. Change classes

| Class | Examples | Minimum evidence before merge |
|---|---|---|
| G0 — archival/non-authoritative evidence | historical snapshots, source inventory, audit evidence that changes no decisions | E0 + E2; E1 where a verifier exists |
| G1 — governance/status normalization | readiness/status, authority procedures, scope normalization | E0 + E2; owner/Audit verification required before using it to close a finding |
| P1 — non-normative proposal | R&D/audit proposals, owner decision packs | E0 + E2; merge may preserve proposal history but never equals owner acceptance |
| C1 — canonical owner contract | normative files under specifications | identifiable owner approval + exact version/provenance + E2; cross-domain review as required |
| R1 — repository tooling/CI | workflows, repository scripts, gitignore, hooks | E1 + E3 Engineering/DevOps/Security as applicable |
| I1 — application implementation | src, migrations, daemon/backend/UI implementation | accepted contracts + QA plan + E1 + E3 |
| S1 — security/privileged implementation | authz engine, sessions, wcpd/wcp-privd, privileged adapters | all I1 requirements + Security review + negative tests |
| L1 — release/install/upgrade | packages, installer, migrations, systemd, release automation | DevOps/Engineering/Security/QA evidence for affected path + rollback/recovery proof |

The same PR may contain multiple classes. The strictest applicable evidence requirement governs the merge unless the PR is split.

## 3. Mono-maintainer rule

Current CODEOWNERS assigns repository paths to the same GitHub account.

Therefore:
- CODEOWNERS routing is not independent approval;
- a PR opened by the maintainer and reviewed/commented by the same account remains E2;
- ChatGPT self-review in the same account/session remains E2;
- automated CI remains E1;
- neither E1 nor E2 is relabeled E3.

### Allowed progress without E3

The project may continue:
- research;
- audits;
- proposal drafting;
- owner handoffs;
- evidence collection;
- automated tests;
- non-authoritative documentation publication;
- governance normalization that does not pretend to close its own audit requirement.

### Blocked conclusions without E3

Without E3, the project must not claim:
- AUDIT-WCP-0110 VERIFIED/CLOSED;
- independent Engineering review complete;
- first CODE-BASELINE approved;
- security-critical implementation independently accepted;
- an implementation PR passed a separate reviewer merely because CI is green.

If the project intentionally operates with one human maintainer, MASTER may document the operational limitation or accepted risk, but an accepted risk is not renamed independent review.

## 4. Review SHA binding

A technical review applies to a precise commit SHA.

After the reviewed SHA changes:
- documentation-only merge-base synchronization with no reviewed blob change may be explicitly revalidated;
- substantive reviewed-file changes require the reviewer to examine the new SHA;
- a previous approval must not be presented as applying automatically to changed code.

Evidence should record:

    PR =
    REVIEWED SHA =
    REVIEWER =
    REVIEW CLASS =
    FILES / SCOPE =
    VERDICT =
    RESERVATIONS =
    FOLLOW-UP SHA =

## 5. Proposal merge rule

A non-normative proposal may be merged into main for traceability if all of the following are explicit:
- status is PROPOSAL / REVIEW / NON-NORMATIVE;
- owner approval is not claimed;
- canonical specifications are not silently altered;
- downstream implementation is forbidden until owner ratification;
- the proposal is linked from its finding/issue;
- superseded proposal versions remain distinguishable.

Therefore a merged PR #43-style document would mean preserved proposal evidence, not acceptance of D/T/C/H/S/F decisions.

## 6. Canonical contract merge rule

A file becoming implementation authority must have:
- owner/domain;
- exact version;
- final status;
- provenance/source;
- content-change declaration;
- compatibility/version impact;
- dependent owner review where cross-domain;
- tests/assertions expected;
- path accepted by the current gate/manifests.

A summary, MASTER reconstruction, historical candidate or audit proposal cannot be promoted merely because it looks complete.

## 7. Application implementation merge rule

Before merging implementation code for SC-M01:
1. SC-M01 must be explicitly resumed by MASTER;
2. implementation must fit governance/SC-M01-SCOPE.md;
3. applicable owner decisions/contracts must be ratified;
4. tests for implemented behavior must exist and execute;
5. CI E1 must pass on the candidate/merge SHA;
6. Engineering E3 must review the exact candidate;
7. Security review is required for trust/security/privileged changes;
8. DB review is required for schema/migration changes;
9. QA verifies the relevant test evidence;
10. unresolved blocking review comments remain merge blockers.

Creating code before CODE-BASELINE is allowed only after SC-M01 resume; CODE-BASELINE is established later by its own verification decision.

## 8. Required GitHub enforcement target

Current observed main state:

    protected = false
    required status checks = none

This is a finding, not a hidden assumption.

The desired enforcement target, subject to repository-plan/admin capability, is:
- pull request required for protected paths / main;
- force-push protection;
- deletion protection;
- conversation resolution before merge;
- required CI checks after those checks exist reliably on main;
- required review count consistent with actual independent reviewer availability;
- restricted bypass/admin policy;
- stale approval handling where supported.

### Bootstrap ordering for CI checks

Do not configure a required check before the workflow/check exists on main and has a successful run.

Safe order:
1. merge/review workflow implementation under current governance;
2. run it successfully on main;
3. record exact check/job names;
4. configure required-status enforcement;
5. open a negative test PR and prove a failing required check blocks merge.

This prevents a self-inflicted deadlock from requiring a nonexistent check.

## 9. Current PR #42 rule

PR #42 modifies repository security/CI tooling and is class R1.

Current evidence includes:
- repeated successful GitHub Actions runs;
- repository/tooling regression tests;
- secret-check evidence;
- exact SHA/hash evidence;
- same-author review/comments.

This is E1 + E2.

It still lacks E3.

Therefore:

    PR #42 = OPEN / TESTED / NOT INDEPENDENTLY REVIEWED
    AUDIT-WCP-0111 = fix implemented, not yet verified/closed
    AUDIT-WCP-0109 = CI partially remediated, enforcement still missing
    AUDIT-WCP-0110 = unresolved

## 10. Current PR #43 rule

PR #43 contains non-normative contract/toolchain proposals and is class P1.

It may be reviewed and preserved without pretending owner acceptance.

Before any proposal decision becomes normative:
- relevant owner must ACCEPT / AMEND / REJECT / point to ALREADY DEFINED evidence;
- resulting normative delta must be published in the owner domain;
- version impact and tests must be recorded.

No SOURCE CODE consumer may treat PR #43 alone as an implementation contract.

## 11. Audit finding closure

### AUDIT-WCP-0109

Needs both:
- appropriate CI/checks exist and work;
- actual merge/push enforcement is demonstrated.

A green workflow alone is not closure.

### AUDIT-WCP-0110

Needs E3 or an Audit/MASTER decision explicitly changing the project's requirement.

A mono-maintainer note or risk acceptance can document the limitation but does not satisfy the current independent-review wording.

## 12. No fake reviewer / no fake team rule

Forbidden:
- claiming a sub-agent reviewed a PR when no separately evidenced reviewer exists;
- using a second label/persona from the same author process as independent approval;
- treating automated test output as reviewer approval;
- treating GitHub CODEOWNERS assignment as proof a review happened;
- recording APPROVED when only COMMENT/self-review exists.

## 13. Evidence retention

For important reviews/tests, keep durable repository references to:
- PR number;
- exact SHA;
- run/job URL/ID;
- artifact digest where used;
- reviewer identity/role;
- conclusion and limitations.

Temporary CI artifacts may expire. Durable summaries should preserve enough metadata to reproduce or understand the evidence without copying secrets into Git.

## 14. Current state

    MAIN PROTECTION = NOT ENFORCED
    PR #42 = E1 + E2, E3 MISSING
    PR #43 = P1 PROPOSALS, OWNER ACCEPTANCE MISSING
    APPLICATION IMPLEMENTATION = NOT STARTED
    SC-M01 = PAUSED — TARGETED EVIDENCE
    CODE-BASELINE = NONE

This policy makes review evidence honest; it does not manufacture the missing reviewer or administration permission.
