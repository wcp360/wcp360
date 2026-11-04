# WCP360 Documentation

Technical documentation is grouped here so the repository root can focus on project entry points, implementation components and delivery. Resume candidate development through the [Core working guide](../WCP360_Core/WORKING-GUIDE.md) and [verified status](../WCP360_Core/STATUS.md). The [baseline manifest](manifests/WCP360-BM-0001.md) and [project status](governance/PROJECT-STATUS.md) retain their authority; directory names, candidate code and passing CI do not establish production readiness.

## Daily development entry points

| Need | Maintained source |
| :--- | :--- |
| Find the official repository location and transfer record | [Repository transfer](governance/maintenance/REPOSITORY-TRANSFER-2026-11-03.md) |
| Find consolidated histories, source locations and final branch disposition | [Integration register](governance/branches/BRANCH-INTEGRATION-2026-11-02.md) and [completed closure](governance/branches/BRANCH-COMPLETION-2026-11-03.md) |
| Resume a bounded Core change and keep documents consistent | [Core working guide](../WCP360_Core/WORKING-GUIDE.md) |
| See the verified revision, evidence, current limits and next checkpoint | [Core status](../WCP360_Core/STATUS.md) |
| See the owner decision for the twelve unresolved privileged IPC bindings | [Deny-until-approved decision for issue #91](governance/decisions/OWNER-DECISION-SC-M01-PRIVILEGED-IPC-BINDINGS-2026-11-03.md) |
| Follow dependency order and acceptance | [Core roadmap](../WCP360_Core/ROADMAP.md) |
| Find code, owners, CLI and resource locations | [Navigation](../WCP360_Core/NAVIGATION.md) and [structure](../WCP360_Core/STRUCTURE.md) |
| Find multi-server, storage, containers, VM, performance and defensive-security scope | [Nine-domain infrastructure coverage](development/INFRASTRUCTURE-PREMIUM-COVERAGE.md) |
| Map named platform integrations and free reseller hosting features | [Eleven-area platform register](development/PLATFORM-INTEGRATIONS-COVERAGE.md) and [Core reseller guide](../WCP360_Core/src/Module/Tenancy/RESELLER-PACKAGES.md) |
| Find Community/PRO boundaries, premium packages and the Core license | [Edition strategy](development/EDITIONS-AND-PREMIUM.md), [Core licensing](../WCP360_Core/LICENSING.md), [business modules](../WCP360_Modules/CATALOG.md) and [premium connectors](../WCP360_Plugins/premium/CATALOG.md) |
| Inspect the 233 capability statuses and 24 service families | [Capability register](../WCP360_Core/CAPABILITY-MAP.md) and [service register](../WCP360_Core/resources/services/ROADMAP.md) |

Each document owns a different answer. Update its owning register rather than creating another feature list, service matrix or progress percentage.

## Documentation index

| Directory | Purpose | How to use it |
| :--- | :--- | :--- |
| [development/](development/README.md) | Core/SDK/plugin source organization, lifecycle, setup and qualification strategy. | Follow dependency order and applicable owner contracts; distinguish executable candidate paths from documented reservations and production acceptance. |
| [specifications/](https://github.com/wcp360/wcp360/blob/da265278c20e230d5ba4614647a8271f4b0d7506/documentation/specifications/README.md) | Published owner specifications: API, IPC, database, security, QA, Engineering and DevOps. | Selected versions and accepted scope come from the manifest. |
| [contracts/](contracts/README.md) | Contract navigation and implementation consumption guidance. | Use the published specification bodies rather than creating duplicate contracts. |
| [manifests/](manifests/WCP360-BM-0001.md) | Baseline membership, versions and implementation gates. | Read the latest explicit decisions; older entries are retained as history. |
| [governance/](governance/PROJECT-STATUS.md) | Project status, decisions, handoffs, reviews, audits and publication evidence. | Follow owner authority and the [review policy](https://github.com/wcp360/wcp360/blob/da265278c20e230d5ba4614647a8271f4b0d7506/documentation/governance/REVIEW-MERGE-POLICY.md). |
| [design-system/](https://github.com/wcp360/wcp360/blob/da265278c20e230d5ba4614647a8271f4b0d7506/documentation/design-system/V2.5.3.36.4/README.md) | Published Design System Markdown reference. | Documentary reference; UI prototypes remain in WCP360_Prototype. |
| [library-mirror/](https://github.com/wcp360/wcp360/blob/da265278c20e230d5ba4614647a8271f4b0d7506/documentation/library-mirror/README.md) | The 147-file materialized Project/Library mirror and its integrity manifest. | Preserve source bytes and provenance; mirror publication does not promote authority. |
| [Consolidated developer snapshot](../WCP360_Developper/WCP360_CONSOLIDATED_MD_2026-09-27/) | Earlier consolidated documentation snapshots. | Historical, non-normative evidence. |
| [visuals/](visuals/README.md) | Explanatory project visuals. | Read alongside current owner specifications. |

## Canonical implementation inputs

Commit-pinned references below preserve the exact selected source and remain accessible when a historical copy is absent from the checkout. The baseline manifest selects the applicable versions; a navigation link neither changes their bytes nor grants production acceptance.

| Input | Published artifact |
| :--- | :--- |
| API | [API Contract v1.0](specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md) |
| IPC | [IPC-B/1](specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md) |
| Permissions | [Permission Registry v1.0](specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md) |
| Security | [Security Baseline v1.0](specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md) |
| Database | [Database Model v1.0](specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md) |
| QA | [SC-M01 Test Baseline v1.0](https://github.com/wcp360/wcp360/blob/da265278c20e230d5ba4614647a8271f4b0d7506/documentation/specifications/tests/WCP360_SC-M01_Test_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md) |
| Engineering | [Selected v0.3 ruleset, retained legacy filename](https://github.com/wcp360/wcp360/blob/da265278c20e230d5ba4614647a8271f4b0d7506/documentation/specifications/engineering/WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md) |
| DevOps | [DevOps Baseline v1.0](https://github.com/wcp360/wcp360/blob/da265278c20e230d5ba4614647a8271f4b0d7506/documentation/specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md) and [inert daemon toolchain addendum](https://github.com/wcp360/wcp360/blob/da265278c20e230d5ba4614647a8271f4b0d7506/documentation/specifications/devops/WCP360_SC-M01_DAEMON_FOUNDATION_TOOLCHAIN_ADDENDUM_v1.0.md) |

The October 31 relocation preserves every file under specifications and manifests byte-for-byte. Historical publication paths inside accepted artifacts remain provenance locators; apply the [path migration map](PATH-MIGRATION.json) when resolving them in the current checkout. No baseline, approval, security invariant or implementation gate changes through this reorganization.

## Repository navigation

- [Project overview](../README.md), [architecture](../ARCHITECTURE.md) and [plugin catalogue](../PLUGIN_CATALOG.md).
- [Core application candidate](../WCP360_Core/README.md), [UI prototype](../WCP360_Prototype/README.md), [developer corpus](../WCP360_Developper/README.md), [plugins](../WCP360_Plugins/) and [qualified release records](../WCP360_Releases/README.md).
- [Core engine](development/CORE-ENGINE.md), [plugin lifecycle](development/PLUGIN-LIFECYCLE.md), [delivery](development/DELIVERY-AND-RELEASES.md), [SDK](../WCP360_SDK/README.md) and [build procedures](../WCP360_Build/README.md).
- [Repository structure and history access](REPOSITORY-STRUCTURE.md), [branch reconciliation](governance/branches/BRANCH-RECONCILIATION-2026-10-31.md), [contribution guide](../CONTRIBUTING.md) and [security reporting](../SECURITY.md).

## Documentary provenance

`DOC-BASELINE = v4.0.21.23` remains the selected documentary baseline. The physical canonical archive `WCP-Design-System-Spec-v4.0.21.23-global-canonical-registry-consolidation.zip` has not been reconstructed or substituted; publication remains tracked by issues #16 and #24.

The available 42-Markdown consolidated snapshot under `WCP360_Developper/WCP360_CONSOLIDATED_MD_2026-09-27/` predates final H3-CM/H3-V2 closure and does not replace accepted owner artifacts. The [materialized inventory](https://github.com/wcp360/wcp360/blob/da265278c20e230d5ba4614647a8271f4b0d7506/documentation/MATERIALIZED-MARKDOWN-INVENTORY.md) and [147-file mirror manifest](https://github.com/wcp360/wcp360/blob/da265278c20e230d5ba4614647a8271f4b0d7506/documentation/library-mirror/MANIFEST.md) retain their original evidence. When the exact DOC archive becomes available, preserve its bytes and relative paths under `documentation/baselines/v4.0.21.23/` with reviewed integrity metadata.
