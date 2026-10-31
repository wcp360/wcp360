# Core engine implementation guide

The Core is a modular Symfony application with essential hosting ownership and a built-in extension-management domain. It must remain usable with zero optional plugins. This is a target design/acceptance guide; current runtime authority remains the [manifest](../manifests/WCP360-BM-0001.md).

## Domain ownership

Use `src/Module/<Domain>/{Domain,Application,Infrastructure,UI}`. Start with the modules required by the accepted slice; directory names alone are not authorization to implement every domain.

| Domain | Responsibilities retained in Core |
| :--- | :--- |
| IdentityAccess | Local users, credentials, MFA, sessions, role/permission enforcement, revocation and recovery. |
| Tenancy | Accounts, tenants, organizations, delegated reseller context, subscriptions/entitlements and resource ceilings. |
| Hosting | Sites, domains, authoritative DNS, TLS, PHP runtime pools and baseline web-engine adapters. |
| Mail | Baseline mailbox/domain management and the selected mail-service adapter; optional clients/relays do not own the baseline. |
| Data | Hosted database lifecycle, users/grants and control-plane/tenant database separation. |
| Files | Canonical scoped file-operation authority, quota/storage boundaries and confinement requirements. |
| Operations | Durable jobs, idempotency, outbox, leases/fencing, retries, approvals, cancellation and uncertain-outcome reconciliation. |
| BackupRestore | Baseline local backup/restore, retention, recovery references and restore verification. |
| Transfers | Source/destination inventory, compatibility, resource mapping, cutover, verification and recovery across instances; reuses existing resource owners and Operations. |
| Observability | Local health, durable audit, logs, metrics and baseline administrator alerts. |
| Platform | Configuration, node inventory, approved service/network adapters and agent interaction through typed ports. |
| ExtensionManagement | Package registry, dependencies, compatibility, capabilities, lifecycle, health and isolated contribution registration. |
| Setup | Installation/setup state, restricted wizard orchestration, enrollment, upgrades and recovery handoff. |

All API, database, permissions, IPC and event identifiers come from their accepted namespaces. Do not infer an action grant from a plugin ID, job ID, menu item or IPC operation ID.

## Locate functions, commands and Linux configuration

The [Core navigation index](../../WCP360_Core/NAVIGATION.md) maps a requested change to the relevant source. Named module directories document ownership; create Domain/Application/Infrastructure/UI code directories with their first accepted implementation.

Symfony service wiring, packages and routes belong in config. Versioned service templates/profiles belong in [resources/services](../../WCP360_Core/resources/services/README.md): Nginx, Apache, PHP-FPM, Postfix, Dovecot, Rspamd, PostgreSQL, MySQL, MariaDB, Redis, DNS, systemd, SSH, firewall, log rotation and backup tooling. A resource folder does not enable or qualify that engine.

Domain CLI classes belong in UI/Console and reuse the application use case. Fixed system operations remain in the accepted Go agent/broker boundary. Rendering templates does not grant PHP write access to /etc; a validated, revision-bound operation must use approved primitives and verify the observed service result.

[Dependency ownership](../../WCP360_Core/DEPENDENCIES.md) distinguishes Core Composer dependencies, independently versioned SDK/plugin graphs, Go modules, specialized Python tools and OS packages. No global functions directory, universal dependency tree, duplicate service default or second privileged command registry is introduced.

The [README capability map](../../WCP360_Core/CAPABILITY-MAP.md) traces 233 capabilities to thirteen Core owners. Instance transfers have their own coordinator; migrations/ remains Core schema history. Shared HTTP/security primitives and chart presentation have named source locations without creating another domain authority.

## Shared services built once

