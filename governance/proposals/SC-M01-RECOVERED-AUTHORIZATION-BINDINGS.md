# WCP360 — Recovered Evidence — Privileged API/IPC Bindings

Recovery correction date: 2026-10-02
Historical decision period searched: 2026-09-27 → 2026-09-28
Status of this file: RECOVERED EVIDENCE — NOT CURRENT CANONICAL AUTHORITY
Provenance correction: NO EXPLICIT OWNER-RATIFIED/FROZEN PROOF FOUND FOR B01–B05

This file corrects the provenance of the previously published recovery note.

The identifiers below are real and are present in the current FINAL API/IPC exports. However, the historical phrase `FROZEN_BY_API_IPC_OWNER` was found in an **assistant-authored H3-V2 freeze/output**, not in an independently recovered explicit owner statement declaring these five cross-domain bindings FINAL/RATIFIED.

Therefore this file must not describe B01–B05 or D01 as recovered owner-ratified decisions.

## 1. What is verifiably present today

Current canonical API/IPC exports contain the relevant identifiers and per-contract rows.

### Service reload

Current canonical identifiers:

    API-WCP-0802
    permission_id = servers.services.manage
    origin/API action_id = servers.service.reload

    IPC-WCP-1029
    IPC row action_id = servers.service.reload
    ipc_operation_id = service.reload

This proves the identifiers/contract rows are current canonical content.

It does **not** by itself prove that Security's broad parent/action cell has already been harmonized to the exact origin→technical binding intended by the historical H3-V2 output.

### Service restart

Current canonical identifiers:

    API-WCP-0803
    permission_id = servers.services.manage
    origin/API action_id = servers.service.restart

    IPC-WCP-1030
    IPC row action_id = servers.service.restart
    ipc_operation_id = service.restart

Same conclusion: current identifiers are canonical; the exact cross-domain parent-binding interpretation still requires owner harmonization/confirmation.

### Firewall apply

Current canonical identifiers:

    API-WCP-0809
    permission_id = network.firewall.manage
    origin/API action_id = network.firewall.apply

    IPC-WCP-1026
    IPC row action_id = firewall.policy.apply
    ipc_operation_id = firewall.policy.apply
    surface/scope = WCP-ADMIN / server

The distinction between API origin action and the technical IPC row is visible in current canonical content.

The exact owner-approved rule connecting those namespaces is still the audit gap.

### Database grants

Current canonical API:

    API-WCP-0305
    permission_id = databases.grants.manage
    origin/API action_id = databases.grants.set

Current canonical IPC:

    IPC-WCP-1023
    action_id = database_grant.apply
    ipc_operation_id = database_grant.apply

    IPC-WCP-1024
    action_id = database_grant.revoke
    ipc_operation_id = database_grant.revoke

Security also requires tenant/resource constraints and forbids arbitrary SQL.

These rows establish the current identifiers. They do not recover a final owner statement proving exactly when an authorized grants.set workflow may invoke apply, revoke, or both, nor the exact physical grant_set representation.

## 2. Historical evidence classification

Targeted history search found three classes of evidence.

### A — provisional/candidate material

Earlier H3-C/H3-CM material treated the relevant API/IPC registries and many per-operation mappings as candidate/partial/pending.

This is not final authority.

### B — assistant-authored H3-V2 freeze/output

A later assistant H3-V2 output used the listed API-WCP/IPC-WCP identifiers and the label:

    FROZEN_BY_API_IPC_OWNER

That output is useful evidence of how the closing package was assembled.

It is **not** sufficient proof that a separate API/IPC owner explicitly issued a FINAL/FROZEN/RATIFIED decision for each B01–B05 binding.

### C — current canonical exports

API 1.0 and IPC WCP360-IPC-B/1 are now FINAL canonical exports and contain the identifiers/rows above.

Current Security v1.0 additionally states:

    wcpd validates the origin (permission_id, action_id) binding
    wcp-privd enforces bounded ipc_operation_id and target constraints

But Security's operation matrix still contains broad permission-like parent/action values for several rows, which is exactly why AUDIT-WCP-0104 remains open.

## 3. Corrected conclusion for B01–B05

The correct evidence classification is:

    B01–B05 CURRENT CANONICAL IDENTIFIERS = YES
    B01–B05 EXPLICIT OWNER-RATIFIED CROSS-DOMAIN BINDING RECOVERED = NO
    HISTORICAL H3-V2 ASSISTANT FREEZE EVIDENCE = YES
    CURRENT CANONICAL HARMONIZATION / OWNER CONFIRMATION = REQUIRED

SOURCE CODE must not infer aliases from text similarity.

## 4. Corrected conclusion for D01

The architecture already establishes distinct typed namespaces:

    permission_id != action_id != ipc_operation_id

and current Security establishes:

    wcpd validates origin permission/action
    wcp-privd validates bounded technical ipc_operation_id/target

The current API/IPC rows demonstrate that origin/API action and technical IPC action can be lexically identical in some workflows and different in others.

What remains unresolved is the exact current normative rule for:

- what IPC request `action_id` represents when it differs from the origin action;
- how `authorization_context_ref` carries/resolves the origin action;
- how parent→technical-step membership is registered and checked;
- how substitutions are refused.

Therefore:

    D01 = OPEN — OWNER HARMONIZATION REQUIRED
    NOT = recovered final owner decision

The v0.2 recommendation may still be reviewed, but it is not retroactively ratified.

## 5. What remains useful from the historical H3-V2 output

It remains useful as a **candidate intended mapping** for owner review:

- service reload should not be authorized merely by matching permission text;
- service restart remains distinct from reload;
- firewall apply remains WCP-ADMIN/server-bound;
- grants technical steps remain inside the authorized database/identity/tenant workflow;
- technical sub-operations do not automatically create application permissions.

Each must be accepted/amended/rejected by the current owner against the canonical exports.

## 6. Required owner response

For B01–B05 and D01:

    REFERENCE EXAMINED =
    DECISION = ACCEPT / AMEND / REJECT / ALREADY DEFINED
    CANONICAL EVIDENCE =
    REQUIRED DELTA =
    VERSION IMPACT =
    TESTS =
    DEPENDENCIES =

If an owner can point to an exact existing FINAL source that ratifies the mapping, this recovery classification may be upgraded using that evidence.

## 7. Gate

    RECOVERED EXPLICIT OWNER RATIFICATION = NO
    CURRENT CANONICAL IDENTIFIERS PRESENT = YES
    OWNER HARMONIZATION REQUIRED = YES
    APPLICATION CODE = NONE
    QA EXECUTION = 0
    SC-M01 = PAUSED — TARGETED EVIDENCE
    CODE-BASELINE = NONE

This correction prevents an assistant-authored historical freeze label from being mistaken for owner authority.
