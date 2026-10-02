# WCP360 — Recovered Owner Evidence — Privileged API/IPC Bindings

Recovery date: 2026-10-02
Historical decision period: 2026-09-28
Authority recovered: API / IPC owner validation with ratified/FROZEN mappings
Status of this file: RECOVERED EVIDENCE — NOT CURRENT CANONICAL AUTHORITY

This file preserves owner-decision evidence recovered from prior WCP360 conversations. It does not silently rewrite the current API, IPC or Security canonical exports.

## 1. Recovered owner mappings

### Service reload

Recovered owner mapping:

    API contract = API-WCP-0802
    permission_id = servers.services.manage
    origin action_id = servers.service.reload
    IPC contract = IPC-WCP-1029
    technical action / ipc operation = service.reload
    resource = server/service
    surface/scope = WCP-ADMIN / administrative server scope

Historical status reported:

    FROZEN_BY_API_IPC_OWNER
    ratified technical binding

No new permission named servers.service.reload was created.

### Service restart

Recovered owner mapping:

    API contract = API-WCP-0803
    permission_id = servers.services.manage
    origin action_id = servers.service.restart
    IPC contract = IPC-WCP-1030
    technical action / ipc operation = service.restart
    resource = server/service
    surface/scope = WCP-ADMIN / administrative server scope

Historical status:

    FROZEN_BY_API_IPC_OWNER
    ratified technical binding

No new permission named servers.service.restart was created.

### Firewall apply

Recovered owner mapping:

    API contract = API-WCP-0809
    permission_id = network.firewall.manage
    origin action_id = network.firewall.apply
    IPC contract = IPC-WCP-1026
    technical action / ipc operation = firewall.policy.apply
    resource = server + validated policy revision
    scope = server / WCP-ADMIN

Historical status:

    FROZEN_BY_API_IPC_OWNER
    ratified technical binding

### Database grant set → technical grant operations

Recovered owner evidence identifies:

    API contract = API-WCP-0305
    permission_id = databases.grants.manage
    origin action_id = databases.grants.set

Technical IPC steps:

    IPC-WCP-1023
    technical action / ipc operation = database_grant.apply

    IPC-WCP-1024
    technical action / ipc operation = database_grant.revoke

Recovered scope evidence:

    same hosted database
    same database identity/user
    same Tenant boundary
    no arbitrary SQL
    no new permission for each technical step

Historical owner output described the grant technical mappings as ratified/frozen, with the technical steps reusable through the authorized origin binding.

## 2. Recovered semantic rule

The recovered owner decisions distinguish:

    origin permission/action
    !=
    technical IPC action/operation

The technical IPC step does not create a new application permission.

The authorization chain preserves the origin binding and then selects an explicitly allowed bounded technical operation.

This is consistent with the current canonical Security concept that wcpd validates the origin permission/action binding while wcp-privd enforces the bounded ipc_operation_id and target constraints.

## 3. Why AUDIT-WCP-0104 still found a gap

The current final API/IPC exports preserve most of the identifiers above.

However the current Security matrix has rows where the action/approved-parent column uses broad permission-like names such as:

    servers.services.manage
    network.firewall.manage
    databases.grants.manage

That current export does not fully expose the recovered origin→technical mapping in a deterministic form.

Therefore the remaining problem is primarily:

    canonical republication / harmonization
    + exact target/revision/context constraints
    + tests

not a reason for SOURCE CODE to invent aliases.

## 4. What is recovered versus still open

Recovered owner evidence:

- reload origin → technical step;
- restart origin → technical step;
- firewall apply origin → technical step;
- grants.set origin → apply/revoke technical steps;
- technical action is distinct from origin action when needed;
- no new application permission is created for technical sub-operations.

Still not fully recovered/defined:

- exact trusted authorization-context lifecycle;
- exact API→IPC revision translation for expected/config/active revisions;
- exact physical grant_set representation;
- audit event stage naming and correlation;
- exact current normative wording to insert into Security/API/IPC.

## 5. Required republication

This recovered evidence should be handled by the relevant owners in one of three ways:

1. point to a current canonical exact source already containing the mapping;
2. republish the recovered mapping as a content-preserving canonical clause;
3. explicitly amend it, with version impact and rationale.

Until then:

    RECOVERED OWNER DECISION = YES
    CURRENT CANONICAL HARMONIZATION = REQUIRED
    SOURCE CODE CONSUMPTION FROM THIS FILE = FORBIDDEN

This file is evidence for owner review, not a normative contract.
