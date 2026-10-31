# WCP360 — Recovered Engineering v0.2 Historical Evidence

**Status:** HISTORICAL RECOVERY EVIDENCE ONLY — NON-NORMATIVE  
**Recovery date:** 2026-10-03  
**Target final owner export:** WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md  
**Logical identity:** ENG-WCP-RULESET-0001 v0.2  
**Issue:** #9

This file preserves what can be recovered from historical WCP360 Engineering / Code Review work.

It deliberately separates:
1. final owner metadata, which is confirmed;
2. PHASE G / H3-C candidate content, which is recoverable but was not itself the final canonical export;
3. unknown final-body details, which must not be reconstructed.

This file is not Engineering authority and must never be copied into specifications/engineering/ as a substitute for the final owner export.

## 1. Confirmed final-owner metadata

Recovered project history confirms:

    ENGINEERING RULESET = ENG-WCP-RULESET-0001
    VERSION = v0.2
    FINAL APPROVED CONTENT = YES
    HISTORICAL CONTENT INCLUDED = NO
    CONTENT CHANGE = NONE
    READY FOR SOURCE CODE CONSUMPTION = YES
    BLOCKER = NONE

The exact final export filename is confirmed as:

    WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md

What is not confirmed is the complete final file body.

## 2. Recoverable candidate identity

A prior Engineering harmonization artifact existed as:

    ENG-WCP-RULESET-0001 v0.2 — PHASE G HARMONIZED CANDIDATE

Recovered status:

    HARMONIZATION COMPLETE — CANDIDATE

This candidate must remain distinct from the later FINAL APPROVED metadata.

## 3. Candidate coverage recovered from Phase G

The candidate covered, at minimum:

- repository architecture;
- naming and typed namespaces;
- module/component boundaries;
- dependency rules and layering;
- error/failure handling;
- configuration;
- secrets;
- logging;
- testability;
- security;
- maintainability;
- complexity;
- duplication;
- concurrency;
- code review gates;
- merge requirements;
- Definition of Done.

The previous Phase E engineering rules remained the working base for these categories during harmonization.

## 4. Recovered candidate invariants

### Typed namespaces remain distinct

Recovered Engineering rule:

    permission_id != action_id != job_type != event_id

The project-wide typed-identifier model also preserves distinctions among permission, action, job, event, IPC operation, error and audit identifiers.

Lexical equality does not collapse semantic types.

### Security / privileged boundary

Recovered candidate architecture:

    backend
    → wcpd (non-root)
    → bounded authenticated IPC
    → wcp-privd (root minimal)
    → explicit privileged primitive

Recovered forbidden implementation patterns include:

    shell.exec
    arbitrary command execution
    arbitrary executable
    generic sudo proxy
    generic systemctl proxy
    arbitrary privileged filesystem write/target
    arbitrary signal

Web code must not directly access wcp-privd.

WCP-USER and WCP-ADMIN remain separate surfaces.

Control-plane PostgreSQL, hosted database engines and tenant/application DB contents remain separate authority domains.

## 5. Fail-closed / authorization behavior recovered

Recovered candidate requirements include:

- authorization is explicit and fail closed;
- unknown permission/action/resource conditions do not become implicit allow;
- UI visibility does not authorize;
- generic or unknown privileged operations have no fallback path;
- implementation consumes approved owner contracts rather than manufacturing permission/action identifiers.

## 6. Failure semantics recovered

Candidate/review logic preserved distinctions such as:

    timeout != definite failure
    retry != reconciliation
    rollback != compensation
    execution success != verified success

Recovered H3-C/H3-V2 extensions include explicit treatment of:

    UNKNOWN_OUTCOME
    RECONCILIATION_REQUIRED

For a possibly executed external effect with no provable final result:

    observe
    → reconcile
    → only then decide retry/final state

A blind destructive retry is not acceptable Engineering behavior.

## 7. H3-C refresh — traceability classifications

Recovered H3-C refresh added explicit graph/traceability defect classes:

    orphan
    dangling
    missing-edge

Recovered intent:
- orphan = artifact/entity lacks a required parent/owner/reference;
- dangling = reference points to an absent/unresolvable target;
- missing-edge = both endpoints exist but a required relationship/traceability link is absent.

Exact final wording and every sub-classification are not recoverable.

## 8. H3-C refresh — stale baseline control

Recovered check:

    ACTIVE BASELINE OR EXPLICIT MASTER AUTHORIZATION

Implementation/review must not silently consume stale/superseded baselines.

A reference outside the active baseline requires explicit authority rather than selecting a newer-looking version.

## 9. H3-C refresh — recovery review

Recovered Engineering review requirements include examination of:

- failure after durable intent;
- failure before/after dispatch;
- lost result / lost acknowledgement;
- UNKNOWN_OUTCOME;
- reconciliation before retry;
- restart/recovery behavior;
- stale executor/worker risk;
- compensation versus rollback;
- proof that a final result is observed/verified rather than guessed.

The exact final v0.2 checklist wording is not recoverable.

## 10. Traceability envelope recovered

