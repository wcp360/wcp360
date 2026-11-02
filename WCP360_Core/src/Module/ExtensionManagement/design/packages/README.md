# Optional package and premium lifecycle

**Status: design reservation, recorded 2026-11-02. The implemented registry still supports its fixed reference candidate only; this directory implements no general package manager.**

[ExtensionManagement](../../README.md) remains the single Core package-lifecycle owner. This location prepares its existing responsibility within the thirteen-module structure. It adds no automatic discovery, production manifest, commercial contract, installed package or permission grant. Start with the [Core structure](../../../../../STRUCTURE.md), [extension resource index](../../../../../resources/extensions/README.md) and [detached lifecycle design](../../../../../../documentation/development/PLUGIN-LIFECYCLE.md).

## Package types and ownership

| Kind | Source and authority | Removal boundary |
| :--- | :--- | :--- |
| Built-in Core module | Controlled Core source and release; existing domain owns its rules/data. | Change through supported Core updates/migrations, not runtime plugin uninstall. |
| Optional free or premium plugin | Independent package under [WCP360_Plugins](../../../../../../WCP360_Plugins/README.md), dedicated dependencies/process/storage and approved APIs. | Drain/revoke contributions and preserve declared data; Core and existing hosted resources remain operable. |
| Active engine/provider adapter | Its existing Core resource owner retains the sites, services or data it manages. | Refuse removal until dependent resources have an accepted replacement, migration or ownership transfer. |

Premium is a commercial attribute of an optional package, not an extra execution privilege or a second lifecycle manager. Authentication/permissions belong to [IdentityAccess](../../../IdentityAccess/README.md), commercial rights to [Tenancy](../../../Tenancy/design/licensing/README.md), durable work to [Operations](../../../Operations/README.md), and Core installation/update/recovery to [Setup](../../../Setup/README.md).

Installation, enablement, licensing and resource authorization are separate decisions. An installed package starts disabled. A valid entitlement does not admit an unknown publisher, satisfy dependency/capability checks or grant cross-tenant access. A valid package signature does not prove commercial rights.

## Admission and isolated execution

The accepted production package contract must define identity/publisher, immutable artifact evidence, compatibility, dependencies/conflicts, capability changes, settings/secret references, owned data/migrations, approved contributions, budgets, egress, health, recovery and removal behavior. This reservation invents no manifest fields, endpoint names, tables, signing protocol or package state enum.

Existing candidate manifests remain distinct authoring inputs until a versioned schema and explicit adapters are accepted. Directory presence must not execute code. Build each plugin's dependencies independently in the controlled build lane; package installation must not edit the Core lock/vendor, register an unrestricted Symfony bundle, load plugin Twig into Core, expose arbitrary root hooks or resolve unreviewed dependencies inside the panel.

Use a dedicated non-root process identity, bounded resources/network access and scoped revocable credentials. HTTP, CLI, UI contributions, jobs and events invoke the same authorized owning use cases. The plugin receives neither a direct privileged broker channel nor a host Docker socket. A premium package cannot bypass these limits.

## Planned lifecycle

The phases below describe requirements; they are not new canonical states or implemented commands.

| Phase | Required decision and durable boundary |
| :--- | :--- |
| Discover and stage | Inspect bounded trusted metadata and exact artifacts without executing package code; reject invalid trust, paths and compatibility. |
| Install disabled | Admit dependencies, capabilities, isolation and owned migrations; record partial/uncertain preparation honestly. |
| Enable | Recheck operator authority, commercial rights where applicable, resource scope and readiness before publishing approved contributions. |
| Run | Reauthorize concrete resource effects, enforce budgets, bound retries/logs and contain repeated failures. |
| Disable or quarantine | Refuse new work, withdraw entry points, drain/checkpoint or fence jobs and revoke access without interrupting unrelated Core services. |
| Update | Review exact artifact/capability/data changes, prepare recovery, drain the previous instance and verify the candidate before explicit reactivation. |
| Uninstall | Require a disabled/drained package and resolved consumer dependencies; remove only declared package-owned components. Retain owned data by default. |
| Purge | A separate explicit authorized decision over declared plugin-owned data, retention and recovery; never imply deletion of Core/tenant resources or audit history. |

A failed migration or missing acknowledgement is not proof of rollback. Preserve durable evidence and reconcile before replay. No promise of universal zero downtime is made for active engine replacement or incompatible data changes.

## Commercial expiry and continuity

Use Tenancy's future local rights result; do not contact a separate vendor from every request or maintain a competing entitlement cache in each controller. Trial expiry, subscription expiry, unknown license state and commercial downgrade require an explicit product policy before enforcement. This document chooses no price, trial/grace period or offline protocol.

Commercial transitions must preserve essential Core login/administration, security, hosted services and baseline backup/recovery. Do not delete package/customer data, stop a hosted workload or purge resources merely because a trial ends. Any restriction on new premium work and treatment of in-flight operations must follow the accepted policy, current authorization and safe lifecycle boundary.

Core must remain usable with zero optional plugins and during catalogue/license/vendor outages. Removing a management integration does not remove the sites, applications, databases, mailboxes, volumes or remote resources it helped manage. Keep a Core-readable inventory and recovery path. If an essential dependency cannot be retained or transferred safely, refuse removal and expose the reason.

## Acceptance to define before implementation

| Boundary | Required refusal or recovery evidence |
| :--- | :--- |
| Artifact and manifest | Unknown/revoked publisher, wrong digest/signature, malformed/oversized archives, traversal, symlink escape and unsupported manifest versions. |
| Compatibility | Missing/conflicting/cyclic dependencies, unsupported Core/API/provider versions and undeclared capability increases. |
| Authority | A paid/installed/enabled package still fails for anonymous, wrong role/tenant/resource, stale revision, revoked credentials or missing effect approval. |
| Isolation | Crash, timeout, memory/log/queue pressure, unapproved egress and attempted Core dependency/database/file or privileged-channel access. |
| Lifecycle | Failed health publication, racing enable/disable/update, active jobs, interrupted migrations, uncertain effects and reboot recover without unsafe replay. |
| Retention and continuity | Uninstall preserves data by default; explicit purge remains confined. Trial/vendor failure or plugin removal preserves Core, existing workloads, inventory and recovery. |

These requirements need future synthetic contract/lifecycle and disposable-runtime evidence. This directory executes no tests, installs no package and does not promote capability or release status.