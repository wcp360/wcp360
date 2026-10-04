# WCP360 — GitHub Main Enforcement Activation Plan

**Status:** ADMINISTRATIVE EXECUTION PLAN — NOT YET ENFORCED  
**Related finding:** AUDIT-WCP-0109  
**Current main:** 1b828833bb13b99381e65971c58d9d18c0f57af4  
**Observed branch state:** protected=false; required status checks=none

The installed GitHub connector in this session does not expose branch-protection/ruleset write actions. Therefore this file prepares the exact target and negative proof sequence without claiming the settings are active.

## 1. Dependency order

Do not require PR #42 checks before their workflow exists on main.

Correct order:

    1. obtain E3 for PR #42
    2. fix any E3 blocking findings
    3. re-run E1 on final SHA/current main
    4. merge PR #42
    5. run Repository guardrails successfully on main
    6. record exact check contexts
    7. enable branch/ruleset enforcement
    8. run negative enforcement proofs
    9. only then consider AUDIT-WCP-0109 verification

## 2. Intended required CI checks

After PR #42 is merged and successful on main, candidate required job contexts are:

    gitignore-regression
    repository-secrets

Before enforcement, verify the actual GitHub check-run/context names from the first successful main run. Do not configure an assumed name.

If GitHub exposes workflow/job qualification in required contexts differently, use the observed exact contexts.

## 3. Main branch target controls

Subject to account/repository feature availability:

### ENF-01 — pull request path

For ordinary contributions to main:

    pull request required = YES

Direct commits should be limited to explicitly documented emergency/administrative policy if any. Current one-maintainer reality must not be disguised as multi-review approval.

### ENF-02 — required status checks

After checks are proven on main:

    repository-secrets = REQUIRED
    gitignore-regression = REQUIRED

Recommended:
- require branch up to date before merge only if it does not create an operational deadlock and the repository workflow is compatible;
- otherwise require checks against GitHub's tested merge result / current base policy.

Owner/Admin must decide exact setting based on GitHub available controls.

### ENF-03 — force push

    force push to main = DISABLED

Any exceptional history repair must be a documented administrator recovery procedure, not normal workflow.

### ENF-04 — branch deletion

    main deletion = DISABLED

### ENF-05 — conversation resolution

    required before merge = YES

Only when GitHub plan/settings support it.

### ENF-06 — required review

Current policy requires E3 for R1/I1/S1 and first CODE-BASELINE-sensitive changes.

However current repository has one configured CODEOWNER account.

Do not configure a numerical required-approval count that permanently deadlocks the repository unless a valid independent reviewer/team is actually available.

Two valid administrative strategies:

#### Strategy A — independent reviewer exists

    required approvals >= 1
    CODEOWNER/review assignment updated to real reviewer/team as appropriate
    stale approval handling = enabled where supported

#### Strategy B — no persistent independent GitHub reviewer yet

Keep the governance E3 blocker outside numeric branch settings temporarily, while:
- required CI is enforced;
- direct pushes are blocked/restricted;
- merge checklist requires E3 evidence for R1/I1/S1;
- Audit/MASTER records that GitHub cannot technically enforce reviewer independence yet.

Strategy B does not close AUDIT-WCP-0110.

## 4. Bypass policy

Admin/bypass rights should be minimal.

Any allowed bypass should require:
- actor identifiable;
- reason recorded;
- incident/emergency reference if applicable;
- follow-up review;
- no silent bypass of failing security checks for normal development.

Do not put a fake second account/team in bypass/reviewer configuration.

## 5. Negative enforcement proofs

After settings are active, create disposable/controlled PRs proving enforcement.

### ENF-T01 — failing repository-secrets blocks merge

Create a synthetic branch modifying a safe test fixture in a way that intentionally triggers the repository-secret check using a synthetic token pattern.

Expected:
- job FAIL;
- merge blocked by required check;
- no real credential used;
- test branch deleted afterward.

Never commit a real credential.

### ENF-T02 — failing gitignore-regression blocks merge

Create a synthetic branch that removes one required ignore rule or adds an overbroad rule known to fail the 52-case checker.

Expected:
- gitignore-regression FAIL;
- merge blocked.

Revert/delete test branch afterward.

### ENF-T03 — clean PR can satisfy checks

A no-risk documentation change or controlled fixture change should produce both required checks SUCCESS and become merge-eligible subject to review policy.

### ENF-T04 — direct push / force push behavior

Using a disposable non-critical commit attempt and an authorized test identity where possible:
- verify direct push is rejected according to policy;
- verify force push to main is rejected.

Do not rewrite main history merely to test this.

### ENF-T05 — unresolved conversation

If enabled:
- create a review conversation on a disposable PR;
- confirm merge is blocked until resolved.

### ENF-T06 — reviewer requirement

Only when Strategy A is enabled:
- confirm author cannot satisfy independent approval alone;
- confirm actual reviewer approval on exact SHA satisfies requirement;
- push a new substantive commit and confirm stale-review policy behaves as configured.

## 6. Evidence to retain

For each enforcement proof:

    SETTINGS SNAPSHOT / SCREENSHOT OR API RESPONSE =
    REPOSITORY =
    BRANCH / PR =
    BASE SHA =
    HEAD SHA =
    CHECK CONTEXT =
    EXPECTED BLOCK =
    ACTUAL RESULT =
    ACTOR =
    DATE =
    CLEANUP =
    ISSUE / AUDIT REF = AUDIT-WCP-0109

Do not include secrets/tokens/cookies in evidence.

## 7. GitHub administration limitation in current session

The connected GitHub toolset currently permits repository/file/PR/workflow operations but exposes no supported branch-protection/ruleset update action.

Therefore:

    ENFORCEMENT ACTIVATED BY THIS SESSION = NO

This is an access/tooling boundary, not evidence that enforcement is impossible on the repository.

Do not change repository visibility or subscription merely to obtain a protection feature without explicit owner decision.

## 8. Finding closure criteria

AUDIT-WCP-0109 can be considered ready for verification only after:

    repository CI exists on main
    required checks configured
    force-push/deletion policy configured
    PR/direct-push policy configured
    reviewer policy aligned with actual reviewer availability
    negative checks demonstrate failed required checks block merge
    clean checks demonstrate intended merge path
    bypass policy reviewed
    evidence tied to exact settings/PRs

Current state before those steps:

    CI CANDIDATE = PR #42
    PR #42 E3 = MISSING
    WORKFLOW ON MAIN = NOT YET MERGED
    MAIN PROTECTION = OFF
    REQUIRED CHECKS = NONE
    AUDIT-WCP-0109 = OPEN