| Foundation | Expected behavior and proof |
| :--- | :--- |
| Tables and data access views | Pagination, multi-sort, filters, search, selected columns, saved views and bounded export; tenant/surface filters enforced by the server. |
| Forms | Reusable field definitions, server validation, error mapping, CSRF, upload bounds and accessible labels. |
| UI components | Twig components for navigation, dialogs, confirmation, notifications, empty/loading/error states and charts. |
| Accessibility and layout | Keyboard/focus behavior, semantics, contrast, responsive layouts, localization and theme support. |
| Authorization policy | Shared policy/permission evaluation with current resource, ownership, delegation and entitlement checks; no duplicate security logic per controller. |
| Operation plumbing | Typed results/errors, correlation, audit, idempotency, safe retries, durable intent/outcomes and reconciliation. |
| Operational resilience | Bounded work, timeouts, resource budgets, cancellation, visible dependency failure and local recovery. |

A shared UI component cannot bypass the domain's authorization. An export, bulk action or background job reauthorizes its concrete resources. Thin controllers and Twig presentations call application services; a global functions/ directory must not become an untyped service locator.

## Symfony frontend and process contexts

The target is PHP 8.4 / Symfony with Twig, Symfony UX Turbo/Stimulus, reusable Twig components, AssetMapper and the selected CSS pipeline. Pin exact framework/component/build versions and generate a real application lock as part of the approved bootstrap.

ADMIN and USER may reuse reviewed source code but execute with separate origins, cookies, session stores/claims, runtime identities and database grants. Workers have a dedicated identity. Shared code does not imply a shared security context or one universal credential set.

Keep first-party templates in templates/admin, templates/user, templates/components and templates/setup. Hosted customer content and detached plugin templates are not loaded as Core Twig sources.

## Extension manager built into Core

The manager must provide:

- catalog/installed inventory, publisher identity and trusted artifact metadata;
- compatibility, dependency-cycle, conflict, active-engine and capability-diff checks;
- bounded download/staging, digest/signature/trust/revocation validation and package-path checks;
- isolated installation and owned-data migration with durable lifecycle records;
- explicit enablement, health-based activation and atomic publication of approved contributions;
- supervised health, bounded logs/metrics, crash quarantine and circuit breaking;
- drain/checkpoint/fencing of active jobs on disable, update or removal;
- credential/subscription/route revocation and explicit retention/export/purge handling;
- local recovery and administration when optional plugins, marketplaces or vendor services are unavailable.

The UI is only a client of these server-side operations. Production manifest fields, endpoint names, database persistence and exact lifecycle enums require the appropriate owner contracts; do not invent tables or grant aliases from this design list.

## Go and Python boundaries

```text
Symfony application / approved worker path
  -> non-root wcpd
  -> authenticated bounded IPC-B
  -> minimal-root wcp-privd
  -> one registered privileged primitive
```

The source folders agents/wcpd and agents/wcp-privd keep runtime roles separate. A common repository path is not a shared Unix identity or one combined daemon. No PHP process, plugin, Python diagnostic or ordinary worker gains direct privileged-broker access.

Go module manifests and toolchains are maintained per approved component. The current accepted inert CLI is still reviewed under SOURCE/daemon on its existing branch. Moving its source requires updating gates/paths and rerunning exact integration evidence.

Python is reserved for specialized non-root diagnostics, not privileged execution, Core business ownership or a substitute application backend.

## Core acceptance journey

Use synthetic resources on the supported target:

1. Install and complete restricted setup; establish local administrator access and recovery.
2. Create an account/tenant and a site/domain with the approved adapters.
3. Observe a hosted response, issue/configure the qualified TLS workflow and verify the result.
4. Exercise files/database and baseline local backup/restore with proper scope separation.
5. Restart workers/agents and recover interrupted jobs without duplicate destructive effects.
6. Install, enable, crash, disable and uninstall an optional reference plugin.
7. Verify Core login, local administration, audit, baseline backup/recovery and the pre-existing hosted site continue.
8. Inject denied tenant/surface/permission input, plugin dependency failures and uncertain acknowledgements.
9. Upgrade from a qualified previous state and rehearse the documented recovery path.

Every result needs a candidate SHA, environment, assertion, outcome and retained evidence. The existing full capability/test inventories determine the final release's applicable coverage; this journey alone is not whole-product certification.