Historical harmonization/review work required preservation of typed traceability identifiers represented by:

    request_id
    correlation_id
    trace_id
    span_id
    job_id
    action_id
    ipc_operation_id
    audit_event_id
    audit_record_id
    incident_id

Not every identifier necessarily belongs in every component.

A trace/correlation identifier must never become authorization.

## 11. Test-state discipline recovered

Candidate/H3-V2 rules preserved:

    tests = PLANNED / READY / BLOCKED where applicable
    PASS = 0 before real execution
    FAIL = 0 before real execution

No test result may be fabricated merely because a contract exists.

A later executable candidate may be tested while CODE-BASELINE = NONE; the baseline is established after implementation/review/verification.

## 12. Review / merge / DoD evidence recovered

Historical context confirms that Engineering v0.2 defined or carried:

- Code Review Gates;
- Merge Requirements;
- Definition of Done.

The exact full final lists are not recoverable.

Recovered minimum review themes include:

- readability;
- maintainability;
- architecture/boundaries;
- dependencies/layering;
- naming/conventions;
- error handling;
- security;
- performance implications;
- concurrency;
- tests;
- owner-contract alignment.

These themes are also reflected in the current non-authoritative Engineering input pack.

They must not be mistaken for the complete final v0.2 review checklist.

## 13. Explicitly unresolved / non-blocking candidate follow-ups

Recovered candidate/H3-C status identified several items as not numerically/finally fixed:

- machine-enforceable CI gates;
- exact static-analysis/tooling stack;
- complexity thresholds;
- duplication thresholds;
- test coverage thresholds;
- dependency scanning;
- SBOM policy/mechanism;
- lockfile enforcement details;
- performance thresholds/benchmarks where measurement-derived.

Recovered rule:

    do not invent arbitrary quantitative thresholds

These gaps were treated as non-blocking follow-up in the candidate phase, not as permission for SOURCE CODE to choose arbitrary values later.

Current AUDIT-WCP-0113 and issue #9 still require the actual final Engineering owner position before implementation compliance can be claimed.

## 14. Candidate status after H3-C refresh

Recovered historical status language includes:

    ENG-WCP-RULESET-0001 v0.2 + H3-C REFRESH DELTA
    NON-CRITICAL OMISSIONS CORRECTED

and later READY WITH NON-BLOCKING FOLLOW-UP style status in harmonization/review context.

These statuses describe the candidate/review package, not a recovered byte-identical FINAL APPROVED export.

## 15. What cannot be promoted from this evidence

This evidence cannot establish:

- exact repository directory/layering rules from final v0.2;
- exact allowed/forbidden dependency graph;
- exact naming conventions for every language/component;
- exact exception/error class policy;
- exact logging schema;
- exact configuration API;
- exact formatter/linter/type-checker/static-analyzer tools;
- exact language/framework/runtime selection;
- exact complexity/duplication/coverage thresholds;
- exact required CI check names;
- exact review count or GitHub merge settings;
- exact Definition of Done text;
- exact release-quality gates;
- exact final ordering/wording of rules.

Therefore:

    FINAL ENGINEERING METADATA = CONFIRMED
    FINAL ENGINEERING BODY = NOT RECOVERED
    PHASE G/H3-C CANDIDATE EVIDENCE = PARTIALLY RECOVERED
    CANDIDATE → FINAL PROMOTION = FORBIDDEN
    ISSUE #9 = OPEN

## 16. Relationship to current repository governance

Current main contains separate governance that was not reconstructed from the candidate:

- governance/AUTHORITY-PRECEDENCE.md;
- governance/SC-M01-SCOPE.md;
- governance/REVIEW-MERGE-POLICY.md;
- governance/audits/AUDIT-WCP-0100/CLOSURE-MATRIX.md.

These current files constrain current work, but they do not prove what the missing final Engineering v0.2 file said.

Likewise, PR #42 repository tooling is an implementation candidate requiring Engineering/DevOps/Security review; it is not the missing ruleset.

## 17. Owner-assisted republication

Engineering should use this evidence only as a retrieval aid.

If Engineering has the original final v0.2 source:

    republish exact owner file
    CONTENT CHANGE = NONE
    provide provenance/commit/source

If Engineering must reconstruct/amend its final rules from candidate + later decisions:

    do not claim byte-identical recovery
    declare real content delta
    declare version impact
    cross-check current governance/security/QA/DevOps contracts
    publish as an explicit owner decision

MASTER must not convert this historical candidate evidence into specifications/engineering/.

## 18. Gate state

    ENGINEERING RULESET = ENG-WCP-RULESET-0001 v0.2
    FINAL OWNER METADATA = CONFIRMED
    FINAL BODY FULLY RECOVERED = NO
    HISTORICAL CANDIDATE CONTENT PARTIALLY RECOVERED = YES
    CANONICAL IMPORT = NO
    ISSUE #9 = OPEN

    SC-M01 EVIDENCE = 5 / 8
    SC-M01 = PAUSED — TARGETED EVIDENCE
    SC-M02 = NOT AUTHORIZED
    CODE-BASELINE = NONE
