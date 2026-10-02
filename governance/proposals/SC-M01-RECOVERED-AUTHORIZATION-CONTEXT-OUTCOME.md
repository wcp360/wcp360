# WCP360 — Recovered Evidence — Authorization Context / UNKNOWN_OUTCOME

Recovery date: 2026-10-02
Historical decision period searched: 2026-09-27 → 2026-09-28
Status: RECOVERED EVIDENCE + CURRENT CANONICAL CROSS-CHECK — NON-NORMATIVE

This note narrows two open decision points without declaring them closed:

- C05 trusted authorization-context lifecycle;
- T04 timeout / UNKNOWN_OUTCOME boundary.

It distinguishes what is already current canonical, what is recovered from final H3-CM/H3-V2 evidence, and what remains genuinely undecided.

## 1. Current canonical authorization-context evidence

Security v1.0 §7 already defines the trusted authorization context as preserving at least:

    authorization_context_id
    origin_surface
    origin_actor_type
    origin_actor_reference
    origin_tenant_id when applicable
    origin_permission_id
    origin_action_id
    resource_type
    resource_id
    job_id
    correlation_id
    ipc_operation_id
    authorization_decision = ALLOW
    authorization_decided_at

It also states:

    context is not a bearer credential
    context is not accepted directly from browser surfaces
    unknown permission/action/IPC binding = DENY
    no side effect
    audit evidence

Security v1.0 §9 further states:

    wcpd validates the origin (permission_id, action_id) binding
    wcp-privd enforces bounded ipc_operation_id and target constraints

IPC WCP360-IPC-B/1 requires the request envelope to carry:

    authorization_context_ref

along with:

    request_id
    correlation_id
    job_id?
    action_id
    ipc_operation_id
    resource_type
    resource_id
    deadline_ms

Therefore C05 is not a blank design problem.

## 2. Recovered final historical evidence for wcpd participation

Targeted recovery of final H3-V2 Security material states that:

    authorization_context_id is created/validated by wcpd

and that the broker-facing trusted context passed through the IPC-B lifecycle preserves:

    origin permission/action
    resource
    tenant when applicable
    job
    correlation
    ipc_operation_id
    authorization decision/time

Recovered final architecture also keeps peer authentication separate:

    only wcpd authenticates as the IPC-B peer
    authorization context is policy/accountability data
    authorization context is not peer authentication
    no browser/token credential is passed as broker authentication

This recovered evidence is consistent with the current Security export.

It does not, however, recover the full storage/resolver lifecycle.

## 3. C05 corrected classification

C05 should therefore be read as:

    C05 = OPEN / PARTIALLY DEFINED

Already defined or recovered:

- trusted context field set exists;
- browser cannot self-assert it as a bearer credential;
- wcpd validates origin permission/action binding;
- wcp-privd checks bounded technical operation/target;
- historical final evidence says wcpd creates/validates authorization_context_id;
- IPC-B carries authorization_context_ref;
- peer authentication is separate from authorization context.

Still open / not recovered as an explicit complete final contract:

- where/how the context is durably stored or resolved;
- whether authorization_context_ref is opaque DB ID, signed handle, in-memory reference or another typed reference;
- integrity/authenticity mechanism between lookup and execution;
- expiration semantics;
- revocation semantics;
- execution-time freshness/revalidation rules;
- behavior when resolver/storage is unavailable;
- replay/substitution prevention for a valid reference against another target/action/job;
- retention/audit lifecycle;
- exact worker interaction with the context.

No storage mechanism, signing scheme, TTL or database table is chosen by this note.

## 4. Current canonical UNKNOWN_OUTCOME evidence

IPC-B/1 §5 already states:

    timeout != definite failure
    retry != reconciliation
    rollback != compensation
    execution success != verified success

and:

    if execution may have occurred but cannot be proven
    → UNKNOWN_OUTCOME
    → reconciliation
    → no blind retry

The IPC response already contains:

    outcome
    error_code?
    result?
    reconciliation_required

Each active operation also declares reconciliation/observation behavior.

Therefore the post-dispatch uncertain-result rule is already canonical.

## 5. T04 corrected classification

T04 should therefore be read as:

    T04 = OPEN / PARTIALLY DEFINED

Already canonical:

- timeout is not automatically a definite failure;
- execution possibly occurred + completion cannot be proven → UNKNOWN_OUTCOME;
- reconciliation is mandatory;
- blind retry is forbidden;
- execution success and verified success are distinct.

Still open in the transport/phase model:

- exact boundary that proves “dispatch did not occur”;
- exact error priority before dispatch;
- whether a deadline expiration before dispatch maps to OPERATION_TIMEOUT or another phase-specific error;
- how a durable final result is recovered when response transmission fails after result persistence;
- interaction with API idempotency/job status retrieval;
- how the implementation records the dispatch boundary durably enough for recovery;
- how deadline expiration during verification/reconciliation is represented.

The P0–P6 model remains a proposal to make those phase boundaries explicit; it does not redefine the already-final UNKNOWN_OUTCOME rule.

## 6. QA implications

Existing proposed cases should be classified accordingly.

### C05-related

Positive/negative tests should prove:

- browser cannot provide a trusted context directly;
- wrong origin permission/action fails;
- valid reference cannot be substituted to another resource/action;
- revoked/expired context behavior follows the future owner lifecycle rule;
- unavailable resolver fails closed.

The first two are grounded in current canonical Security; lifecycle cases remain BLOCKED/PENDING owner decision.

### T04-related

Tests should distinguish:

1. no dispatch proven;
2. dispatch possibly occurred;
3. effect/result verified and durably stored;
4. response lost after durable result;
5. result genuinely unknown.

The canonical rule already determines case 2:

    UNKNOWN_OUTCOME + reconciliation + no blind retry

Cases 1/4/5 still need transport/persistence specifics.

## 7. Decision-matrix effect

This evidence does **not** reduce the total number of open semantic decision IDs because C05 and T04 still contain unresolved lifecycle/phase semantics.

It changes their quality:

    C05 = OPEN / PARTIALLY DEFINED
    T04 = OPEN / PARTIALLY DEFINED

This avoids asking owners to redesign facts that already exist while preserving the truly missing details.

## 8. Gate

    CANONICAL SPECIFICATIONS MODIFIED = NO
    NEW STORAGE MODEL = NO
    NEW WIRE FIELD = NO
    APPLICATION CODE = NONE
    QA EXECUTION = 0
    SC-M01 = PAUSED — TARGETED EVIDENCE
    CODE-BASELINE = NONE

SOURCE CODE must consume the final owner contracts, not this recovery note.
