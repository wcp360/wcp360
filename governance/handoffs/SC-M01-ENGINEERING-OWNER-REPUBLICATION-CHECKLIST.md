# WCP360 — Engineering Ruleset v0.2 Owner Republication Checklist

**Status:** OWNER RECOVERY AID — NON-CANONICAL  
**Owner:** WCP360 — ENGINEERING / CODE REVIEW  
**Issue:** #9  
**Reference main:** e51333cff4e3ab42b976aafda557cc4b4ebc648d

This checklist separates recovered Engineering themes from the missing exact final v0.2 body.

## Confirmed final metadata

    ENGINEERING RULESET = ENG-WCP-RULESET-0001
    VERSION = v0.2
    FINAL APPROVED CONTENT = YES
    HISTORICAL CONTENT INCLUDED = NO
    READY FOR SOURCE CODE CONSUMPTION = YES
    BLOCKER = NONE

The final body existed historically but is not fully recovered.

## Recovered themes

Evidence supports that the ruleset covered at least:

- repository architecture and owner boundaries;
- typed namespace separation;
- dependencies/layering;
- configuration/secrets;
- error/failure handling;
- testability;
- secure coding;
- maintainability;
- complexity/duplication review;
- concurrency;
- review/merge gates;
- build/test quality;
- first implementation foundation only.

These themes are not sufficient to reconstruct exact final rules.

## Missing material to republish or re-decide

### ENG-R01 — repository/layer architecture
Exact allowed component/layer dependency graph.

### ENG-R02 — naming/conventions
Exact conventions by implementation language/component.

### ENG-R03 — error policy
Exact exception/error/result rules and conversion boundaries.

### ENG-R04 — logging policy
Exact structured logging schema and mandatory/forbidden fields beyond Security redaction.

### ENG-R05 — configuration API
Exact configuration loading/validation/override rules.

### ENG-R06 — runtime/framework/toolchain
Exact final language/framework/runtime selection if v0.2 contained one.

If the final v0.2 never fixed these, owner must explicitly state NOT DEFINED IN v0.2 rather than inventing a historical choice.

### ENG-R07 — formatter/linter/static analysis
Exact tools and version/lock policy.

### ENG-R08 — complexity/duplication thresholds
Exact metrics/thresholds or explicit qualitative-review rule.

### ENG-R09 — test/coverage thresholds
Exact gates or explicit statement that no numeric threshold is canonical.

### ENG-R10 — CI required checks
Exact required check classes/names, or explicit separation between semantic Engineering requirements and GitHub-specific names.

### ENG-R11 — review requirement
Exact independent-review requirement and reviewer-count semantics.

Current governance E0/E1/E2/E3 may constrain current work, but does not prove the historical v0.2 wording.

### ENG-R12 — Definition of Done
Exact final DoD conditions.

### ENG-R13 — generated code/artifacts
Owner rules for generated source/assets and editable authority.

### ENG-R14 — dependency policy
Allowed dependency direction and third-party introduction/review rules.

### ENG-R15 — concurrency
Exact expectations for race handling, shared state and concurrency tests.

### ENG-R16 — security review integration
Exact Engineering obligations for Security/privileged code.

### ENG-R17 — performance implications
Exact code-review requirements versus Performance-owner benchmarks.

### ENG-R18 — release quality gates
Exact Engineering conditions for package/release acceptance.

### ENG-R19 — final ordering / precedence
Exact final rule hierarchy and exceptions.

### ENG-R20 — SC-M01 consumption
Exact rules for first foundation review and CODE-BASELINE eligibility.

## Toolchain caution

Current WCP360 evidence does **not** establish a final post-Sprint-0 choice of:

- web/backend framework;
- PHP version;
- Go version;
- Node use/non-use;
- npm/pnpm/yarn;
- dependency managers;
- canonical build command;
- static analysis tools.

Do not republish cancelled Sprint 0 choices as if they were v0.2.

## Owner publication choices

### Option A — exact final source recovered

    REPUBLICATION TYPE = EXACT
    CONTENT CHANGE = NONE
    PROVENANCE =
    ORIGINAL SOURCE/DIGEST =

### Option B — owner reconstructs/amends

    REPUBLICATION TYPE = OWNER RECONSTRUCTION / AMENDMENT
    CONTENT CHANGE = YES
    VERSION IMPACT =
    ENG-R01..R20 DECISIONS =
    CROSS-DOMAIN IMPACT =

If the owner materially changes v0.2, it must decide whether a new ruleset version is required.

## PR #42 relationship

PR #42 is repository tooling implementation.

It is not ENG-WCP-RULESET-0001 v0.2.

Engineering may use the final/re-published ruleset to review PR #42 at an exact SHA, but must not infer the ruleset from PR #42.

## Acceptance block

Issue #9 can close only when:

    COMPLETE OWNER EXPORT = YES
    ENG-R01..R20 = RESOLVED / NOT-APPLICABLE WITH RATIONALE
    CANONICAL PATH = specifications/engineering/WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md
    FINAL OWNER STATUS = YES
    READY FOR SOURCE CODE CONSUMPTION = YES
    HISTORICAL CANDIDATE != CANONICAL AUTHORITY

Until then:

    #9 = OPEN
    SC-M01 = PAUSED
    CANONICAL EVIDENCE = 5 / 8
