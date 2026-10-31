# WCP360 — Recovered Account-Override / Entitlement Evidence

**Status:** HISTORICAL RECOVERY EVIDENCE — NON-CANONICAL  
**Recovery date:** 2026-10-04  
**Target:** S06 account-override ceiling / AUDIT-WCP-0118  
**Current canonical sources:** Permission Registry v1.0 + Security Baseline v1.0

Purpose: prevent historical candidate entitlement/override designs from being mistaken for a FINAL Security decision.

## 1. Current canonical facts

Permission Registry v1.0 fixes:

    explicit DENY
    > account override
    > role grants
    > plan / feature envelope
    > default policy

It also fixes:

- DEMO is an additional hard restriction;
- reseller delegation cannot exceed reseller effective envelope;
- temporary privilege does not mutate persistent roles;
- approval never replaces execution-time authorization.

Security v1.0 fixes final authorization as an intersection including:

    identity
    surface
    action
    permission
    role constraints
    entitlement
    resource
    tenant boundary
    ownership/delegation
    policy/context
    authentication strength
    resource state
    approval when required

Therefore:

    permission-resolution ALLOW
    != sufficient final authorization

and an account override cannot by itself bypass another mandatory Security operand.

## 2. Historical material recovered

Earlier Design/functional material included models such as:

    RBAC
    → platform/reseller ceiling
    → plan
    → account override
    → resource override
    → account mode
    → security floor
    → effective entitlement

and candidate operations such as entitlement override set/reset.

Other historical candidate material stated examples equivalent to:

    permission allow + entitlement deny → DENY
    permission deny + entitlement allow → DENY
    explicit deny > inherited allow

These are useful design evidence.

However targeted recovery found no separately identifiable FINAL / RATIFIED / MASTER-CLOSED owner decision that answers the precise S06 question:

    may a positive account override exceed the plan/feature envelope?
    may it create/override entitlement?
    may it bypass a missing entitlement?

Some recovered Design material was marked FROZEN FOR CONTRACT PREPARATION or candidate rather than final Security authority.

## 3. Correct classification

The evidence supports:

    explicit DENY precedence = FINAL
    final Security intersection = FINAL
    entitlement remains a separate Security operand = FINAL
    reseller effective-envelope ceiling = FINAL
    DEMO hard restriction = FINAL in current Permission Registry

But:

    positive account-override ceiling = NOT FINALLY RECOVERED
    positive override → entitlement creation/bypass = NOT FINALLY RECOVERED

Therefore:

    S06 = OPEN — EXPLICIT OWNER DECISION REQUIRED

This is not a recovery failure that MASTER should fill by inference.

## 4. Safe implementation boundary before S06 decision

SOURCE CODE must not infer any of the following:

- account override can create an entitlement;
- account override can exceed plan/feature ceiling;
- account override can bypass DEMO;
- account override can bypass tenant/ownership/delegation;
- account override can bypass MFA/approval/resource state;
- account override can turn a Security DENY into final ALLOW merely because its resolution priority is high.

The only safe generic reading today is:

    account override participates in permission-resolution priority
    final authorization still requires all mandatory Security operands

The exact positive ceiling remains owner work.

## 5. Required Security owner decision

Security should publish one explicit rule for positive account overrides.

Decision packet:

    OVERRIDE POSITIVE CEILING =
    MAY EXCEED PLAN/FEATURE = YES / NO / CONDITIONAL
    MAY CREATE ENTITLEMENT = YES / NO
    MAY OVERRIDE MISSING ENTITLEMENT = YES / NO / CONDITIONAL
    DEMO INTERACTION =
    RESELLER INTERACTION =
    TENANT/OWNERSHIP EFFECT = NONE / explicit rule
    AUDIT EVENT =
    REVISION / CONCURRENCY MODEL =
    ERROR / SAFE EXTERNAL PROJECTION =
    VERSION IMPACT =

If the answer is conditional, the condition must be represented by an existing typed policy/entitlement rule or by an explicit new owner delta. Do not encode a hidden special case in application code.

## 6. Database impact

After Database #6 is available, Security/Database should identify:

- persisted override record;
- scope (account / tenant / resource / permission);
- positive/negative/INHERIT representation if applicable;
- revision/concurrency rule;
- active/effective dates if applicable;
- audit linkage;
- relation to plan/features and entitlement state.

This recovery note creates no table or column.

## 7. QA impact

Existing SQ cases already cover:

- explicit DENY + positive grant/override cannot become final ALLOW;
- permission ALLOW + entitlement missing → final DENY;
- tenant/ownership invalid → final DENY.

Additional positive-override cases remain BLOCKED until S06 owner decision:

    plan disallows + positive override
    feature absent + positive override
    entitlement absent + positive override
    DEMO + positive override
    reseller delegation ceiling + positive override

No TEST-* IDs or PASS/FAIL are created here.

## 8. Current state

    HISTORICAL CANDIDATE MODELS FOUND = YES
    FINAL POSITIVE-OVERRIDE CEILING FOUND = NO
    CURRENT CANONICAL SECURITY CHANGED = NO
    S06 = OPEN
    APPLICATION CODE = NONE
    APPLICATION TEST EXECUTION = 0
    SC-M01 = PAUSED
