# WCP360 — Engineering v0.2 Recovery / Republication Conformance Matrix

**Status:** DERIVED RECOVERY EVIDENCE — NON-CANONICAL  
**Reference main:** ad3a3df25ee7d20ea8e1c726c785a6c483790668  
**Issue:** #9  
**Owner:** WCP360 — ENGINEERING / CODE REVIEW

Sources:

- \`governance/handoffs/SC-M01-ENGINEERING-OWNER-REPUBLICATION-CHECKLIST.md\`
- \`governance/recovery/SC-M01-RECOVERED-ENGINEERING-V0.2-EVIDENCE.md\`
- current governance such as \`REVIEW-MERGE-POLICY.md\` and \`SC-M01-SCOPE.md\` only as current constraints, not as proof of historical v0.2 wording.

Purpose: show what historical v0.2 evidence actually recovers and what Engineering must still retrieve or re-decide.

## 1. Recovery result

    ENG-R items examined = 20
    historical candidate/final-theme evidence = substantial
    exact final v0.2 body = not recovered
    exact-complete ENG-R items sufficient for byte-faithful autonomous republication = 0
    safe MASTER promotion of candidate = NO
    Engineering owner action required = YES

The recovery sharply limits the design space but does not establish the final wording/tools/thresholds.

## 2. States

- **SUBSTANTIAL PARTIAL RECOVERY** — strong candidate/final-invariant evidence exists, but exact final rule set is incomplete.
- **PARTIAL RECOVERY** — theme/rule fragments exist but implementation-detail wording is missing.
- **EXISTENCE RECOVERED / EXACT RULE MISSING** — history proves the topic existed in the ruleset but not its final contents.
- **NOT RECOVERED / OWNER DECISION REQUIRED** — final value/tool/threshold cannot be established.

## 3. ENG-R01…ENG-R20

| ID | Topic | Recovery state | Recovered evidence | Still missing / owner action |
|---|---|---|---|---|
| ENG-R01 | repository/layer architecture | PARTIAL RECOVERY | owner/component boundaries and architecture separation are repeatedly recovered | exact allowed dependency/layer graph and exceptions |
| ENG-R02 | naming/conventions | PARTIAL RECOVERY | typed namespace separation is strongly recovered | exact per-language/component naming conventions |
| ENG-R03 | error policy | SUBSTANTIAL PARTIAL RECOVERY | timeout != failure, retry != reconciliation, rollback != compensation, UNKNOWN_OUTCOME/reconciliation behavior | exact final exception/result/error conversion rules and layer boundaries |
| ENG-R04 | logging policy | EXISTENCE RECOVERED / EXACT RULE MISSING | logging was a covered Engineering category; Security redaction constraints exist separately | exact structured logging schema/mandatory fields beyond Security |
| ENG-R05 | configuration API | EXISTENCE RECOVERED / EXACT RULE MISSING | configuration/secrets were covered; typed/fail-closed principles appear in current owner material | exact load/validation/override API and precedence |
| ENG-R06 | runtime/framework/toolchain | NOT RECOVERED / OWNER DECISION REQUIRED | no reliable post-Sprint-0 final stack/version recovered | exact language/framework/runtime selections or explicit NOT DEFINED IN v0.2 |
| ENG-R07 | formatter/linter/static analysis | NOT RECOVERED / OWNER DECISION REQUIRED | historical follow-up mentions machine-enforceable CI/static tooling | exact tools, versions and lock policy |
| ENG-R08 | complexity/duplication thresholds | PARTIAL RECOVERY | complexity/duplication review themes recovered; do-not-invent-arbitrary-threshold rule | exact numeric thresholds or explicit qualitative-only rule |
| ENG-R09 | test/coverage thresholds | PARTIAL RECOVERY | testability/quality gates theme and no-fake-result discipline recovered | exact thresholds or explicit no-numeric-threshold decision |
| ENG-R10 | CI required checks | PARTIAL RECOVERY | CI/merge quality theme recovered; current policy separates E1/E2/E3 | exact historical v0.2 check classes/names or explicit semantic-only requirement |
| ENG-R11 | review requirement | SUBSTANTIAL PARTIAL RECOVERY | code-review gates/merge requirements existed; current governance now requires E3 for implementation/repository tooling classes | exact historical reviewer count/approval semantics |
| ENG-R12 | Definition of Done | EXISTENCE RECOVERED / EXACT RULE MISSING | DoD explicitly existed | exact final DoD clauses/order |
| ENG-R13 | generated code/artifacts | NOT RECOVERED / OWNER DECISION REQUIRED | current governance has source-authority/reproducibility concepts, but not proof of final v0.2 text | exact generated-source/assets/editable-authority rules |
| ENG-R14 | dependency policy | PARTIAL RECOVERY | dependency/layering category recovered | exact allowed dependency directions and third-party introduction/review policy |
| ENG-R15 | concurrency | PARTIAL RECOVERY | concurrency/race/stale-worker review themes recovered | exact implementation/review/testing requirements |
| ENG-R16 | security review integration | SUBSTANTIAL PARTIAL RECOVERY | privileged boundary, no generic exec/fallback, fail-closed authorization strongly recovered | exact Engineering checklist obligations/approval points |
| ENG-R17 | performance implications | PARTIAL RECOVERY | performance implications were a review theme; quantitative optimization belongs to Performance owner | exact Engineering-vs-Performance review boundary wording |
| ENG-R18 | release quality gates | EXISTENCE/PARTIAL RECOVERY | merge/release quality category existed in broader Engineering/DevOps work | exact Engineering release acceptance conditions |
| ENG-R19 | final ordering / precedence | NOT RECOVERED / OWNER DECISION REQUIRED | current authority governance exists, but does not prove historical v0.2 ordering | exact final rule hierarchy/exceptions or owner-approved current replacement |
| ENG-R20 | SC-M01 consumption | PARTIAL RECOVERY | first implementation foundation only, no fake test results, CODE-BASELINE after implementation/review/verification are recovered/current | exact final Engineering review/DoD conditions for SC-M01 and first CODE-BASELINE |

## 4. Strong recovered Engineering invariants

Engineering should not silently weaken these historically recovered/project-final principles while recreating the ruleset:

- typed identifier namespaces remain distinct;
- WCP-USER != WCP-ADMIN;
- backend → wcpd non-root → bounded authenticated IPC → wcp-privd minimal root → explicit privileged primitive;
- no shell.exec/arbitrary command/generic sudo/systemctl/arbitrary signal/arbitrary privileged filesystem target;
- no web direct access to wcp-privd;
- control-plane PostgreSQL != hosted DB engines != tenant/application DB contents;
- authorization is explicit/fail closed;
- unknown privileged operation has no generic fallback;
- implementation consumes owner contracts rather than inventing permission/action IDs;
- timeout != definite failure;
- retry != reconciliation;
- rollback != compensation;
- execution success != verified success;
- UNKNOWN_OUTCOME requires observation/reconciliation before retry;
- trace/correlation identifiers never become authorization;
- no fabricated PASS/FAIL before execution;
- stale/superseded baseline must not be silently consumed;
- code review must examine architecture/boundaries/dependencies/naming/errors/security/performance/concurrency/tests/owner-contract alignment.

These are recovery/current-governance constraints, not a reconstructed full v0.2 file.

## 5. Toolchain warning

No reliable final post-Sprint-0 owner decision establishes:

- web/backend framework;
- PHP version;
- Go version;
- Node use/non-use;
- npm/pnpm/yarn;
- dependency managers;
- canonical build command;
- formatter/linter/static-analysis stack;
- numeric complexity/duplication/coverage thresholds.

Do not revive cancelled Sprint 0 choices as historical v0.2 facts.

## 6. Owner path

### Path A — retained exact v0.2 source

    REPUBLICATION TYPE = EXACT
    CONTENT CHANGE = NONE
    VERSION = v0.2
    PROVENANCE = exact retained source
    ENG-R01..R20 = resolved by source

Republish at the canonical path and verify against current cross-domain governance.

### Path B — exact source unavailable

Engineering must explicitly decide the missing ENG-R items using current owner constraints.

    REPUBLICATION TYPE = OWNER RECONSTRUCTION / AMENDMENT
    CONTENT CHANGE = REAL DELTA
    VERSION IMPACT = OWNER DECISION
    ENG-R01..R20 = owner decisions / not-applicable with rationale
    CROSS-DOMAIN REVIEW = Security/DevOps/QA/Performance as applicable

The historical Phase G/H3-C candidate must remain evidence, not authority.

## 7. PR #42 consequence

PR #42 currently has repeated E1 automated evidence and same-author E2 publication-control evidence.

It still lacks E3 independent review under current \`REVIEW-MERGE-POLICY.md\`.

Engineering may use the eventual final/re-published ruleset to review PR #42, but must not infer v0.2 from PR #42.

## 8. Closure readiness

    HISTORICAL RECOVERY HAS REACHED A USEFUL STOP = YES
    OWNER CHECKLIST COMPLETE = YES
    FINAL OWNER BODY / DECISIONS = MISSING
    CANONICAL ENGINEERING ARTIFACT = MISSING
    ISSUE #9 = OPEN

The next meaningful Engineering action is owner republication/reconstruction and exact-SHA review, not another generic historical search.
