# WCP360 — Materialized Markdown Inventory

Reference date: 2026-09-29

This inventory records the WCP360 Markdown files currently materialized and directly retrievable from Project/Library sources, and their GitHub publication state.

## Total materialized Markdown files

```text
SOURCE MATERIALIZED MARKDOWN = 147
GITHUB LIBRARY MIRROR = 147
MISSING FROM MIRROR = 0
```

Full mirror:

```text
documentation/library-mirror/
```

The mirror preserves the relative Project/Library paths and is documentary evidence only. It does not promote historical, candidate, staging, audit or superseded files to active implementation authority.

## Canonical implementation artifacts already published in GitHub

```text
specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/api/WCP360_API_IPC_CANONICAL_IMPLEMENTATION_EXPORT_MANIFEST.md
specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md
```

## Design System documentation

The active Design System baseline remains:

```text
DESIGN-BASELINE = V2.5.3.36.4
```

A curated snapshot is published under:

```text
documentation/design-system/V2.5.3.36.4/
```

The complete materialized Project/Library Markdown set, including staging/audit/history evidence, is retained in the library mirror.

## Documentation baseline

Canonical documentary baseline:

```text
DOC-BASELINE = v4.0.21.23
```

The exact canonical ZIP itself is not silently substituted by an older archive. Materialized Markdown evidence is versioned as-is; owner authority and WCP360-BM-0001 determine what is active.

## SC-M01 evidence gate

```text
CANONICAL IMPLEMENTATION EVIDENCE IN specifications/ = 5 / 8

REMAINING:
- DB Model v1.0
- Test Baseline / SC-M01 requirements v1.0
- Engineering Ruleset ENG-WCP-RULESET-0001 v0.2

SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Tracking:
- governance/CANONICAL-ARTIFACT-INDEX.md
- governance/SC-M01-CANONICAL-ARTIFACT-STATUS.md
- issue #13
