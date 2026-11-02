# Repository structure

> **Current navigation — November 3, 2026:** development sources are consolidated on main. Follow the [integration register](governance/branches/BRANCH-INTEGRATION-2026-11-02.md), [Core working guide](../WCP360_Core/WORKING-GUIDE.md) and [current status](../WCP360_Core/STATUS.md). The dated reorganization account below remains historical; SOURCE/research are separate retained candidates.

**Effective:** October 31, 2026. This is a directory and navigation change, not implementation, owner-contract ratification or release authorization.

```text
wcp360/
├── .github/
├── README.md
├── ARCHITECTURE.md
├── CONTRIBUTING.md
├── SECURITY.md
├── PLUGIN_CATALOG.md
├── documentation/
│   ├── development/
│   ├── contracts/
│   ├── governance/
│   ├── manifests/
│   ├── specifications/
│   └── reference snapshots and visuals
├── WCP360_Core/
│   ├── src/
│   ├── templates/
│   ├── agents/
│   ├── installer/
│   └── Symfony configuration/assets/tests
├── WCP360_Plugins/
│   ├── _template/
│   ├── wcp360-file-manager-xampp/
│   └── wcp360-insights-connector/
├── WCP360_SDK/
├── WCP360_Build/
├── WCP360_Releases/
├── WCP360_Prototype/
└── WCP360_Developper/
```

## Implementation and documentation boundaries

| Location | Responsibility |
| :--- | :--- |
| [WCP360_Core](../WCP360_Core/NAVIGATION.md) | Indexed business modules, commands, dependencies, Symfony configuration and Linux-service resources; separate agents, installer/setup and tests. Documentation scaffold; runtime implementation pending. |
| WCP360_Prototype | Browser UI prototype, styles, assets and design notes. |
| WCP360_Developper | Existing developer/product corpus and retained historical snapshots. |
| WCP360_Plugins | One independently developed/tested package per detached plugin and an authoring template. |
| WCP360_SDK | Versioned public integration contracts/client and synthetic test support; implementation pending. |
| WCP360_Build | Controlled environment/assembly/qualification/publication procedures; implementation pending. |
| WCP360_Releases | Qualified version records and immutable artifact references; none currently qualified. |
| documentation | Owner specifications, contracts, manifests, governance and documentary reference material. |
| Root Markdown files | GitHub project entry points, architecture, security reporting, contribution guidance and plugin navigation. |

## Why the root src directory was removed

`src/` contained one 425-byte README and no code. That README used an obsolete paused-state summary and duplicated the existing `WCP360_Core` reservation. Removing the root placeholder does not remove an application implementation.

`WCP360_Core/src/` now reserves the Symfony source layout with README guidance; no application classes are supplied by this scaffold. Implement actual code only within the accepted slice. Existing daemon and platform candidates on other branches keep their reviewed `SOURCE/` paths until separately reconciled; this cleanup does not move or approve them.

## Documentation moved under one root

| Previous root | Current location | Files moved |
| :--- | :--- | ---: |
| `contracts/` | `documentation/contracts/` | 1 |
| `governance/` | `documentation/governance/` | 99 |
| `manifests/` | `documentation/manifests/` | 1 |
| `specifications/` | `documentation/specifications/` | 11 |

All **112 files** are retained. The entire specifications tree (11 files, including its indices/addendum) and the baseline manifest keep their original Git blobs. Navigation-only changes affect selected README/publication links. Existing snapshot JSON, mirror manifests and exact commit citations remain historical evidence rather than rewritten records.

Root security reporting remains in `SECURITY.md`, and GitHub templates/ownership remain in `.github/`. Security policy changes are limited to document locators; reporting rules, contact, boundaries and assessment guidance are preserved.

## Archive removal and recovery

The old `archive/` tree contained **2,118 files**. **1,865** have identical blobs and modes elsewhere in the retained repository. The other **253** are historical versions or import metadata and remain available at the immutable pre-cleanup commit:

- [Complete previous archive](https://github.com/yyogas/wcp360/tree/112c790111e192156c405c83cde0ddd2eeafd481/archive).
- [Original import inventory](https://github.com/yyogas/wcp360/blob/112c790111e192156c405c83cde0ddd2eeafd481/archive/imports/2026-10-30/INVENTORY.json).
- [Earlier Design System import](https://github.com/yyogas/wcp360/tree/112c790111e192156c405c83cde0ddd2eeafd481/archive/imports/2026-10-30/workspace/WCP360_Design_System).

`archive/` is removed from the current tree; Git history is not rewritten. The 253 historical versions are not described as duplicates. Current Design System and developer sources are retained. Previously removed Internal Management and Official Website folders are not restored.

## Core / plugins / release organization

The [development strategy](development/README.md) defines the ordered Core foundation, public SDK seam, isolated plugin host, reference plugin, installer/wizard and qualification work. Follow [plugin lifecycle acceptance](development/PLUGIN-LIFECYCLE.md) rather than loading detached code into Core.

WCP360_Release was renamed to WCP360_Releases as a publication boundary. Its earlier unqualified v1.0.0/v1.1.0 templates moved to [Core installer scaffolds](../WCP360_Core/installer/scaffolds/README.md); their shell bytes and modes remain unchanged. The former latest alias is removed until a stable target is actually qualified. No qualified release has been deleted.

## Existing branches and pinned consumers

Use [PATH-MIGRATION.json](PATH-MIGRATION.json) for old-to-current prefix mapping and unchanged specification blobs. Older PRs may still reference root `specifications/`, `manifests/`, `governance/` or archive paths in their workflows and scripts.

Before merging those PRs, adapt their active consumers to the new paths, rerun relevant tests on the actual integration and bind review to the resulting SHA. Keep historical commit citations and frozen evidence records intact. Do not merge an old branch by restoring retired root directories.

The ten existing open candidates and their branch heads/bases are unchanged by this cleanup. No new CI execution, independent E3 approval, audit closure, CODE-BASELINE, privileged functionality or production release is claimed.
