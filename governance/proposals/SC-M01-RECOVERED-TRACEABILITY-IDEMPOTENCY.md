# WCP360 — Recovered / Canonical Evidence — Traceability and Idempotency

Recovery date: 2026-10-02
Status: EVIDENCE NARROWING — NON-NORMATIVE
Affected decision points: D04, T05, H04

This note separates behavior that is already FINAL/canonical from the smaller pieces that still require owner decisions.

## 1. Current canonical traceability facts

API 1.0 defines typed boundaries:

    permission_id != action_id != job_type != event_id
    != audit_event_id != audit_record_id
    != ipc_operation_id != error_code

It also defines:

    audit_event_id  = semantic audit event TYPE
    audit_record_id = immutable persisted OCCURRENCE
    correlation_id  = relationship/grouping identifier

and states:

    mandatory audit loss is forbidden
    state-changing DB mutations use atomic DB/outbox audit durability where applicable
    external/privileged effects require durable pre-effect intent
    and durable post-effect result/audit evidence

API job semantics already define:

    timeout != definite failure
    retry != reconciliation
    rollback != compensation
    execution success != verified success

and:

    dispatch occurred but completion cannot be proven
    → UNKNOWN_OUTCOME
    → reconciliation mandatory
    → blind retry forbidden

IPC-B carries:

    request_id
    correlation_id
    job_id?
    action_id
    ipc_operation_id
    resource identity
    authorization_context_ref

and requires correlated completion/failure evidence.

## 2. Recovered final H3-CM/H3-V2 traceability evidence

Targeted history recovery confirms final decisions that:

    audit_event_id = semantic TYPE
    audit_record_id = immutable OCCURRENCE
    audit_event_id 1:N audit_record_id
    audit_record_id != correlation_id/request_id/job_id

and that linkage identifiers are not authorization evidence.

Recovered final workflow semantics include:

    durable intent
    → execute
    → durable result/audit

with:

    uncertain external effect
    → UNKNOWN_OUTCOME
    → observation/reconciliation
    → escalation if unresolved

and final/closed traceability was described as:

    request
    → authorization context
    → action
    → operation
    → outcome
    → durable audit occurrence

This is compatible with current API/Security/IPC exports.

## 3. D04 corrected classification

D04 is therefore:

    OPEN / PARTIALLY DEFINED

Already canonical/recovered:

- typed identifiers remain distinct;
- request/correlation/job IDs are linkage, not authorization;
- audit event type and audit occurrence are distinct;
- durable intent exists before external effect;
- durable result/audit is required after effect;
- uncertain outcome requires reconciliation;
- origin action and technical IPC operation are both traceable fields/concepts.

Still open:

- exact parent workflow → technical step relation in the current canonical authorization registry;
- exact audit event type for each intermediate technical step versus only parent workflow;
- firewall event naming/stage mismatch;
- exact PARTIAL_EFFECT / partially-succeeded audit representation;
- final reconciliation event/result naming;
- whether every technical step gets a distinct audit_record or some are nested/linked under a parent record;
- exact persistence/FK/order relationship between job, audit_record, outbox/event and reconciliation entities;
- how late results after UNKNOWN_OUTCOME are attached to the durable occurrence.

No new audit schema/event taxonomy is created by this note.

## 4. Current canonical idempotency facts

API 1.0 profile RK already defines:

    Idempotency-Key required for mutations
    same key + same semantic request
    → same logical result

    same key + different semantic request
    → IDEMPOTENCY_CONFLICT

API profile RN adds:

    submission protected by idempotency key
    execution never blindly redispatched after UNKNOWN_OUTCOME
    reconciliation precedes retry

IPC-B per-operation rows already classify technical protection as examples including:

    key-required
    key-protected
    revision-protected
    operation-ID protected
    plan-ID protected
    desired-state protected
    replay-safe

Therefore T05/H04 are not blank idempotency design problems.

## 5. H04 corrected classification

H04 is:

    OPEN / PARTIALLY DEFINED

Already canonical:

- mutations under RK require an Idempotency-Key;
- semantic replay behavior is fixed;
- conflicting reuse returns IDEMPOTENCY_CONFLICT;
- unsafe execution cannot be blindly redispatched.

Still open at HTTP transport level:

- whether Idempotency-Key is exclusively an HTTP header;
- whether logical idempotency_key entries in request-schema lists represent the same transport value or an actual body field;
- if two locations are accepted for compatibility, precedence/equality/refusal rules;
- exact HTTP validation/error status mapping.

The proposal recommendation “single transport header, no duplicate body source” remains an owner decision, not a recovered final rule.

## 6. T05 corrected classification

T05 is:

    OPEN / PARTIALLY DEFINED

Already canonical:

- API logical idempotency semantics;
- request_id/correlation_id/job_id are distinct identifiers;
- IPC operations declare protection classes;
- UNKNOWN_OUTCOME is not a blind-retry condition.

Still open:

- durable deduplication key representation;
- scope (principal/tenant/action/resource/version);
- semantic request canonicalization/hash inputs;
- relation between API Idempotency-Key and technical IPC protection class;
- relation to request_id/job_id/operation-ID;
- concurrent acquisition behavior;
- retained logical result representation;
- retention/expiry/reuse safety;
- physical uniqueness/transaction constraints;
- cross-tenant collision/isolation prevention.

Physical persistence remains Database #6.

## 7. QA implications

Already-determined cases can be asserted at contract level once transport bindings are ratified:

- same key + same semantic request = same logical result;
- same key + different semantic request = IDEMPOTENCY_CONFLICT;
- UNKNOWN_OUTCOME is not blindly retried;
- correlation_id alone never authorizes anything.

Still-blocked physical tests include:

- concurrent duplicate mutation race;
- crash after intent / before effect;
- crash after effect / before durable result;
- retention/expiry reuse;
- DB uniqueness and claim behavior.

## 8. Decision-matrix effect

No decision ID is closed.

Classification becomes:

    D04 = OPEN / PARTIALLY DEFINED
    H04 = OPEN / PARTIALLY DEFINED
    T05 = OPEN / PARTIALLY DEFINED

The total count of open semantic decision IDs is unchanged.

This prevents owners from re-deciding already-final semantics while keeping persistence/transport/event details explicit.

## 9. Gate

    CANONICAL SPECIFICATIONS MODIFIED = NO
    DB SCHEMA CREATED = NO
    HTTP ROUTE/HEADER BINDING CREATED = NO
    APPLICATION CODE = NONE
    APPLICATION TEST EXECUTIONS = 0
    SC-M01 = PAUSED — TARGETED EVIDENCE
    CODE-BASELINE = NONE
