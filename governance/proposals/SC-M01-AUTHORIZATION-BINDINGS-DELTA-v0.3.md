# WCP360 — Authorization Bindings — proposal v0.3

Date: 2026-10-02
Status: PROPOSAL — REVIEW — NON-NORMATIVE
Supersedes for review: SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.2.md
Finding: AUDIT-WCP-0104

This v0.3 incorporates recovered H3-V2 mapping evidence, with corrected provenance: current canonical identifiers are verified, but no separate explicit owner-ratified/FROZEN proof was recovered for B01–B05.

Evidence companion:

governance/proposals/SC-M01-RECOVERED-AUTHORIZATION-BINDINGS.md

## 1. Reclassification

The following are no longer treated as blank design questions.

Current canonical mappings / historical H3-V2 intended mappings:

    B01 service reload
    B02 service restart
    B03 firewall apply
    B04 grants apply
    B05 grants revoke

Current architecture + historical H3-V2 intended semantic rule:

    origin permission/action
    → explicit bounded technical IPC action/operation

A technical IPC sub-operation does not create a new application permission.

Therefore the main remediation need is:

    republication / harmonization into current canonical owner artifacts
    + exact context/target/revision constraints
    + QA evidence

not invention by SOURCE CODE.

## 2. B01 — service reload

Recovered:

    permission = servers.services.manage
    origin action = servers.service.reload
    IPC operation = service.reload
    API-WCP-0802
    IPC-WCP-1029

Status:

    CURRENT CANONICAL IDENTIFIERS PRESENT
    OWNER BINDING HARMONIZATION REQUIRED

## 3. B02 — service restart

Recovered:

    permission = servers.services.manage
    origin action = servers.service.restart
    IPC operation = service.restart
    API-WCP-0803
    IPC-WCP-1030

Status:

    CURRENT CANONICAL IDENTIFIERS PRESENT
    OWNER BINDING HARMONIZATION REQUIRED

## 4. B03 — firewall apply

Recovered:

    permission = network.firewall.manage
    origin action = network.firewall.apply
    IPC operation = firewall.policy.apply
    API-WCP-0809
    IPC-WCP-1026
    server scope / WCP-ADMIN
    validated policy revision

Status:

    CURRENT CANONICAL IDENTIFIERS PRESENT
    OWNER BINDING HARMONIZATION REQUIRED

## 5. B04/B05 — grants

Recovered:

    permission = databases.grants.manage
    origin action = databases.grants.set
    API-WCP-0305

Technical steps:

    IPC-WCP-1023 = database_grant.apply
    IPC-WCP-1024 = database_grant.revoke

Current/required constraints:

    same database
    same DB identity/user
    same Tenant boundary
    no arbitrary SQL
    no new permission per technical step

Status:

    RECOVERED OWNER-FROZEN MAPPING
    DB PHYSICAL REPRESENTATION / EXACT DELTA STILL REQUIRED

## 6. D01 — origin action versus technical action

Current typed namespaces and Security responsibilities establish part of the architectural distinction needed by D01; historical H3-V2 output additionally proposed/froze the following interpretation:

    application permission/origin action
    != technical IPC action when the workflow requires a technical sub-step

and:

    no new application permission is created merely because a technical operation exists

Therefore:

    D01 = OPEN
    CURRENT OWNER HARMONIZATION = REQUIRED
    ASSISTANT H3-V2 FREEZE LABEL = NOT OWNER RATIFICATION

The remaining exact question is how the current IPC envelope field named action_id represents this distinction on the wire. If the current canonical IPC table already fixes it per row, the owner may resolve the gap by documenting that the envelope action_id is the technical row action and the origin action lives in the trusted authorization context. If another model is intended, it must be an explicit amendment.

## 7. Decisions still truly open

### D02 — trusted authorization-context lifecycle

Still open:

- producer;
- storage/resolver;
- binding;
- expiry/revocation;
- execution-time freshness;
- replay/substitution behavior;
- unavailable dependency behavior.

### D03 — API → IPC target/revision translation

Still open or dependent:

- expected_config_revision ↔ config_revision_id;
- expected_current_revision ↔ expected_active_revision;
- privileges[] ↔ physical grant_set representation;
- same-resource identity guarantees.

Database portions depend on #6.

### D04 — audit/result-stage correlation

Still open:

- origin request / job / technical step correlation;
- firewall audit-event naming/stage;
- partial effect;
- UNKNOWN_OUTCOME and final reconciliation event/result relationship.

## 8. Revised QA interpretation

The previously proposed C01–C17 remain useful.

They should now be read as tests of a recovered binding, not tests choosing among arbitrary mappings.

Especially:

- substitution from reload to restart remains DENY unless the context authorized restart;
- permission text alone is not an action alias;
- grants apply context does not automatically authorize revoke unless the approved grants.set workflow/context includes that step;
- same Tenant/database/identity target is required;
- technical step validity never replaces execution-time Security gates.

No tests are executed by this proposal.

## 9. Status summary

    B01–B05 = CURRENT CANONICAL IDENTIFIERS / OWNER HARMONIZATION REQUIRED
    D01 = OPEN / OWNER HARMONIZATION REQUIRED
    D02 = OPEN
    D03 = OPEN / DATABASE-DEPENDENT
    D04 = OPEN

Actual new semantic decisions remaining in this authorization delta:

    3

Historical H3-V2 intended mappings requiring current owner confirmation/harmonization:

    B01–B05 + D01

## 10. Gate

    CANONICAL CONTENT CHANGED BY THIS FILE = NONE
    APPLICATION CODE = NONE
    QA EXECUTION = 0
    SC-M01 = PAUSED — TARGETED EVIDENCE
    CODE-BASELINE = NONE

Historical evidence reduces ambiguity but does not prove owner ratification. SOURCE CODE cannot consume this proposal directly.
