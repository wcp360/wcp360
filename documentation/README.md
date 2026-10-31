# WCP360 Documentation

Technical documentation is grouped here so the repository root can focus on project entry points, implementation components and delivery. Start with the [baseline manifest](manifests/WCP360-BM-0001.md) and [project status](governance/PROJECT-STATUS.md); directory names do not establish authority or implementation readiness.

## Documentation index

| Directory | Purpose | How to use it |
| :--- | :--- | :--- |
| [specifications/](specifications/README.md) | Published owner specifications: API, IPC, database, security, QA, Engineering and DevOps. | Selected versions and accepted scope come from the manifest. |
| [contracts/](contracts/README.md) | Contract navigation and implementation consumption guidance. | Use the published specification bodies rather than creating duplicate contracts. |
| [manifests/](manifests/WCP360-BM-0001.md) | Baseline membership, versions and implementation gates. | Read the latest explicit decisions; older entries are retained as history. |
| [governance/](governance/PROJECT-STATUS.md) | Project status, decisions, handoffs, reviews, audits and publication evidence. | Follow owner authority and the [review policy](governance/REVIEW-MERGE-POLICY.md). |
| [design-system/](design-system/V2.5.3.36.4/README.md) | Published Design System Markdown reference. | Documentary reference; UI prototypes remain in WCP360_Design_System. |
| [library-mirror/](library-mirror/README.md) | The 147-file materialized Project/Library mirror and its integrity manifest. | Preserve source bytes and provenance; mirror publication does not promote authority. |
| [snapshots/](snapshots/) | Earlier consolidated documentation snapshots. | Historical, non-normative evidence. |
| [visuals/](visuals/README.md) | Explanatory project visuals. | Read alongside current owner specifications. |

## Canonical implementation inputs

| Input | Published artifact |
| :--- | :--- |
| API | [API Contract v1.0](specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md) |
| IPC | [IPC-B/1](specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md) |
| Permissions | [Permission Registry v1.0](specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md) |
| Security | [Security Baseline v1.0](specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md) |
| Database | [Database Model v1.0](specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md) |
| QA | [SC-M01 Test Baseline v1.0](specifications/tests/WCP360_SC-M01_Test_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md) |
| Engineering | [Selected v0.3 ruleset, retained legacy filename](specifications/engineering/WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md) |
| DevOps | [DevOps Baseline v1.0](specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md) and [inert daemon toolchain addendum](specifications/devops/WCP360_SC-M01_DAEMON_FOUNDATION_TOOLCHAIN_ADDENDUM_v1.0.md) |

The October 31 relocation preserves every file under specifications and manifests byte-for-byte. Historical publication paths inside accepted artifacts remain provenance locators; apply the [path migration map](PATH-MIGRATION.json) when resolving them in the current checkout. No baseline, approval, security invariant or implementation gate changes through this reorganization.

## Repository navigation

- [Project overview](../README.md), [architecture](../ARCHITECTURE.md) and [plugin catalogue](../PLUGIN_CATALOG.md).
- [Core application reservation](../WCP360_Core/README.md), [UI prototype](../WCP360_Design_System/README.md), [developer corpus](../WCP360_Developper/README.md), [plugins](../WCP360_Plugins/) and [release preparation](../WCP360_Release/README.md).
- [Repository structure and history access](REPOSITORY-STRUCTURE.md), [branch reconciliation](governance/branches/BRANCH-RECONCILIATION-2026-10-31.md), [contribution guide](../CONTRIBUTING.md) and [security reporting](../SECURITY.md).

## Documentary provenance

`DOC-BASELINE = v4.0.21.23` remains the selected documentary baseline. The physical canonical archive `WCP-Design-System-Spec-v4.0.21.23-global-canonical-registry-consolidation.zip` has not been reconstructed or substituted; publication remains tracked by issues #16 and #24.

The available 42-Markdown consolidated snapshot under `snapshots/2026-09-27/` predates final H3-CM/H3-V2 closure and does not replace accepted owner artifacts. The [materialized inventory](MATERIALIZED-MARKDOWN-INVENTORY.md) and [147-file mirror manifest](library-mirror/MANIFEST.md) retain their original evidence. When the exact DOC archive becomes available, preserve its bytes and relative paths under `documentation/baselines/v4.0.21.23/` with reviewed integrity metadata.
